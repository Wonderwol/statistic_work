// ===== View переключение + charts init =====
window.initializeCharts = function () {
  buildStudentsLineChart();
  buildStudentsAreaRankChart();
};

window.showCards = function () {
  localStorage.setItem('nimro_open_view', 'cards');

  const yearTableFG = document.getElementById('year-filter-table');
  const yearChartFG = document.getElementById('chart-year-filter');
  if (yearTableFG) yearTableFG.style.display = 'none';
  if (yearChartFG) yearChartFG.style.display = '';

  document.body.classList.remove('view-table');
  document.body.classList.add('view-cards');

  document.querySelectorAll('.statistics').forEach(b => b.style.display = '');
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
    });
  }
};

window.showTable = function () {
  localStorage.setItem('nimro_open_view', 'table');

  const yearTableFG = document.getElementById('year-filter-table');
  const yearChartFG = document.getElementById('chart-year-filter');
  if (yearTableFG) yearTableFG.style.display = '';
  if (yearChartFG) yearChartFG.style.display = 'none';

  document.body.classList.remove('view-cards');
  document.body.classList.add('view-table');

  document.querySelectorAll('.statistics').forEach(b => b.style.display = 'none');
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

  const cloned = table.cloneNode(true);
  cloned.setAttribute('border', '1');
  cloned.style.borderCollapse = 'collapse';

  // базовые стили
  cloned.querySelectorAll('th, td').forEach((cell) => {
    cell.style.border = '1px solid #000';
    cell.style.padding = '4px 6px';
    cell.style.fontFamily = 'Calibri, Arial, sans-serif';
    cell.style.fontSize = '11pt';
    cell.style.whiteSpace = 'nowrap';
  });

  // заголовки
  cloned.querySelectorAll('thead th').forEach((th) => {
    th.style.backgroundColor = '#6d444b';
    th.style.color = '#ffffff';
    th.style.fontWeight = 'bold';
  });

  // строка "Итого" (если есть)
  cloned.querySelectorAll('tbody tr').forEach((tr) => {
    if (!tr.classList.contains('table-total')) return;
    tr.querySelectorAll('td').forEach((td) => {
      td.style.backgroundColor = '#6d444b';
      td.style.color = '#ffffff';
      td.style.fontWeight = 'bold';
    });
  });

  const html = '<html><head><meta charset="UTF-8"></head><body>' + cloned.outerHTML + '</body></html>';
  const blob = new Blob(['\ufeff' + html], { type: 'application/vnd.ms-excel;charset=utf-8' });
  const url = URL.createObjectURL(blob);

  const a = document.createElement('a');
  a.href = url;
  a.download = 'обучающиеся_' + new Date().toLocaleDateString('ru-RU') + '.xls';
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
};