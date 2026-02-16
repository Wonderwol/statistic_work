<?php
// Абсолютный путь к конфигу
declare(strict_types=1);

$docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
require_once $docRoot . '/statistics/config/config.php';
require_once __DIR__ . '/data.php';
?>

<!----------------------- HTML --------------------------------->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Открытая статистика образовательных организаций</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <?php
        include $docRoot . '/statistics/styles/style_by_type.php';
        include $docRoot . '/statistics/styles/shared/style_footer.php';
        include $docRoot . '/statistics/styles/shared/style_header.php';
        include $docRoot . '/statistics/styles/shared/style_nav_left.php';  // НАВИГАЦИОННАЯ ПАНЕЛЬ
        require_once __DIR__ . '/js_payload.php';
    ?> <!----ПОДКЛЮЧЕНИЕ JS-СКРИПТА----->

    <link rel="icon" type="image/png" sizes="16x16" href="/statistics/src/img/favicon16x16.png"> <!-- Иконка вкладки браузера -->
</head>
<body>
    <?php 
    include $docRoot . '/statistics/pages/shared/header.php';
    include $docRoot . '/statistics/nav/nav_left.php'; 
    ?> <!-- HEADER -->     <!-- Навигационная панель -->    <!---- ОТНОСИТЕЛЬНЫЙ ПУТЬ---->
     
        <!-- Основной контент -->
    <div class="content-area">
    <div class="container">

        <!-- Хлебные крошки -->
        <div class="breadcrumbs">
            <?php
            // Получаем текущий путь
            $currentPath = $_SERVER['REQUEST_URI'];
            $scriptPath = $_SERVER['SCRIPT_NAME'];

            // Определяем базовые пути
            $baseUrl = 'http://' . $_SERVER['HTTP_HOST'];
            $currentDir = dirname($scriptPath);

            // Всегда Главная
            $crumbs = [];
            $crumbs[] = '<a href="' . $baseUrl . '/">Главная</a>';

            // Проверяем, находимся ли мы в statistics
            if (strpos($currentDir, 'statistics') !== false) {
                $crumbs[] = '<a href="' . $baseUrl . '/statistics/">Статистика и аналитика</a>';

                // Проверяем, находимся ли в open или в index файле
                if (strpos($currentPath, 'open') !== false || basename($scriptPath) === 'by_type.php') {
                    $crumbs[] = '<a href="' . $baseUrl . '/statistics/open/">Открытая статистика</a>';
                }
            }

            // Последний элемент - текущая страница
            $crumbs[] = '<span>Сеть образовательных организаций</span>';

            // Объединяем с разделителями
            echo implode('&nbsp;>&nbsp;', $crumbs);
            ?>
        </div>

        <div class="filters">
            <div class="page-head">
                <h1 class="page-head__title" style="color:#2c3e50; font-weight:bold;">
                    Сеть образовательных организаций Новосибирской области
                </h1>

                <div class="page-head__actions">
                    <a href="/statistics/pages/info.php" class="info-link" style="margin-top: 2px;">
                        <img src="/statistics/src/img/info.png" alt="Информация">
                    </a>

                    <button id="showCardsBtn" class="view-btn active" onclick="showCards()">график</button>
                    <button id="showTableBtn" class="view-btn" onclick="showTable()">таблица</button>
                </div>
            </div>

			<!-- Информация о данных -->
                <p style="color: gray; margin: 8px 0 20px 0; font-size: 14px;">
                    Информация по состоянию на: <strong style="color: #6d444b;"><?php echo htmlspecialchars($displayTime); ?></strong>, статистика в % и ед.
                </p>
            <!------------------------------------>

            <form method="GET" action="">
                <div class="filter-row">
                    <!-- Тип организации (радиокнопки) -->
                    <div class="filter-group">
                        <div class="dropdown-search-container" id="org_type-container">
                            <input type="text" 
                                class="dropdown-search-input" 
                                placeholder="Выберите уровень..." 
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
               placeholder="Выберите год/годы..."
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
               placeholder="Год для графика..."
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
                
                <!-- Тип местности (радиокнопки) -->
                <div class="filter-group">
                    <div class="dropdown-search-container" id="locality-container">
                        <input type="text" 
                            class="dropdown-search-input" 
                            placeholder="Выберите тип..." 
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
                    <button type="button" class="btn-secondary" onclick="window.location.href='by_type.php'">Сбросить</button>  <!-- ЗАМЕНИТЬ ПРИ СМЕНЕ ИМЕНИ ФАЙЛА -->
                </div>
            </form>
        </div>
        
        <?php if (!empty($organizations)): ?>


        <!-- ГРАФИКИ -->
        <div class="chart-container">
            <div class="chart-box chart-box--card">
                <div class="chart-header">
                    <div>
                        <h3>
                            Структура по типам
                            <?= $show_single_year_charts
                                ? '(' . $years[0] . ')'
                                : '(' . $years[0] . '–' . $years[count($years) - 1] . ')' ?>
                        </h3>
                        <div class="chart-subnote">Итого по каждому году показано справа от столбика, доли — в подсказке.</div>
                    </div>

                    <div id="structureLegend" class="chart-legend" aria-label="Легенда структуры"></div>
                </div>

                <div class="chart-wrap chart-wrap--big no-hover">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Таблица -->
        
        <?php include $docRoot . '/statistics/pages/partials/table.php'; ?>

        <br>

        <!-- Панель таблицы (кнопка экспорта) -->
        <div id="exportExcelPanel" style="display:none; justify-content:flex-end; margin-bottom:12px;">
            <button type="button" class="btn-primary" onclick="exportToExcel()">
                Экспорт в Excel
            </button>
        </div>

    </div>

<?php else: ?>
    <?php
        $emptyIcon = '📝';
        $emptyTitle = 'Организации не найдены';
        $emptyMessage = 'Измените параметры фильтрации или добавьте данные в систему.';
        include $docRoot . '/statistics/pages/shared/empty_state.php';
    ?>
<?php endif; ?>

    </div> <!-- закрыли .container -->

    </div> <!-- закрыли .content-area -->

    <?php if (!empty($organizations)): ?>
<aside id="statsDock" class="stats-dock" aria-label="Сводка по типам организаций">
    <?php
        /**
         * ВАЖНО:
         * - таблица может быть по нескольким годам (year_id[])
         * - график и карточки должны быть строго по одному году (chart_year_id)
         *
         * Поэтому карточки берём из агрегата $tableByYear по году графика,
         * а НЕ суммируем весь массив $organizations (там могут быть несколько лет).
         */
        $cardsYear = (isset($years[0]) && (string)$years[0] !== '') ? (string)$years[0] : (string)($yearPeriod ?? '');
        $cardsRow  = ($cardsYear !== '' && isset($tableByYear[$cardsYear]) && is_array($tableByYear[$cardsYear]))
            ? $tableByYear[$cardsYear]
            : [];

        $total_all = (int)($cardsRow['Total_organizations'] ?? 0);
        $nursery   = (int)($cardsRow['Nursery_school_primary'] ?? 0);
        $primary   = (int)($cardsRow['Primary_school'] ?? 0);
        $basic     = (int)($cardsRow['Basic_school'] ?? 0);
        $secSum    = (int)($cardsRow['sec_sc_sum'] ?? 0);
        $sanat     = (int)($cardsRow['Sanatorium_schools'] ?? 0);
        $ovz       = (int)($cardsRow['Special_needs_schools'] ?? 0);
        $evening   = (int)($cardsRow['Evening_schools'] ?? 0);
        $branches  = (int)($cardsRow['Branches'] ?? 0);

        function nf($v){ return number_format((int)$v, 0, '.', ' '); }
    ?>

    <div class="stats-dock__head">Типы организаций</div>

    <div class="stats-dock__list">
        <div class="stat-card stat-card--dock stat-card--dock-total">
            <h3>ОО всего</h3>
            <div class="stat-value"><?= nf($total_all) ?></div>
        </div>

        <div class="stat-card stat-card--dock">
            <h3>НОШ-д/сад</h3>
            <div class="stat-value"><?= nf($nursery) ?></div>
        </div>

        <div class="stat-card stat-card--dock">
            <h3>НОШ</h3>
            <div class="stat-value"><?= nf($primary) ?></div>
        </div>

        <div class="stat-card stat-card--dock">
            <h3>ООШ</h3>
            <div class="stat-value"><?= nf($basic) ?></div>
        </div>

        <div class="stat-card stat-card--dock">
            <h3>СОШ (всего)</h3>
            <div class="stat-value"><?= nf($secSum) ?></div>
        </div>

        <div class="stat-card stat-card--dock">
            <h3>Санаторные</h3>
            <div class="stat-value"><?= nf($sanat) ?></div>
        </div>

        <div class="stat-card stat-card--dock">
            <h3>Для детей с ОВЗ</h3>
            <div class="stat-value"><?= nf($ovz) ?></div>
        </div>

        <div class="stat-card stat-card--dock">
            <h3>Вечерние</h3>
            <div class="stat-value"><?= nf($evening) ?></div>
        </div>

        <div class="stat-card stat-card--dock">
            <h3>Филиалы</h3>
            <div class="stat-value"><?= nf($branches) ?></div>
        </div>
    </div>
</aside>
<?php endif; ?>

    <?php
        include $docRoot . '/statistics/pages/shared/footer.php';
        include $docRoot . '/statistics/scripts/edu_orgs/by_type_script.php';
    ?>
</body>
</html>