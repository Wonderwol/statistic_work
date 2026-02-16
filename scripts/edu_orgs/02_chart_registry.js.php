const ChartRegistry = (() => {
  const charts = {};

  function destroy(key) {
    if (!charts[key]) return;
    try { charts[key].destroy(); } catch (e) {}
    charts[key] = null;
  }

  function create(key, factory) {
    destroy(key);
    const inst = factory();
    charts[key] = inst;
    return inst;
  }

  function destroyAll() {
    Object.keys(charts).forEach(destroy);
  }

  function resizeAll() {
    Object.values(charts).forEach((c) => {
      if (!c) return;
      try { c.resize(); } catch (e) {}
      try { c.update('none'); } catch (e) {}
    });
  }

  return { create, destroy, destroyAll, resizeAll, _charts: charts };
})();
