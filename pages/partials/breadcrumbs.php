<?php
declare(strict_types=1);

/**
 * Ожидает массив:
 * $breadcrumbs = [
 *   ['title' => '...', 'href' => '/...'],   // ссылка
 *   ['title' => '...'],                    // текущая (без ссылки)
 * ];
 */

$items = $breadcrumbs ?? [];
if (!is_array($items) || $items === []) {
    $items = [
        ['title' => 'Статистические данные', 'href' => '/statistics/'],
    ];
}

$out = [];

foreach ($items as $it) {
    if (is_string($it)) {
        $title = $it;
        $href  = null;
    } elseif (is_array($it)) {
        $title = (string)($it['title'] ?? '');
        $href  = $it['href'] ?? null;
    } else {
        continue;
    }

    // Убираем нумерацию в начале: "1. ...", "2. ...", "10. ..."
    $title = preg_replace('/^\s*\d+\.\s*/u', '', $title);

    $titleEsc = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

    if (is_string($href) && $href !== '') {
        $hrefEsc = htmlspecialchars($href, ENT_QUOTES, 'UTF-8');
        $out[] = '<a href="' . $hrefEsc . '">' . $titleEsc . '</a>';
    } else {
        $out[] = '<span>' . $titleEsc . '</span>';
    }
}
?>
<div class="breadcrumbs">
    <?= implode('&nbsp;>&nbsp;', $out) ?>
</div>
