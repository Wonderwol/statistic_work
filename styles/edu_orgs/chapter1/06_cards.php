/* Сетка карточек: 2 в строку */
.statistics {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
  margin-top: 12px;
  margin-bottom: 12px;
  align-items: stretch;
}

/* На телефоне — 1 колонка */
@media (max-width: 760px){
  .statistics{
    grid-template-columns: 1fr;
  }
}

/* Если нужно, чтобы “итоговая” карточка была на всю ширину */
.stat-card--wide{
  grid-column: 1 / -1;
}


/* Карточка */
.stat-card {
  background-color: var(--white);
  padding: 25px;
  border-radius: var(--border-radius);
  box-shadow: var(--shadow);
  display: flex;
  flex-direction: column;
  transition: var(--transition);
  border-top: 4px solid var(--primary-color);
  align-items: center;
}

/* На случай, если в HTML остались inline style width:49%/50% */
.statistics .stat-card[style] {
  width: auto !important;
  max-width: none !important;
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stat-card h3 {
  color: var(--primary-color);
  margin-bottom: 15px;
  font-size: 16px;
  font-weight: 600;
  line-height: 1.4;
}

.stat-value {
  font-size: 42px;
  font-weight: 700;
  color: var(--primary-color);
  margin-top: auto;
}

/* Пустое состояние (у тебя в HTML class="no-results") */
.no-results {
  padding: 60px 20px;
  text-align: center;
  color: var(--primary-color);
}

.no-results h2 {
  margin-bottom: 15px;
  font-size: 24px;
}

.no-results p {
  color: #666;
  font-size: 16px;
}

/* ===== Компактная сводка в правой колонке ===== */
.stats-panel__title{
  grid-column: 1 / -1;
  margin: 0 0 4px 0;
  font-size: 13px;
  font-weight: 800;
  color: rgba(44,62,80,0.8);
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
  color: rgba(44,62,80,0.85);
  text-align: left;
}

.dashboard__side .stat-value{
  margin: 0;
  font-size: 22px;
  line-height: 1;
  font-weight: 900;
}

.dashboard__side .stat-card--total .stat-value{
  font-size: 26px;
}

.dashboard__side .stat-card--total h3{
  font-weight: 900;
}

/* ===== Компактные “строчные” карточки внутри dock ===== */
.stats-dock__head{
  font-weight: 900;
  font-size: 13px;
  color: rgba(44,62,80,.85);
  letter-spacing: .2px;
  margin: 2px 2px 10px 2px;
}

.stats-dock__list{
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
}

/* “ОО всего” (total) — на всю ширину */


  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;

  text-align: center;
  padding: 14px 12px;
  gap: 6px;
}

/* На узких — обратно в 1 колонку */
@media (max-width: 460px){
  .stats-dock__list{
    grid-template-columns: 1fr;
  }
}

.stats-dock .stat-card{
  padding: 10px 12px;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;

  border-top-width: 3px;
}

.stats-dock .stat-card:hover{
  transform: none;
  box-shadow: var(--shadow);
}

.stats-dock .stat-card h3{
  margin: 0;
  font-size: 13px;
  font-weight: 800;
  line-height: 1.25;
  color: rgba(44,62,80,.86);
  text-align: left;
}

.stats-dock .stat-value{
  margin: 0;
  font-size: 22px;
  font-weight: 900;
  line-height: 1;
  color: var(--primary-color);
}

.stats-dock .stat-card--dock-total .stat-value{
  margin: 0;
  color: #fff;
  font-size: 30px;
  font-weight: 900;
}

.stats-dock .stat-card{
  border: 1px solid rgba(0,0,0,.06);
  background: rgba(255,255,255,.98);
}

.stats-dock .stat-card h3{
  max-width: 70%;
}

/* ===== Акцент для карточки "ОО всего" (в правом dock) ===== */


.stats-dock .stat-card--dock-total h3{
  margin: 0;
  width: 100%;
  text-align: center;
  color: var(--primary-color);
}

.stats-dock .stat-card--dock-total .stat-value{
  margin: 0;
  color: var(--primary-color);
  font-size: 30px;
  font-weight: 900;
}

.stats-dock .stat-card--dock-total{
  grid-column: 1 / -1;              /* ВАЖНО: на всю ширину сетки */
  border: 1px solid rgba(255,255,255,.18);
  border-top-width: 4px;
  border-top-color: rgba(255,255,255,.35);

  display: flex;
  flex-direction: column;           /* вертикально */
  justify-content: center;
  align-items: center;

  text-align: center;
  padding: 14px 12px;
  gap: 6px;
  min-height: 96px;
}

.stats-dock .stat-card--dock-total h3{
  margin: 0;
  width: 100%;
  max-width: none;                  /* перебиваем max-width:70% */
  text-align: center;
  color: rgba(44,62,80,.86);
  font-size: 13px;
  font-weight: 900;
  line-height: 1.25;
}

.stats-dock .stat-card--dock-total .stat-value{
  margin: 0;
  color: var(--primary-color);
  font-size: 30px;
  font-weight: 900;
  line-height: 1;
}

/* ===== Dock (сводка справа) ===== */

.stats-dock__head{
  font-weight: 900;
  font-size: 13px;
  color: rgba(255,255,255,.92);
  letter-spacing: .2px;
  margin: 2px 2px 10px 2px;
}

.stats-dock__list{
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
}

@media (max-width: 460px){
  .stats-dock__list{ grid-template-columns: 1fr; }
}

/* Все карточки внутри dock — белые, без hover-движения */
.stats-dock .stat-card{
  background: #fff;
  border: 1px solid rgba(0,0,0,.08);
  border-top: 0;
  box-shadow: none;

  padding: 10px 12px;
  border-radius: 12px;

  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  gap: 10px;

  transition: none;
}

.stats-dock .stat-card:hover{
  transform: none;
  box-shadow: none;
}

.stats-dock .stat-card h3{
  margin: 0;
  font-size: 13px;
  font-weight: 800;
  line-height: 1.25;
  color: rgba(44,62,80,.86);
  text-align: left;
  max-width: 70%;
}

.stats-dock .stat-value{
  margin: 0;
  font-size: 22px;
  font-weight: 900;
  line-height: 1;
  color: var(--primary-color);
}

/* "ОО всего" — на всю ширину и по центру */
.stats-dock .stat-card--dock-total{
  grid-column: 1 / -1;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
  min-height: 96px;
  padding: 14px 12px;
}

.stats-dock .stat-card--dock-total h3{
  width: 100%;
  max-width: none;
  text-align: center;
  color: var(--primary-color);
  font-weight: 900;
}

.stats-dock .stat-card--dock-total .stat-value{
  font-size: 30px;
}



