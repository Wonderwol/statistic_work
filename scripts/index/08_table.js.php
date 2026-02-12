// Табличная логика: переключение вида + экспорт
window.showCards = function () {
  localStorage.setItem('nimro_open_view', 'cards');

  const dock = document.getElementById('statsDock');
  if (dock) dock.style.display = '';

  document.body.classList.remove('view-table');
  document.body.classList.add('view-cards');

  document.querySelectorAll('.statistics').forEach(b => b.style.display = '');
  document.querySelectorAll('.stat-card').forEach(b => b.style.display = '');
  document.querySelectorAll('.chart-container').forEach(b => b.style.display = '');

  const tableView = document.getElementById('tableView');
  if (tableView) tableView.style.display = 'none';

  const exportPanel = document.getElementById('exportExcelPanel');
  if (exportPanel) exportPanel.style.display = 'none';

  const showCardsBtn = document.getElementById('showCardsBtn');
  const showTableBtn = document.getElementById('showTableBtn');
  if (showCardsBtn) showCardsBtn.classList.add('active');
  if (showTableBtn) showTableBtn.classList.remove('active');

  if (typeof window.initializeCharts === 'function') {
    window.initializeCharts();
    requestAnimationFrame(() => {
      if (typeof ChartRegistry !== 'undefined' && ChartRegistry && typeof ChartRegistry.resizeAll === 'function') {
        ChartRegistry.resizeAll();
      }
      if (typeof window.__nimroDockSchedule === 'function') {
        window.__nimroDockSchedule();
      }
    });
  } else {
    if (typeof window.__nimroDockSchedule === 'function') {
      window.__nimroDockSchedule();
    }
  }
};

window.showTable = function () {
  localStorage.setItem('nimro_open_view', 'table');

  const dock = document.getElementById('statsDock');
  if (dock) dock.style.display = 'none';

  document.body.classList.remove('view-cards');
  document.body.classList.add('view-table');

  document.querySelectorAll('.statistics').forEach(b => b.style.display = 'none');
  document.querySelectorAll('.stat-card').forEach(b => b.style.display = 'none');
  document.querySelectorAll('.chart-container').forEach(b => b.style.display = 'none');

  const tableView = document.getElementById('tableView');
  if (tableView) tableView.style.display = 'block';

  const exportPanel = document.getElementById('exportExcelPanel');
  if (exportPanel) exportPanel.style.display = 'flex';

  const showCardsBtn = document.getElementById('showCardsBtn');
  const showTableBtn = document.getElementById('showTableBtn');
  if (showTableBtn) showTableBtn.classList.add('active');
  if (showCardsBtn) showCardsBtn.classList.remove('active');

  if (typeof ChartRegistry !== 'undefined' && ChartRegistry && typeof ChartRegistry.destroyAll === 'function') {
    ChartRegistry.destroyAll();
  }
};

window.exportToExcel = function () {
  const table = document.querySelector('#tableView table');
  if (!table) { alert('Таблица не найдена!'); return; }

  const html =
    '<html><head><meta charset="UTF-8"></head><body><table border="1">' +
    table.innerHTML +
    '</table></body></html>';

  const blob = new Blob([html], { type: 'application/vnd.ms-excel' });
  const url = URL.createObjectURL(blob);

  const a = document.createElement('a');
  a.href = url;
  a.download = 'статистика_образования_' + new Date().toLocaleDateString('ru-RU') + '.xls';
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
};

/* ===== Позиционирование dock: центрируем относительно графика ===== */
onReady(() => {
  const dock = document.getElementById('statsDock');
  const filters = document.querySelector('.filters');
  if (!dock || !filters) return;

  let raf = 0;

  function getAnchor() {
    const el =
      document.querySelector('.chart-box--card') ||
      document.querySelector('.chart-box') ||
      document.querySelector('.chart-container');
    if (!el) return null;

    const cs = getComputedStyle(el);
    if (cs.display === 'none' || cs.visibility === 'hidden') return null;

    return el;
  }

  function recalc() {
    raf = 0;

    if (getComputedStyle(dock).position !== 'fixed') {
      dock.style.top = '';
      dock.style.maxHeight = '';
      return;
    }

    if (dock.style.display === 'none') return;

    const margin = 12;

    const filtersRect = filters.getBoundingClientRect();
    const minTop = Math.max(margin, Math.round(filtersRect.bottom + margin));

    const dockRect = dock.getBoundingClientRect();
    const anchor = getAnchor();

    let top = minTop;

    if (anchor) {
      const aRect = anchor.getBoundingClientRect();
      top = Math.round(aRect.top + (aRect.height - dockRect.height) / 2);
      top = Math.max(minTop, top);
    }

    const maxTop = Math.max(margin, Math.round(window.innerHeight - margin - 180));
    top = Math.min(top, maxTop);

    dock.style.top = top + 'px';
    dock.style.maxHeight = Math.max(180, window.innerHeight - top - margin) + 'px';
  }

  function schedule() {
    if (raf) return;
    raf = requestAnimationFrame(recalc);
  }

  window.__nimroDockSchedule = schedule;

  schedule();
  window.addEventListener('resize', schedule, { passive: true });
});
