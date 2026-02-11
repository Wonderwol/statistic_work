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

  function qs(id){ return document.getElementById(id); }

  var nav = qs('nimroNavLeft');
  var openBtn = qs('nimroNavOpen');
  var closeBtn = qs('nimroNavClose');
  var backdrop = qs('nimroNavBackdrop');
  var search = qs('nimroNavSearch');

  if (!nav) return;

  function openNav(){
    nav.classList.add('is-open');
    document.body.classList.add('nimro-nav-left-open');
    if (search) setTimeout(function(){ search.focus(); }, 0);
  }

  function closeNav(){
    nav.classList.remove('is-open');
    document.body.classList.remove('nimro-nav-left-open');
  }

  if (openBtn) openBtn.addEventListener('click', openNav);
  if (closeBtn) closeBtn.addEventListener('click', closeNav);
  if (backdrop) backdrop.addEventListener('click', closeNav);

  document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') closeNav();
  });

  // Поиск по пунктам меню
  if (search) {
    search.addEventListener('input', function(){
      var q = (search.value || '').toLowerCase().trim();
      var sections = nav.querySelectorAll('details.nav-section');
      sections.forEach(function(sec){
        var anyVisible = false;
        var items = sec.querySelectorAll('.nav-menu li');
        items.forEach(function(li){
          var a = li.querySelector('a');
          var text = (a ? a.textContent : '').toLowerCase();
          var visible = (q === '') || (text.indexOf(q) !== -1);
          li.style.display = visible ? '' : 'none';
          if (visible) anyVisible = true;
        });
        sec.style.display = anyVisible ? '' : 'none';
        if (q !== '' && anyVisible) sec.setAttribute('open', 'open');
      });
    });
  }

  // Позиционирование: подстраиваем left/top под реальный контент и шапку
  function updateVars(){
    // LEFT: подгоняем к реальному левому краю контентного контейнера
    var container = document.querySelector('.content-area .container') || document.querySelector('.container');
    var navW = nav.offsetWidth || 320;
    var left = 16;

    if (container && window.innerWidth > 920) {
      var cr = container.getBoundingClientRect();
      left = Math.max(16, Math.round(cr.left - navW - 16));
    }

    document.documentElement.style.setProperty('--nimro-nav-left', left + 'px');

    // TOP: если есть #header — красиво уезжаем вверх при скролле
    var header = document.getElementById('header');
    var top = 16;

    if (header && window.innerWidth > 920) {
      var h = header.offsetHeight || 0;
      var y = window.scrollY || window.pageYOffset || 0;
      top = Math.max(16, Math.round(h - y + 16));
    }

    document.documentElement.style.setProperty('--nimro-nav-top', top + 'px');

    // DOCK: если меню перекрывает контейнер — добавляем отступ контенту
    if (container && window.innerWidth > 920) {
      var nr = nav.getBoundingClientRect();
      var cr2 = container.getBoundingClientRect();
      var overlaps = (nr.right + 8) > cr2.left;
      document.body.classList.toggle('nimro-nav-docked', overlaps);
    } else {
      document.body.classList.remove('nimro-nav-docked');
    }
  }

  window.addEventListener('resize', updateVars, { passive: true });
  window.addEventListener('scroll', updateVars, { passive: true });
  updateVars();
})();
</script>
