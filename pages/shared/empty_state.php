<?php
declare(strict_types=1);

// Можно переопределять перед include:
$emptyIcon = $emptyIcon ?? '📝';
$emptyTitle = $emptyTitle ?? 'Организации не найдены';
$emptyMessage = $emptyMessage ?? 'Измените параметры фильтрации или добавьте данные в систему.';
?>
<div class="no-results">
    <h2><?= htmlspecialchars($emptyIcon . ' ' . $emptyTitle, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></h2>
    <p><?= htmlspecialchars($emptyMessage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></p>
</div>