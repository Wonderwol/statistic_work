<?php
declare(strict_types=1);

if (defined('NIMRO_NAV_LEFT_INCLUDED')) {
    return;
}
define('NIMRO_NAV_LEFT_INCLUDED', true);

$uri = strtok((string)($_SERVER['REQUEST_URI'] ?? ''), '?');

function nav_active(string $path, string $uri): string {
    return (strpos($uri, $path) !== false) ? ' active' : '';
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
                    <a class="<?= trim(nav_active('/v3/pages/index/index.php', $uri)) ?>" href="/v3/pages/index/index.php">
                        <span class="nav-ico">ОО</span>
                        <span class="nav-txt">Сеть образовательных организаций</span>
                    </a>
                </li>
            </ul>
        </details>

        <details class="nav-section">
            <summary>Обучающиеся образовательных организаций</summary>
            <ul class="nav-menu">
                <li>
                    <a class="<?= trim(nav_active('/v3/pages/students.php', $uri)) ?>" href="/v3/pages/students.php">
                        <span class="nav-ico">👨‍🎓</span>
                        <span class="nav-txt">Обучающиеся (сводная страница)</span>
                    </a>
                </li>

                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">📄</span><span class="nav-txt">Численность обучающихся в НСО по видам организаций</span></a></li>
                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">📄</span><span class="nav-txt">Численность обучающихся в НСО по уровням образования</span></a></li>
                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">📊</span><span class="nav-txt">Численность обучающихся с ОВЗ в НСО</span></a></li>
                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">📈</span><span class="nav-txt">Численность обучающихся в НСО по классам</span></a></li>
                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">📈</span><span class="nav-txt">Численность обучающихся — иностранных граждан в НСО по классам</span></a></li>
                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">📈</span><span class="nav-txt">Численность обучающихся в НСО по сменам</span></a></li>
                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">📈</span><span class="nav-txt">Численность обучающихся в НСО в группах продлённого дня</span></a></li>
            </ul>
        </details>

        <details class="nav-section">
            <summary>Сотрудники образовательных организаций</summary>
            <ul class="nav-menu">
                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">⚙️</span><span class="nav-txt">Численность сотрудников в НСО по должностям</span></a></li>
                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">👥</span><span class="nav-txt">Численность сотрудников в НСО по возрастам</span></a></li>
                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">🔐</span><span class="nav-txt">Численность сотрудников в НСО по стажу</span></a></li>
                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">🎓</span><span class="nav-txt">Численность сотрудников в НСО по уровню образования</span></a></li>
                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">🔄</span><span class="nav-txt">Повышение квалификации за 3 года (по должностям)</span></a></li>
                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">📌</span><span class="nav-txt">Количество вакансий в ОО НСО (по должностям)</span></a></li>
                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">🧑‍🏫</span><span class="nav-txt">Численность учителей в ОО НСО по предметам</span></a></li>
            </ul>
        </details>

        <details class="nav-section">
            <summary>Углублённое изучение предметов</summary>
            <ul class="nav-menu">
                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">⏳</span><span class="nav-txt">Раздел в разработке</span></a></li>
            </ul>
        </details>

        <details class="nav-section">
            <summary>Имущество образовательных организаций</summary>
            <ul class="nav-menu">
                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">🗺️</span><span class="nav-txt">Ввод зданий в эксплуатацию</span></a></li>
                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">♿</span><span class="nav-txt">Доступность для маломобильных групп населения</span></a></li>
                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">📶</span><span class="nav-txt">Доступность и скорость Wi-Fi</span></a></li>
                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">🏫</span><span class="nav-txt">Наличие специализированных кабинетов по предметам</span></a></li>
            </ul>
        </details>

        <details class="nav-section">
            <summary>Образовательная среда организаций</summary>
            <ul class="nav-menu">
                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">🚌</span><span class="nav-txt">Подвоз обучающихся до ОО</span></a></li>
                <li><a href="#"<?= nav_disabled_attrs() ?>><span class="nav-ico">🍽️</span><span class="nav-txt">Обеспеченность горячим питанием</span></a></li>
            </ul>
        </details>

    </div>
</nav>

<?php include __DIR__ . '/../scripts/nav/nav_left_script.php'; ?>
