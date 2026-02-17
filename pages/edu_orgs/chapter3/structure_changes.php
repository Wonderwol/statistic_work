<?php
declare(strict_types=1);

$docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
require_once $docRoot . '/statistics/config/config.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2. Изменения структуры сети ОО</title>

    <?php
        include $docRoot . '/statistics/styles/style_by_type.php';
        include $docRoot . '/statistics/styles/shared/style_footer.php';
        include $docRoot . '/statistics/styles/shared/style_header.php';
        include $docRoot . '/statistics/styles/shared/style_nav_left.php';
    ?>

    <link rel="icon" type="image/png" sizes="16x16" href="/statistics/src/img/favicon16x16.png">
</head>
<body>

<?php
include $docRoot . '/statistics/pages/shared/header.php';
include $docRoot . '/statistics/nav/nav_left.php';
?>

<div class="content-area">
  <div class="container">

    <?php
    $breadcrumbs = [
      ['title' => 'Статистические данные', 'href' => '/statistics/'],
      ['title' => 'Сеть образовательных организаций', 'href' => '/statistics/pages/edu_orgs/index.php'],
      ['title' => '2. Изменения структуры сети ОО'],
    ];
    include $docRoot . '/statistics/pages/partials/breadcrumbs.php';
    ?>

    <div class="filters">
      <div class="page-head">
        <h1 class="page-head__title" style="color:#2c3e50; font-weight:bold;">
          2. Изменения структуры сети ОО
        </h1>

        <div class="page-head__actions">
          <button class="view-btn" onclick="window.location.href='/statistics/pages/edu_orgs/index.php'">к подразделам</button>
        </div>
      </div>

      <p style="color: gray; margin: 8px 0 0 0; font-size: 14px;">
        Раздел в разработке.
      </p>
    </div>

  </div>
</div>

<?php include $docRoot . '/statistics/pages/shared/footer.php'; ?>
</body>
</html>
