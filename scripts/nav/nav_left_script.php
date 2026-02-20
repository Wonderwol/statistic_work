<?php
declare(strict_types=1);

if (defined('NIMRO_NAV_LEFT_SCRIPT_INCLUDED')) {
    return;
}
define('NIMRO_NAV_LEFT_SCRIPT_INCLUDED', true);
?>
<script>
(function () {
  if (window.__nimroNavLeftInit) return;
  window.__nimroNavLeftInit = true;

  function byId(id){ return document.getElementById(id); }

  var nav = byId('nimroNavLeft');
  var openBtn = byId('nimroNavOpen');
  var closeBtn = byId('nimroNavClose');
  var backdrop = byId('nimroNavBackdrop');
  var edge = byId('nimroNavEdge');
  var hotzone = byId('nimroNavHotzone');

  function getFixedTopOffsetPx(){
    var h = 0;

    // 1) Битрикс админ-панель (обычно fixed сверху)
    var bxPanel = document.getElementById('panel') || document.getElementById('bx-panel');
    if (bxPanel && bxPanel.getBoundingClientRect) {
      var rect = bxPanel.getBoundingClientRect();
      // Если реально висит сверху (а не скрыта)
      if (rect.height > 0 && rect.bottom > 0 && rect.top <= 0) {
        h += Math.round(rect.height);
      }
    }

    // 2) Шапка учитывается ТОЛЬКО если она фиксированная/sticky
    var header = document.getElementById('header')
      || document.querySelector('header')
      || document.querySelector('.site-header')
      || document.querySelector('.header');

    if (header && header.getBoundingClientRect && window.getComputedStyle) {
      var cs = getComputedStyle(header);
      var pos = (cs.position || '').toLowerCase();
      var top = (cs.top || '');
      var isFixedLike = (pos === 'fixed') || (pos === 'sticky');
      var topIsZero = (top === '0px' || top === '0' || top === 'auto');

      if (isFixedLike && topIsZero) {
        h += Math.round(header.getBoundingClientRect().height || 0);
      }
    }

    return Math.max(0, h);
  }

  function updateHeaderOffset(){
    var px = getFixedTopOffsetPx();
    document.documentElement.style.setProperty('--nimro-header-offset', px + 'px');
  }

  updateHeaderOffset();
  window.addEventListener('resize', updateHeaderOffset, { passive: true });
  window.addEventListener('load', updateHeaderOffset, { passive: true });

  if (!nav) return;

  document.body.classList.remove('nimro-nav-left-open', 'nimro-nav-left-hover-open');
  nav.classList.remove('is-open');

  var isClickOpen = false;
  var closeTimer = 0;
  var hoverEnabled = !!(window.matchMedia && window.matchMedia('(hover:hover) and (pointer:fine)').matches);

  function setExpanded(v){
    if (openBtn) openBtn.setAttribute('aria-expanded', v ? 'true' : 'false');
  }

  function openClick(){
    isClickOpen = true;
    nav.classList.add('is-open');
    document.body.classList.add('nimro-nav-left-open');
    document.body.classList.remove('nimro-nav-left-hover-open');
    setExpanded(true);
  }

  function closeClick(){
    isClickOpen = false;
    nav.classList.remove('is-open');
    document.body.classList.remove('nimro-nav-left-open');
    document.body.classList.remove('nimro-nav-left-hover-open');
    setExpanded(false);
  }

  function openHover(){
    if (!hoverEnabled || isClickOpen) return;
    if (closeTimer) { clearTimeout(closeTimer); closeTimer = 0; }
    nav.classList.add('is-open');
    document.body.classList.add('nimro-nav-left-hover-open');
  }

  function closeHoverSoon(){
    if (!hoverEnabled || isClickOpen) return;
    if (closeTimer) clearTimeout(closeTimer);
    closeTimer = setTimeout(function(){
      nav.classList.remove('is-open');
      document.body.classList.remove('nimro-nav-left-hover-open');
      closeTimer = 0;
    }, 160);
  }

  if (openBtn){
    setExpanded(false);
    openBtn.addEventListener('click', openClick);
  }
  if (closeBtn) closeBtn.addEventListener('click', closeClick);
  if (backdrop) backdrop.addEventListener('click', closeClick);

  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') closeClick();
  });

  // Hover-открытие: ЛЕВЫЙ край
  if (hoverEnabled) {
    if (edge) {
      edge.addEventListener('mouseenter', openHover);
      edge.addEventListener('mouseleave', closeHoverSoon);
    }
    if (hotzone) {
      hotzone.addEventListener('mouseenter', openHover);
      hotzone.addEventListener('mouseleave', closeHoverSoon);
    }
    nav.addEventListener('mouseenter', openHover);
    nav.addEventListener('mouseleave', closeHoverSoon);

    document.addEventListener('mousemove', function(e){
      if (isClickOpen) return;
      if (e.clientX <= 28) openHover(); // расширенный левый край
    }, { passive: true });
  }

  nav.addEventListener('click', function(e){
    var a = e.target && e.target.closest ? e.target.closest('a') : null;
    if (!a) return;
    if (isClickOpen) closeClick();
  });
})();
</script>
