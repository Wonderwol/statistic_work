<?php
declare(strict_types=1);

if (defined('NIMRO_NAV_LEFT_INCLUDED')) {
    return;
}
define('NIMRO_NAV_LEFT_INCLUDED', true);

$uri = strtok((string)($_SERVER['REQUEST_URI'] ?? ''), '?');

function nav_active(string $needle, string $uri): string {
    return (strpos($uri, $needle) !== false) ? ' active' : '';
}

function nav_disabled_attrs(): string {
    return ' class="is-disabled" aria-disabled="true"';
}
?>

<button type="button" class="nav-left-fab" id="nimroNavOpen" aria-controls="nimroNavLeft" aria-label="Открыть разделы">
    ☰ Разделы
</button>

<div class="nav-left-backdrop" id="nimroNavBackdrop" aria-hidden="true"></div>

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
                    <a class="<?= trim(nav_active('/statistics/pages/edu_orgs/index.php', $uri)) ?>"
                       href="/statistics/pages/edu_orgs/index.php">
                        <span class="nav-ico">ОО</span>
                        <span class="nav-txt">Сеть образовательных организаций</span>
                    </a>
                </li>

                <li>
                    <a class="<?= trim(nav_active('/statistics/pages/edu_orgs/chapter1/', $uri)) ?>"
                       href="/statistics/pages/edu_orgs/chapter1/by_type.php">
                        <span class="nav-ico">📊</span>
                        <span class="nav-txt">Состояние сети ОО за учебный год</span>
                    </a>
                </li>

                <li>
                    <a class="<?= trim(nav_active('/statistics/pages/edu_orgs/chapter2/', $uri)) ?>"
                       href="/statistics/pages/edu_orgs/chapter2/dynamics.php">
                        <span class="nav-ico">🔀</span>
                        <span class="nav-txt">Изменения структуры сети ОО</span>
                    </a>
                </li>

                <li>
                    <a class="<?= trim(nav_active('/statistics/pages/edu_orgs/chapter3/', $uri)) ?>"
                       href="/statistics/pages/edu_orgs/chapter3/structure_changes.php">
                        <span class="nav-ico">📈</span>
                        <span class="nav-txt">Общая динамика сети ОО</span>
                    </a>
                </li>
            </ul>
        </details>

        <details class="nav-section">
            <summary>Обучающиеся образовательных организаций</summary>
            <ul class="nav-menu">
                <li>
                    <a class="<?= trim(nav_active('/statistics/pages/students.php', $uri)) ?>"
                       href="/statistics/pages/students.php">
                        <span class="nav-ico">👨‍🎓</span>
                        <span class="nav-txt">Обучающиеся (сводная)</span>
                    </a>
                </li>
            </ul>
        </details>

        <details class="nav-section">
            <summary>Сотрудники образовательных организаций</summary>
            <ul class="nav-menu">
                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">⏳</span><span class="nav-txt">Раздел в разработке</span></a></li>
            </ul>
        </details>

        <details class="nav-section">
            <summary>Имущество образовательных организаций</summary>
            <ul class="nav-menu">
                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">⏳</span><span class="nav-txt">Раздел в разработке</span></a></li>
            </ul>
        </details>

    </div>
</nav>

<?php include __DIR__ . '/../scripts/nav/nav_left_script.php'; ?>
