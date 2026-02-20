<?php
declare(strict_types=1);

/**
 * Ожидает массив:
 * $breadcrumbs = [
 *   ['title' => '...'],                    // элемент
 *   ['title' => '...', 'href' => '/...'],  // href теперь игнорируется (ссылки убраны)
 * ];
 */

$items = $breadcrumbs ?? [];
if (!is_array($items) || $items === []) {
    $items = [
        ['title' => 'Статистические данные'],
    ];
}

$out = [];
$cnt = count($items);
$i = 0;

foreach ($items as $it) {
    $i++;

    if (is_string($it)) {
        $title = $it;
    } elseif (is_array($it)) {
        $title = (string)($it['title'] ?? '');
    } else {
        continue;
    }

    // Убираем нумерацию в начале: "1. ...", "2. ...", "10. ..."
    $title = preg_replace('/^\s*\d+\.\s*/u', '', $title);
    $titleEsc = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

    // Все элементы — НЕ ссылки. Последний отмечаем как текущую страницу.
    if ($i === $cnt) {
        $out[] = '<span aria-current="page">' . $titleEsc . '</span>';
    } else {
        $out[] = '<span>' . $titleEsc . '</span>';
    }
}
?>
<div class="breadcrumbs">
    <?= implode('&nbsp;>&nbsp;', $out) ?>
</div>
