<?php
declare(strict_types=1);

$flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
?>
<script>
window.pieLabels = <?= json_encode($pieLabels ?? [], $flags) ?>;
window.pieData   = <?= json_encode($pieData ?? [], $flags) ?>;

// График "ОО по районам"
window.areaRankLabels = <?= json_encode($areaRankLabels ?? [], $flags) ?>;
window.areaRankValues = <?= json_encode($areaRankValues ?? [], $flags) ?>;
window.areaRankCodes  = <?= json_encode($areaRankCodes ?? [], $flags) ?>;

// оставляю для совместимости (если где-то еще используется)
window.selectedAreaCode  = <?= json_encode($selectedAreaCode ?? '', $flags) ?>;

// то, что читает график (подсветка выбранного района)
window.highlightAreaCode = <?= json_encode($selectedAreaCode ?? '', $flags) ?>;
</script>
