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


    dock.style.maxHeight = ''; // на всякий случай сбрасываем перед измерением
    const dockRect = dock.getBoundingClientRect();
    const anchor = getAnchor();

    let top = minTop;

    if (anchor) {
      const aRect = anchor.getBoundingClientRect();

            // Центр dock = центр графика
      const targetCenter = aRect.top + (aRect.height / 2);
      top = Math.round(targetCenter - (dockRect.height / 2));

      // Ограничение только сверху (не наезжать на фильтры)
      top = Math.max(minTop, top);

    }

     const prevH = dockRect.height;

    dock.style.top = top + 'px';
    dock.style.maxHeight = ''; // не ограничиваем высоту => не будет внутреннего скролла

    // Если после установки top/zoom изменилась высота (переносы текста) — пересчитаем ещё раз
    const newH = dock.getBoundingClientRect().height;
    if (Math.abs(newH - prevH) >= 2) {
      schedule();
    } // не ограничиваем высоту => не будет внутреннего скролла
  }

  function schedule() {
    if (raf) return;
    raf = requestAnimationFrame(recalc);
  }

  window.__nimroDockSchedule = schedule;

  schedule();

  // обычный resize (иногда не срабатывает на zoom)
  window.addEventListener('resize', schedule, { passive: true });

  // scroll тоже может менять положение anchor относительно viewport
  window.addEventListener('scroll', schedule, { passive: true });

  // zoom в Chrome/Edge чаще всего дергает visualViewport, а не window.resize
  if (window.visualViewport) {
    window.visualViewport.addEventListener('resize', schedule, { passive: true });
    window.visualViewport.addEventListener('scroll', schedule, { passive: true });
  }

  // на всякий случай при смене вкладки/возврате в окно
  document.addEventListener('visibilitychange', () => {
    if (!document.hidden) schedule();
  }, { passive: true });
});

// FIX: при клике по выпадающим спискам фильтров не выделять/копировать текст вокруг
onReady(() => {
  const filters = document.querySelector('.filters');
  if (!filters) return;

  filters.addEventListener('mousedown', (e) => {
    const t = e.target;
    if (!t) return;

    // селект / опции / инпуты внутри блока фильтров
    const tag = (t.tagName || '').toUpperCase();
    if (tag === 'SELECT' || tag === 'OPTION' || tag === 'INPUT' || tag === 'TEXTAREA') {
      const sel = window.getSelection ? window.getSelection() : null;
      if (sel && sel.removeAllRanges) sel.removeAllRanges();
    }
  }, true);
});


