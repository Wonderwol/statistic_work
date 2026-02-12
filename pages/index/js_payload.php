<?php
declare(strict_types=1);

$flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
?>
<script>
window.pieLabels = <?= json_encode($pieLabels ?? [], $flags) ?>;
window.pieData   = <?= json_encode($pieData ?? [], $flags) ?>;
</script>
