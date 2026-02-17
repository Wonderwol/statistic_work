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
    <title>Сеть образовательных организаций</title>

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
      ['title' => 'Сеть образовательных организаций'],
    ];
    include $docRoot . '/statistics/pages/partials/breadcrumbs.php';
    ?>

    <div class="filters">
      <div class="page-head">
        <h1 class="page-head__title" style="color:#2c3e50; font-weight:bold;">
          Сеть образовательных организаций
        </h1>
      </div>

      <p style="color: gray; margin: 8px 0 18px 0; font-size: 14px;">
        Выберите подраздел:
      </p>

      <div style="display:grid; gap:10px; grid-template-columns: 1fr;">
        <a class="view-btn active" style="text-decoration:none; text-align:center;"
           href="/statistics/pages/edu_orgs/chapter1/by_type.php">
          Состояние сети ОО за учебный год
        </a>

        <a class="view-btn" style="text-decoration:none; text-align:center;"
           href="/statistics/pages/edu_orgs/chapter2/dynamics.php">
          Изменения структуры сети ОО
        </a>

        <a class="view-btn" style="text-decoration:none; text-align:center;"
           href="/statistics/pages/edu_orgs/chapter3/structure_changes.php">
          Общая динамика сети ОО
        </a>
      </div>
    </div>

  </div>
</div>

<?php include $docRoot . '/statistics/pages/shared/footer.php'; ?>
</body>
</html>
