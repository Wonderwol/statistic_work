<?php
declare(strict_types=1);

if (defined('NIMRO_NAV_LEFT_INCLUDED')) {
    return;
}
define('NIMRO_NAV_LEFT_INCLUDED', true);

$uri = strtok((string)($_SERVER['REQUEST_URI'] ?? ''), '?');

function nav_active(string $needle, string $uri): string {
    return (strpos($uri, $needle) !== false) ? 'active' : '';
}

function nav_disabled_attrs(): string {
    return 'class="is-disabled" aria-disabled="true" tabindex="-1"';
}
?>

<div class="nav-left-edge" id="nimroNavEdge" aria-hidden="true"></div>

<div class="nav-left-hotzone" id="nimroNavHotzone" aria-hidden="true"></div>

<div class="nav-left-backdrop" id="nimroNavBackdrop" aria-hidden="true"></div>

<!-- Кнопка открытия для тач-устройств (на десктопе скрыта CSS-ом) -->
<button type="button"
        class="nav-left-fab"
        id="nimroNavOpen"
        aria-controls="nimroNavLeft"
        aria-expanded="false">
  <span class="nav-left-fab__ico" aria-hidden="true">☰</span>
  <span class="nav-left-fab__txt">Разделы</span>
</button>

<nav class="left-navigation" id="nimroNavLeft" aria-label="Разделы">
  <div class="nav-topbar">
    <div class="nav-topbar__title">Разделы</div>
    <button type="button" class="nav-topbar__close" id="nimroNavClose" aria-label="Закрыть">✕</button>
  </div>

  <div class="nav-panel">
    <details class="nav-section" open>
      <summary>Сеть образовательных организаций</summary>
      <ul class="nav-menu">
        <li>
          <a class="<?= nav_active('/statistics/pages/edu_orgs/chapter1/', $uri) ?>"
             href="/statistics/pages/edu_orgs/chapter1/by_type.php">
            <span class="nav-ico" aria-hidden="true">
              <svg viewBox="0 0 24 24" role="img" focusable="false" aria-hidden="true">
                <path d="M11 2a10 10 0 1 0 10 10h-9a1 1 0 0 1-1-1V2zm2.2.3V10h7.7A8.2 8.2 0 0 0 13.2 2.3z" fill="currentColor"/>
              </svg>
            </span>
            <span class="nav-txt">Состояние сети ОО за учебный год</span>
          </a>
        </li>

        <li>
          <a class="<?= nav_active('/statistics/pages/edu_orgs/chapter2/', $uri) ?>"
             href="/statistics/pages/edu_orgs/chapter2/structure_changes.php">
            <span class="nav-ico" aria-hidden="true">
              <svg viewBox="0 0 24 24" role="img" focusable="false" aria-hidden="true">
                <path d="M7 7h10l-2-2 1.4-1.4L21.8 9l-5.4 5.4L15 13l2-2H7V7zm10 10H7l2 2-1.4 1.4L2.2 15l5.4-5.4L9 11l-2 2h10v4z" fill="currentColor"/>
              </svg>
            </span>
            <span class="nav-txt">Изменения структуры сети ОО</span>
          </a>
        </li>

        <li>
          <a class="<?= nav_active('/statistics/pages/edu_orgs/chapter3/', $uri) ?>"
             href="/statistics/pages/edu_orgs/chapter3/dynamics.php">
            <span class="nav-ico" aria-hidden="true">
              <svg viewBox="0 0 24 24" role="img" focusable="false" aria-hidden="true">
                <path d="M4 19h16v2H2V3h2v16zm4-3 3-4 3 3 5-7 1.6 1.2-6.6 9.3-3-3-2 2.7H8v-2z" fill="currentColor"/>
              </svg>
            </span>
            <span class="nav-txt">Общая динамика сети ОО</span>
          </a>
        </li>
      </ul>
    </details>

    <!-- ВОТ ТУТ: вместо "в разработке" — реальная ссылка -->
    <details class="nav-section" open>
      <summary>Обучающиеся образовательных организаций</summary>
      <ul class="nav-menu">
        <li>
          <a class="<?= nav_active('/statistics/pages/students/chapter1/', $uri) ?>"
             href="/statistics/pages/students/chapter1/students_total.php">
            <span class="nav-ico" aria-hidden="true">
              <svg viewBox="0 0 24 24" role="img" focusable="false" aria-hidden="true">
                <path d="M12 3 2 8l10 5 10-5-10-5zm0 7L2 5v11l10 5 10-5V5l-10 5zm0 3.2 8-4v6.6l-8 4-8-4V9.2l8 4z" fill="currentColor"/>
              </svg>
            </span>
            <span class="nav-txt">Численность обучающихся за учебный год</span>
          </a>
        </li>
      </ul>
    </details>

    <details class="nav-section">
      <summary>Сотрудники образовательных организаций</summary>
      <ul class="nav-menu">
        <li>
          <a href="#" <?= nav_disabled_attrs() ?>>
            <span class="nav-ico" aria-hidden="true">
              <svg viewBox="0 0 24 24" role="img" focusable="false" aria-hidden="true">
                <path d="M6 2h12v6l-4 4 4 4v6H6v-6l4-4-4-4V2zm2 2v3.2l4 4 4-4V4H8zm8 16v-3.2l-4-4-4 4V20h8z" fill="currentColor"/>
              </svg>
            </span>
            <span class="nav-txt">Раздел в разработке</span>
          </a>
        </li>
      </ul>
    </details>

    <details class="nav-section">
      <summary>Имущество образовательных организаций</summary>
      <ul class="nav-menu">
        <li>
          <a href="#" <?= nav_disabled_attrs() ?>>
            <span class="nav-ico" aria-hidden="true">
              <svg viewBox="0 0 24 24" role="img" focusable="false" aria-hidden="true">
                <path d="M6 2h12v6l-4 4 4 4v6H6v-6l4-4-4-4V2zm2 2v3.2l4 4 4-4V4H8zm8 16v-3.2l-4-4-4 4V20h8z" fill="currentColor"/>
              </svg>
            </span>
            <span class="nav-txt">Раздел в разработке</span>
          </a>
        </li>
      </ul>
    </details>

  </div>
</nav>
<!-- /nimroNavLeft -->

<?php
// JS должен быть именно здесь и именно этим файлом
include_once __DIR__ . '/../scripts/nav/nav_left_script.php';
?>