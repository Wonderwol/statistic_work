<?php
declare(strict_types=1);

$docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');

$currentPath = (string)(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/');
$currentPath = ($currentPath !== '/') ? rtrim($currentPath, '/') : '/';

$currentSection = (string)($_GET['section'] ?? '');
$currentSection = preg_replace('/[^a-z0-9_\-]/i', '', $currentSection);

function nimro_nav_stub(string $key): string
{
    return '/v3/pages/stub.php?section=' . rawurlencode($key);
}

function nimro_nav_parse_section(string $query): string
{
    $out = [];
    parse_str($query, $out);
    $s = (string)($out['section'] ?? '');
    $s = preg_replace('/[^a-z0-9_\-]/i', '', $s);
    return $s;
}

function nimro_nav_norm_path(string $path): string
{
    $p = preg_replace('~/+~', '/', $path);
    $p = $p === '' ? '/' : $p;
    return ($p !== '/') ? rtrim($p, '/') : '/';
}

function nimro_nav_is_active(string $href, string $currentPath, string $currentSection): bool
{
    $u = parse_url($href);
    $hrefPath = nimro_nav_norm_path((string)($u['path'] ?? $href));
    $hrefQuery = (string)($u['query'] ?? '');

    if ($hrefPath === '/v3/pages/stub.php' && $currentPath === '/v3/pages/stub.php') {
        $target = nimro_nav_parse_section($hrefQuery);
        if ($target === '') return false;
        if ($currentSection === $target) return true;

        if ($currentSection !== '' && strncmp($currentSection, $target . '_', strlen($target) + 1) === 0) {
            return true;
        }
        return false;
    }

    if ($hrefPath === $currentPath) return true;

    if ($hrefPath !== '/' && $hrefPath !== '/v3/pages/stub.php') {
        $a = $currentPath . '/';
        $b = $hrefPath . '/';
        if (strncmp($a, $b, strlen($b)) === 0) {
            return true;
        }
    }

    return false;
}

function nimro_nav_link_attrs(string $href, string $currentPath, string $currentSection): string
{
    if (!nimro_nav_is_active($href, $currentPath, $currentSection)) {
        return '';
    }
    return ' class="active" aria-current="page"';
}

function nimro_nav_section_open(array $section, string $currentPath, string $currentSection, bool $isFirst): bool
{
    if (nimro_nav_is_active((string)$section['href'], $currentPath, $currentSection)) return true;

    foreach ($section['items'] as $item) {
        if (nimro_nav_is_active((string)$item['href'], $currentPath, $currentSection)) return true;
    }

    return $isFirst;
}

include $docRoot . '/v3/styles/shared/style_nav_left.php';
include $docRoot . '/v3/scripts/nav/nav_left_script.php';

$menu = [
    [
        'title' => 'Сеть образовательных организаций',
        'href'  => '/v3/pages/index/index.php',
        'items' => [
            ['href' => '/v3/pages/index/index.php', 'icon' => '📊', 'text' => 'Сеть образовательных организаций'],
            ['href' => '/v3/pages/info.php',        'icon' => 'ℹ️', 'text' => 'Информация о разделе'],
        ],
    ],
    [
        'title' => 'Обучающиеся образовательных организаций',
        'href'  => nimro_nav_stub('students'),
        'items' => [
            ['href' => nimro_nav_stub('students_types'),         'icon' => '📄', 'text' => 'Численность обучающихся по видам организаций'],
            ['href' => nimro_nav_stub('students_levels'),        'icon' => '📄', 'text' => 'Численность обучающихся по уровням образования'],
            ['href' => nimro_nav_stub('students_ovz'),           'icon' => '📊', 'text' => 'Численность обучающихся с ОВЗ'],
            ['href' => nimro_nav_stub('students_classes'),       'icon' => '📈', 'text' => 'Численность обучающихся по классам'],
            ['href' => nimro_nav_stub('students_foreign'),       'icon' => '📈', 'text' => 'Численность обучающихся – иностранных граждан (по классам)'],
            ['href' => nimro_nav_stub('students_shifts'),        'icon' => '📈', 'text' => 'Численность обучающихся по сменам'],
            ['href' => nimro_nav_stub('students_extended_day'),  'icon' => '📈', 'text' => 'Численность обучающихся в группах продлённого дня'],
        ],
    ],
    [
        'title' => 'Сотрудники образовательных организаций',
        'href'  => nimro_nav_stub('staff'),
        'items' => [
            ['href' => nimro_nav_stub('staff_positions'),  'icon' => '⚙️', 'text' => 'Численность сотрудников по должностям'],
            ['href' => nimro_nav_stub('staff_ages'),       'icon' => '👥', 'text' => 'Численность сотрудников по возрастам'],
            ['href' => nimro_nav_stub('staff_experience'), 'icon' => '🔐', 'text' => 'Численность сотрудников по стажу'],
            ['href' => nimro_nav_stub('staff_edu_level'),  'icon' => '🎓', 'text' => 'Численность сотрудников по уровню образования'],
            ['href' => nimro_nav_stub('staff_training_3y'),'icon' => '📚', 'text' => 'Повышение квалификации за последние 3 года (по должностям)'],
            ['href' => nimro_nav_stub('staff_vacancies'),  'icon' => '🧩', 'text' => 'Количество вакансий по должностям'],
            ['href' => nimro_nav_stub('staff_subjects'),   'icon' => '🧑‍🏫', 'text' => 'Численность учителей по предметам'],
        ],
    ],
    [
        'title' => 'Углублённое изучение предметов',
        'href'  => nimro_nav_stub('advanced_subjects'),
        'items' => [
            ['href' => nimro_nav_stub('advanced_subjects'), 'icon' => '🧠', 'text' => 'Углублённое изучение предметов'],
        ],
    ],
    [
        'title' => 'Имущество образовательных организаций',
        'href'  => nimro_nav_stub('property'),
        'items' => [
            ['href' => nimro_nav_stub('buildings_commissioning'), 'icon' => '🏗️', 'text' => 'Ввод зданий в эксплуатацию'],
            ['href' => nimro_nav_stub('accessibility_mgn'),       'icon' => '♿', 'text' => 'Доступность для МГН'],
            ['href' => nimro_nav_stub('wifi_speed'),              'icon' => '📶', 'text' => 'Доступность и скорость Wi-Fi'],
            ['href' => nimro_nav_stub('specialized_rooms'),       'icon' => '🧪', 'text' => 'Наличие специализированных кабинетов'],
        ],
    ],
    [
        'title' => 'Образовательная среда организаций',
        'href'  => nimro_nav_stub('environment'),
        'items' => [
            ['href' => nimro_nav_stub('busing_access'), 'icon' => '🚌', 'text' => 'Доступность подвоза обучающихся'],
            ['href' => nimro_nav_stub('hot_meals'),     'icon' => '🍲', 'text' => 'Обеспеченность горячим питанием'],
        ],
    ],
];

function h(string $v): string { return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>

<button class="nav-left-fab" id="nimroNavOpen" type="button" aria-controls="nimroNavLeft" aria-label="Открыть меню">
    ☰ Разделы
</button>

<div class="nav-left-backdrop" id="nimroNavBackdrop" aria-hidden="true"></div>

<nav class="left-navigation" id="nimroNavLeft" aria-label="Навигация по разделам">
    <div class="nav-topbar">
        <div class="nav-topbar__title">Навигация</div>
        <button class="nav-topbar__close" id="nimroNavClose" type="button" aria-label="Закрыть меню">✕</button>
    </div>

    <div class="nav-panel">
        <?php foreach ($menu as $i => $section): ?>
            <?php $open = nimro_nav_section_open($section, $currentPath, $currentSection, $i === 0); ?>
            <details class="nav-section" <?= $open ? 'open' : '' ?>>
                <summary><?= h((string)$section['title']) ?></summary>
                <ul class="nav-menu">
                    <?php foreach ($section['items'] as $item): ?>
                        <?php $href = (string)$item['href']; ?>
                        <li>
                            <a href="<?= h($href) ?>"<?= nimro_nav_link_attrs($href, $currentPath, $currentSection) ?>>
                                <span class="nav-ico"><?= h((string)$item['icon']) ?></span>
                                <span class="nav-txt"><?= h((string)$item['text']) ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </details>
        <?php endforeach; ?>
    </div>
</nav>
