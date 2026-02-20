.results {
  background-color: var(--white);
  border-radius: var(--border-radius);
  box-shadow: var(--shadow);
  margin-top: 30px;

  /* адаптив без @media */
  overflow-x: auto;
}

/* таблица только внутри .results (не трогаем другие table на странице) */
.results table {
  width: 100%;
  min-width: 800px; /* чтобы не схлопывалась на узких экранах */
  border-collapse: collapse;
}

.results thead th {
  background: var(--primary-color);
  color: var(--white);
  padding: 12px 15px;
  text-align: center;
  font-weight: 600;
  font-size: 14px;
  position: sticky;
  top: 0;
  z-index: 2;
  white-space: nowrap;
}

.results td {
  padding: 10px 15px;
  border-bottom: 1px solid #eee;
  text-align: center;
  font-size: 14px;
  white-space: nowrap;
}

.results tbody tr {
  background-color: var(--white);
  transition: var(--transition);
}

.results tbody tr:nth-child(even) {
  background: #f9f9f9;
}

.results tbody tr:hover {
  background-color: var(--primary-light);
}

/* первая колонка “название” */
.results td:first-child {
  text-align: left;
  font-weight: 600;
  color: #333;
}

/* итоговые строки (в HTML: background-color:#6d444b без пробела) */
.results tr[style*="background-color:#6d444b"],
.results tr[style*="background-color: #6d444b"] {
  background: #6d444b !important;
  font-weight: 700;
}

/* делаем белым ВСЁ содержимое строки, включая первую колонку и ссылки */
.results tr[style*="background-color:#6d444b"] td,
.results tr[style*="background-color:#6d444b"] th,
.results tr[style*="background-color:#6d444b"] td:first-child,
.results tr[style*="background-color:#6d444b"] td a,
.results tr[style*="background-color:#6d444b"] td *,

.results tr[style*="background-color: #6d444b"] td,
.results tr[style*="background-color: #6d444b"] th,
.results tr[style*="background-color: #6d444b"] td:first-child,
.results tr[style*="background-color: #6d444b"] td a,
.results tr[style*="background-color: #6d444b"] td * {
  color: #fff !important;
}

#tableView tr.row-total td,
#tableView tr.row-total th{
  background-color:#6d444b;
  color:#fff;
  font-weight:bold;
}

/* Жирный шрифт только для заголовков и "итого" */
#tableView th,
#tableView td{
  font-weight: 400 !important; /* все строки обычным */
}

#tableView thead th{
  font-weight: 700 !important; /* заголовки жирным */
}

#tableView tbody tr.row-total td,
#tableView tbody tr.row-total th{
  font-weight: 700 !important; /* "итого ОО" жирным */
}

/* =========================================================
   Адаптив таблицы (без изменения её стиля)
   ========================================================= */

/* Контейнер таблицы должен уметь скроллиться по X */
.results{
  max-width: 100%;
  overflow-x: auto;
  overflow-y: hidden;
  -webkit-overflow-scrolling: touch;
}

/* Чтобы таблица не “сжималась” странно и корректно жила внутри скролл-контейнера */
.results table{
  width: 100%;
}
