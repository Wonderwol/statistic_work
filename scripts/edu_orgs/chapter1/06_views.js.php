window.showCards = function () {
  localStorage.setItem('nimro_open_view', 'cards');

  document.querySelectorAll('.statistics').forEach(b => b.style.display = '');
  document.querySelectorAll('.stat-card').forEach(b => b.style.display = '');
  document.querySelectorAll('.chart-container').forEach(b => b.style.display = '');

  const tableView = document.getElementById('tableView');
  if (tableView) tableView.style.display = 'none';

  const showCardsBtn = document.getElementById('showCardsBtn');
  const showTableBtn = document.getElementById('showTableBtn');
  if (showCardsBtn) showCardsBtn.classList.add('active');
  if (showTableBtn) showTableBtn.classList.remove('active');

  if (typeof window.initializeCharts === 'function') {
    window.initializeCharts();
    requestAnimationFrame(() => ChartRegistry.resizeAll());
  }
};

window.showTable = function () {
  localStorage.setItem('nimro_open_view', 'table');

  document.querySelectorAll('.statistics').forEach(b => b.style.display = 'none');
  document.querySelectorAll('.stat-card').forEach(b => b.style.display = 'none');
  document.querySelectorAll('.chart-container').forEach(b => b.style.display = 'none');

  const tableView = document.getElementById('tableView');
  if (tableView) tableView.style.display = 'block';

  const showCardsBtn = document.getElementById('showCardsBtn');
  const showTableBtn = document.getElementById('showTableBtn');
  if (showTableBtn) showTableBtn.classList.add('active');
  if (showCardsBtn) showCardsBtn.classList.remove('active');

  ChartRegistry.destroyAll();
};
