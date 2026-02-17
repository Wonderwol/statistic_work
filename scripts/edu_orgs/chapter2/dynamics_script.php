<?php
declare(strict_types=1);
?>
<script>
(function () {
  'use strict';

<?php include __DIR__ . '/00_boot.js.php'; ?>
<?php include __DIR__ . '/07_filters.js.php'; ?>
<?php include __DIR__ . '/10_dynamics_charts.js.php'; ?>

  onReady(function () {
    try { initDynFilters(); } catch (e) { console.error('[nimro] initDynFilters error', e); }
    try { initDynamicsCharts(); } catch (e) { console.error('[nimro] initDynamicsCharts error', e); }
  });
})();
</script>
