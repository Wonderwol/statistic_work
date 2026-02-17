function cssVar(name, fallback) {
  const v = getComputedStyle(document.documentElement).getPropertyValue(name);
  return (v || '').trim() || fallback;
}

function hexToRgb(hex) {
  const h = String(hex || '').trim().replace('#', '');
  if (h.length === 3) {
    const r = parseInt(h[0] + h[0], 16);
    const g = parseInt(h[1] + h[1], 16);
    const b = parseInt(h[2] + h[2], 16);
    return { r, g, b };
  }
  if (h.length === 6) {
    const r = parseInt(h.slice(0, 2), 16);
    const g = parseInt(h.slice(2, 4), 16);
    const b = parseInt(h.slice(4, 6), 16);
    return { r, g, b };
  }
  return null;
}

function toRgba(color, alpha) {
  const c = String(color || '').trim();
  if (!c) return `rgba(0,0,0,${alpha})`;

  const m = c.match(/rgba?\(\s*([0-9.]+)\s*,\s*([0-9.]+)\s*,\s*([0-9.]+)(?:\s*,\s*([0-9.]+))?\s*\)/i);
  if (m) {
    const r = Number(m[1]), g = Number(m[2]), b = Number(m[3]);
    return `rgba(${r},${g},${b},${alpha})`;
  }

  if (c.startsWith('#')) {
    const rgb = hexToRgb(c);
    if (rgb) return `rgba(${rgb.r},${rgb.g},${rgb.b},${alpha})`;
  }

  return c;
}

const COLOR_PRIMARY   = cssVar('--primary-color',   '#6d444b');
const COLOR_SECONDARY = cssVar('--secondary-color', '#3498db');
const COLOR_SUCCESS   = cssVar('--success-color',   '#2ecc71');
const COLOR_DANGER    = cssVar('--danger-color',    '#e74c3c');

const THEME_PALETTE = [
  COLOR_PRIMARY,
  COLOR_SECONDARY,
  '#0F766E',
  '#7C3AED',
  '#D97706',
  COLOR_DANGER,
  COLOR_SUCCESS,
  '#475569',
  '#0891B2',
  '#A21CAF'
];

// Данные для текущей страницы (минимум: то, что реально используешь в pieChart)
function getChartData() {
  return {
    pieLabels: safeArr(window.pieLabels),
    pieData: safeArr(window.pieData)
  };
}
