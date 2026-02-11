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

  --nimro-nav-w: 320px;
  --nimro-nav-left: 16px;
  --nimro-nav-top: 16px;

  --nimro-nav-text: rgba(44, 62, 80, .92);
  --nimro-nav-muted: rgba(44, 62, 80, .72);
  --nimro-nav-border: rgba(0,0,0,.10);
  --nimro-nav-shadow: 0 10px 26px rgba(0,0,0,.10);
  --nimro-nav-radius: 14px;
}

/* ===== SIDEBAR ===== */
.left-navigation{
  position: fixed;
  left: var(--nimro-nav-left);
  top: var(--nimro-nav-top);

  width: var(--nimro-nav-w);
  max-height: calc(100vh - var(--nimro-nav-top) - 16px);
  overflow: hidden;

  background: #fff;
  border: 1px solid var(--nimro-nav-border);
  border-radius: var(--nimro-nav-radius);
  box-shadow: var(--nimro-nav-shadow);
  z-index: 120;
}

/* внутренняя прокрутка */
.left-navigation .nav-panel{
  padding: 10px 12px 14px 12px;
  max-height: calc(100vh - var(--nimro-nav-top) - 16px - 46px);
  overflow: auto;
  overscroll-behavior: contain;
  scrollbar-gutter: stable;
}

/* ===== TOP BAR (коричневая полоса) ===== */
.left-navigation .nav-topbar{
  height: 46px;
  display:flex;
  align-items:center;
  justify-content: space-between;
  gap: 10px;

  padding: 0 12px;

  background: var(--nimro-nav-accent);
  color: #fff;
}

.left-navigation .nav-topbar__title{
  font-size: 14px;
  font-weight: 900;
  letter-spacing: .2px;
}

.left-navigation .nav-topbar__close{
  display:none;
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

/* ===== SECTIONS ===== */
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

.left-navigation details.nav-section > summary:hover{
  background: rgba(109, 68, 75, .08);
}

.left-navigation details.nav-section[open]{
  border-color: rgba(109, 68, 75, .18);
}

/* ===== LINKS ===== */
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

.left-navigation .nav-menu a:focus-visible{
  outline: 3px solid rgba(109, 68, 75, 0.30);
  outline-offset: 2px;
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

.left-navigation .nav-menu .nav-txt{
  display: inline-block;
  padding-top: 1px;
}

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

.left-navigation .nav-menu a.active .nav-ico{
  background: var(--nimro-nav-accent);
  border-color: rgba(0,0,0,.08);
  color: #fff;
}

/* ===== SCROLLBAR ===== */
.left-navigation .nav-panel{
  scrollbar-width: thin;
  scrollbar-color: rgba(109, 68, 75, .45) transparent;
}
.left-navigation .nav-panel::-webkit-scrollbar{ width: 10px; }
.left-navigation .nav-panel::-webkit-scrollbar-track{ background: transparent; }
.left-navigation .nav-panel::-webkit-scrollbar-thumb{
  background: rgba(109, 68, 75, .26);
  border-radius: 10px;
  border: 3px solid transparent;
  background-clip: content-box;
}
.left-navigation:hover .nav-panel::-webkit-scrollbar-thumb{
  background: rgba(109, 68, 75, .44);
  border: 2px solid transparent;
  background-clip: content-box;
}

/* ===== BACKDROP + MOBILE BUTTON ===== */
.nav-left-backdrop{
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.35);
  z-index: 119;
  display: none;
}

.nav-left-fab{
  position: fixed;
  left: 12px;
  top: 12px;
  z-index: 121;

  display: none;
  align-items: center;
  gap: 8px;

  border: 1px solid rgba(0,0,0,.08);
  background: var(--nimro-nav-accent);
  color: #fff;
  border-radius: 12px;
  padding: 10px 12px;
  font-weight: 900;
  font-size: 13px;
  cursor: pointer;
  box-shadow: 0 10px 26px rgba(0,0,0,.12);
}

.nav-left-fab:hover{
  background: rgba(109, 68, 75, .92);
}

/* ===== RESPONSIVE (off-canvas) ===== */
@media (max-width: 920px){
  .nav-left-fab{ display: inline-flex; }

  .left-navigation{
    left: 0;
    top: 0;
    width: min(360px, 90vw);
    height: 100vh;
    max-height: none;

    border-radius: 0 16px 16px 0;
    transform: translateX(-110%);
    transition: transform .18s ease;
  }

  .left-navigation.is-open{
    transform: translateX(0);
  }

  body.nimro-nav-left-open .nav-left-backdrop{
    display: block;
  }

  .left-navigation .nav-topbar__close{
    display:inline-flex;
  }
}

/* ===== DOCK CONTENT (desktop) ===== */
@media (min-width: 921px){
  body.nimro-nav-docked .content-area{
    padding-left: calc(var(--nimro-nav-w) + 24px);
  }
}
</style>
