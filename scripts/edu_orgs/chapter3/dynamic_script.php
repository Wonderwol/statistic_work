<?php
declare(strict_types=1);
?>
<script>
(function () {
  'use strict';

<?php include __DIR__ . '/00_boot.js.php'; ?>
<?php include __DIR__ . '/07_filters.js.php'; ?>
<?php include __DIR__ . '/11_line_chart.js.php'; ?>

  onReady(function () {
    try { initCh3Filters(); } catch (e) { console.error('[nimro] initCh3Filters error', e); }
    try { initCh3LineChart(); } catch (e) { console.error('[nimro] initCh3LineChart error', e); }
  });
})();
</script>
