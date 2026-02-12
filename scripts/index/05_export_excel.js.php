window.exportToExcel = function () {
  const table = document.querySelector('#tableView table');
  if (!table) { alert('Таблица не найдена!'); return; }

  // маркер версии, чтобы сразу увидеть что файл реально обновился
  console.log('[exportToExcel] v2');

  function sanitizeFilename(s) {
    return String(s ?? '')
      .trim()
      .replace(/[\\\/:*?"<>|]/g, '-')   // запрещённые символы Windows
      .replace(/\s+/g, '_')
      .replace(/_+/g, '_')
      .replace(/^_+|_+$/g, '');
  }

  function getSelectedYearNames() {
    const group = document.getElementById('year-group');
    if (!group) return [];

    const checked = group.querySelectorAll('input[type="checkbox"]:checked, input[type="radio"]:checked');
    const years = [];

    checked.forEach((cb) => {
      const label = group.querySelector(`label[for="${cb.id}"]`);
      const text = (label ? label.textContent : cb.value).trim();
      if (text) years.push(text);
    });

    // уникальные
    return Array.from(new Set(years));
  }

  // 1) имя файла
  const years = getSelectedYearNames();
  const yearsPart = years.length ? sanitizeFilename(years.join('__')) : 'без_года';

  const now = new Date();
  const dd = String(now.getDate()).padStart(2, '0');
  const mm = String(now.getMonth() + 1).padStart(2, '0');
  const yyyy = now.getFullYear();

  // 2) экспортируем таблицу как HTML
    '<html><head><meta charset="UTF-8"></head><body>' +
    '<table border="1">' + table.innerHTML + '</table>' +
    '</body></html>';

  // BOM чтобы Excel нормально открыл кириллицу
  const blob = new Blob(['\ufeff' + html], { type: 'application/vnd.ms-excel;charset=utf-8' });
  const url = URL.createObjectURL(blob);

  const a = document.createElement('a');
  a.href = url;
  a.download = `статистика_${yearsPart}_${dd}.${mm}.${yyyy}.xls`;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
};
