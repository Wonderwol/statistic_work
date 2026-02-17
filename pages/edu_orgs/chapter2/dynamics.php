<?php
declare(strict_types=1);

$docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
require_once $docRoot . '/statistics/config/config.php';
require_once __DIR__ . '/data.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Общая динамика сети ОО</title>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  <?php
    include $docRoot . '/statistics/styles/edu_orgs/chapter3/style.php';
    include $docRoot . '/statistics/styles/shared/style_footer.php';
    include $docRoot . '/statistics/styles/shared/style_header.php';
    include $docRoot . '/statistics/styles/shared/style_nav_left.php';
    require_once __DIR__ . '/js_payload.php';
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
      ['title' => '3. Общая динамика сети ОО'],
    ];
    include $docRoot . '/statistics/pages/partials/breadcrumbs.php';
    ?>

    <div class="filters">
      <div class="page-head">
        <h1 class="page-head__title" style="color:#2c3e50; font-weight:800;">
          Общая динамика сети ОО
        </h1>

        <div class="page-head__actions">
          <a href="/statistics/pages/info.php" class="info-link info-link--circle" title="Информация" aria-label="Информация">
            <img src="/statistics/src/img/info.png" alt="">
          </a>
        </div>
      </div>

      <p style="color: gray; margin: 8px 0 16px 0; font-size: 14px;">
        Информация по состоянию на:
        <strong style="color: #6d444b;"><?= safeEcho($displayTime ?? '') ?></strong>,
        статистика в % и ед.
      </p>

      <form method="GET" action="">
        <div class="filter-row">

          <!-- Уровень представления данных -->
          <div class="filter-group">
            <div class="dropdown-search-container" id="org_type-container">
              <input type="text" class="dropdown-search-input" id="org_type-search" readonly style="cursor:pointer;">
              <div class="selected-count" id="org_type-selected-count">
                <span class="clear-selection" id="org_type-clear">(очистить)</span>
              </div>
              <div class="dropdown-checkbox-group" id="org_type-group">
                <?php foreach ($org_types_data as $type): ?>
                  <div class="checkbox-item" data-org-type-id="<?= safeEcho($type['id']) ?>">
                    <input type="radio"
                           id="org_type_<?= safeEcho($type['id']) ?>"
                           name="org_type"
                           value="<?= safeEcho($type['id']) ?>"
                           <?= (!empty($org_types) && in_array($type['id'], (array)$org_types)) ? 'checked' : '' ?>>
                    <label for="org_type_<?= safeEcho($type['id']) ?>"><?= safeEcho($type['name']) ?></label>
                  </div>
                <?php endforeach; ?>
                <div class="no-results">Ничего не найдено</div>
              </div>
            </div>
          </div>

          <!-- Учебные годы (несколько) -->
          <div class="filter-group">
            <div class="dropdown-search-container" id="year-container">
              <input type="text" class="dropdown-search-input" id="year-search" readonly style="cursor:pointer;">
              <div class="selected-count" id="year-selected-count">
                Выбрано: <span id="year-count">0</span>
                <span class="clear-selection" id="year-clear">(очистить)</span>
                <span style="float:right;" class="select-all" id="year-select-all">Выбрать все</span>
              </div>
              <div class="dropdown-checkbox-group" id="year-group">
                <?php foreach ($years_data as $year): ?>
                  <div class="checkbox-item" data-year-id="<?= safeEcho($year['id']) ?>">
                    <input type="checkbox"
                           id="year_<?= safeEcho($year['id']) ?>"
                           name="year_id[]"
                           value="<?= safeEcho($year['id']) ?>"
                           <?= (is_array($year_ids) && in_array($year['id'], $year_ids, true)) ? 'checked' : '' ?>>
                    <label for="year_<?= safeEcho($year['id']) ?>"><?= safeEcho($year['name']) ?></label>
                  </div>
                <?php endforeach; ?>
                <div class="no-results">Ничего не найдено</div>
              </div>
            </div>
          </div>

          <!-- Тип местности -->
          <div class="filter-group">
            <div class="dropdown-search-container" id="locality-container">
              <input type="text" class="dropdown-search-input" id="locality-search" readonly style="cursor:pointer;">
              <div class="selected-count" id="locality-selected-count">
                <span class="clear-selection" id="locality-clear">(очистить)</span>
              </div>
              <div class="dropdown-checkbox-group" id="locality-group">
                <?php foreach ($locality_types_data as $type): ?>
                  <div class="checkbox-item" data-locality-id="<?= safeEcho($type['id']) ?>">
                    <input type="radio"
                           id="locality_<?= safeEcho($type['id']) ?>"
                           name="locality_type"
                           value="<?= safeEcho($type['id']) ?>"
                           <?= (!empty($locality_types) && in_array($type['id'], (array)$locality_types)) ? 'checked' : '' ?>>
                    <label for="locality_<?= safeEcho($type['id']) ?>"><?= safeEcho($type['name']) ?></label>
                  </div>
                <?php endforeach; ?>
                <div class="no-results">Ничего не найдено</div>
              </div>
            </div>
          </div>

        </div>

        <div class="buttons">
          <button type="submit" class="btn-primary">Применить фильтры</button>
          <button type="button" class="btn-secondary" onclick="window.location.href='/statistics/pages/edu_orgs/chapter3/dynamics.php'">Сбросить</button>
        </div>
      </form>
    </div>

    <?php if (!empty($hasOrganizations)): ?>

      <div class="chart-container chart-container--top">
        <div class="chart-box chart-box--card">
          <div class="chart-header">
            <div>
              <h3>Структура сети ОО по годам (100%), %</h3>
            </div>
          </div>
          <div class="chart-wrap chart-wrap--network">
            <canvas id="networkChart"></canvas>
          </div>
        </div>

        <div class="chart-box chart-box--card">
          <div class="chart-header">
            <div>
              <h3>Кроме того, филиалы (ед.)</h3>
            </div>
          </div>
          <div class="chart-wrap chart-wrap--branches">
            <canvas id="branchesChart"></canvas>
          </div>
        </div>
      </div>

      <div class="chart-container">
        <div class="chart-box chart-box--card">
          <div class="chart-header">
            <div>
              <h3>Из СОШ: структура по видам (100%), %</h3>
            </div>
          </div>
          <div class="chart-wrap chart-wrap--secondary">
            <canvas id="secondaryChart"></canvas>
          </div>
        </div>
      </div>

    <?php else: ?>
      <?php
        $emptyIcon = '📝';
        $emptyTitle = 'Данные не найдены';
        $emptyMessage = 'Измените параметры фильтрации или добавьте данные в систему.';
        include $docRoot . '/statistics/pages/shared/empty_state.php';
      ?>
    <?php endif; ?>

  </div>
</div>

<?php
include $docRoot . '/statistics/pages/shared/footer.php';
include $docRoot . '/statistics/scripts/edu_orgs/chapter2/dynamics_script.php';
?>
</body>
</html>
