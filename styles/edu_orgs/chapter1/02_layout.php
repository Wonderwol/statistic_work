.content-area{
    flex: 1;
    width: 100%;
    padding: 0; /* чтобы ширину задавал именно .container */
}

.content-area .container{
    width: 100%;
    max-width: var(--nimro-page-max);
    margin: 0 auto;
    padding: 16px var(--nimro-page-pad);
    box-sizing: border-box;
}

/* мобильные: колонка должна быть 100% */
@media (max-width: 920px){
  .content-area .container{
    max-width: 100%;
    padding: 12px 16px;
  }
}

/* Чуть компактнее на телефоне */
@media (max-width: 640px){
    .content-area{
        padding: 12px;
    }
}

/* Ряд заголовка страницы: переносится и не ломается на узких экранах */
.page-head{
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 8px;
}

.page-head__title{
    margin: 0;
    flex: 1 1 320px;
    min-width: 240px;

    /* как во 2 разделе: "Изменения структуры сети ОО" */
    color: #2c3e50;
    font-weight: 800;
}

.page-head__actions{
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    justify-content: flex-end;
}

/* На телефоне кнопки вида становятся удобными */
@media (max-width: 640px){
    .page-head__actions{
        width: 100%;
        justify-content: flex-start;
    }
    .view-btn{
        flex: 1 1 140px;
        min-width: 0; /* перебиваем min-width:100px */
    }
}

/* Хлебные крошки в общей колонке */
.breadcrumbs{
  margin: 0 0 10px 0;
  padding: 5px 0;
  font-size: 13px;
  color: rgba(0, 0, 0, 0.6);
}
.breadcrumbs a{
  color: #6d444b;
  text-decoration: none;
  opacity: 0.8;
}
.breadcrumbs span{
  color: rgba(0, 0, 0, 0.7);
}

/* ===== Dashboard (график слева, сводка справа) ===== */
.dashboard{
  display: grid;
  grid-template-columns: minmax(0, 1fr) 360px;
  gap: 18px;
  align-items: start;
  margin-top: 16px;
}

.dashboard__main{ min-width: 0; }
.dashboard__side{ min-width: 0; }

@media (max-width: 1100px){
  .dashboard{ grid-template-columns: minmax(0, 1fr) 320px; }
}

@media (max-width: 920px){
  .dashboard{ grid-template-columns: 1fr; }
}

/* Табличный вид: правая колонка убирается, чтобы таблица была на всю ширину */
body.view-table .dashboard{
  grid-template-columns: 1fr;
}
body.view-table .dashboard__side{
  display: none;
}

/* Убираем лишний верхний отступ у графиков внутри дашборда */
.dashboard .chart-container{ margin-top: 0; }

/* ===== Правый dock со сводкой (вне центральной колонки .container) ===== */
:root{
  --nimro-dock-w: 360px;
  --nimro-dock-r: 16px; /* отступ от правого края окна */
  --nimro-dock-z: 900;  /* ниже nav-left (1000+), выше контента */
}

.stats-dock{
  position: fixed;
  right: var(--nimro-dock-r);
  top: 220px;
  width: min(var(--nimro-dock-w), 34vw);
  max-height: none;
  overflow: visible;

  transform: none !important;
  transition: none !important;
  animation: none !important;

  z-index: var(--nimro-dock-z);

  padding: 12px;
  border-radius: 14px;
  background: #fff;

  /* было */
  border: 1px solid rgba(0,0,0,.08);

  /* добавляем полоску как у графика */
  border-top: 4px solid var(--primary-color);

  box-shadow: 0 14px 40px rgba(0,0,0,.12);
  backdrop-filter: none;
}


/* На узких экранах фиксированная панель будет мешать — уводим в поток */
@media (max-width: 920px){
  .stats-dock{
    position: static;
    width: 100%;
    max-height: none;
    margin: 12px 0 0 0;
    top: auto;
    right: auto;
    backdrop-filter: none;
  }
}

.stats-dock--inline{
  position: static;
  right: auto;
  top: auto;
  width: 100%;
  max-height: none;
  overflow: visible;
  z-index: auto;
}

/* на узких экранах — уводим в поток (иначе будет мешать) */
@media (max-width: 920px){
  .stats-dock--floating{
    position: static;
    width: 100%;
    max-height: none;
    overflow: visible;
    margin-top: 12px;
  }
}

/* ==========================
   FIX: не выделять текст при клике по фильтрам
   ========================== */

/* Запрещаем выделение на “декоративных” блоках */
.stats-dock,
.stats-dock *,
.statistics,
.statistics *,
.stat-card,
.stat-card *,
.chart-container,
.chart-container *,
.chart-box,
.chart-box *,
.chart-legend,
.chart-legend *,
.page-head,
.page-head *,
.breadcrumbs,
.breadcrumbs * {
  -webkit-user-select: none;
  user-select: none;
}

/* Но на формах (включая select) — выделение/работа как обычно */
.filters select,
.filters option,
.filters input,
.filters textarea,
.filters button,
.filters label,
.filters .form-control,
.filters .form-select {
  -webkit-user-select: auto;
  user-select: auto;
}

/* ===== info icon: без видимого "блока" вокруг ===== */
.page-head__actions .info-link--icon{
  width: 28px;
  height: 28px;
  padding: 0;
  margin: 0;

  display: inline-flex;
  align-items: center;
  justify-content: center;

  background: transparent !important;
  border: 0 !important;
  border-radius: 50% !important;
  box-shadow: none !important;

  flex: 0 0 auto;
}

.page-head__actions .info-link--icon img{
  width: 28px;
  height: 28px;
  display: block;        /* убирает baseline-артефакты */
}

/* лёгкий hover (круглый), если нужен */
.page-head__actions .info-link--icon:hover{
  background: rgba(0,0,0,0.06) !important;
}

/* аккуратный фокус (тоже круглый) */
.page-head__actions .info-link--icon:focus-visible{
  outline: none;
  box-shadow: 0 0 0 3px rgba(0,0,0,0.15) !important;
}
