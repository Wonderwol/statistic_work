<?php
declare(strict_types=1);

$flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;

$payload = [
    'years' => $chartYears ?? [],
    'network' => [
        'labels' => $networkLabels ?? [],
        'series' => $networkSeries ?? [],
        'totals' => $networkTotals ?? [],
    ],
    'branches' => $branchesSeries ?? [],
    'secondary' => [
        'labels' => $secLabels ?? [],
        'series' => $secSeries ?? [],
        'totals' => $secTotals ?? [],
    ],
];
?>
<script>
window.__nimroEduOrgsDynamics = <?= json_encode($payload, $flags) ?>;
</script>
