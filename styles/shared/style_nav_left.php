<?php
declare(strict_types=1);

if (defined('NIMRO_NAV_LEFT_STYLE_INCLUDED')) {
    return;
}
define('NIMRO_NAV_LEFT_STYLE_INCLUDED', true);
?>
<style>
:root{
  --nimro-nav-accent: var(--primary-color, #6d444b);
  --nimro-nav-accent-light: var(--primary-light, #eadee0);

  /* подпись на вертикальной ручке */
  --nimro-nav-edge-label: "Разделы";

  --nimro-nav-edge-w: 46px;
  --nimro-nav-edge-h: 150px;

  /*
    Ширина меню НЕ должна заходить на основную колонку:
    подстраиваемся под левый “зазор” вокруг центральной колонки (var(--nimro-page-max)),
    но держим в разумных пределах.
  */
  --nimro-nav-w: clamp(
    220px,
    calc((100vw - var(--nimro-page-max, 60vw)) / 2 - 12px),
    320px
  );

  --nimro-nav-border: rgba(0,0,0,.10);
  --nimro-nav-shadow: 0 18px 60px rgba(0,0,0,.20);
  --nimro-nav-text: rgba(44, 62, 80, .92);
  --nimro-nav-muted: rgba(44, 62, 80, .62);
}

/* при открытом меню блокируем прокрутку страницы */
body.nimro-nav-left-open{
  overflow: hidden;
}

/* =========================
   Ручка слева
   ========================= */
.nav-left-edge{
  position: fixed;
  left: 0;
  top: 50%;
  transform: translateY(-50%);

  width: var(--nimro-nav-edge-w);
  height: var(--nimro-nav-edge-h);
  z-index: 1001;

  background: var(--nimro-nav-accent);
  border-radius: 0 14px 14px 0;

  box-shadow:
    8px 0 22px rgba(0,0,0,.18),
    inset -1px 0 0 rgba(255,255,255,.22);

  user-select: none;
  cursor: pointer;
}

/* Невидимая зона (увеличенная область наведения) */
.nav-left-hotzone{
  position: fixed;
  left: 0;
  top: var(--nimro-header-offset, 0px);
  width: 28px;
  height: calc(100vh - var(--nimro-header-offset, 0px));
  z-index: 1000;
  background: transparent;
}

/* Надпись */
.nav-left-edge::before{
  content: var(--nimro-nav-edge-label, "Разделы");
  position: absolute;

  left: 16px;
  top: 50%;
  transform: translate(-50%, -50%) rotate(180deg);

  writing-mode: vertical-rl;
  text-orientation: mixed;

  font: inherit;
  font-weight: 600;
  font-size: 15px;

  color: rgba(255,255,255,.96);
  white-space: nowrap;
  pointer-events: none;
}

/* Стрелка */
.nav-left-edge::after{
  content: "";
  position: absolute;

  right: 7px;
  top: 50%;

  width: 10px;
  height: 10px;
  border-right: 3px solid rgba(255,255,255,.96);
  border-bottom: 3px solid rgba(255,255,255,.96);
  border-radius: 1px;

  transform: translateY(-50%) rotate(-45deg);
  transition: transform .15s ease;
  pointer-events: none;
}

.nav-left-edge:hover{ filter: brightness(0.98); }
.nav-left-edge:hover::after{
  transform: translateX(2px) translateY(-50%) rotate(-45deg);
}

/* когда меню открыто — стрелку разворачиваем */
body.nimro-nav-left-open .nav-left-edge::after,
body.nimro-nav-left-hover-open .nav-left-edge::after{
  transform: translateY(-50%) rotate(135deg);
}

/* На тач-устройствах скрываем ручку и hotzone */
@media (hover:none), (pointer:coarse){
  .nav-left-edge{ display:none; }
  .nav-left-hotzone{ display:none; }
}

/* ===== КНОПКА (вверху) ===== */
.nav-left-fab{
  position: fixed;
  left: 12px;
  top: calc(var(--nimro-header-offset, 0px) + 12px);
  z-index: 1002;

  display: inline-flex;
  align-items: center;
  gap: 8px;

  border: 1px solid rgba(0,0,0,.10);
  background: var(--nimro-nav-accent);
  color: #fff;
  border-radius: 12px;
  padding: 10px 12px;

  font-weight: 900;
  font-size: 13px;
  line-height: 1;
  cursor: pointer;
  box-shadow: 0 10px 26px rgba(0,0,0,.14);
}
.nav-left-fab:hover{ filter: brightness(0.96); }

@media (hover:hover) and (pointer:fine){
  .nav-left-fab{ display:none; }
}

body.nimro-nav-left-open .nav-left-fab{
  opacity: 0;
  pointer-events: none;
}

/* ===== BACKDROP =====
   Визуально убираем “затемнение”, но оставляем слой для клика (закрытие меню)
*/
.nav-left-backdrop{
  position: fixed;
  left: 0;
  right: 0;
  top: var(--nimro-header-offset, 0px);
  height: calc(100vh - var(--nimro-header-offset, 0px));
  background: transparent;
  z-index: 1000;
  display: none;
}
body.nimro-nav-left-open .nav-left-backdrop{ display: block; }

/* ===== МЕНЮ ===== */
.left-navigation{
  position: fixed;
  left: 0;
  top: var(--nimro-header-offset, 0px);
  height: calc(100vh - var(--nimro-header-offset, 0px));
  box-sizing: border-box;

  width: var(--nimro-nav-w);

  /* УБРАЛИ rgba/градиент => полностью непрозрачный фон */
  background: #ffffff;

  border-right: 1px solid var(--nimro-nav-border);
  box-shadow: var(--nimro-nav-shadow);

  transform: translateX(-110%);
  transition: transform .18s ease;
  will-change: transform;

  z-index: 4500;

  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.left-navigation.is-open{ transform: translateX(0); }

/* верхняя панель (без прозрачности) */
.left-navigation .nav-topbar{
  height: 46px;
  display:flex;
  align-items:center;
  justify-content: space-between;
  gap: 10px;

  padding: 0 12px;
  background: var(--nimro-nav-accent);
  color: #fff;
  flex: 0 0 auto;
}
.left-navigation .nav-topbar__title{
  font-size: 14px;
  font-weight: 900;
  letter-spacing: .2px;
}

/* кнопка закрытия (без прозрачности) */
.left-navigation .nav-topbar__close{
  display:inline-flex;
  border: 1px solid rgba(0,0,0,.12);
  background: #ffffff;
  color: var(--nimro-nav-accent);
  border-radius: 10px;
  padding: 6px 10px;
  cursor: pointer;
}
.left-navigation .nav-topbar__close:hover{
  filter: brightness(0.96);
}

/* внутренняя область */
.left-navigation .nav-panel{
  padding: 10px 12px 14px 12px;
  overflow: auto;
  overscroll-behavior: contain;
  flex: 1 1 auto;

  scrollbar-width: thin;
  scrollbar-color: rgba(109, 68, 75, .45) transparent;
}

.left-navigation .nav-panel::-webkit-scrollbar{ width: 10px; }
.left-navigation .nav-panel::-webkit-scrollbar-track{ background: transparent; }
.left-navigation .nav-panel::-webkit-scrollbar-thumb{
  background: rgba(109, 68, 75, .35);
  border-radius: 10px;
  border: 2px solid transparent;
  background-clip: padding-box;
}

/* секции (без прозрачности) */
.left-navigation details.nav-section{
  margin: 10px 0;
  border: 1px solid rgba(0,0,0,.06);
  border-radius: 12px;
  background: #ffffff;
  overflow: hidden;
}

.left-navigation details.nav-section > summary{
  list-style: none;
  cursor: pointer;
  user-select: none;

  display:flex;
  align-items:center;
  justify-content: space-between;
  gap: 12px;

  padding: 10px 10px;
  font-weight: 900;
  font-size: 13px;
  color: rgba(109, 68, 75, .92);
  letter-spacing: .15px;
}
.left-navigation details.nav-section > summary::-webkit-details-marker{ display:none; }

/* hover — тоже без rgba */
.left-navigation details.nav-section > summary:hover{
  background: #f7f2f3;
}

.left-navigation details.nav-section > summary::after{
  content:"▾";
  font-size: 14px;
  line-height: 1;
  color: rgba(109, 68, 75, .95);
  display: inline-block;
  transform: rotate(-90deg);
  transition: transform .15s ease;
}
.left-navigation details.nav-section[open] > summary::after{ transform: rotate(0deg); }

/* список */
.left-navigation .nav-menu{
  list-style: none;
  margin: 0;
  padding: 0 10px 10px 10px;
}
.left-navigation .nav-menu li{ margin: 4px 0; }

.left-navigation .nav-menu a{
  position: relative;
  display:flex;
  align-items:flex-start;
  gap: 10px;
  width: 100%;

  padding: 10px 10px 10px 12px;
  border-radius: 10px;

  color: var(--nimro-nav-text);
  text-decoration: none;
  font-size: 13px;
  line-height: 1.25;

  border: 1px solid transparent;
  transition: background-color .15s ease, border-color .15s ease, transform .12s ease;
}

.left-navigation .nav-menu a:focus-visible{
  outline: 3px solid rgba(109, 68, 75, .28);
  outline-offset: 2px;
}

/* hover — без прозрачности */
.left-navigation .nav-menu a:hover{
  background: #f4edef;
  border-color: #e7d2d6;
}

.left-navigation .nav-menu a:active{ transform: translateY(1px); }

/* active — без прозрачности */
.left-navigation .nav-menu a.active{
  background: #efe2e4;
  border-color: #e2c8cd;
  font-weight: 900;
}
.left-navigation .nav-menu a.active::before{
  content:"";
  position:absolute;
  left: 6px;
  top: 8px;
  bottom: 8px;
  width: 3px;
  border-radius: 3px;
  background: var(--nimro-nav-accent);
}

.left-navigation .nav-menu a.is-disabled{
  opacity: .55;
  cursor: default;
  pointer-events: none;
}

.left-navigation .nav-menu .nav-ico{
  flex: 0 0 auto;
  display: inline-flex;
  align-items: center;
  justify-content: center;

  min-width: 26px;
  height: 22px;
  padding: 0 6px;

  border-radius: 8px;
  background: var(--nimro-nav-accent-light);
  border: 1px solid rgba(109, 68, 75, .18);
  color: var(--nimro-nav-accent);
  font-weight: 900;
  line-height: 1;

  margin-top: 1px;
}

.left-navigation .nav-menu .nav-ico svg{
  width: 16px;
  height: 16px;
  display: block;
}
.left-navigation .nav-menu a.active .nav-ico{
  background: var(--nimro-nav-accent);
  border-color: rgba(0,0,0,.08);
  color: #fff;
}
</style>
