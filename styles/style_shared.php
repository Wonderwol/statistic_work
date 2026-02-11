<?php
declare(strict_types=1);
?>
<style>
<?php
$shared = __DIR__ . '/shared';
$files = [
    $shared . '/nav_left.php',
];

foreach ($files as $f) {
    if (is_file($f)) {
        include $f;
        echo "\n";
    }
}
?>
</style>
