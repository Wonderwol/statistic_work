/* Сетка графиков */
.chart-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(min(420px, 100%), 1fr));
  gap: 20px;
  margin-top: 18px;
  width: 100%;
}

/* Карточка графика */
.chart-box {
  background: #fff;
  box-shadow: var(--shadow);
  border: 1px solid rgba(0,0,0,0.06);
  padding: 16px;
  border-radius: var(--border-radius);
  border-top: 4px solid var(--primary-color);
  overflow: hidden; /* важно: ничего не "вылезает" */
}

.chart-box--card:hover {
  transform: none;
  box-shadow: var(--shadow);
}


/* Заголовок */
.chart-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 10px;
}

.chart-header h3 {
  margin: 0;
  color: #2c3e50;
  font-weight: 700;
  font-size: 15px;
  line-height: 1.35;
  flex: 1;
}

.chart-subnote{
  margin-top: 4px;
  font-size: 12px;
  color: rgba(44,62,80,0.65);
}

.chart-wrap{
  overflow: hidden; /* страховка от любых оверфлоу */
}

/* Кнопки/действия у графика */
.chart-actions,
.chart-controls {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.chart-btn {
  appearance: none;
  border: 1px solid rgba(15,23,42,.14);
  background: #fff;
  border-radius: 10px;
  padding: 7px 10px;
  font-size: 12px;
  font-weight: 700;
  color: rgba(15,23,42,.78);
  cursor: pointer;
  transition: transform .12s ease, background .12s ease, border-color .12s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.chart-btn:hover {
  transform: translateY(-1px);
  background: rgba(2,6,23,.02);
  border-color: rgba(15,23,42,.22);
}

.chart-btn:active {
  transform: translateY(0);
}

.chart-btn svg {
  width: 16px;
  height: 16px;
}

/* Область для canvas */
.chart-wrap {
  position: relative;
  width: 100%;
  flex: 0 0 auto;     /* важно: не flex:1, иначе height может "схлопываться" при зуме */
  min-height: 320px;
  height: 420px;      /* базовая высота для обычных графиков */
}


/* Canvas всегда занимает chart-wrap */
.chart-wrap > canvas {
  width: 100% !important;
  height: 100% !important;
  display: block;
}

/* Кастомный tooltip (если используется) */
.chart-tooltip {
  position: absolute;
  pointer-events: none;
  transform: translate(-50%, -110%);
  min-width: 160px;
  max-width: 260px;
  padding: 10px;
  border-radius: 12px;
  background: rgba(15,23,42,.92);
  color: #fff;
  box-shadow: 0 18px 50px rgba(2,6,23,.35);
  opacity: 0;
  transition: opacity .08s ease;
  z-index: 5;
  font-size: 12px;
  line-height: 1.25;
}

.chart-tooltip.is-visible {
  opacity: 1;
}

/* Fullscreen (если у тебя есть логика) */
.chart-box.is-fullscreen {
  position: fixed;
  inset: 12px;
  z-index: 9999;
  margin: 0;
  background: #fff;
  border-radius: 16px;
  padding: 14px;
}

.chart-box.is-fullscreen .chart-wrap {
  min-height: 0;
}

/* Большой график, если нужен */
.chart-wrap.chart-wrap--big {
  height: 560px;
  min-height: 560px;
}

/* Специфичный pieChart (если используется именно такой высокий) */
#pieChart {
  max-height: none !important;
}

.chart-topbar{
  display:flex;
  align-items:flex-start;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 10px;
}

.chart-title{
  margin: 0;
  font-weight: 900;
  color: #2c3e50;
  font-size: 16px;
}

.chart-legend{
  display:flex;
  flex-wrap: wrap;
  gap: 8px;
  justify-content: flex-end;
}

.chart-legend__item{
  display:inline-flex;
  align-items:center;
  gap: 8px;
  padding: 6px 10px;
  border-radius: 999px;
  background: var(--primary-light);
  border: 1px solid rgba(109, 68, 75, .18);
  color: var(--primary-color);
  font-weight: 800;
  font-size: 12px;
  line-height: 1;
  white-space: nowrap;
}

.chart-legend__dot{
  width: 10px;
  height: 10px;
  border-radius: 50%;
  flex: 0 0 auto;
}

.chart-legend__value{
  color: rgba(44,62,80,.85);
  font-weight: 900;
  margin-left: 6px;
}
