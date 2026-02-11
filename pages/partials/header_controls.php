<?php
declare(strict_types=1);
?>
<div style="margin: 0 0 10px 0; padding: 5px 0; font-size: 13px; color: rgba(0, 0, 0, 0.6);">
<?php
$currentPath = $_SERVER['REQUEST_URI'] ?? '';
$scriptPath  = $_SERVER['SCRIPT_NAME'] ?? '';

$baseUrl = 'http://' . ($_SERVER['HTTP_HOST'] ?? '');
$currentDir = dirname($scriptPath);

$crumbs = [];
$crumbs[] = '<a href="' . $baseUrl . '/" style="color: #6d444b; text-decoration: none; opacity: 0.8;">Главная</a>';

if (strpos($currentDir, 'statistics') !== false) {
    $crumbs[] = '<a href="' . $baseUrl . '/statistics/" style="color: #6d444b; text-decoration: none; opacity: 0.8;">Статистика и аналитика</a>';

    if (strpos($currentPath, 'open') !== false || basename($scriptPath) === 'index.php') {
        $crumbs[] = '<a href="' . $baseUrl . '/statistics/open/" style="color: #6d444b; text-decoration: none; opacity: 0.8;">Открытая статистика</a>';
    }
}

$crumbs[] = '<span style="color: rgba(0, 0, 0, 0.7);">Сеть образовательных организаций</span>';

echo implode('&nbsp;>&nbsp;', $crumbs);
?>
</div>
