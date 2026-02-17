<?php
declare(strict_types=1);

// Базовые стили такие же, как у chapter1
include __DIR__ . '/../chapter1/style.php';
?>
<style>
/* Верхние 2 графика: широкий + узкий */
.chart-container--top{
  grid-template-columns: minmax(520px, 2fr) minmax(280px, 1fr);
}
@media (max-width: 980px){
  .chart-container--top{ grid-template-columns: 1fr; }
}

/* Info-иконка без "квадратного блока" вокруг */
.info-link.info-link--circle{
  width: 28px;
  height: 28px;
  padding: 0;
  margin: 0;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border-radius: 50% !important;
  background: transparent !important;
  border: 0 !important;
  box-shadow: none !important;
  text-decoration: none;
  flex: 0 0 auto;
}
.info-link.info-link--circle:hover{
  background: rgba(0,0,0,0.06) !important;
}
.info-link.info-link--circle img{
  width: 28px;
  height: 28px;
  display: block;
}

/* ===== prettier charts: стабильные высоты ===== */
.chart-wrap--network   { height: 300px; }
.chart-wrap--branches  { height: 300px; }
.chart-wrap--secondary { height: 320px; }

@media (max-width: 980px){
  .chart-wrap--network,
  .chart-wrap--branches,
  .chart-wrap--secondary { height: 320px; }
}

/* Легенда компактнее */
.chart-box .chartjs-legend ul { margin: 0; padding: 0; }

</style>
