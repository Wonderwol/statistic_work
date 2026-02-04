<?php
// ожидает переменную $pageTitle
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Проект') ?></title>

    <link rel="stylesheet" href="/css/common.css">
    <?php if (!empty($pageCss)): ?>
        <link rel="stylesheet" href="/css/<?= $pageCss ?>">
    <?php endif; ?>
</head>
<body>

<header>
    <h1>Раздел статистики</h1>
</header>

<main>
