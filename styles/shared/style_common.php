<?php
declare(strict_types=1);

if (defined('NIMRO_STYLE_COMMON_INCLUDED')) {
    return;
}
define('NIMRO_STYLE_COMMON_INCLUDED', true);

/*
  Общие стили приложения:
  - токены
  - базовые правила
  - layout
  - фильтры + dropdown (shared)
  - кнопки
  - карточки
  - графики
  - таблицы
*/

$baseDir = is_dir(__DIR__ . '/../edu_orgs/chapter1/parts')
    ? (__DIR__ . '/../edu_orgs/chapter1/parts')
    : (__DIR__ . '/../edu_orgs/chapter1');

$sharedDir = __DIR__;

$files = [
    $baseDir   . '/00_tokens.php',
    $baseDir   . '/01_base.php',
    $baseDir   . '/02_layout.php',

    // shared
    $sharedDir . '/03_filters.php',
    $sharedDir . '/04_dropdown.php',

    $baseDir   . '/05_buttons.php',
    $baseDir   . '/06_cards.php',
    $baseDir   . '/07_charts.php',
    $baseDir   . '/08_table.php',
];
?>
<style>
<?php
foreach ($files as $f) {
    if (is_file($f)) {
        include $f;
        echo "\n";
    } else {
        echo "/* [nimro] missing style part: " . htmlspecialchars($f, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . " */\n";
    }
}
?>
</style>