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
        borderWidth: 0,
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
    }),
    plugins: [barValueLabels]
  }));
};
