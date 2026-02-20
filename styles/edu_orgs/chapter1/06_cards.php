/* =========================================================
   CARDS (общие) + DOCK (карточки справа)
   ========================================================= */

/* Сетка карточек: 2 в строку */
.statistics{
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
  margin-top: 12px;
  margin-bottom: 12px;
  align-items: stretch;
}

/* На телефоне — 1 колонка */
@media (max-width: 760px){
  .statistics{ grid-template-columns: 1fr; }
}

/* Итоговая карточка на всю ширину */
.stat-card--wide{ grid-column: 1 / -1; }

/* Базовая карточка */
.stat-card{
  background-color: var(--white);
  padding: 25px;
  border-radius: var(--border-radius);
  box-shadow: var(--shadow);
  display: flex;
  flex-direction: column;
  transition: transform .16s ease, box-shadow .16s ease;
  border-top: 4px solid var(--primary-color);
  align-items: center;
  box-sizing: border-box;
}

/* На случай, если в HTML остались inline style width:49%/50% */
.statistics .stat-card[style]{
  width: auto !important;
  max-width: none !important;
}

@media (hover:hover) and (pointer:fine){
  .stat-card:hover{
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,.15);
  }
}

.stat-card h3{
  color: var(--primary-color);
  margin-bottom: 15px;
  font-size: 16px;
  font-weight: 600;
  line-height: 1.4;
}

.stat-value{
  font-size: 42px;
  font-weight: 700;
  color: var(--primary-color);
  margin-top: auto;
}

/* Пустое состояние */
.no-results{
  padding: 60px 20px;
  text-align: center;
  color: var(--primary-color);
}
.no-results h2{ margin-bottom: 15px; font-size: 24px; }
.no-results p{ color: #666; font-size: 16px; }

/* ===== Компактная сводка в правой колонке (если используется) ===== */
.stats-panel__title{
  grid-column: 1 / -1;
  margin: 0 0 4px 0;
  font-size: 13px;
  font-weight: 800;
  color: rgba(44,62,80,.8);
  letter-spacing: .2px;
}

.dashboard__side .statistics{
  grid-template-columns: 1fr;
  gap: 10px;
  margin: 0;
}
.dashboard__side .stat-card{
  padding: 12px 14px;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  border-top-width: 3px;
}
.dashboard__side .stat-card:hover{
  transform: none;
  box-shadow: var(--shadow);
}
.dashboard__side .stat-card h3{
  margin: 0;
  font-size: 13px;
  line-height: 1.25;
  font-weight: 700;
  color: rgba(44,62,80,.85);
  text-align: left;
}
.dashboard__side .stat-value{
  margin: 0;
  font-size: 22px;
  line-height: 1;
  font-weight: 900;
}

/* =========================================================
   DOCK (карточки справа внутри .stats-dock)
   ========================================================= */

/* FIX 1: фон панели с карточками — строго белый (убираем серость от rgba+blur) */
.stats-dock{
  background: #fff !important;
  backdrop-filter: none !important;
  -webkit-backdrop-filter: none !important;
}

/* Если stats-dock ещё и chart-box — не режем тени */
.stats-dock.chart-box{
  overflow: visible !important;
  background: #fff !important;
}

/* Заголовок */
.stats-dock__head{
  font-weight: 700;
  font-size: 15px;
  color: #2c3e50;
  letter-spacing: 0;
  margin: 2px 2px 10px 2px;
}

.stats-dock,
.stats-dock *{
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* Сетка карточек dock */
.stats-dock__list{
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
  padding: 4px;             /* чтобы тени не упирались в край */
  background: #fff !important;
  overflow: visible;
}

@media (max-width: 460px){
  .stats-dock__list{ grid-template-columns: 1fr; }
}

/* Dock-карточки: по умолчанию белые + коричневая рамка */
/* Dock-карточки: белые + толстая коричневая рамка (красивее) */
.stats-dock .stat-card{
  box-sizing: border-box;

  background: #fff !important;

  /* толще граница */
  border: 3px solid var(--primary-color) !important;
  border-radius: 14px !important;

  padding: 10px 12px !important;

  display: flex;
  flex-direction: row !important;
  justify-content: space-between;
  align-items: center !important;
  gap: 10px;

  transform: none !important;
  filter: none !important;

  /* более приятная “карточная” глубина */
  box-shadow:
    0 10px 24px rgba(0,0,0,.10),
    inset 0 1px 0 rgba(255,255,255,.85) !important;

  transition:
    background-color .22s cubic-bezier(.2,.8,.2,1),
    border-color .22s cubic-bezier(.2,.8,.2,1),
    box-shadow .22s cubic-bezier(.2,.8,.2,1),
    color .22s cubic-bezier(.2,.8,.2,1) !important;
}

.stats-dock .stat-card h3{
  margin: 0 !important;
  font-size: 13px !important;
  font-weight: 700 !important;     /* как у заголовков в других блоках */
  line-height: 1.35 !important;
  color: var(--primary-color) !important;
  text-align: left !important;
  max-width: 70%;
  letter-spacing: 0;
  transition: color .22s cubic-bezier(.2,.8,.2,1) !important;
}

.stats-dock .stat-value{
  margin: 0 !important;
  font-size: 22px !important;
  font-weight: 900 !important;     /* как “жирные значения” в других блоках */
  line-height: 1 !important;
  color: var(--primary-color) !important;
  letter-spacing: 0;
  transition: color .22s cubic-bezier(.2,.8,.2,1) !important;
}

/* "ОО всего" / "Итого ОО" — на всю ширину + крупнее */
.stats-dock .stat-card--dock-total{
  grid-column: 1 / -1;            /* на всю ширину сетки */
  width: 100%;
  justify-content: center !important;
  padding: 14px 14px !important;
  gap: 8px;
}

/* Надпись "Итого ОО" — больше */
.stats-dock .stat-card--dock-total h3{
  font-size: 15px !important;
  font-weight: 900 !important;
  line-height: 1.15 !important;
  max-width: none !important;
}

/* Число — больше */
.stats-dock .stat-card--dock-total .stat-value{
  font-size: 36px !important;
  font-weight: 1000 !important;
  line-height: 1 !important;
}

/* Hover: фон коричневый, текст белый, граница не выделяется (сливается с фоном) */
@media (hover:hover) and (pointer:fine){
  .stats-dock .stat-card:hover{
    background: var(--primary-color) !important;

    /* граница не выделяется: делаем её того же цвета, что фон */
    border-color: var(--primary-color) !important;

    /* без внутреннего "канта" */
    box-shadow: 0 18px 42px rgba(0,0,0,.18) !important;
  }

  .stats-dock .stat-card:hover h3,
  .stats-dock .stat-card:hover .stat-value{
    color: #fff !important;
  }
}

/* Подпись в dock — как .chart-subnote из 07_charts.php */
.stats-dock__caption{
  margin-top: 4px;
  margin-bottom: 10px;
  font-size: 12px;
  line-height: 1.35;
  color: rgba(44,62,80,0.65);
}