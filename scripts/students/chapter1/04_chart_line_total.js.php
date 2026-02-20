function buildStudentsLineChart() {
  const canvas = document.getElementById('studentsLineChart');
  if (!canvas) return;

  const d = getStudentsData();

  const primary = cssVar('--primary-color', '#6d444b');
  const grid = toRgba(primary, 0.10);
  const text = cssVar('--text-color', 'rgba(44,62,80,.92)');

  const maxLen = Math.max(d.lineLabels.length, d.lineValues.length);
  const labels = d.lineLabels.slice(0, maxLen);
  const values = d.lineValues.slice(0, maxLen);

  ChartRegistry.create('students_line', () => new Chart(canvas.getContext('2d'), {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Обучающиеся, чел.',
        data: values,
        borderColor: primary,
        backgroundColor: toRgba(primary, 0.12),
        fill: true,
        tension: 0.32,
        pointRadius: 3,
        pointHoverRadius: 5,
        borderWidth: 2
      }]
    },
    options: buildOptions({
      plugins: {
        tooltip: {
          callbacks: {
            label: (ctx) => {
              const v = Number(ctx.parsed.y || 0);
              return ` ${fmt.format(Math.round(v))} чел.`;
            }
          }
        }
      },
      scales: {
        x: {
          grid: { color: 'transparent' },
          ticks: { color: text, maxRotation: 0, autoSkip: true }
        },
        y: {
          beginAtZero: true,
          grid: { color: grid },
          ticks: {
            color: text,
            callback: (v) => fmt.format(Number(v) || 0)
          }
        }
      }
    })
  }));
}