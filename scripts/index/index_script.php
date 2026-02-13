<?php
// /v3/scripts/index/index_script.php
?>
<script>
(function () {
  'use strict';

<?php include __DIR__ . '/00_boot.js.php'; ?>
<?php include __DIR__ . '/01_theme_and_data.js.php'; ?>
<?php include __DIR__ . '/02_chart_registry.js.php'; ?>
<?php include __DIR__ . '/03_chart_options.js.php'; ?>
<?php include __DIR__ . '/04_chart_pie.js.php'; ?>
// Удалил 05 и 06
<?php include __DIR__ . '/07_filters.js.php'; ?>
<?php include __DIR__ . '/08_table.js.php'; ?>

})();
</script>
