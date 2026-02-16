<?php
    declare(strict_types=1);
?>

<style>
    <?php
        $dir = __DIR__ . '/edu_orgs';

        $files = [
            $dir . '/00_tokens.php',
            $dir . '/01_base.php',
            $dir . '/02_layout.php',
            $dir . '/03_filters.php',
            $dir . '/04_dropdown.php',
            $dir . '/05_buttons.php',
            $dir . '/06_cards.php',
            $dir . '/07_charts.php',
            $dir . '/08_table.php',
        ];

        foreach ($files as $f) {
            if (is_file($f)) {
                include $f;
                echo "\n";
            }
        }
    ?>
</style>