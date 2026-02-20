/* =========================================================
   SHARED: FILTERS (layout + container + buttons line)
   ========================================================= */

.filters{
  background-color: var(--white);
  padding: 25px;
  border-radius: var(--border-radius);
  box-shadow: var(--shadow);
  margin-bottom: 30px;

  /* как у .chart-box */
  border: 1px solid rgba(0,0,0,0.06);
  border-top: 4px solid var(--primary-color);
  overflow: hidden;
}

.filter-row{
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.filter-group{
  flex: 1 1 280px;
  min-width: 240px;
}

@media (max-width: 760px){
  .filter-group{ min-width: 200px; }
}

.filter-label{
  font-weight: 600;
  color: var(--primary-color);
  margin-bottom: 8px;
  display: block;
  font-size: 14px;
}

/* Открытый фильтр должен быть выше соседей */
.filter-group.dropdown-open{
  position: relative;
  z-index: 5000;
}

/* Контейнер кнопок фильтров */
.buttons{
  display: flex;
  gap: 15px;
  margin-top: 25px;
  padding-top: 20px;
  border-top: 1px solid var(--medium-gray);
  flex-wrap: wrap;
}

/* Чтобы кнопки не растягивались на всю ширину */
.buttons .btn-primary,
.buttons .btn-secondary{
  flex: 0 0 auto;
}

/* Скрываем "Выбрано: 0" для негодовых фильтров */
#org_type-count,
#locality-count,
#chart_year-count{
  display: none;
}

/* Если где-то остался блок "ничего не найдено" — прячем */
.no-results{
  display: none !important;
}

/* Информационные иконки */
.info-link{
  display: inline-flex;
  align-items: center;
  color: var(--primary-color);
  text-decoration: none;
  font-size: 14px;
  transition: var(--transition);
}

.info-link:hover{
  color: var(--primary-hover);
}