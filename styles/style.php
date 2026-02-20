<?php
declare(strict_types=1);

if (defined('NIMRO_STUDENTS_CH1_STYLE')) {
    return;
}
define('NIMRO_STUDENTS_CH1_STYLE', true);

// Берём весь базовый UI (токены, layout, кнопки, карточки, таблицы + shared фильтры)
include __DIR__ . '/../../edu_orgs/chapter1/style.php';
?>
<style>
/* =========================================================
   STUDENTS / CH1 — фильтры и режимы отображения
   ========================================================= */

/* Если на странице два фильтра года: один для таблицы, другой для графика */
body.view-cards #year-filter-table{ display: none !important; }
body.view-cards #chart-year-filter{ display: block !important; }

body.view-table #chart-year-filter{ display: none !important; }
body.view-table #year-filter-table{ display: block !important; }

/* Чтобы “год (график)” был визуально как обычный фильтр */
#chart-year-filter .dropdown-search-input{
  font-weight: 500;
}

/* Мелкая косметика: чуть плотнее строка фильтров на широких экранах */
@media (min-width: 1200px){
  .filter-row{ gap: 10px; }
  .filter-group{ flex-basis: 260px; }
}
</style>