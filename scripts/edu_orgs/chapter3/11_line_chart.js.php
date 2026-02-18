function initCh3LineChart() {
  if (typeof Chart === 'undefined') return;

  const canvas = document.getElementById('lineChart');
  if (!canvas) return;

  const payload = window.__nimroEduOrgsLine;
  if (!payload || !payload.series || !Array.isArray(payload.labels)) return;

  const labels = payload.labels.map(v => String(v ?? ''));
  const series = payload.series || {};
  const meta = payload.meta || {};

  const legend = document.querySelector('.line-legend');

  function getTitle(key) {
    return String(meta[key] ?? key);
  }

  function getSeriesData(key) {
    const arr = series[key];
    if (!Array.isArray(arr)) return labels.map(() => 0);
    return arr.map(v => {
      const n = Number(v);
      return Number.isFinite(n) ? n : 0;
    });
  }

  let activeKey = String(payload.defaultKey || 'total');
  if (!(activeKey in series)) {
    const keys = Object.keys(series);
    activeKey = keys.length ? keys[0] : 'total';
  }

  const primary = cssVar('--primary-color', '#6d444b');

  const pointValueLabels = {
    id: 'pointValueLabels',
    afterDatasetsDraw(chart) {
      const { ctx } = chart;
      const meta0 = chart.getDatasetMeta(0);
      if (!meta0 || !meta0.data) return;

      ctx.save();
      ctx.font = '800 12px Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial';
      ctx.fillStyle = 'rgba(44,62,80,0.88)';
      ctx.textAlign = 'center';
      ctx.textBaseline = 'bottom';

      meta0.data.forEach((pt, i) => {
        const v = Number(chart.data.datasets[0].data[i] ?? 0);
        if (!Number.isFinite(v)) return;

        const text = fmt.format(v);
        let y = pt.y - 10;
        if (y < 12) y = 12;
        ctx.fillText(text, pt.x, y);
      });

      ctx.restore();
    }
  };

  const chart = new Chart(canvas.getContext('2d'), {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: getTitle(activeKey),
        data: getSeriesData(activeKey),
        borderColor: primary,
        backgroundColor: 'transparent',
        pointBackgroundColor: primary,
        pointBorderColor: primary,
        pointRadius: 4,
        pointHoverRadius: 5,
        borderWidth: 4,
        tension: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      events: [],
      layout: { padding: { top: 28, right: 10, bottom: 8, left: 10 } },
      plugins: {
        legend: { display: false },
        tooltip: {
          displayColors: false,
          callbacks: {
            title: (items) => (items && items[0] ? String(items[0].label || '') : ''),
            label: (ctx) => fmt.format(ctx.parsed?.y ?? ctx.raw ?? 0)
          }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { color: 'rgba(44,62,80,0.80)', font: { weight: '700' } }
        },
        y: {
          beginAtZero: false,
          grid: { color: 'rgba(0,0,0,0.06)' },

          // Убираем дробные подписи (3.2, 7.5 и т.п.) на оси Y.
          // Данные целочисленные, поэтому оставляем только целые тики.
          afterBuildTicks: (scale) => {
            const orig = Array.isArray(scale.ticks) ? scale.ticks.slice() : [];
            const filtered = orig.filter(t => Number.isInteger(t.value));
            if (filtered.length >= 2) scale.ticks = filtered;
          },

          ticks: {
            color: 'rgba(44,62,80,0.80)',
            precision: 0,
            callback: (v) => (Number.isInteger(v) ? fmt.format(v) : '')
          }
        }
      }
    },
    plugins: [pointValueLabels]
  });

  function setLegendActive(key) {
    if (!legend) return;
    legend.querySelectorAll('[data-key]').forEach(btn => {
      const k = String(btn.getAttribute('data-key') || '');
      btn.classList.toggle('is-active', k === key);
    });
  }

  function setSeries(key) {
    const k = String(key || '');
    if (!k || !(k in series)) return;
    activeKey = k;

    chart.data.datasets[0].label = getTitle(k);
    chart.data.datasets[0].data = getSeriesData(k);
    chart.update();
    setLegendActive(k);
  }

  setLegendActive(activeKey);

  if (legend) {
    legend.addEventListener('click', function (e) {
      const btn = (e.target instanceof Element) ? e.target.closest('[data-key]') : null;
      if (!btn) return;
      setSeries(btn.getAttribute('data-key'));
    });
  }

  if (!window.__nimroCh3LineResizeBound) {
    window.__nimroCh3LineResizeBound = true;
    let raf = 0;
    const schedule = () => {
      if (raf) return;
      raf = requestAnimationFrame(() => {
        raf = 0;
        try { chart.resize(); } catch (e) {}
        try { chart.update('none'); } catch (e) {}
      });
    };
    window.addEventListener('resize', schedule, { passive: true });
    if (window.visualViewport) window.visualViewport.addEventListener('resize', schedule, { passive: true });
  }
}
