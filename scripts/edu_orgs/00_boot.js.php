const fmt = new Intl.NumberFormat('ru-RU');

window.__nimroIndexScriptVer = '2026-02-12-modular-flat-1';

// Надёжный “ready”: работает и если DOMContentLoaded уже прошёл
function onReady(fn) {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', fn, { once: true });
  } else {
    fn();
  }
}

function safeArr(v) { return Array.isArray(v) ? v : []; }
