// Бар-график: количество ОО по районам (вертикальный)
// - убираем "Итого/Всего" из данных графика
// - районы сортируем по убыванию
// - по умолчанию (когда в фильтрах "Всего"/без района) НИЧЕГО не выделяем
// - если выбран район — подсвечиваем только его
// - базовый цвет столбцов: серый RGB(177,177,177)
(function () {
  'use strict';

  function cssVar(name, fallback) {
    const v = getComputedStyle(document.documentElement).getPropertyValue(name);
    return (v || '').trim() || fallback;
  }

  function hexToRgb(hex) {
    const h = String(hex || '').trim().replace('#', '');
    if (h.length === 3) {
      return {
        r: parseInt(h[0] + h[0], 16),
        g: parseInt(h[1] + h[1], 16),
        b: parseInt(h[2] + h[2], 16)
      };
    }
    if (h.length === 6) {
      return {
        r: parseInt(h.slice(0, 2), 16),
        g: parseInt(h.slice(2, 4), 16),
        b: parseInt(h.slice(4, 6), 16)
      };
    }
    return null;
  }

  function toRgba(color, alpha) {
    const c = String(color || '').trim();
    if (!c) return `rgba(0,0,0,${alpha})`;

    const m = c.match(/rgba?\(\s*([0-9.]+)\s*,\s*([0-9.]+)\s*,\s*([0-9.]+)(?:\s*,\s*([0-9.]+))?\s*\)/i);
    if (m) return `rgba(${Number(m[1])},${Number(m[2])},${Number(m[3])},${alpha})`;

    if (c.startsWith('#')) {
      const rgb = hexToRgb(c);
      if (rgb) return `rgba(${rgb.r},${rgb.g},${rgb.b},${alpha})`;
    }

    return `rgba(0,0,0,${alpha})`;
  }

  function isTotalLabel(label) {
    const s = String(label || '').trim().toLowerCase();
    return s.includes('итого') || s.includes('всего');
  }

  function initAreaRankChart() {
    if (typeof Chart === 'undefined') return;

    const el = document.getElementById('areaRankChart');
    if (!el) return;

    const fmt = new Intl.NumberFormat('ru-RU');
    const d = (typeof getChartData === 'function') ? getChartData() : {};

    const labels0 = Array.isArray(d.areaRankLabels) ? d.areaRankLabels.map(v => String(v ?? '').trim()) : [];
    const values0 = Array.isArray(d.areaRankValues) ? d.areaRankValues.map(v => Number(v ?? 0)) : [];
    const codes0  = Array.isArray(d.areaRankCodes)  ? d.areaRankCodes.map(v => String(v ?? '').trim()) : [];

    const n = Math.min(labels0.length, values0.length, codes0.length);
    if (!n) return;

    // Собираем и убираем "Итого/Всего"
    const districts = [];
    for (let i = 0; i < n; i++) {
      const label = labels0[i];
      const code  = codes0[i];
      const value = Number.isFinite(values0[i]) ? values0[i] : 0;
      if (!label) continue;
      if (isTotalLabel(label)) continue;
      districts.push({ label, code, value });
    }
    if (!districts.length) return;

    // Сортировка по убыванию
    districts.sort((a, b) => {
      if (b.value !== a.value) return b.value - a.value;
      return a.label.localeCompare(b.label, 'ru');
    });

    const labels = districts.map(x => x.label);
    const values = districts.map(x => x.value);
    const codes  = districts.map(x => x.code);

    // Подсветка: только если выбран район
    const sel = String(d.highlightAreaCode ?? window.selectedAreaCode ?? '').trim();
    let activeIndex = -1;
    if (sel) {
      const idx = codes.indexOf(sel);
      if (idx >= 0) activeIndex = idx;
    }

    const COLOR_DEFAULT = 'rgba(177, 177, 177, 1)'; // серый
    const primary = cssVar('--primary-color', '#6d444b');
    const COLOR_ACTIVE = toRgba(primary, 1);

    const bg = labels.map((_, i) => (i === activeIndex ? COLOR_ACTIVE : COLOR_DEFAULT));

    // Подписи значений над столбиками
    const valueLabels = {
      id: 'areaRankValueLabels',
      afterDatasetsDraw(chart) {
        const { ctx } = chart;
        const meta = chart.getDatasetMeta(0);
        if (!meta || !meta.data) return;

        ctx.save();
        ctx.font = '800 11px Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial';
        ctx.fillStyle = 'rgba(44,62,80,0.86)';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'bottom';

        meta.data.forEach((bar, i) => {
          const v = Number(chart.data.datasets[0].data[i] ?? 0);
          if (!Number.isFinite(v) || v <= 0) return;
          ctx.fillText(fmt.format(Math.round(v)), bar.x, bar.y - 6);
        });

        ctx.restore();
      }
    };

    ChartRegistry.create('areaRank', () => new Chart(el.getContext('2d'), {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          data: values.map(v => Number.isFinite(v) ? Math.round(v) : 0),
          backgroundColor: bg,
          borderWidth: 0,
          borderRadius: { topLeft: 8, topRight: 8, bottomLeft: 0, bottomRight: 0 },
          borderSkipped: false,
          minBarLength: 4,
          categoryPercentage: 0.92,
          barPercentage: 0.92
        }]
      },
      options: buildOptions({
        layout: { padding: { top: 10, right: 10, bottom: 74, left: 10 } },
        plugins: {
          legend: { display: false },
          tooltip: {
            displayColors: false,
            callbacks: {
              title: (items) => (items && items[0] ? String(items[0].label ?? '') : ''),
              label: (ctx) => `ОО: ${fmt.format(Number(ctx.parsed?.y ?? ctx.raw ?? 0))}`
            }
          }
        },
        scales: {
          x: {
            grid: { display: false },
            ticks: {
              autoSkip: false,
              maxRotation: 60,
              minRotation: 60,
              color: 'rgba(44,62,80,0.86)',
              font: { weight: '700', size: 10 },
              padding: 6
            }
          },
          y: {
            beginAtZero: true,
            grid: { color: 'rgba(0,0,0,0.06)' },
            ticks: {
              color: 'rgba(44,62,80,0.86)',
              callback: (v) => fmt.format(v)
            }
          }
        }
      }),
      plugins: [valueLabels]
    }));
  }

  const prevInit = window.initializeCharts;
  window.initializeCharts = function () {
    if (typeof prevInit === 'function') prevInit();
    initAreaRankChart();
  };
})();