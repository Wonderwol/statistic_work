<script>
document.addEventListener('DOMContentLoaded', function() {
    // Конфигурация всех фильтров
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
    
    // Инициализация всех фильтров
    Object.keys(filtersConfig).forEach(filterName => {
        const config = filtersConfig[filterName];
        initFilter(filterName, config);
    });
    
    // Закрытие всех выпадающих списков при клике вне их
    document.addEventListener('click', function(event) {
        let shouldCloseAll = true;
        
        Object.keys(filtersConfig).forEach(filterName => {
            const container = document.getElementById(filtersConfig[filterName].containerId);
            if (container && container.contains(event.target)) {
                shouldCloseAll = false;
            }
        });
        
        if (shouldCloseAll) {
            Object.keys(filtersConfig).forEach(filterName => {
                const group = document.getElementById(filtersConfig[filterName].groupId);
                const container = document.getElementById(filtersConfig[filterName].containerId);
                const fg = container ? container.closest('.filter-group') : null;

                if (group) group.classList.remove('active');
                if (container) container.classList.remove('active');
                if (fg) fg.classList.remove('dropdown-open');
            });
        }

    });
    
    // Инициализация фильтра
    function initFilter(filterName, config) {
        const searchInput = document.getElementById(config.searchId);
        const checkboxGroup = document.getElementById(config.groupId);
        const countSpan = document.getElementById(config.countId);
        const clearBtn = document.getElementById(config.clearId);
        const container = document.getElementById(config.containerId);
        const selectAllBtn = config.selectAllId ? document.getElementById(config.selectAllId) : null;
        
        if (!searchInput || !checkboxGroup) return;
        
        const checkboxes = checkboxGroup.querySelectorAll('input[type="' + (config.isRadio ? 'radio' : 'checkbox') + '"]');
        const checkboxItems = checkboxGroup.querySelectorAll('.checkbox-item');
        const noResults = checkboxGroup.querySelector('.no-results');
        
        // Инициализация текста в поле поиска
        updateSearchInputText(filterName);
        
        // Показать/скрыть список при клике
        searchInput.addEventListener('click', function(e) {
            e.stopPropagation();

            // Закрыть другие открытые списки + убрать им "верхний слой"
            Object.keys(filtersConfig).forEach(otherFilter => {
                if (otherFilter !== filterName) {
                    const otherGroup = document.getElementById(filtersConfig[otherFilter].groupId);
                    const otherContainer = document.getElementById(filtersConfig[otherFilter].containerId);
                    const otherFilterGroup = otherContainer ? otherContainer.closest('.filter-group') : null;

                    if (otherGroup) otherGroup.classList.remove('active');
                    if (otherContainer) otherContainer.classList.remove('active');
                    if (otherFilterGroup) otherFilterGroup.classList.remove('dropdown-open');
                }
            });

            // Открыть/закрыть текущий
            const willOpen = !checkboxGroup.classList.contains('active');
            checkboxGroup.classList.toggle('active', willOpen);

            if (container) container.classList.toggle('active', willOpen);

            const fg = container ? container.closest('.filter-group') : null;
            if (fg) fg.classList.toggle('dropdown-open', willOpen);
        });
        
        // Поиск
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            let hasVisibleItems = false;
            
            checkboxItems.forEach(item => {
                const label = item.querySelector('label');
                const text = label.textContent.toLowerCase();
                
                if (text.includes(searchTerm)) {
                    item.style.display = 'flex';
                    hasVisibleItems = true;
                } else {
                    item.style.display = 'none';
                }
            });
            
            if (noResults) {
                noResults.style.display = hasVisibleItems ? 'none' : 'block';
            }
        });
        
        // Обновление счетчика и текста
        function updateFilterState() {
            updateSelectedCount(filterName);
            updateSearchInputText(filterName);
        }
        
        // Обновление счетчика
        function updateSelectedCount(filterName) {
            const config = filtersConfig[filterName];
            const countSpan = document.getElementById(config.countId);
            const checkboxes = document.querySelectorAll(`#${config.groupId} input[type="${config.isRadio ? 'radio' : 'checkbox'}"]`);
            
            if (!countSpan) return;
            
            let checkedCount;
            if (config.isRadio) {
                checkedCount = Array.from(checkboxes).some(cb => cb.checked) ? 1 : 0;
            } else {
                checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
            }
            
            countSpan.textContent = checkedCount;
            
            // Обновить текст кнопки "Выбрать все"
            if (selectAllBtn && filterName === 'year') {
                selectAllBtn.textContent = checkedCount === checkboxes.length ? 'Снять все' : 'Выбрать все';
            }
        }
        
        // Обновление текста в поле поиска
        function updateSearchInputText(filterName) {
            const config = filtersConfig[filterName];
            const searchInput = document.getElementById(config.searchId);
            const checkboxes = document.querySelectorAll(`#${config.groupId} input[type="${config.isRadio ? 'radio' : 'checkbox'}"]`);
            
            if (!searchInput) return;
            
            let selectedText = [];
            
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const label = document.querySelector(`label[for="${checkbox.id}"]`);
                    if (label) selectedText.push(label.textContent);
                }
            });
            
            if (selectedText.length > 0) {
                if (filterName === 'year' && selectedText.length > 3) {
                    searchInput.value = `Выбрано ${selectedText.length} лет ▼`;
                } else {
                    searchInput.value = selectedText.join(', ') + ' ▼';
                }
            } else {
                searchInput.value = config.placeholder;
            }
        }
        
        // Обновление при изменении чекбоксов
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (config.isRadio && this.checked) {
                    setTimeout(() => {
                        checkboxGroup.classList.remove('active');
                        if (container) container.classList.remove('active');
                        const fg = container ? container.closest('.filter-group') : null;
                        if (fg) fg.classList.remove('dropdown-open');
                    }, 300);
                }
                updateFilterState();
            });
        });
        
        // Обработка клика на элементе
        checkboxItems.forEach(item => {
            item.addEventListener('click', function(e) {
                const input = this.querySelector('input');
                if (e.target !== input && !e.target.classList.contains('clear-selection') && !e.target.classList.contains('select-all')) {
                    if (config.isRadio) {
                        input.checked = true;
                    } else {
                        input.checked = !input.checked;
                    }
                    input.dispatchEvent(new Event('change'));
                }
            });
        });
        
        // Очистка выбора
        if (clearBtn) {
            clearBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateFilterState();
            });
        }
        
        // Кнопка "Выбрать все" для чекбоксов
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                
                checkboxes.forEach(checkbox => {
                    checkbox.checked = !allChecked;
                });
                
                updateFilterState();
            });
        }
        
        // Инициализация начального состояния
        updateFilterState();
    }
    
    // ========== ФУНКЦИИ ДЛЯ ГРАФИКОВ И ТАБЛИЦ ==========
    
    // Функции showCards() и showTable() уже есть у вас
    // Функция initializeCharts() уже есть у вас
    
    // По умолчанию показываем карточки
    showCards();
    
    // Инициализируем графики
    initializeCharts();
});

// Функции для переключения между карточками и таблицей
function showCards() {
    const statBlocks = document.querySelectorAll('.statistics');
    statBlocks.forEach(function(block) {
        block.style.display = 'block';
    });
    
    const tableView = document.getElementById('tableView');
    if (tableView) tableView.style.display = 'none';
    
    const showCardsBtn = document.getElementById('showCardsBtn');
    const showTableBtn = document.getElementById('showTableBtn');
    
    if (showCardsBtn) showCardsBtn.classList.add('active');
    if (showTableBtn) showTableBtn.classList.remove('active');
}

function showTable() {
    const statBlocks = document.querySelectorAll('.statistics');
    statBlocks.forEach(function(block) {
        block.style.display = 'none';
    });
    
    const tableView = document.getElementById('tableView');
    if (tableView) tableView.style.display = 'block';
    
    const showCardsBtn = document.getElementById('showCardsBtn');
    const showTableBtn = document.getElementById('showTableBtn');
    
    if (showTableBtn) showTableBtn.classList.add('active');
    if (showCardsBtn) showCardsBtn.classList.remove('active');
}

// Функция для инициализации графиков
function initializeCharts() {
    // График 1: Общая динамика
    const totalChartCanvas = document.getElementById('totalChart');
    if (totalChartCanvas) {
        const totalCtx = totalChartCanvas.getContext('2d');
        new Chart(totalCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($years ?? []); ?>,
                datasets: [
                    {
                        label: 'Всего организаций',
                        data: <?php echo json_encode($totalOrganizations ?? []); ?>,
                        borderColor: '#6d444b', // Ваш основной цвет
                        backgroundColor: 'rgba(109, 68, 75, 0.1)', // Прозрачная версия
                        borderWidth: 3,
                        tension: 0.1,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'Количество'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Учебный год'
                        }
                    }
                }
            }
        });
    }

    // График 2: Типы школ
    const schoolTypesChartCanvas = document.getElementById('schoolTypesChart');
    if (schoolTypesChartCanvas) {
        const typesCtx = schoolTypesChartCanvas.getContext('2d');
        new Chart(typesCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($schoolTypesLabels ?? []); ?>,
                datasets: [{
                    label: 'Количество организаций',
                    data: <?php echo json_encode($schoolTypesData ?? []); ?>,
                    backgroundColor: [
                        'rgba(109, 68, 75, 0.7)',    // Ваш цвет
                        'rgba(152, 251, 152, 0.7)',  // Ваш hover цвет
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)'
                    ],
                    borderColor: [
                        'rgb(109, 68, 75)',
                        'rgb(152, 251, 152)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 206, 86)',
                        'rgb(75, 192, 192)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Количество'
                        }
                    }
                }
            }
        });
    }

    // График 3: Сравнение по годам
    const comparisonChartCanvas = document.getElementById('comparisonChart');
    if (comparisonChartCanvas && window.nurseryData && window.basicData) {
        const compareCtx = comparisonChartCanvas.getContext('2d');
        new Chart(compareCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($years ?? []); ?>,
                datasets: [
                    {
                        label: 'НОШ д/сад',
                        data: window.nurseryData,
                        backgroundColor: 'rgba(109, 68, 75, 0.7)'
                    },
                    {
                        label: 'Основные школы',
                        data: window.basicData,
                        backgroundColor: 'rgba(152, 251, 152, 0.7)'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Количество'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Учебный год'
                        }
                    }
                }
            }
        });
    }

    // График 4: Круговая диаграмма
    const pieChartCanvas = document.getElementById('pieChart');
    if (pieChartCanvas) {
        const pieCtx = pieChartCanvas.getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($pieLabels ?? []); ?>,
                datasets: [{
                    data: <?php echo json_encode($pieData ?? []); ?>,
                    backgroundColor: [
                        'rgba(109, 68, 75, 0.7)',
                        'rgba(152, 251, 152, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)'
                    ],
                    borderColor: [
                        'rgb(109, 68, 75)',
                        'rgb(152, 251, 152)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 206, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(153, 102, 255)',
                        'rgb(255, 159, 64)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            padding: 15
                        }
                    }
                }
            }
        });
    }
}

// === ДОБАВЬТЕ ЭТОТ КОД В КОНЕЦ ВАШЕГО СКРИПТА ===

// Простая функция экспорта таблицы в Excel
window.exportToExcel = function() {
    try {
        const table = document.querySelector('#tableView table');
        if (!table) {
            alert('Таблица не найдена!');
            return;
        }
        
        // Создаем HTML таблицу для Excel
        let html = '<html><head><meta charset="UTF-8"></head><body>';
        html += '<table border="1">' + table.innerHTML + '</table>';
        html += '</body></html>';
        
        // Создаем и скачиваем файл
        const blob = new Blob([html], {type: 'application/vnd.ms-excel'});
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        
        a.href = url;
        a.download = 'статистика_образования_' + new Date().toLocaleDateString('ru-RU') + '.xls';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        
    } catch (error) {
        console.error('Ошибка при экспорте:', error);
        alert('Ошибка при экспорте таблицы');
    }
};

// Добавляем кнопку экспорта при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    const viewControls = document.querySelector('.view-controls');
    if (viewControls && !document.querySelector('.export-btn')) {
        const exportBtn = document.createElement('button');
        exportBtn.className = 'view-btn';
        exportBtn.textContent = 'экспорт';
        exportBtn.onclick = exportToExcel;
        exportBtn.title = 'Экспорт таблицы в Excel';
        viewControls.appendChild(exportBtn);
    }
    
    // Простая функция для показа пояснений
    const infoLink = document.querySelector('a[href="#"]');
    if (infoLink) {
        infoLink.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Справка по данным:\n\n' +
                  '• НОШ д/сад - Начальные школы-детские сады\n' +
                  '• НОШ - Начальные общеобразовательные школы\n' +
                  '• ООШ - Основные общеобразовательные школы\n' +
                  '• СОШ - Средние общеобразовательные школы\n' +
                  '• УИОП - Углубленное изучение отдельных предметов\n' +
                  '• ОВЗ - Ограниченные возможности здоровья');
        });
    }
});

// Скрываем preloader когда все загружено
document.addEventListener('DOMContentLoaded', function() {
    // Ждем полной загрузки страницы
    window.addEventListener('load', function() {
        setTimeout(function() {
            const preloader = document.getElementById('preloader');
            if (preloader) {
                preloader.style.transition = 'opacity 0.3s';
                preloader.style.opacity = '0';
                setTimeout(() => {
                    preloader.style.display = 'none';
                }, 300);
            }
        }, 300); // Минимальная задержка
    });
});

/* index_script.php — красивые графики Chart.js (BI style) */

(function () {
  'use strict';

  // ---------- helpers ----------
  const nf = new Intl.NumberFormat('ru-RU');

  function num(v) {
    const n = Number(v);
    return Number.isFinite(n) ? n : 0;
  }

  function sum(arr) {
    return (arr || []).reduce((a, b) => a + num(b), 0);
  }

  function safeArr(a) {
    return Array.isArray(a) ? a : [];
  }

  function getCssVar(name, fallback) {
    const v = getComputedStyle(document.documentElement).getPropertyValue(name).trim();
    return v || fallback;
  }

  // Ненавязчивая современная палитра (стабильная и читаемая)
  const PALETTE = [
    'rgba(54, 162, 235, 0.75)',
    'rgba(255, 99, 132, 0.70)',
    'rgba(255, 206, 86, 0.75)',
    'rgba(75, 192, 192, 0.75)',
    'rgba(153, 102, 255, 0.75)',
    'rgba(255, 159, 64, 0.75)',
    'rgba(46, 204, 113, 0.70)',
    'rgba(149, 165, 166, 0.65)',
    'rgba(52, 73, 94, 0.60)',
  ];

  const PALETTE_BORDER = [
    'rgb(54, 162, 235)',
    'rgb(255, 99, 132)',
    'rgb(255, 206, 86)',
    'rgb(75, 192, 192)',
    'rgb(153, 102, 255)',
    'rgb(255, 159, 64)',
    'rgb(46, 204, 113)',
    'rgb(149, 165, 166)',
    'rgb(52, 73, 94)',
  ];

  function colors(n) {
    const bg = [];
    const br = [];
    for (let i = 0; i < n; i++) {
      bg.push(PALETTE[i % PALETTE.length]);
      br.push(PALETTE_BORDER[i % PALETTE_BORDER.length]);
    }
    return { bg, br };
  }

  function buildLegendLabelsWithPercent(chart) {
    const data = chart.data;
    const ds = data.datasets[0];
    const total = (ds.data || []).reduce((a, b) => a + num(b), 0) || 1;

    const meta = chart.getDatasetMeta(0);

    return (data.labels || []).map((label, i) => {
      const v = num(ds.data[i]);
      const p = (v / total) * 100;
      const style = meta.controller.getStyle(i);

      return {
        text: `${label}: ${nf.format(v)} (${p.toFixed(1)}%)`,
        fillStyle: style.backgroundColor,
        strokeStyle: style.borderColor,
        lineWidth: style.borderWidth,
        hidden: !chart.getDataVisibility(i),
        index: i,
      };
    });
  }

  // ---------- Chart.js global defaults ----------
  if (window.Chart) {
    Chart.defaults.font.family = 'Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial';
    Chart.defaults.font.size = 12;
    Chart.defaults.color = 'rgba(44, 62, 80, 0.85)';
    Chart.defaults.animation.duration = 900;
    Chart.defaults.animation.easing = 'easeOutQuart';
    Chart.defaults.interaction.mode = 'index';
    Chart.defaults.interaction.intersect = false;
    Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(44,62,80,0.95)';
    Chart.defaults.plugins.tooltip.titleColor = '#fff';
    Chart.defaults.plugins.tooltip.bodyColor = '#fff';
    Chart.defaults.plugins.tooltip.padding = 12;
    Chart.defaults.plugins.tooltip.displayColors = true;
  }

  // ---------- plugins ----------
  const centerTextPlugin = {
    id: 'centerTextPlugin',
    afterDraw(chart, args, opts) {
      const { ctx, chartArea } = chart;
      if (!chartArea) return;

      const cx = (chartArea.left + chartArea.right) / 2;
      const cy = (chartArea.top + chartArea.bottom) / 2;

      const lines = safeArr(opts && opts.lines);
      if (!lines.length) return;

      ctx.save();
      ctx.textAlign = 'center';
      ctx.textBaseline = 'middle';

      let y = cy - (lines.length - 1) * 10;
      for (const line of lines) {
        ctx.font = line.font || '700 18px Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial';
        ctx.fillStyle = line.color || 'rgba(44, 62, 80, 0.9)';
        ctx.fillText(line.text || '', cx, y);
        y += line.step || 20;
      }

      ctx.restore();
    }
  };

  // ---------- create charts ----------
  function makeLineChart() {
    const el = document.getElementById('totalChart');
    if (!el) return;

    const years = safeArr(window.years);
    const data = safeArr(window.totalOrganizations).map(num);

    const ctx = el.getContext('2d');

    const accent = getCssVar('--bs-primary', 'rgb(54, 162, 235)');
    const fill = 'rgba(54, 162, 235, 0.15)';

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: years,
        datasets: [{
          label: 'Итого организаций',
          data,
          tension: 0.35,
          borderWidth: 2,
          borderColor: accent,
          backgroundColor: fill,
          fill: true,
          pointRadius: 3,
          pointHoverRadius: 6,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label(ctx) {
                return ` ${ctx.dataset.label}: ${nf.format(num(ctx.parsed.y))}`;
              }
            }
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: { maxRotation: 0, autoSkip: true }
          },
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(0,0,0,0.06)' },
            ticks: {
              callback: (v) => nf.format(v)
            }
          }
        }
      }
    });
  }

  function makeSchoolTypesBar() {
    const el = document.getElementById('schoolTypesChart');
    if (!el) return;

    const labels = safeArr(window.schoolTypesLabels);
    const values = safeArr(window.schoolTypesData).map(num);

    const ctx = el.getContext('2d');
    const { bg, br } = colors(labels.length);

    // Скрываем нулевые категории (чтобы кадетские корпуса не “занимали место”, если 0)
    const filtered = labels.map((l, i) => ({ l, v: values[i] }))
      .filter(x => num(x.v) > 0);

    const fl = filtered.map(x => x.l);
    const fv = filtered.map(x => x.v);
    const { bg: fbg, br: fbr } = colors(fl.length);

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: fl,
        datasets: [{
          label: 'Количество',
          data: fv,
          backgroundColor: fbg,
          borderColor: fbr,
          borderWidth: 1.5,
          borderRadius: 10,
          maxBarThickness: 46
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label(ctx) {
                return ` ${ctx.label}: ${nf.format(num(ctx.parsed.y))}`;
              }
            }
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: { autoSkip: false }
          },
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(0,0,0,0.06)' },
            ticks: { callback: (v) => nf.format(v) }
          }
        }
      }
    });
  }

  function makeCompareBar() {
    const el = document.getElementById('compareChart');
    if (!el) return;

    const years = safeArr(window.years);
    const nursery = safeArr(window.nurseryData).map(num);
    const basic = safeArr(window.basicData).map(num);
    const special = safeArr(window.specialData).map(num);

    const ctx = el.getContext('2d');

    const datasets = [
      { label: 'НОШ д/сад', data: nursery, colorIndex: 0 },
      { label: 'Основные школы', data: basic, colorIndex: 3 },
      { label: 'ОВЗ школы', data: special, colorIndex: 5 },
    ];

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: years,
        datasets: datasets.map(ds => ({
          label: ds.label,
          data: ds.data,
          backgroundColor: PALETTE[ds.colorIndex % PALETTE.length],
          borderColor: PALETTE_BORDER[ds.colorIndex % PALETTE_BORDER.length],
          borderWidth: 1.5,
          borderRadius: 8,
          maxBarThickness: 26
        }))
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'top',
            labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 10 }
          },
          tooltip: {
            callbacks: {
              label(ctx) {
                return ` ${ctx.dataset.label}: ${nf.format(num(ctx.parsed.y))}`;
              }
            }
          }
        },
        scales: {
          x: { grid: { display: false } },
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(0,0,0,0.06)' },
            ticks: { callback: (v) => nf.format(v) }
          }
        }
      }
    });
  }

  function makeStructureDoughnut() {
    const el = document.getElementById('pieChart');
    if (!el) return;

    const labels = safeArr(window.pieLabels);
    const values = safeArr(window.pieData).map(num);

    const ctx = el.getContext('2d');
    const { bg, br } = colors(labels.length);

    // Итого без филиалов (по методике источника)
    const idxBranches = labels.findIndex(x => String(x).toLowerCase().includes('филиал'));
    const branches = idxBranches >= 0 ? num(values[idxBranches]) : 0;
    const totalAll = sum(values);
    const totalWithoutBranches = totalAll - branches;

    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels,
        datasets: [{
          data: values,
          backgroundColor: bg,
          borderColor: br,
          borderWidth: 2,
          borderRadius: 10,
          spacing: 4,
          hoverOffset: 10,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '62%',
        plugins: {
          legend: {
            position: 'right',
            labels: {
              usePointStyle: true,
              pointStyle: 'circle',
              boxWidth: 10,
              padding: 14,
              generateLabels: buildLegendLabelsWithPercent
            },
            onClick(e, item, legend) {
              legend.chart.toggleDataVisibility(item.index);
              legend.chart.update();
            }
          },
          tooltip: {
            callbacks: {
              label(ctx) {
                const v = num(ctx.parsed);
                const total = sum(ctx.dataset.data) || 1;
                const p = (v / total) * 100;
                return ` ${ctx.label}: ${nf.format(v)} (${p.toFixed(1)}%)`;
              }
            }
          },
          centerTextPlugin: {
            lines: [
              { text: `Итого: ${nf.format(totalWithoutBranches)}`, font: '800 18px Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial', color: 'rgba(44,62,80,0.95)', step: 22 },
              { text: 'без филиалов', font: '600 12px Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial', color: 'rgba(44,62,80,0.70)', step: 18 },
              ...(branches > 0 ? [{ text: `филиалы: ${nf.format(branches)} (всего: ${nf.format(totalAll)})`, font: '500 11px Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial', color: 'rgba(44,62,80,0.60)', step: 18 }] : [])
            ]
          }
        }
      },
      plugins: [centerTextPlugin]
    });
  }

  function initAll() {
    if (!window.Chart) return;

    // Если у тебя есть кнопки/перерисовка — лучше хранить инстансы и destroy(),
    // но для первичной красивой версии достаточно разового init.
    makeLineChart();
    makeSchoolTypesBar();
    makeCompareBar();
    makeStructureDoughnut();
  }

  // Запуск
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }

})();

</script>