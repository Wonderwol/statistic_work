<script>
(function () {
  const fmt = new Intl.NumberFormat('ru-RU');

  function safeArr(v) { return Array.isArray(v) ? v : []; }

  // Данные из index.php
  const years = safeArr(window.years);
  const totalOrganizations = safeArr(window.totalOrganizations);
  const schoolTypesLabels = safeArr(window.schoolTypesLabels);
  const schoolTypesData = safeArr(window.schoolTypesData);
  const nurseryData = safeArr(window.nurseryData);
  const basicData = safeArr(window.basicData);
  const specialData = safeArr(window.specialData);
  const pieLabels = safeArr(window.pieLabels);
  const pieData = safeArr(window.pieData);

  // ВАЖНО: filtersConfig должен быть в области видимости initFilter
  const filtersConfig = {
    'org_type': {
      isRadio: true,
      searchId: 'org_type-search',
      groupId: 'org_type-group',
      countId: 'org_type-count',
      clearId: 'org_type-clear',
      containerId: 'org_type-container',
      placeholder: 'Уровень представления данных'
    },
    'year': {
      isRadio: false,
      searchId: 'year-search',
      groupId: 'year-group',
      countId: 'year-count',
      clearId: 'year-clear',
      containerId: 'year-container',
      selectAllId: 'year-select-all',
      placeholder: 'Учебный год'
    },
    'locality': {
      isRadio: true,
      searchId: 'locality-search',
      groupId: 'locality-group',
      countId: 'locality-count',
      clearId: 'locality-clear',
      containerId: 'locality-container',
      placeholder: 'Тип местности'
    }
  };

  document.addEventListener('DOMContentLoaded', function () {
    Object.keys(filtersConfig).forEach(name => initFilter(name, filtersConfig[name]));

    // Закрытие дропдаунов по клику вне
    document.addEventListener('click', function (event) {
      let shouldCloseAll = true;

      Object.keys(filtersConfig).forEach(name => {
        const container = document.getElementById(filtersConfig[name].containerId);
        if (container && container.contains(event.target)) shouldCloseAll = false;
      });

      if (shouldCloseAll) {
        Object.keys(filtersConfig).forEach(name => {
          const group = document.getElementById(filtersConfig[name].groupId);
          const container = document.getElementById(filtersConfig[name].containerId);
          const fg = container ? container.closest('.filter-group') : null;

          if (group) group.classList.remove('active');
          if (container) container.classList.remove('active');
          if (fg) fg.classList.remove('dropdown-open');
        });
      }
    });

    const savedView = localStorage.getItem('nimro_open_view') || 'cards';
    if (savedView === 'table') showTable();
    else showCards();

    // Графики имеет смысл инициализировать только когда выбран режим "график"
    if (savedView !== 'table') initializeCharts();
  });

  function initFilter(filterName, config) {
    const searchInput = document.getElementById(config.searchId);
    const checkboxGroup = document.getElementById(config.groupId);
    const clearBtn = document.getElementById(config.clearId);
    const container = document.getElementById(config.containerId);
    const selectAllBtn = config.selectAllId ? document.getElementById(config.selectAllId) : null;

    if (!searchInput || !checkboxGroup) return;

    const checkboxes = checkboxGroup.querySelectorAll('input[type="' + (config.isRadio ? 'radio' : 'checkbox') + '"]');
    const checkboxItems = checkboxGroup.querySelectorAll('.checkbox-item');
    const noResults = checkboxGroup.querySelector('.no-results');

    function updateSelectedCount() {
      const countSpan = document.getElementById(config.countId);
      if (!countSpan) return;

      let checkedCount;
      if (config.isRadio) checkedCount = Array.from(checkboxes).some(cb => cb.checked) ? 1 : 0;
      else checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;

      countSpan.textContent = checkedCount;

      if (selectAllBtn && filterName === 'year') {
        selectAllBtn.textContent = checkedCount === checkboxes.length ? 'Снять все' : 'Выбрать все';
      }
    }

    function updateSearchInputText() {
      const selectedText = [];
      checkboxes.forEach(cb => {
        if (cb.checked) {
          const label = document.querySelector('label[for="' + cb.id + '"]');
          if (label) selectedText.push(label.textContent.trim());
        }
      });

      if (selectedText.length > 0) {
        if (filterName === 'year' && selectedText.length > 3) searchInput.value = `Выбрано ${selectedText.length} лет ▼`;
        else searchInput.value = selectedText.join(', ') + ' ▼';
      } else {
        searchInput.value = config.placeholder;
      }
    }

    function updateFilterState() {
      updateSelectedCount();
      updateSearchInputText();
    }

    // Открыть/закрыть список
    searchInput.addEventListener('click', function (e) {
      e.stopPropagation();

      // закрыть остальные
      Object.keys(filtersConfig).forEach(otherName => {
        if (otherName === filterName) return;
        const otherGroup = document.getElementById(filtersConfig[otherName].groupId);
        const otherContainer = document.getElementById(filtersConfig[otherName].containerId);
        const otherFG = otherContainer ? otherContainer.closest('.filter-group') : null;

        if (otherGroup) otherGroup.classList.remove('active');
        if (otherContainer) otherContainer.classList.remove('active');
        if (otherFG) otherFG.classList.remove('dropdown-open');
      });

      const willOpen = !checkboxGroup.classList.contains('active');
      checkboxGroup.classList.toggle('active', willOpen);
      if (container) container.classList.toggle('active', willOpen);
      const fg = container ? container.closest('.filter-group') : null;
      if (fg) fg.classList.toggle('dropdown-open', willOpen);
    });

    // Поиск (если input не readonly)
    searchInput.addEventListener('input', function () {
      const searchTerm = this.value.toLowerCase().trim();
      let hasVisibleItems = false;

      checkboxItems.forEach(item => {
        const label = item.querySelector('label');
        const text = (label ? label.textContent : '').toLowerCase();
        const ok = text.includes(searchTerm);
        item.style.display = ok ? 'flex' : 'none';
        if (ok) hasVisibleItems = true;
      });

      if (noResults) noResults.style.display = hasVisibleItems ? 'none' : 'block';
    });

    // Изменение чекбоксов/радио
    checkboxes.forEach(cb => {
      cb.addEventListener('change', function () {
        if (config.isRadio && this.checked) {
          setTimeout(() => {
            checkboxGroup.classList.remove('active');
            if (container) container.classList.remove('active');
            const fg = container ? container.closest('.filter-group') : null;
            if (fg) fg.classList.remove('dropdown-open');
          }, 250);
        }
        updateFilterState();
      });
    });

    // Клик по строке чекбокса
    checkboxItems.forEach(item => {
      item.addEventListener('click', function (e) {
        const input = this.querySelector('input');
        if (!input) return;
        if (e.target === input) return;

        if (config.isRadio) input.checked = true;
        else input.checked = !input.checked;

        input.dispatchEvent(new Event('change'));
      });
    });

    // Очистить
    if (clearBtn) {
      clearBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        checkboxes.forEach(cb => cb.checked = false);
        updateFilterState();
      });
    }

    // Выбрать все (для year)
    if (selectAllBtn) {
      selectAllBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
        updateFilterState();
      });
    }

    updateFilterState();
  }

  window.showCards = function () {
  localStorage.setItem('nimro_open_view', 'cards');

  // показать карточки/статистику
  document.querySelectorAll('.statistics').forEach(b => b.style.display = '');
  document.querySelectorAll('.stat-card').forEach(b => b.style.display = '');

  // показать графики
  document.querySelectorAll('.chart-container').forEach(b => b.style.display = '');

  // скрыть таблицу
  const tableView = document.getElementById('tableView');
  if (tableView) tableView.style.display = 'none';

  // кнопки
  const showCardsBtn = document.getElementById('showCardsBtn');
  const showTableBtn = document.getElementById('showTableBtn');
  if (showCardsBtn) showCardsBtn.classList.add('active');
  if (showTableBtn) showTableBtn.classList.remove('active');

  // если график был уничтожен в режиме таблицы — пересоздадим
  if (typeof window.__charts === 'undefined' || !window.__charts || !window.__charts.structure) {
    if (typeof window.initializeCharts === 'function') window.initializeCharts();
  }
};

window.showTable = function () {
  localStorage.setItem('nimro_open_view', 'table');

  // скрыть карточки/статистику
  document.querySelectorAll('.statistics').forEach(b => b.style.display = 'none');
  document.querySelectorAll('.stat-card').forEach(b => b.style.display = 'none');

  // скрыть графики
  document.querySelectorAll('.chart-container').forEach(b => b.style.display = 'none');

  // показать таблицу
  const tableView = document.getElementById('tableView');
  if (tableView) tableView.style.display = 'block';

  // кнопки
  const showCardsBtn = document.getElementById('showCardsBtn');
  const showTableBtn = document.getElementById('showTableBtn');
  if (showTableBtn) showTableBtn.classList.add('active');
  if (showCardsBtn) showCardsBtn.classList.remove('active');

  // реально "убить" график, чтобы он исчезал не только визуально
  if (window.__charts && window.__charts.structure) {
    window.__charts.structure.destroy();
    window.__charts.structure = null;
  }
};

  const charts = {};

  function baseOptions() {
  return {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: 'nearest', intersect: true },
    animation: { duration: 900, easing: 'easeOutQuart' },
    layout: { padding: { top: 6, right: 10, bottom: 6, left: 8 } },
    onHover: (evt, activeEls) => {
    const c = evt?.native?.target;
    if (c) c.style.cursor = activeEls && activeEls.length ? 'pointer' : 'default';
    },
    plugins: {
      legend: {
        labels: {
          boxWidth: 12,
          boxHeight: 12,
          padding: 14,
          font: { weight: '600' }
        }
      },
      tooltip: {
        backgroundColor: 'rgba(20,20,20,0.92)',
        padding: 12,
        cornerRadius: 10,
        titleColor: '#fff',
        bodyColor: '#fff',
        displayColors: true,
        callbacks: {
          label: (ctx) => {
            const label = ctx.dataset?.label ? `${ctx.dataset.label}: ` : '';
            const val = (ctx.parsed && typeof ctx.parsed === 'object') ? (ctx.parsed.y ?? 0)
                      : (typeof ctx.parsed === 'number') ? ctx.parsed
                      : (ctx.raw ?? 0);
            return label + fmt.format(val);
          }
        }
      }
    },
    scales: {
      x: {
        grid: { display: false },
        ticks: { color: 'rgba(44,62,80,0.85)', font: { weight: '600' } }
      },
      y: {
        grid: { color: 'rgba(0,0,0,0.06)' },
        ticks: {
          color: 'rgba(44,62,80,0.85)',
          callback: (v) => fmt.format(v)
        }
      }
    }
  };
}


  function destroyIfExists(key) {
    if (charts[key]) { charts[key].destroy(); charts[key] = null; }
  }

  window.initializeCharts = function () {
  if (typeof Chart === 'undefined') return;

  // Определяем major версию Chart.js (2 / 3 / 4)
  const major = (() => {
    const v = (Chart.version || '3').split('.')[0];
    const n = parseInt(v, 10);
    return Number.isFinite(n) ? n : 3;
  })();

  function destroyIfExists(key) {
    if (charts[key]) { charts[key].destroy(); charts[key] = null; }
  }

  function tooltipLabel(ctx) {
    // v3+: ctx.parsed может быть числом/объектом; v2: ctx.yLabel/ctx.xLabel
    const val =
      (ctx && ctx.parsed != null && typeof ctx.parsed === 'object' && ctx.parsed.y != null) ? ctx.parsed.y :
      (ctx && ctx.parsed != null && typeof ctx.parsed === 'number') ? ctx.parsed :
      (ctx && ctx.raw != null) ? ctx.raw :
      (ctx && ctx.yLabel != null) ? ctx.yLabel :
      0;
    return fmt.format(val);
  }

  function scalesBar() {
    if (major >= 3) {
      return {
        x: {
          title: { display: true, text: 'Тип' },
          ticks: {
            callback: function (value) {
              const label = this.getLabelForValue(value);
              return (label && label.length > 22) ? (label.slice(0, 22) + '…') : label;
            }
          }
        },
        y: {
          beginAtZero: true,
          title: { display: true, text: 'Количество' },
          ticks: { callback: (v) => fmt.format(v) }
        }
      };
    }
    // Chart.js v2
    return {
      xAxes: [{
        scaleLabel: { display: true, labelString: 'Тип' },
        ticks: {
          callback: function (label) {
            return (label && label.length > 22) ? (label.slice(0, 22) + '…') : label;
          }
        }
      }],
      yAxes: [{
        ticks: {
          beginAtZero: true,
          callback: function (v) { return fmt.format(v); }
        },
        scaleLabel: { display: true, labelString: 'Количество' }
      }]
    };
  }

  function scalesLine() {
    if (major >= 3) {
      return {
        x: { title: { display: true, text: 'Учебный год' } },
        y: {
          title: { display: true, text: 'Количество' },
          ticks: { callback: (v) => fmt.format(v) }
        }
      };
    }
    return {
      xAxes: [{ scaleLabel: { display: true, labelString: 'Учебный год' } }],
      yAxes: [{
        ticks: { callback: function (v) { return fmt.format(v); } },
        scaleLabel: { display: true, labelString: 'Количество' }
      }]
    };
  }

  function basePluginsNoLegend() {
    if (major >= 3) {
      return {
        legend: { display: false },
        tooltip: { callbacks: { label: tooltipLabel } }
      };
    }
    return {
      legend: { display: false },
      tooltips: { callbacks: { label: function (item) { return fmt.format(item.yLabel); } } }
    };
  }

  function basePluginsWithLegendRight() {
    if (major >= 3) {
      return {
        legend: { position: 'right' },
        tooltip: { callbacks: { label: tooltipLabel } }
      };
    }
    return {
      legend: { position: 'right' },
      tooltips: { callbacks: { label: function (item) { return fmt.format(item.yLabel); } } }
    };
  }

  // Настройки по умолчанию
  Chart.defaults.font && (Chart.defaults.font.family = 'Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial');
  Chart.defaults.color && (Chart.defaults.color = 'rgba(44, 62, 80, 0.9)');

  // Чтобы один график не ломал остальные — каждый в try/catch
  // 1) totalChart (line)
  try {
    const totalEl = document.getElementById('totalChart');
    if (totalEl) {
      destroyIfExists('total');
      charts.total = new Chart(totalEl.getContext('2d'), {
        type: 'line',
        data: {
          labels: years,
          datasets: [{
            label: 'Всего организаций',
            data: totalOrganizations,
            borderColor: '#6d444b',
            backgroundColor: 'rgba(109, 68, 75, 0.14)',
            pointRadius: 4,
            pointHoverRadius: 6,
            pointBackgroundColor: '#6d444b',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            borderWidth: 3,
            tension: 0.28,
            fill: true
            }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: (major >= 3) ? {
            tooltip: { callbacks: { label: tooltipLabel } }
          } : {
            tooltips: { callbacks: { label: function (item) { return fmt.format(item.yLabel); } } }
          },
          scales: scalesLine()
        }
      });
    }
  } catch (e) { console.error('totalChart error:', e); }

  // 2) schoolTypesChart (bar) — среднеобразовательные
  try {
    const typesEl = document.getElementById('schoolTypesChart');
    if (typesEl) {
      destroyIfExists('types');
      charts.types = new Chart(typesEl.getContext('2d'), {
        type: 'bar',
        data: {
          labels: schoolTypesLabels,
          datasets: [{
            label: 'Количество организаций',
            data: schoolTypesData,
            backgroundColor: 'rgba(109, 68, 75, 0.75)',
            borderRadius: 10,
            borderSkipped: false
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: basePluginsNoLegend(),
          scales: scalesBar()
        }
      });
    }
  } catch (e) { console.error('schoolTypesChart error:', e); }

  // 3) pieChart — ПЕРЕДЕЛЫВАЕМ В СТОЛБЧАТЫЙ "структура по типам" (один dataset)
  try {
    // 3) Структура по типам — ОДИН столбик (stacked)
const pieEl = document.getElementById('pieChart');
if (pieEl) {
  destroyIfExists('pie');

  // Палитра (хватит на 8+ типов; если типов больше — цвета циклятся)
  const palette = [
    'rgba(109, 68, 75, 0.80)',
    'rgba(152, 251, 152, 0.80)',
    'rgba(54, 162, 235, 0.80)',
    'rgba(255, 206, 86, 0.80)',
    'rgba(75, 192, 192, 0.80)',
    'rgba(153, 102, 255, 0.80)',
    'rgba(255, 159, 64, 0.80)',
    'rgba(201, 203, 207, 0.80)',
    'rgba(46, 204, 113, 0.80)',
    'rgba(231, 76, 60, 0.80)'
  ];

  // Делаем datasets: каждый тип = отдельный слой в одном столбике
 const datasets = pieLabels.map((label, i) => ({
  label,
  data: [Number(pieData[i] ?? 0)],
  backgroundColor: palette[i % palette.length],
  borderColor: 'rgba(255,255,255,0.95)',
  borderWidth: 2,
  stack: 'structure',
    barThickness: 99,
    maxBarThickness: 99,
    borderWidth: 5,
    hoverBorderWidth: 3,
    minBarLength: 12,

  borderRadius: 8,
  borderSkipped: false,
}));

  charts.pie = new Chart(pieEl.getContext('2d'), {
    type: 'bar',
    data: {
      labels: ['Структура'], // одна категория => один столбик
      datasets
    },
    options: Object.assign(baseOptions(), {
      plugins: {
        ...baseOptions().plugins,
        legend: { position: 'right' },
        tooltip: {
          ...baseOptions().plugins.tooltip,
          callbacks: {
            label: (ctx) => {
              const name = ctx.dataset?.label ? ctx.dataset.label + ': ' : '';
              const val = (ctx.parsed && typeof ctx.parsed === 'object') ? (ctx.parsed.y ?? 0) : (ctx.raw ?? 0);
              return name + fmt.format(val);
            }
          }
        }
      },
      scales: {
        x: { stacked: true },
        y: {
          stacked: true,
          beginAtZero: true,
          title: { display: true, text: 'Количество' },
          ticks: { callback: (v) => fmt.format(v) }
        }
      }
    })
  });
}
  } catch (e) { console.error('pieChart(bar structure) error:', e); }

  // 4) comparisonChart (bar) — сравнение по годам (если есть)
  try {
    const compEl = document.getElementById('comparisonChart');
    if (compEl && years.length > 1) {
      destroyIfExists('compare');
      charts.compare = new Chart(compEl.getContext('2d'), {
        type: 'bar',
        data: {
          labels: years,
          datasets: [
            { label: 'НОШ д/сад', data: nurseryData, backgroundColor: 'rgba(109, 68, 75, 0.75)', borderRadius: 10, borderSkipped: false },
            { label: 'ООШ',      data: basicData,   backgroundColor: 'rgba(152, 251, 152, 0.75)', borderRadius: 10, borderSkipped: false },
            { label: 'ОВЗ',      data: specialData, backgroundColor: 'rgba(54, 162, 235, 0.75)', borderRadius: 10, borderSkipped: false }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: (major >= 3) ? { tooltip: { callbacks: { label: tooltipLabel } } } : { tooltips: { callbacks: { label: function (item) { return fmt.format(item.yLabel); } } } },
          scales: (major >= 3)
            ? { y: { beginAtZero: true, ticks: { callback: (v) => fmt.format(v) } }, x: { title: { display: true, text: 'Учебный год' } } }
            : { yAxes: [{ ticks: { beginAtZero: true, callback: function (v) { return fmt.format(v); } } }], xAxes: [{ scaleLabel: { display: true, labelString: 'Учебный год' } }] }
        }
      });
    }
  } catch (e) { console.error('comparisonChart error:', e); }
};


  window.exportToExcel = function () {
    const table = document.querySelector('#tableView table');
    if (!table) { alert('Таблица не найдена!'); return; }

    const html = '<html><head><meta charset="UTF-8"></head><body><table border="1">' + table.innerHTML + '</table></body></html>';
    const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
    const url = URL.createObjectURL(blob);

    const a = document.createElement('a');
    a.href = url;
    a.download = 'статистика_образования_' + new Date().toLocaleDateString('ru-RU') + '.xls';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  };
})();


function baseOptions() {
  return {
    responsive: true,
    maintainAspectRatio: false,

    // УБРАТЬ внутренние отступы (те самые "поля")
    layout: { padding: 0 },

    interaction: { mode: 'nearest', intersect: true },
    animation: { duration: 900, easing: 'easeOutQuart' },

    plugins: {
      legend: {
        labels: {
          boxWidth: 12,
          boxHeight: 12,
          padding: 10,
          font: { weight: '600' }
        }
      },
      tooltip: {
        backgroundColor: 'rgba(20,20,20,0.92)',
        padding: 12,
        cornerRadius: 10,
        titleColor: '#fff',
        bodyColor: '#fff',
        displayColors: true,
        callbacks: {
          label: (ctx) => {
            const label = ctx.dataset?.label ? `${ctx.dataset.label}: ` : '';
            const val = (ctx.parsed && typeof ctx.parsed === 'object') ? (ctx.parsed.y ?? 0)
                      : (typeof ctx.parsed === 'number') ? ctx.parsed
                      : (ctx.raw ?? 0);
            return label + fmt.format(val);
          }
        }
      }
    },

    scales: {
      x: {
        grid: { display: false, drawBorder: false },  // убираем рамку
        border: { display: false },                  // Chart.js v4
        ticks: { color: 'rgba(44,62,80,0.85)', font: { weight: '600' }, padding: 6 }
      },
      y: {
        grid: { color: 'rgba(0,0,0,0.06)', drawBorder: false }, // убираем рамку
        border: { display: false },                              // Chart.js v4
        ticks: { color: 'rgba(44,62,80,0.85)', callback: (v) => fmt.format(v), padding: 6 }
      }
    }
  };
}


</script>
