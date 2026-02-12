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

  --nimro-nav-w: min(420px, 92vw);
  --nimro-nav-border: rgba(0,0,0,.10);
  --nimro-nav-shadow: 0 18px 60px rgba(0,0,0,.20);
  --nimro-nav-text: rgba(44, 62, 80, .92);
}

/* при открытом меню блокируем прокрутку страницы */
body.nimro-nav-left-open{
  overflow: hidden;
}

/* ===== КНОПКА (вверху) ===== */
.nav-left-fab{
  position: fixed;
  left: 12px;
  top: 12px;
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
  cursor: pointer;
  box-shadow: 0 10px 26px rgba(0,0,0,.14);
}

.nav-left-fab:hover{ background: rgba(109, 68, 75, .92); }

/* когда меню открыто — кнопка должна быть закрыта (перекрыта меню) */
body.nimro-nav-left-open .nav-left-fab{
  opacity: 0;
  pointer-events: none;
}

/* ===== BACKDROP ===== */
.nav-left-backdrop{
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.35);
  z-index: 1000;
  display: none;
}
body.nimro-nav-left-open .nav-left-backdrop{ display: block; }

/* ===== МЕНЮ (всегда off-canvas) НА ВСЮ ВЫСОТУ ===== */
.left-navigation{
  position: fixed;
  left: 0;
  top: 0;
  height: 100vh;
  width: var(--nimro-nav-w);

  background: #fff;
  border-right: 1px solid var(--nimro-nav-border);
  box-shadow: var(--nimro-nav-shadow);

  /* без скруглений и “зазоров” */
  border-radius: 0;

  transform: translateX(-110%);
  transition: transform .18s ease;
  z-index: 1003;

  display: flex;
  flex-direction: column;
  overflow: hidden;
}
.left-navigation.is-open{ transform: translateX(0); }

/* верхняя панель */
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
.left-navigation .nav-topbar__close{
  display:inline-flex;
  border: 1px solid rgba(255,255,255,.35);
  background: rgba(255,255,255,.10);
  color: #fff;
  border-radius: 10px;
  padding: 6px 10px;
  cursor: pointer;
}
.left-navigation .nav-topbar__close:hover{
  background: rgba(255,255,255,.18);
}

/* внутренняя область — скролл только вертикальный */
.left-navigation .nav-panel{
  padding: 10px 12px 14px 12px;
  overflow: auto;
  overscroll-behavior: contain;
  flex: 1 1 auto;
}

/* секции */
.left-navigation details.nav-section{
  margin: 10px 4px;
  border: 1px solid rgba(0,0,0,.06);
  border-radius: 12px;
  background: linear-gradient(180deg, rgba(109, 68, 75, .06), rgba(0,0,0,0));
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
  font-size: 12px;
  color: rgba(109, 68, 75, .90);
  text-transform: uppercase;
  letter-spacing: .35px;
}
.left-navigation details.nav-section > summary::-webkit-details-marker{ display:none; }
.left-navigation details.nav-section > summary::after{
  content:"▾";
  font-size: 14px;
  line-height: 1;
  color: rgba(109, 68, 75, .95);
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
.left-navigation .nav-menu a:hover{
  background: rgba(109, 68, 75, .08);
  border-color: rgba(109, 68, 75, .18);
}
.left-navigation .nav-menu a:active{ transform: translateY(1px); }

.left-navigation .nav-menu a.active{
  background: rgba(109, 68, 75, 0.14);
  border-color: rgba(109, 68, 75, 0.28);
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
}
.left-navigation .nav-menu a.active .nav-ico{
  background: var(--nimro-nav-accent);
  border-color: rgba(0,0,0,.08);
  color: #fff;
}
</style>
