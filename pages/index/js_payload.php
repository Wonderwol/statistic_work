<?php
declare(strict_types=1);

// Этот файл должен подключаться ПОСЛЕ того, как в index.php уже посчитаны:
// $years, $totalOrganizations, $nurseryData, $basicData, $specialData,
// $schoolTypesLabels, $schoolTypesData, $pieLabels, $pieData

$flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
?>
<script>
window.years = <?= json_encode($years ?? [], $flags) ?>;
window.totalOrganizations = <?= json_encode($totalOrganizations ?? [], $flags) ?>;
window.nurseryData = <?= json_encode($nurseryData ?? [], $flags) ?>;
window.basicData = <?= json_encode($basicData ?? [], $flags) ?>;
window.specialData = <?= json_encode($specialData ?? [], $flags) ?>;

window.schoolTypesLabels = <?= json_encode($schoolTypesLabels ?? [], $flags) ?>;
window.schoolTypesData = <?= json_encode($schoolTypesData ?? [], $flags) ?>;

window.pieLabels = <?= json_encode($pieLabels ?? [], $flags) ?>;
window.pieData = <?= json_encode($pieData ?? [], $flags) ?>;
window.pieSeries = <?= json_encode($pieSeries ?? [], $flags) ?>;
</script>
