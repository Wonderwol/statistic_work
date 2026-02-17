<?php
declare(strict_types=1);

if (defined('NIMRO_EDUORGS_CH1_STYLE')) return;
define('NIMRO_EDUORGS_CH1_STYLE', true);

// Если папка parts есть — берём оттуда.
// Если её нет (как у тебя сейчас) — берём файлы прямо из текущей папки.
$baseDir = is_dir(__DIR__ . '/parts') ? (__DIR__ . '/parts') : __DIR__;
?>
<style>
<?php
$files = [
    $baseDir . '/00_tokens.php',
    $baseDir . '/01_base.php',
    $baseDir . '/02_layout.php',
    $baseDir . '/03_filters.php',
    $baseDir . '/04_dropdown.php',
    $baseDir . '/05_buttons.php',
    $baseDir . '/06_cards.php',
    $baseDir . '/07_charts.php',
    $baseDir . '/08_table.php',
];

foreach ($files as $f) {
    if (is_file($f)) {
        include $f;
        echo "\n";
    } else {
        // Чтобы в случае ошибок было видно в исходнике страницы
        echo "/* missing: " . htmlspecialchars($f, ENT_QUOTES, 'UTF-8') . " */\n";
    }
}
?>
</style>
