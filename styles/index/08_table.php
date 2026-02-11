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

/* итоговые строки (если у тебя в HTML они задаются inline background-color) */
.results tr[style*="background-color: #6d444b"] {
  background: #6d444b !important;
  color: #fff;
  font-weight: 700;
}
