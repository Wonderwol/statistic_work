<?php
declare(strict_types=1);

$flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
?>
<script>
// line chart (динамика)
window.studentsLineLabels = <?= json_encode($lineLabels ?? [], $flags) ?>;
window.studentsLineValues = <?= json_encode($lineValues ?? [], $flags) ?>;

// rank chart (топ районов)
window.areaRankLabels = <?= json_encode($areaRankLabels ?? [], $flags) ?>;
window.areaRankValues = <?= json_encode($areaRankValues ?? [], $flags) ?>;
window.areaRankCodes  = <?= json_encode($areaRankCodes ?? [], $flags) ?>;

window.highlightAreaCode = <?= json_encode($highlightAreaCode ?? '', $flags) ?>;
window.rankYearLabel     = <?= json_encode($rankYearLabel ?? '', $flags) ?>;
</script>