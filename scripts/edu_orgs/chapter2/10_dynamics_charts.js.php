function initDynamicsCharts() {
  if (typeof Chart === 'undefined') return;

  const data = window.__nimroEduOrgsDynamics || {};
  const years = Array.isArray(data.years) ? data.years : [];

  const net = data.network || {};
  const netLabels = Array.isArray(net.labels) ? net.labels : [];
  const netSeriesAbs = Array.isArray(net.series) ? net.series : [];
  const netTotals = Array.isArray(net.totals) ? net.totals : [];

  const branchesAbs = Array.isArray(data.branches) ? data.branches : [];

  const sec = data.secondary || {};
  const secLabels = Array.isArray(sec.labels) ? sec.labels : [];
  const secSeriesAbs = Array.isArray(sec.series) ? sec.series : [];
  const secTotals = Array.isArray(sec.totals) ? sec.totals : [];

  function cssVar(name, fallback) {
    const v = getComputedStyle(document.documentElement).getPropertyValue(name);
    return (v || '').trim() || fallback;
  }

  const COLOR_PRIMARY = cssVar('--primary-color', '#6d444b');

  // палитры под скрин (примерно)
  const NET_COLORS = [
    '#5B8FF9', // НОШ д/сад
    '#F97316', // НОШ
    '#9CA3AF', // ООШ
    '#FACC15', // СОШ
    '#2563EB', // санаторные
    '#22C55E', // ОВЗ
    '#111827'  // вечерние
  ];

  const SEC_COLORS = [
    '#5B8FF9', // СОШ
    '#F97316', // СОШ с УИОП
    '#9CA3AF', // гимназии
    '#FACC15', // лицеи
    '#2563EB'  // кадетские корпуса
  ];

  function calcPercentMatrix(absMatrix, totals) {
    return absMatrix.map(arr =>
      arr.map((v, i) => {
        const t = Number(totals[i] ?? 0);
        const x = Number(v ?? 0);
        if (!Number.isFinite(t) || t <= 0) return 0;
        if (!Number.isFinite(x) || x <= 0) return 0;
        return (x / t) * 100;
      })
    );
  }

  function destroyIfExists(key) {
    window.__nimroDynCharts = window.__nimroDynCharts || {};
    const ch = window.__nimroDynCharts[key];
    if (ch && typeof ch.destroy === 'function') {
      try { ch.destroy(); } catch(e) {}
    }
    window.__nimroDynCharts[key] = null;
  }

  // 1) Структура сети (100%)
  (function () {
    const canvas = document.getElementById('networkChart');
    if (!canvas || years.length === 0) return;

    destroyIfExists('network');

    const perc = calcPercentMatrix(netSeriesAbs, netTotals);

    const datasets = netLabels.map((name, i) => ({
      label: name,
      data: perc[i] || years.map(() => 0),
      _abs: (netSeriesAbs[i] || years.map(() => 0)),
      backgroundColor: NET_COLORS[i % NET_COLORS.length],
      borderWidth: 0,
      borderRadius: 0
    }));

    window.__nimroDynCharts.network = new Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: { labels: years, datasets },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            stacked: true,
            min: 0,
            max: 100,
            ticks: {
              callback: (v) => `${v}%`
            },
            grid: { color: 'rgba(0,0,0,0.06)' }
          },
          y: {
            stacked: true,
            grid: { display: false },
            ticks: { color: 'rgba(44,62,80,0.85)', font: { weight: '700' } }
          }
        },
        plugins: {
          legend: { position: 'bottom', labels: { boxWidth: 12 } },
          tooltip: {
            callbacks: {
              label: (ctx) => {
                const pct = Number(ctx.parsed?.x ?? 0);
                const abs = Number(ctx.dataset?._abs?.[ctx.dataIndex] ?? 0);
                return `${ctx.dataset.label}: ${pct.toFixed(1)}% (${fmt.format(abs)})`;
              }
            }
          }
        }
      }
    });
  })();

  // 2) Филиалы (ед.)
  (function () {
    const canvas = document.getElementById('branchesChart');
    if (!canvas || years.length === 0) return;

    destroyIfExists('branches');

    window.__nimroDynCharts.branches = new Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: {
        labels: years,
        datasets: [{
          label: 'кроме того филиалы',
          data: branchesAbs,
          backgroundColor: COLOR_PRIMARY,
          borderWidth: 0,
          borderRadius: 6
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            beginAtZero: true,
            grid: { color: 'rgba(0,0,0,0.06)' },
            ticks: { callback: (v) => fmt.format(v) }
          },
          y: {
            grid: { display: false },
            ticks: { color: 'rgba(44,62,80,0.85)', font: { weight: '700' } }
          }
        },
        plugins: {
          legend: { position: 'bottom' },
          tooltip: {
            callbacks: {
              label: (ctx) => `${ctx.dataset.label}: ${fmt.format(ctx.parsed?.x ?? 0)}`
            }
          }
        }
      }
    });
  })();

  // 3) Из СОШ (100%), с “зумом” по X около 82–100 (динамически от доли СОШ)
  (function () {
    const canvas = document.getElementById('secondaryChart');
    if (!canvas || years.length === 0) return;

    destroyIfExists('secondary');

    const perc = calcPercentMatrix(secSeriesAbs, secTotals);

    // динамический min по доле "СОШ"
    const sosh = perc[0] || [];
    let minSosh = 100;
    sosh.forEach(v => { if (Number.isFinite(v) && v < minSosh) minSosh = v; });
    let xMin = 0;
    if (Number.isFinite(minSosh) && minSosh > 0) {
      xMin = Math.max(0, Math.floor((minSosh - 6) / 2) * 2);
      if (xMin > 90) xMin = 90;
    }

    const datasets = secLabels.map((name, i) => ({
      label: name,
      data: perc[i] || years.map(() => 0),
      _abs: (secSeriesAbs[i] || years.map(() => 0)),
      backgroundColor: SEC_COLORS[i % SEC_COLORS.length],
      borderWidth: 0,
      borderRadius: 0
    }));

    window.__nimroDynCharts.secondary = new Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: { labels: years, datasets },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            stacked: true,
            min: xMin,
            max: 100,
            ticks: { callback: (v) => `${v}%` },
            grid: { color: 'rgba(0,0,0,0.06)' }
          },
          y: {
            stacked: true,
            grid: { display: false },
            ticks: { color: 'rgba(44,62,80,0.85)', font: { weight: '700' } }
          }
        },
        plugins: {
          legend: { position: 'bottom', labels: { boxWidth: 12 } },
          tooltip: {
            callbacks: {
              label: (ctx) => {
                const pct = Number(ctx.parsed?.x ?? 0);
                const abs = Number(ctx.dataset?._abs?.[ctx.dataIndex] ?? 0);
                return `${ctx.dataset.label}: ${pct.toFixed(1)}% (${fmt.format(abs)})`;
              }
            }
          }
        }
      }
    });
  })();
}
