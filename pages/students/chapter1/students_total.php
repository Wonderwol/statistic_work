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
    <title>Численность обучающихся за учебный год</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <?php
        include $docRoot . '/statistics/styles/students/chapter1/style.php';
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
    ['title' => 'Обучающиеся образовательных организаций', 'href' => '/statistics/pages/students/index.php'],
    ['title' => '1. Численность обучающихся за учебный год'],
];
include $docRoot . '/statistics/pages/partials/breadcrumbs.php';
?>

<div class="filters">
    <div class="page-head">
        <h1 class="page-head__title">Численность обучающихся за учебный год</h1>

        <div class="page-head__actions">
            <a href="/statistics/pages/info.php" class="info-link info-link--icon" title="Информация" aria-label="Информация">
                <img src="/statistics/src/img/info.png" alt="Информация">
            </a>

            <div class="view-controls">
                <button id="showCardsBtn" type="button" class="view-btn" onclick="window.showCards && window.showCards()">График</button>
                <button id="showTableBtn" type="button" class="view-btn" onclick="window.showTable && window.showTable()">Таблица</button>
            </div>
        </div>
    </div>

    <p style="color: gray; margin: 8px 0 20px 0; font-size: 14px;">
        Информация по состоянию на: <strong style="color: #6d444b;"><?= safeEcho($displayTime ?? '') ?></strong>, статистика в ед. (чел.).
    </p>

    <form method="GET" action="">
        <div class="filter-row">

            <!-- Район/территория (радио) -->
            <div class="filter-group">
                <div class="dropdown-search-container" id="org_type-container">
                    <input type="text"
                           class="dropdown-search-input"
                           placeholder="Уровень представления данных"
                           id="org_type-search"
                           readonly
                           style="cursor: pointer;">

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
                                <label for="org_type_<?= safeEcho($type['id']) ?>">
                                    <?= safeEcho($type['name']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Учебный год (чекбоксы) — ТОЛЬКО для таблицы -->
            <div class="filter-group" id="year-filter-table">
                <div class="dropdown-search-container" id="year-container">
                    <input type="text"
                           class="dropdown-search-input"
                           placeholder="Учебный год"
                           id="year-search"
                           readonly
                           style="cursor: pointer;">

                    <div class="selected-count" id="year-selected-count">
                        Выбрано: <span id="year-count">0</span>
                        <span class="clear-selection" id="year-clear">(очистить)</span>
                        <span style="float: right;" class="select-all" id="year-select-all">Выбрать все</span>
                    </div>

                    <div class="dropdown-checkbox-group" id="year-group">
                        <?php foreach ($years_data as $year): ?>
                            <div class="checkbox-item" data-year-id="<?= safeEcho($year['id']) ?>">
                                <input type="checkbox"
                                       id="year_<?= safeEcho($year['id']) ?>"
                                       name="year_id[]"
                                       value="<?= safeEcho($year['id']) ?>"
                                       <?= (is_array($year_ids) && in_array($year['id'], $year_ids, true)) ? 'checked' : '' ?>>
                                <label for="year_<?= safeEcho($year['id']) ?>">
                                    <?= safeEcho($year['name']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Учебный год (radio) — ТОЛЬКО для графика -->
            <div class="filter-group" id="chart-year-filter" style="display:none;">
                <div class="dropdown-search-container" id="chart_year-container">
                    <input type="text"
                           class="dropdown-search-input"
                           placeholder="Учебный год (график)"
                           id="chart_year-search"
                           readonly
                           style="cursor: pointer;">

                    <div class="selected-count" id="chart_year-selected-count">
                        <span class="clear-selection" id="chart_year-clear">(очистить)</span>
                    </div>

                    <div class="dropdown-checkbox-group" id="chart_year-group">
                        <?php foreach ($years_data as $year): ?>
                            <div class="checkbox-item" data-chart-year-id="<?= safeEcho($year['id']) ?>">
                                <input type="radio"
                                       id="chart_year_<?= safeEcho($year['id']) ?>"
                                       name="chart_year_id"
                                       value="<?= safeEcho($year['id']) ?>"
                                       <?= ((string)($chart_year_id ?? '') === (string)$year['id']) ? 'checked' : '' ?>>
                                <label for="chart_year_<?= safeEcho($year['id']) ?>">
                                    <?= safeEcho($year['name']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Тип местности (радио) -->
            <div class="filter-group">
                <div class="dropdown-search-container" id="locality-container">
                    <input type="text"
                           class="dropdown-search-input"
                           placeholder="Тип местности"
                           id="locality-search"
                           readonly
                           style="cursor: pointer;">

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
                                <label for="locality_<?= safeEcho($type['id']) ?>">
                                    <?= safeEcho($type['name']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>

        <div class="buttons">
            <button type="submit" class="btn-primary">Применить фильтры</button>
            <button type="button" class="btn-secondary" onclick="window.location.href='/statistics/pages/students/chapter1/students_total.php'">Сбросить</button>
        </div>
    </form>
</div>

<?php
$selectedAreaName = '';
if (!empty($org_types_data) && !empty($highlightAreaCode)) {
    foreach ($org_types_data as $t) {
        if ((string)($t['id'] ?? '') === (string)$highlightAreaCode) {
            $selectedAreaName = (string)($t['name'] ?? '');
            break;
        }
    }
}
if ($selectedAreaName === '') $selectedAreaName = 'Территория не выбрана';
?>

<?php if (!empty($table)): ?>

<!-- Карточки -->
<div class="statistics" style="margin-top: 12px;">
    <div class="stat-card">
        <h3>Обучающихся</h3>
        <div class="stat-value"><?= safeEcho(number_format((int)$cardTotalStudents, 0, '.', ' ')) ?></div>
        <div class="stat-sub">Выбранный год: <?= safeEcho($rankYearLabel ?? '') ?></div>
    </div>
</div>

<!-- Графики -->
<div class="chart-container chart-container--students">

    <div class="chart-box chart-box--card">
        <div class="chart-header">
            <div>
                <h3>Динамика численности обучающихся — <?= safeEcho($selectedAreaName) ?></h3>
                <div class="chart-subnote">Количество обучающихся по выбранной территории. Единица измерения — чел.</div>
            </div>
        </div>
        <div class="chart-wrap chart-wrap--students-line">
            <canvas id="studentsLineChart"></canvas>
        </div>
    </div>

    <div class="chart-box chart-box--card">
        <div class="chart-header">
            <div>
                <h3>Топ-20 территорий по числу обучающихся (<?= safeEcho($rankYearLabel ?? '') ?>)</h3>
                <div class="chart-subnote">Количество обучающихся, единица измерения — чел.</div>
            </div>
        </div>
        <div class="chart-wrap chart-wrap--students-rank">
            <canvas id="studentsAreaRankChart"></canvas>
        </div>
    </div>

</div>

<!-- Таблица -->
<div id="tableView" style="display:none;">
    <div class="results">
        <table>
            <thead>
                <tr>
                    <th style="min-width: 260px;">Территория</th>
                    <?php foreach ($yearsTable as $y): ?>
                        <th><?= safeEcho($y) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($areaKeys as $ac): ?>
                    <?php
                        $row = $table[$ac];
                        $isTotal = ($totalAreaCode !== null && (string)$ac === (string)$totalAreaCode);
                    ?>
                    <tr class="<?= $isTotal ? 'table-total' : '' ?>">
                        <td><?= safeEcho($row['name']) ?></td>
                        <?php foreach ($yearsTable as $y): ?>
                            <?php $v = (float)($row['years'][$y] ?? 0); ?>
                            <td style="text-align:right;"><?= safeEcho(number_format((int)round($v), 0, '.', ' ')) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div id="exportExcelPanel" style="display:none; justify-content:flex-end; margin: 12px 0 0 0;">
        <button type="button" class="btn-primary" onclick="exportToExcel()">Экспорт в Excel</button>
    </div>
</div>

<?php else: ?>
    <?php
        $emptyIcon = '📝';
        $emptyTitle = 'Данные об обучающихся не найдены';

        if (isset($studentsDataFatal) && $studentsDataFatal && !empty($studentsDataError)) {
            $emptyMessage = 'Ошибка получения данных: ' . (string)$studentsDataError;
        } else {
            $emptyMessage = 'Измените параметры фильтрации или добавьте данные в систему.';
        }

        include $docRoot . '/statistics/pages/shared/empty_state.php';
    ?>
<?php endif; ?>

</div>
</div>

<?php
include $docRoot . '/statistics/pages/shared/footer.php';
if (!empty($table)) {
    include $docRoot . '/statistics/scripts/students/chapter1/students_script.php';
}
?>

</body>
</html>