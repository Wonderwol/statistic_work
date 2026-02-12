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

  if (!nav) return;

  function openNav(){
    nav.classList.add('is-open');
    document.body.classList.add('nimro-nav-left-open');
    if (openBtn) openBtn.setAttribute('aria-expanded', 'true');
  }

  function closeNav(){
    nav.classList.remove('is-open');
    document.body.classList.remove('nimro-nav-left-open');
    if (openBtn) openBtn.setAttribute('aria-expanded', 'false');
  }

  if (openBtn){
    openBtn.setAttribute('aria-expanded', 'false');
    openBtn.addEventListener('click', openNav);
  }
  if (closeBtn) closeBtn.addEventListener('click', closeNav);
  if (backdrop) backdrop.addEventListener('click', closeNav);

  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') closeNav();
  });

  // закрывать при клике по ссылке
  nav.addEventListener('click', function(e){
    var a = e.target && e.target.closest ? e.target.closest('a') : null;
    if (a) closeNav();
  });
})();
</script>
