function baseOptions() {
  return {
    responsive: true,
    maintainAspectRatio: false,
    interaction: { mode: 'nearest', intersect: false },
    animation: { duration: 750, easing: 'easeOutQuart' },
    layout: { padding: { top: 6, right: 10, bottom: 6, left: 8 } },
    onHover: (evt, activeEls) => {
      const c = evt?.native?.target;
      if (c) c.style.cursor = activeEls && activeEls.length ? 'pointer' : 'default';
    },
    plugins: {
      legend: { display: false },
      tooltip: {
        backgroundColor: 'rgba(20,20,20,0.92)',
        padding: 12,
        cornerRadius: 10,
        titleColor: '#fff',
        bodyColor: '#fff',
        displayColors: false
      }
    }
  };
}

function buildOptions(extra = {}) {
  const base = baseOptions();
  const out = { ...base, ...extra };

  const bp = base.plugins || {};
  const ep = extra.plugins || {};
  out.plugins = { ...bp, ...ep };

  if ((bp.tooltip || ep.tooltip) && out.plugins) {
    const bt = bp.tooltip || {};
    const et = ep.tooltip || {};
    out.plugins.tooltip = { ...bt, ...et };
    out.plugins.tooltip.callbacks = { ...(bt.callbacks || {}), ...(et.callbacks || {}) };
  }

  if ((bp.legend || ep.legend) && out.plugins) {
    const bl = bp.legend || {};
    const el = ep.legend || {};
    out.plugins.legend = { ...bl, ...el };
    out.plugins.legend.labels = { ...(bl.labels || {}), ...(el.labels || {}) };
  }

  return out;
}
