<?php
declare(strict_types=1);

$flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;

$payload = [
    'labels'     => $chartLabels ?? [],
    'years_raw'  => $chartYearPeriods ?? ($year_ids ?? []),
    'series'     => $series ?? [],
    'meta'       => $seriesLabels ?? [],
    'defaultKey' => 'total',
];
?>
<script>
window.__nimroEduOrgsLine = <?= json_encode($payload, $flags) ?>;
</script>
