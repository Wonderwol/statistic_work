window.exportToExcel = function () {
  const table = document.querySelector('#tableView table');
  if (!table) { alert('Таблица не найдена!'); return; }

  console.log('[exportToExcel] v3-indent-spaces');

  function sanitizeFilename(s) {
    return String(s ?? '')
      .trim()
      .replace(/[\\\/:*?"<>|]/g, '-')
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

    return Array.from(new Set(years));
  }

  // ===== КЛОНИРУЕМ ТАБЛИЦУ И ДОБАВЛЯЕМ "ПРОБЕЛЫ" В НУЖНЫЕ СТРОКИ (1-й столбец) =====
  const clonedTable = table.cloneNode(true);

  const indentLevel2 = new Set([
    'СОШ',
    'СОШ с УИОП',
    'гимназии',
    'лицеи',
    'кадетские корпуса'
  ]);

  function normText(s) {
    return String(s ?? '').replace(/\s+/g, ' ').trim();
  }

  function addExcelSpaces(td, level) {
    // 1 уровень = 4 пробела, 2 уровень = 8 пробелов (подстрой, если надо)
    const spaces = level === 1 ? 4 : 8;

    // Excel корректно сохраняет именно так:
  
    const spacer = '&nbsp;'.repeat(spaces);

    const text = td.textContent; // берём чистый текст, чтобы не тащить лишние теги
    td.innerHTML = spacer + text;

    // убираем процентные padding-left (Excel их часто игнорирует/ломает)
    const st = td.getAttribute('style') || '';
    td.setAttribute('style', st.replace(/padding-left\s*:\s*[^;]+;?/ig, ''));
  }

  clonedTable.querySelectorAll('tbody tr').forEach((tr) => {
    const td = tr.querySelector('td:first-child');
    if (!td) return;

    const label = normText(td.textContent);

    // строго те строки, которые ты перечислил
    if (indentLevel2.has(label)) {
      addExcelSpaces(td, 2);
      return;
    }

    // (дополнительно, если захочешь — можно поддержать и твои padding-left: 18% / 9%)
    // сейчас не трогаю, чтобы не было сюрпризов
  });

  // ===== ИМЯ ФАЙЛА =====
  const years = getSelectedYearNames();
  const yearsPart = years.length ? sanitizeFilename(years.join('__')) : 'без_года';

  const now = new Date();
  const dd = String(now.getDate()).padStart(2, '0');
  const mm = String(now.getMonth() + 1).padStart(2, '0');
  const yyyy = now.getFullYear();

  // ===== HTML ДЛЯ EXCEL =====
  const html =
    `<html xmlns:o="urn:schemas-microsoft-com:office:office"
           xmlns:x="urn:schemas-microsoft-com:office:excel"
           xmlns="http://www.w3.org/TR/REC-html40">
      <head>
        <meta charset="UTF-8">
        <!--[if gte mso 9]>
        <xml>
          <x:ExcelWorkbook>
            <x:ExcelWorksheets>
              <x:ExcelWorksheet>
                <x:Name>Таблица</x:Name>
                <x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>
              </x:ExcelWorksheet>
            </x:ExcelWorksheets>
          </x:ExcelWorkbook>
        </xml>
        <![endif]-->
        <style>
          table{border-collapse:collapse;}
          td,th{border:1px solid #000;padding:4px 6px;font-family:Calibri,Arial,sans-serif;font-size:11pt;white-space:nowrap;}
        </style>
      </head>
      <body>
        <table>${clonedTable.innerHTML}</table>
      </body>
    </html>`;

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
