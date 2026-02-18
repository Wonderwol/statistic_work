function initDynamicsCharts() {
  if (typeof Chart === 'undefined') return;

  const data = window.__nimroEduOrgsDynamics || {};
  const years = Array.isArray(data.years) ? data.years : [];
  if (!years.length) return;

  // Хотим: последний год сверху, ранний снизу
  const yearsDesc = years.slice().reverse();

  function nimroRev1(arr, fill = 0) {
    const a = Array.isArray(arr) ? arr.slice() : [];
    while (a.length < years.length) a.push(fill);
    return a.slice(0, years.length).reverse();
  }

  function nimroRev2(matrix, fill = 0) {
    // matrix: [series][year]
    const m = Array.isArray(matrix) ? matrix : [];
    return m.map(row => nimroRev1(row, fill));
  }

  function nimroGetBarSizing(count) {
    const isMobile = window.matchMedia && window.matchMedia('(max-width: 640px)').matches;
    const barThickness = isMobile ? 16 : 22;
    const gap = isMobile ? 10 : 12;

    const extra = isMobile ? 110 : 120;
    const minH = isMobile ? 260 : 320;

    const safeCount = Math.max(1, Number(count) || 1);
    const height = Math.max(minH, extra + safeCount * (barThickness + gap));

    const minBarLength = isMobile ? 6 : 10; // чтобы маленькие значения не исчезали
    return { isMobile, barThickness, gap, height, minBarLength };
  }

  function nimroApplyWrapHeight(canvas, px) {
    const wrap = canvas && canvas.closest ? canvas.closest('.chart-wrap') : null;
    if (!wrap) return;
    wrap.style.height = `${Math.max(1, Math.round(px))}px`;
  }

  const net = data.network || {};
  const netLabels = Array.isArray(net.labels) ? net.labels : [];
  const netSeriesAbs = Array.isArray(net.series) ? net.series : [];
  const netTotals = Array.isArray(net.totals) ? net.totals : [];

  const branchesAbsRaw = Array.isArray(data.branches) ? data.branches : [];

  const sec = data.secondary || {};
  const secLabels = Array.isArray(sec.labels) ? sec.labels : [];
  const secSeriesAbs = Array.isArray(sec.series) ? sec.series : [];
  const secTotals = Array.isArray(sec.totals) ? sec.totals : [];

  function cssVar(name, fallback) {
    const v = getComputedStyle(document.documentElement).getPropertyValue(name);
    return (v || '').trim() || fallback;
  }

  // Tooltip всегда появляется над курсором (а не сбоку)
  if (Chart && Chart.Tooltip && Chart.Tooltip.positioners && !Chart.Tooltip.positioners.nimroTop) {
    Chart.Tooltip.positioners.nimroTop = function (items, eventPosition) {
      const chart = this.chart;
      const area = chart && chart.chartArea ? chart.chartArea : null;

      const x = eventPosition.x;
      const y = eventPosition.y;

      // слегка “зажимаем” caret внутри области графика,
      // чтобы Chart.js реже уводил tooltip влево/вправо
      if (!area) return { x, y };
      const cx = Math.min(Math.max(x, area.left + 12), area.right - 12);
      const cy = Math.min(Math.max(y, area.top + 12), area.bottom - 12);

      return { x: cx, y: cy };
    };
  }

    const COLOR_PRIMARY = cssVar('--primary-color', '#6d444b');

  // Твои цвета (RGB -> HEX)
  const PALETTE = {
    plum:  '#5B393E', // 91 57 62
    beige: '#E4C8B7', // 228 200 183
    sand:  '#CFA487', // 207 164 135
    gray:  '#B1B1B1', // 177 177 177
    red:   '#DA251D', // 218 37 29
    mauve: '#A68E92', // 166 142 146
    brand: COLOR_PRIMARY
  };

  function nimroTintHex(hex, pct) {
    const h = String(hex || '').trim().replace('#', '');
    if (!/^[0-9a-fA-F]{6}$/.test(h)) return hex;

    const r = parseInt(h.slice(0, 2), 16);
    const g = parseInt(h.slice(2, 4), 16);
    const b = parseInt(h.slice(4, 6), 16);

    const clamp = (v) => Math.max(0, Math.min(255, v));
    const mix = (v) => (pct >= 0)
      ? clamp(Math.round(v + (255 - v) * pct))
      : clamp(Math.round(v * (1 + pct)));

    const rr = mix(r).toString(16).padStart(2, '0');
    const gg = mix(g).toString(16).padStart(2, '0');
    const bb = mix(b).toString(16).padStart(2, '0');
    return `#${rr}${gg}${bb}`.toUpperCase();
  }

  function nimroNormLabel(s) {
    return String(s ?? '')
      .trim()
      .toLowerCase()
      .replace(/\s+/g, ' ')
      .replace(/ё/g, 'е');
  }

  // --- Ключи типов (устойчиво к разным формулировкам) ---
  function nimroNetKey(label) {
    const s = nimroNormLabel(label);
    if (s.includes('овз')) return 'ovz';
    if (s.includes('санатор')) return 'sanat';
    if (s.includes('вечер')) return 'evening';
    if (s.includes('д/сад') || s.includes('дс') || s.includes('дет')) return 'nursery';
    if (s.includes('оош')) return 'basic';
    // Важно: "сош" проверяем после "овз/санатор/вечер"
    if (s.includes('сош')) return 'secondary';
    if (s === 'нош' || s.includes('нош')) return 'primary';
    return s;
  }

  function nimroSecKey(label) {
    const s = nimroNormLabel(label);
    if (s.includes('кадет')) return 'cadet';
    if (s.includes('лиц')) return 'lyceum';
    if (s.includes('гимназ')) return 'gym';
    if (s.includes('уиоп') || s.includes('углуб')) return 'sosh_uiop';
    if (s.includes('сош')) return 'sosh';
    return s;
  }

  // --- Цвета по типам ---
  // Сеть: специально разводим близкие пары (beige/sand и brand/plum/mauve)
  const NET_COLOR_BY_KEY = {
    nursery:   nimroTintHex(PALETTE.beige,  0.10),  // светлее
    primary:   nimroTintHex(PALETTE.sand,  -0.30),  // заметно темнее песочного
    basic:     nimroTintHex(PALETTE.gray,  -0.18),  // темнее серого
    secondary: PALETTE.plum,                        // базовый тёмный
    sanat:   nimroTintHex(PALETTE.mauve, -0.6),  // светлее, уходит от plum/brand
    ovz:       nimroTintHex(PALETTE.red,   -0.10),  // чуть глубже
    evening: nimroTintHex(PALETTE.brand,  0.55)    // значительно светлее brand
  };

  // “ИЗ СОШ”: те же тона, но иначе сдвинуты (чтобы отличалось от сети)
  const SEC_COLOR_BY_KEY = {
    sosh:      nimroTintHex(PALETTE.plum,  0.24),
    sosh_uiop: nimroTintHex(PALETTE.brand, -0.22),
    gym:       nimroTintHex(PALETTE.sand,  -0.38),
    lyceum:    nimroTintHex(PALETTE.beige,  0.22),
    cadet:     nimroTintHex(PALETTE.red,   -0.22)
  };

  function nimroPickNetColor(label, i) {
    const key = nimroNetKey(label);
    const c = NET_COLOR_BY_KEY[key];
    if (c) return c;

    // запасной вариант (если вдруг прилетела новая категория)
    const fallback = [PALETTE.beige, PALETTE.gray, PALETTE.plum, PALETTE.red, PALETTE.sand, PALETTE.mauve, PALETTE.brand];
    return fallback[i % fallback.length];
  }

  function nimroPickSecColor(label, i) {
    const key = nimroSecKey(label);
    const c = SEC_COLOR_BY_KEY[key];
    if (c) return c;

    const fallback = [PALETTE.plum, PALETTE.brand, PALETTE.sand, PALETTE.beige, PALETTE.red];
    return fallback[i % fallback.length];
  }

  // --- Порядок стека: раздвигаем похожие (важно!) ---
  // Если какие-то серии нулевые и “исчезают”, этот порядок всё равно держит контраст лучше.
  const NET_STACK_ORDER = ['ovz', 'nursery', 'secondary', 'basic', 'primary', 'evening', 'sanat'];
  const SEC_STACK_ORDER = ['cadet', 'lyceum', 'sosh', 'gym', 'sosh_uiop'];

  function nimroOrderIdx(labels, keyFn, preferredKeys) {
    const items = labels.map((lbl, idx) => ({ idx, key: keyFn(lbl) }));
    const rank = new Map(preferredKeys.map((k, i) => [k, i]));
    items.sort((a, b) => {
      const ra = rank.has(a.key) ? rank.get(a.key) : 999;
      const rb = rank.has(b.key) ? rank.get(b.key) : 999;
      if (ra !== rb) return ra - rb;
      return a.idx - b.idx; // стабильность
    });
    return items.map(x => x.idx);
  }


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
    if (!canvas) return;

    destroyIfExists('network');

    const sizing = nimroGetBarSizing(years.length);
    nimroApplyWrapHeight(canvas, sizing.height);

    const perc = calcPercentMatrix(netSeriesAbs, netTotals);

    const netSeriesAbsR = nimroRev2(netSeriesAbs, 0);
    const netTotalsR = nimroRev1(netTotals, 0);
    const percR = calcPercentMatrix(netSeriesAbsR, netTotalsR);

    const order = nimroOrderIdx(netLabels, nimroNetKey, NET_STACK_ORDER);

    const datasets = order.map((srcIdx, pos) => {
      const name = netLabels[srcIdx];
      const c = nimroPickNetColor(name, srcIdx);

      return {
        label: name,
        data: (percR[srcIdx] || yearsDesc.map(() => 0)),
        _abs: (netSeriesAbsR[srcIdx] || yearsDesc.map(() => 0)),

        // ВАЖНО: без modulo, и цвет выбирается устойчиво
        backgroundColor: c,
        hoverBackgroundColor: nimroTintHex(c, -0.10),
        borderWidth: 0,
        borderSkipped: false,

        borderRadius: 0,
        barThickness: sizing.barThickness,
        maxBarThickness: sizing.barThickness,
        categoryPercentage: 1.0,
        barPercentage: 1.0
      };
    });

    window.__nimroDynCharts.network = new Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: { labels: yearsDesc, datasets },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        transitions: {
          active: { animation: { duration: 0 } } // убираем плавность при hover/unhover
        },
        scales: {
          x: {
            stacked: true,
            min: 0,
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
          legend: { position: 'bottom', labels: { boxWidth: 12, usePointStyle: true } },
         tooltip: {
            position: 'nimroTop',
            xAlign: 'center',
            yAlign: 'bottom',     // tooltip будет НАД курсором
            caretPadding: 10,
            displayColors: false,
            animation: { duration: 0 },
            callbacks: {
              title: () => '',
              label: (ctx) => {
                const pct = Number(ctx.parsed?.x ?? 0);
                const abs = Number(ctx.dataset?._abs?.[ctx.dataIndex] ?? 0);
                return `${pct.toFixed(1)}% · ${fmt.format(abs)}`;
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
    if (!canvas) return;

    destroyIfExists('branches');

    const sizing = nimroGetBarSizing(years.length);
    nimroApplyWrapHeight(canvas, sizing.height);

    const branchesAbs = yearsDesc.map((_, i) => {
      const src = years.length - 1 - i; // берём значения с конца
      const v = Number(branchesAbsRaw[src] ?? 0);
      return Number.isFinite(v) ? v : 0;
    });

    const maxBranches = branchesAbs.reduce((m, v) => Math.max(m, Number(v) || 0), 0);
    const stepBranches = maxBranches <= 30 ? 1 : undefined; // чтобы не было 0.5 / 1.5 и т.п.

    window.__nimroDynCharts.branches = new Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: {
        labels: yearsDesc,
        datasets: [{
          label: 'кроме того филиалы',
          data: branchesAbs,
          backgroundColor: COLOR_PRIMARY,

          borderWidth: 0,
          borderRadius: { topLeft: 0, bottomLeft: 0, topRight: 10, bottomRight: 10 },
          borderSkipped: false,

          minBarLength: sizing.minBarLength,

          barThickness: sizing.barThickness,
          maxBarThickness: sizing.barThickness,
          categoryPercentage: 1.0,
          barPercentage: 1.0
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        transitions: {
          active: { animation: { duration: 0 } }
        },
        scales: {
          x: {
            beginAtZero: true,
            grid: { color: 'rgba(0,0,0,0.06)' },
            ticks: {
              precision: 0,
              stepSize: stepBranches,
              callback: (v) => {
                const n = Number(v);
                if (!Number.isFinite(n)) return '';
                const r = Math.round(n);
                // скрываем дробные подписи, если Chart.js всё же их построил
                if (Math.abs(n - r) > 1e-9) return '';
                return fmt.format(r);
              }
            }
          },
          y: {
            grid: { display: false },
            ticks: { color: 'rgba(44,62,80,0.85)', font: { weight: '700' } }
          }
        },
        plugins: {
          legend: { position: 'bottom', labels: { boxWidth: 12, usePointStyle: true } },
          tooltip: {
            position: 'nimroTop',
            xAlign: 'center',
            yAlign: 'bottom',
            caretPadding: 10,
            displayColors: false,
            animation: { duration: 0 },
            callbacks: {
              title: () => '',
              label: (ctx) => `${fmt.format(ctx.parsed?.x ?? 0)}`
            }
          }
        }
      }
    });
  })();

   // 3) Из СОШ (100%) — БЕЗ зума по X
  (function () {
    const canvas = document.getElementById('secondaryChart');
    if (!canvas) return;

    destroyIfExists('secondary');

    const sizing = nimroGetBarSizing(years.length);
    nimroApplyWrapHeight(canvas, sizing.height);

    const perc = calcPercentMatrix(secSeriesAbs, secTotals);

    const secSeriesAbsR = nimroRev2(secSeriesAbs, 0);
    const secTotalsR = nimroRev1(secTotals, 0);
    const percR = calcPercentMatrix(secSeriesAbsR, secTotalsR);

    // Порядок стека + устойчивые цвета (как ты уже сделал для верхнего графика)
    const order = nimroOrderIdx(secLabels, nimroSecKey, SEC_STACK_ORDER);

    const datasets = order.map((srcIdx) => {
      const name = secLabels[srcIdx];
      const c = nimroPickSecColor(name, srcIdx);

      return {
        label: name,
        data: (percR[srcIdx] || yearsDesc.map(() => 0)),
        _abs: (secSeriesAbsR[srcIdx] || yearsDesc.map(() => 0)),

        backgroundColor: c,
        hoverBackgroundColor: nimroTintHex(c, -0.10),

        borderColor: 'rgba(255,255,255,0.92)',
        borderWidth: 1,
        borderSkipped: false,

        borderRadius: 0,
        barThickness: sizing.barThickness,
        maxBarThickness: sizing.barThickness,
        categoryPercentage: 1.0,
        barPercentage: 1.0
      };
    });

    window.__nimroDynCharts.secondary = new Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: { labels: yearsDesc, datasets },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        transitions: {
          active: { animation: { duration: 0 } }
        },
        scales: {
          x: {
            stacked: true,
            min: 0,
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
          legend: { position: 'bottom', labels: { boxWidth: 12, usePointStyle: true } },
          tooltip: {
            position: 'nimroTop',
            xAlign: 'center',
            yAlign: 'bottom',
            caretPadding: 10,
            displayColors: false,
            animation: { duration: 0 },
            callbacks: {
              title: () => '',
              label: (ctx) => {
                const pct = Number(ctx.parsed?.x ?? 0);
                const abs = Number(ctx.dataset?._abs?.[ctx.dataIndex] ?? 0);
                return `${pct.toFixed(1)}% · ${fmt.format(abs)}`;
              }
            }
          }
        }
      }
    });
  })();


  // авто-подстройка высоты и толщины при ресайзе/масштабе
  if (!window.__nimroDynAutoSizeBound) {
    window.__nimroDynAutoSizeBound = true;
    let raf = 0;

    const schedule = () => {
      if (raf) return;
      raf = requestAnimationFrame(() => {
        raf = 0;

        const charts = window.__nimroDynCharts || {};
        Object.keys(charts).forEach((k) => {
          const chart = charts[k];
          if (!chart || !chart.canvas) return;

          const count = (chart.data && Array.isArray(chart.data.labels)) ? chart.data.labels.length : 1;
          const s = nimroGetBarSizing(count);
          nimroApplyWrapHeight(chart.canvas, s.height);

          const isBranches = (k === 'branches');

          if (chart.data && Array.isArray(chart.data.datasets)) {
            chart.data.datasets.forEach(ds => {
              ds.barThickness = s.barThickness;
              ds.maxBarThickness = s.barThickness;
              ds.categoryPercentage = 1.0;
              ds.barPercentage = 1.0;
              if (isBranches) ds.minBarLength = s.minBarLength;
            });
          }

          try { chart.resize(); } catch (e) {}
          try { chart.update('none'); } catch (e) {}
        });
      });
    };

    window.addEventListener('resize', schedule, { passive: true });
    if (window.visualViewport) window.visualViewport.addEventListener('resize', schedule, { passive: true });
  }
}
