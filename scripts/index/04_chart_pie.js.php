window.initializeCharts = function () {
  if (typeof Chart === 'undefined') return;

  if (Chart.defaults.font) {
    Chart.defaults.font.family = 'Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial';
  }
  if (Chart.defaults.color) {
    Chart.defaults.color = 'rgba(44, 62, 80, 0.9)';
  }

  const pieEl = document.getElementById('pieChart');
  if (!pieEl) return;

  const { pieLabels, pieData } = getChartData();

  // ===== фирменные цвета из CSS =====
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
    return { r: 109, g: 68, b: 75 }; // fallback #6d444b
  }

  function mix(a, b, t) {
    return {
      r: Math.round(a.r + (b.r - a.r) * t),
      g: Math.round(a.g + (b.g - a.g) * t),
      b: Math.round(a.b + (b.b - a.b) * t)
    };
  }

  function shade(rgb, t) {
    // t: -1..1 (минус — темнее к чёрному, плюс — светлее к белому)
    const target = (t >= 0) ? { r: 255, g: 255, b: 255 } : { r: 0, g: 0, b: 0 };
    return mix(rgb, target, Math.min(1, Math.abs(t)));
  }

  function rgba(rgb, a) {
    return `rgba(${rgb.r},${rgb.g},${rgb.b},${a})`;
  }

  const BRAND = hexToRgb(cssVar('--primary-color', '#6d444b'));
  const WHITE = { r: 255, g: 255, b: 255 };

    // Контрастная палитра в стиле сайта: коричневый / бежевый / серый / белый
  // Если в :root нет переменных --nimro-beige/--nimro-gray, будут fallback.
  const BROWN = BRAND;
  const BEIGE = hexToRgb(cssVar('--nimro-beige', '#e8dcc8'));
  const GRAY  = hexToRgb(cssVar('--nimro-gray',  '#b8b0b0'));
  const NEAR_WHITE = { r: 245, g: 245, b: 245 }; // чтобы отличался от чисто белого фона

  // Расширяем палитру, чтобы хватало на много столбцов (контрастно, но в гамме)
  const SITE_RGB = [
    shade(BROWN, -0.18), // темный коричневый
    BROWN,               // коричневый
    shade(BEIGE, -0.10), // бежевый темнее
    BEIGE,               // бежевый
    shade(GRAY, -0.14),  // серый темнее
    GRAY,                // серый
    shade(NEAR_WHITE, -0.08), // почти белый (чуть темнее, чем фон)
    NEAR_WHITE
  ];

  const SITE_COLORS = SITE_RGB.map(c => rgba(c, 0.92));


  const pairs = pieLabels.map((label, i) => {
    const value = Number(pieData[i] ?? 0);
    return {
      label: String(label ?? ''),
      value: Number.isFinite(value) ? value : 0
    };
  }).filter(p => p.value > 0).sort((a, b) => b.value - a.value);

  const labels = pairs.map(p => p.label);
  const values = pairs.map(p => p.value);

  const colors = pairs.map((_, i) => SITE_COLORS[i % SITE_COLORS.length]);
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

        const x = Math.min(bar.x + 8, chartArea.right - 6);
        const y = bar.y;
        ctx.fillText(fmt.format(v), x, y);
      });

      ctx.restore();
    }
  };

  ChartRegistry.create('pie', () => new Chart(pieEl.getContext('2d'), {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'Количество организаций',
        data: values,
        backgroundColor: colors,
        hoverBackgroundColor: colors.map(c => c.replace(/,0\.88\)$/, ',1)')),
        borderColor: 'rgba(80, 60, 60, 0.55)',
        borderWidth: 1.5,
        borderRadius: 10,
        borderSkipped: false,
        minBarLength: 3
      }]
    },
    options: buildOptions({
      indexAxis: 'y',
      plugins: {
        tooltip: {
          callbacks: {
            title: (items) => (items && items[0] ? String(items[0].label || '') : ''),
            label: (ctx) => {
              const v = Number(ctx.parsed?.x ?? ctx.raw ?? 0);
              const pct = total ? (v / total * 100) : 0;
              return `${fmt.format(v)} (${pct.toFixed(1)}%)`;
            }
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
    }),
    plugins: [barValueLabels]
  }));
};
