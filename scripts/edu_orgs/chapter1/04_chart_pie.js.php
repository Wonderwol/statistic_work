window.initializeCharts = function () {
  if (typeof Chart === 'undefined') return;

  const pieEl = document.getElementById('pieChart');
  if (!pieEl) return;

  // fmt должен существовать, но на всякий случай делаем безопасный вариант
  const fmt = (typeof window.fmt !== 'undefined' && window.fmt && typeof window.fmt.format === 'function')
    ? window.fmt
    : new Intl.NumberFormat('ru-RU');

  const { pieLabels, pieData } = getChartData();

  const BAR_COLORS = [
    'rgb(91, 57, 62)',
  ];

  const pairs = pieLabels.map((label, i) => {
    const value = Number(pieData[i] ?? 0);
    return {
      label: String(label ?? ''),
      value: Number.isFinite(value) ? value : 0
    };
  }).filter(p => p.value > 0).sort((a, b) => b.value - a.value);

  const labels = pairs.map(p => p.label);
  const values = pairs.map(p => p.value);

  const colors = pairs.map((_, i) => BAR_COLORS[i % BAR_COLORS.length]);
  const total = values.reduce((s, v) => s + v, 0);

  // ===== фикс толщины баров + адаптивная высота под количество баров =====
  function nimroGetBarSizing(count) {
    const isMobile = window.matchMedia && window.matchMedia('(max-width: 640px)').matches;
    const barThickness = isMobile ? 18 : 35;
    const gap = isMobile ? 10 : 12;

    const extra = isMobile ? 90 : 110;
    const minH = isMobile ? 260 : 340;

    const safeCount = Math.max(1, Number(count) || 1);
    const height = Math.max(minH, extra + safeCount * (barThickness + gap));
    return { isMobile, barThickness, gap, height };
  }

  function nimroApplyWrapHeight(canvas, px) {
    const wrap = canvas && canvas.closest ? canvas.closest('.chart-wrap') : null;
    if (!wrap) return;
    wrap.style.height = `${Math.max(1, Math.round(px))}px`;
  }

  const sizing = nimroGetBarSizing(labels.length);
  nimroApplyWrapHeight(pieEl, sizing.height);

  // ===== резерв справа под подписи (оставляем подпись у конца столбика) =====
  const labelCtx = pieEl.getContext('2d');
  let valuePadRight = 34;

  try {
    if (labelCtx) {
      labelCtx.save();
      labelCtx.font = '800 12px Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial';

      let maxW = 0;
      for (let i = 0; i < values.length; i++) {
        const v = Number(values[i] ?? 0);
        if (!Number.isFinite(v) || v <= 0) continue;
        const t = fmt.format(v);
        const w = labelCtx.measureText(t).width;
        if (w > maxW) maxW = w;
      }

      labelCtx.restore();

      // ширина текста + отступ от конца столбика + небольшой воздух
      valuePadRight = Math.max(
        34,
        Math.ceil(maxW) + (sizing.isMobile ? 18 : 22) + 10
      );
    }
  } catch (e) {
    valuePadRight = 34;
  }

  // подписи значений рядом с концом столбика
  const barValueLabels = {
    id: 'barValueLabels',
    afterDatasetsDraw(chart) {
      const { ctx } = chart;
      const meta = chart.getDatasetMeta(0);
      if (!meta || !meta.data) return;

      ctx.save();
      ctx.font = '800 12px Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial';
      ctx.fillStyle = 'rgba(44,62,80,0.88)';
      ctx.textBaseline = 'middle';
      ctx.textAlign = 'left';

      meta.data.forEach((bar, i) => {
        const v = Number(chart.data.datasets[0].data[i] ?? 0);
        if (!Number.isFinite(v) || v <= 0) return;

        const text = fmt.format(v);
        const textW = ctx.measureText(text).width;

        // рядом с концом столбика
        let x = bar.x + 8;

        // не даём выйти за край канваса
        const maxX = (chart.width - 6) - textW;
        if (x > maxX) x = maxX;

        ctx.fillText(text, x, bar.y);
      });

      ctx.restore();
    }
  };

  ChartRegistry.create('pie', () => new Chart(pieEl.getContext('2d'), {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: '',
        data: values,
        backgroundColor: colors,
        hoverBackgroundColor: colors,
        borderColor: 'rgba(80, 60, 60, 0.55)',
        borderWidth: 1.5,
        borderRadius: 10,
        borderSkipped: false,

        minBarLength: sizing.isMobile ? 6 : 10,

        barThickness: sizing.barThickness,
        maxBarThickness: sizing.barThickness,
        categoryPercentage: 1.0,
        barPercentage: 1.0
      }]
    },
    options: buildOptions({
      indexAxis: 'y',
      // место справа под подписи — ключевой фикс
      layout: { padding: { top: 6, right: valuePadRight, bottom: 6, left: 8 } },
      plugins: {
        legend: { display: false },
        tooltip: {
          displayColors: false,
          callbacks: {
            title: () => '',
            label: (ctx) => {
              const v = Number(ctx.parsed?.x ?? ctx.raw ?? 0);
              const pct = total ? (v / total * 100) : 0;
              return `${pct.toFixed(1)}%`;
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

  // ресайз: переоценка высоты и толщины на смене ширины/масштаба
  if (!window.__nimroPieAutoSizeBound) {
    window.__nimroPieAutoSizeBound = true;
    let raf = 0;

    const schedule = () => {
      if (raf) return;
      raf = requestAnimationFrame(() => {
        raf = 0;
        const chart = ChartRegistry && ChartRegistry._charts ? ChartRegistry._charts.pie : null;
        if (!chart) return;

        const canvas = chart.canvas;
        const count = (chart.data && Array.isArray(chart.data.labels)) ? chart.data.labels.length : 1;
        const s = nimroGetBarSizing(count);
        nimroApplyWrapHeight(canvas, s.height);

        const ds = chart.data?.datasets?.[0];
        if (ds) {
          const next = s.barThickness;
          ds.barThickness = next;
          ds.maxBarThickness = next;
          ds.minBarLength = s.isMobile ? 6 : 10;
        }

        try { chart.resize(); } catch (e) {}
        try { chart.update('none'); } catch (e) {}
      });
    };

    window.addEventListener('resize', schedule, { passive: true });
    if (window.visualViewport) window.visualViewport.addEventListener('resize', schedule, { passive: true });
  }
};
