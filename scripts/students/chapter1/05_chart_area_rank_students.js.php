function buildStudentsAreaRankChart() {
  const canvas = document.getElementById('studentsAreaRankChart');
  if (!canvas) return;

  const d = getStudentsData();

  const primary = cssVar('--primary-color', '#6d444b');
  const secondary = cssVar('--secondary-color', '#3498db');
  const grid = toRgba(primary, 0.10);
  const text = cssVar('--text-color', 'rgba(44,62,80,.92)');

  const labels = d.areaRankLabels;
  const values = d.areaRankValues;

  // подсветка выбранного района
  const bg = values.map((_, i) => {
    const code = String(d.areaRankCodes[i] ?? '');
    return (d.highlightAreaCode && code === d.highlightAreaCode) ? toRgba(primary, 0.85) : toRgba(secondary, 0.65);
  });

  ChartRegistry.create('students_rank', () => new Chart(canvas.getContext('2d'), {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: `Обучающиеся (${d.rankYearLabel || ''}), чел.`,
        data: values,
        backgroundColor: bg,
        borderColor: bg,
        borderWidth: 1,
        borderRadius: 10,
        minBarLength: 4
      }]
    },
    options: buildOptions({
      indexAxis: 'y',
      plugins: {
        tooltip: {
          callbacks: {
            label: (ctx) => {
              const v = Number(ctx.parsed.x || 0);
              return ` ${fmt.format(Math.round(v))} чел.`;
            }
          }
        }
      },
      scales: {
        x: {
          beginAtZero: true,
          grid: { color: grid },
          ticks: { color: text, callback: (v) => fmt.format(Number(v) || 0) }
        },
        y: {
          grid: { color: 'transparent' },
          ticks: { color: text, autoSkip: false }
        }
      }
    })
  }));
}