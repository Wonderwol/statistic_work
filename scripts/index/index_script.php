<script>
(function () {
  const fmt = new Intl.NumberFormat('ru-RU');

  window.__nimroIndexScriptVer = '2026-02-11-2';
  console.log('[nimro] index_script loaded', window.__nimroIndexScriptVer);
// ===== theme colors (из твоих :root токенов в styles/index/00_tokens.php) =====
  function cssVar(name, fallback) {
    const v = getComputedStyle(document.documentElement).getPropertyValue(name);
    return (v || '').trim() || fallback;
  }

  function hexToRgb(hex) {
    const h = String(hex || '').trim().replace('#', '');
    if (h.length === 3) {
      const r = parseInt(h[0] + h[0], 16);
      const g = parseInt(h[1] + h[1], 16);
      const b = parseInt(h[2] + h[2], 16);
      return { r, g, b };
    }
    if (h.length === 6) {
      const r = parseInt(h.slice(0, 2), 16);
      const g = parseInt(h.slice(2, 4), 16);
      const b = parseInt(h.slice(4, 6), 16);
      return { r, g, b };
    }
    return null;
  }

  function toRgba(color, alpha) {
    const c = String(color || '').trim();
    if (!c) return `rgba(0,0,0,${alpha})`;

    // rgb/rgba
    const m = c.match(/rgba?\(\s*([0-9.]+)\s*,\s*([0-9.]+)\s*,\s*([0-9.]+)(?:\s*,\s*([0-9.]+))?\s*\)/i);
    if (m) {
      const r = Number(m[1]), g = Number(m[2]), b = Number(m[3]);
      return `rgba(${r},${g},${b},${alpha})`;
    }

    // hex
    if (c.startsWith('#')) {
      const rgb = hexToRgb(c);
      if (rgb) return `rgba(${rgb.r},${rgb.g},${rgb.b},${alpha})`;
    }

    return c; // на крайний случай
  }

  const COLOR_PRIMARY   = cssVar('--primary-color',   '#6d444b');
  const COLOR_SECONDARY = cssVar('--secondary-color', '#3498db');
  const COLOR_SUCCESS   = cssVar('--success-color',   '#2ecc71');
  const COLOR_DANGER    = cssVar('--danger-color',    '#e74c3c');

  // Палитра “в стиле сайта”: первые 4 — из токенов, остальное — спокойные добавки
  const THEME_PALETTE = [
    COLOR_PRIMARY,
    COLOR_SECONDARY,
    '#0F766E',
    '#7C3AED',
    '#D97706',
    COLOR_DANGER,
    COLOR_SUCCESS,
    '#475569',
    '#0891B2',
    '#A21CAF'
  ];


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

  const pieSeries = safeArr(window.pieSeries); // [тип][год]

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

    // пересоздать/перересайзить графики (после display:none они часто “плывут”)
    if (typeof window.initializeCharts === 'function') {
      window.initializeCharts();
      requestAnimationFrame(() => {
        Object.values(charts).forEach(c => {
          if (!c) return;
          try { c.resize(); } catch (e) {}
          try { c.update('none'); } catch (e) {}
        });
      });
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

    // корректно уничтожить ВСЕ графики (а не window.__charts.structure которого нет)
    Object.keys(charts).forEach((k) => destroyIfExists(k));
  };


  const charts = {};

  function baseOptions() {
  return {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: 'index', intersect: false },
    hover: { mode: 'index', intersect: false },
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
        mode: 'index',
        intersect: false,
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
      tooltip: {
        mode: 'index',
        intersect: false,
        callbacks: { label: tooltipLabel }
      }
    };
  }

  // Chart.js v2
  return {
    legend: { display: false },
    tooltips: {
      mode: 'index',
      intersect: false,
      callbacks: { label: function (item) { return fmt.format(item.yLabel); } }
    }
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
            borderColor: COLOR_PRIMARY,
            backgroundColor: toRgba(COLOR_PRIMARY, 0.14),
            pointRadius: 4,
            pointHitRadius: 18,
            pointHoverRadius: 6,
            pointBackgroundColor: COLOR_PRIMARY,
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

          interaction: { mode: 'index', intersect: false },
          hover: { mode: 'index', intersect: false },
          onHover: (evt, activeEls) => {
            const c = evt?.native?.target;
            if (c) c.style.cursor = activeEls && activeEls.length ? 'pointer' : 'default';
          },

          plugins: (major >= 3) ? {
            legend: { display: false },
            tooltip: {
              mode: 'index',
              intersect: false,
              callbacks: { label: tooltipLabel }
            }
          } : {
            legend: { display: false },
            tooltips: {
              mode: 'index',
              intersect: false,
              callbacks: { label: function (item) { return fmt.format(item.yLabel); } }
            }
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
            backgroundColor: schoolTypesLabels.map((_, i) => toRgba(THEME_PALETTE[i % THEME_PALETTE.length], 0.78)),
            hoverBackgroundColor: schoolTypesLabels.map((_, i) => toRgba(THEME_PALETTE[i % THEME_PALETTE.length], 0.92)),
            borderRadius: 10,
            borderSkipped: false
          }]
        },
        options: {
          interaction: { mode: 'index', intersect: false },
            hover: { mode: 'index', intersect: false },
            onHover: (evt, activeEls) => {
              const c = evt?.native?.target;
              if (c) c.style.cursor = activeEls && activeEls.length ? 'pointer' : 'default';
            },
          responsive: true,
          maintainAspectRatio: false,
          plugins: basePluginsNoLegend(),
          scales: scalesBar()
        }
      });
    }
  } catch (e) { console.error('schoolTypesChart error:', e); }

  // 3) pieChart — структура по типам (горизонтальные бары: видно даже малые значения)
  try {
    const pieEl = document.getElementById('pieChart');
    if (pieEl) {
      destroyIfExists('pie');

      const pairs = pieLabels.map((label, i) => {
      const value = Number(pieData[i] ?? 0);
      return {
        label: String(label ?? ''),
        value: Number.isFinite(value) ? value : 0,
        color: THEME_PALETTE[i % THEME_PALETTE.length]
      };
    }).filter(p => p.value > 0).sort((a, b) => b.value - a.value);

    const labels = pairs.map(p => p.label);
    const values = pairs.map(p => p.value);
    const colors = pairs.map(p => toRgba(p.color, 0.88));
    const total = values.reduce((s, v) => s + v, 0);


      const barValueLabels = {
        id: 'barValueLabels',
        afterDatasetsDraw(chart) {
          const { ctx, chartArea } = chart;
          const meta = chart.getDatasetMeta(0);
          if (!meta || !meta.data) return;

          ctx.save();
          ctx.font = '800 12px Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial';
          ctx.fillStyle = 'rgba(44,62,80,0.88)';
          ctx.textBaseline = 'middle';

          meta.data.forEach((bar, i) => {
            const v = Number(chart.data.datasets[0].data[i] ?? 0);
            if (!Number.isFinite(v) || v <= 0) return;

            // Для горизонтального бара конечная точка — bar.x
            const x = Math.min(bar.x + 8, chartArea.right - 6);
            const y = bar.y;

            ctx.fillText(fmt.format(v), x, y);
          });

          ctx.restore();
        }
      };

      charts.pie = new Chart(pieEl.getContext('2d'), {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Количество организаций',
            data: values,
            backgroundColor: colors,
            borderWidth: 0,
            borderRadius: 10,
            borderSkipped: false,
            minBarLength: 3
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          indexAxis: 'y',
          interaction: { mode: 'nearest', intersect: false },
          animation: { duration: 750, easing: 'easeOutQuart' },
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: 'rgba(20,20,20,0.92)',
              padding: 12,
              cornerRadius: 10,
              titleColor: '#fff',
              bodyColor: '#fff',
              displayColors: false,
              callbacks: {
                title: (items) => (items && items[0] ? String(items[0].label || '') : ''),
                label: (ctx) => {
                  const v = Number(ctx.parsed?.x ?? ctx.raw ?? 0);
                  const pct = total ? (v / total * 100) : 0;
                  return `${fmt.format(v)} (${pct.toFixed(1)}%)`;
                },
                footer: () => (total ? `Итого: ${fmt.format(total)}` : '')
              }
            }
          },
          scales: {
            y: {
              grid: { display: false },
              ticks: { color: 'rgba(44,62,80,0.85)', font: { weight: '700' } }
            },
            x: {
              beginAtZero: true,
              grid: { color: 'rgba(0,0,0,0.06)' },
              ticks: { callback: (v) => fmt.format(v), color: 'rgba(44,62,80,0.85)' }
            }
          }
        },
        plugins: [barValueLabels]
      });
    }
  } catch (e) { console.error('pieChart(horizontal) error:', e); }
  }


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
</script>
