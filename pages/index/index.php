<?php
// Абсолютный путь к конфигу
declare(strict_types=1);

$docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
require_once $docRoot . '/v3/config/config.php';
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
        include $docRoot . '/v3/styles/style_index.php';
        include $docRoot . '/v3/styles/shared/style_footer.php';
        include $docRoot . '/v3/styles/shared/style_header.php';
        include $docRoot . '/v3/styles/shared/style_nav_left.php';  // НАВИГАЦИОННАЯ ПАНЕЛЬ
        require_once __DIR__ . '/js_payload.php';
    ?> <!----ПОДКЛЮЧЕНИЕ JS-СКРИПТА----->
          

    <link rel="icon" type="image/png" sizes="16x16" href="/v3/src/img/favicon16x16.png"> <!-- Иконка вкладки браузера -->
</head>
<body>
    <?php 
    include $docRoot . '/v3/pages/shared/header.php';
    include $docRoot . '/v3/nav/nav_left.php'; 
    ?> <!-- HEADER -->     <!-- Навигационная панель -->    <!---- ОТНОСИТЕЛЬНЫЙ ПУТЬ---->
     
        <!-- Основной контент -->
    <div class="content-area">

     <!-- Хлебные крошки -->
    <div style="margin: 0 0 10px 0; padding: 5px 0; font-size: 13px; color: rgba(0, 0, 0, 0.6);">
        <?php
        // Получаем текущий путь
        $currentPath = $_SERVER['REQUEST_URI'];
        $scriptPath = $_SERVER['SCRIPT_NAME'];
        
        // Определяем базовые пути
        $baseUrl = 'http://' . $_SERVER['HTTP_HOST'];
        $currentDir = dirname($scriptPath);
        
        // Всегда Главная
        $crumbs = [];
        $crumbs[] = '<a href="' . $baseUrl . '/" style="color: #6d444b; text-decoration: none; opacity: 0.8;">Главная</a>';
        
        // Проверяем, находимся ли мы в statistics
        if (strpos($currentDir, 'statistics') !== false) {
            $crumbs[] = '<a href="' . $baseUrl . '/statistics/" style="color: #6d444b; text-decoration: none; opacity: 0.8;">Статистика и аналитика</a>';
            
            // Проверяем, находимся ли в open или в index файле
            if (strpos($currentPath, 'open') !== false || basename($scriptPath) === 'index.php') {
                $crumbs[] = '<a href="' . $baseUrl . '/statistics/open/" style="color: #6d444b; text-decoration: none; opacity: 0.8;">Открытая статистика</a>';
            }
        }
        
        // Последний элемент - текущая страница
        $crumbs[] = '<span style="color: rgba(0, 0, 0, 0.7);">Сеть образовательных организаций</span>';
        
        // Объединяем с разделителями
        echo implode('&nbsp;>&nbsp;', $crumbs);
        ?>
    </div>

    <div class="container">
        <div class="filters">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                    <h1 style="color: #2c3e50; font-weight: bold; margin: 0; flex: 1;">Сеть образовательных организаций Новосибирской области
                </h1>
                    <a href="/v3/pages/info.php" style="margin-top: 6px; margin-right: 18px;">
                        <img src="/v3/src/img/info.png" alt="Информация">
                    </a>
                    <button id="showCardsBtn" class="view-btn active" onclick="showCards()">график</button>
                    <button id="showTableBtn" class="view-btn" onclick="showTable()">таблица</button>
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
                <div class="no-results">Ничего не найдено</div>
                        </div>
                    </div>
                </div>
                
                <!-- Учебный год (чекбоксы) -->
                <div class="filter-group">
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
                                        <?= (is_array($year_ids) && in_array($year['id'], $year_ids)) ? 'checked' : '' ?>>
                                    <label for="year_<?= safeEcho($year['id']) ?>">
                                        <?= safeEcho($year['name']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                            <div class="no-results">Ничего не найдено</div>
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
                            <div class="no-results">Ничего не найдено</div>
                        </div>
                    </div>
                </div>
            </div>
               
                <div class="buttons">
                    <button type="submit" class="btn-primary">Применить фильтры</button>
                    <button type="button" class="btn-secondary" onclick="window.location.href='index.php'">Сбросить</button>  <!-- ЗАМЕНИТЬ ПРИ СМЕНЕ ИМЕНИ ФАЙЛА -->
                </div>
            </form>
        </div>
        
        <?php if (!empty($organizations)): ?>


	    <!-- НАЧАЛО СТАТИСТИКИ -->
<div class="stat-card" style="width: 100%;">
    <h3>Общеобразовательных организаций - всего</h3>
    <div class="stat-value" style="display: inline-block;">
        <?php 
        $total_all = 0;
        foreach ($organizations as $org) {
            // Должно быть Total_organizations из БД (тип 14)
            $total_all += isset($org['Total_organizations']) ? $org['Total_organizations'] : 0;
        }
        echo $total_all;
        ?>
    </div>
</div>

    <!-- Вторая строка (две карточки) -->
    <div class="statistics">
        <div class="stat-card">
            <h3>Начальные школы - детские сады</h3>
            <div class="stat-value">
                <?php echo array_sum(array_column($organizations, 'Nursery_school_primary'));?>
            </div>
        </div>
        <div class="stat-card">
            <h3>Начальные общеобразовательные школы</h3>
            <div class="stat-value">
                <?php echo array_sum(array_column($organizations, 'Primary_school')); ?>
            </div>
        </div>
    </div>

    <!-- Третья строка (две карточки) -->
    <div class="statistics">
        <div class="stat-card">
            <h3>Основные общеобразовательные школы</h3>
            <div class="stat-value">
                <?php echo array_sum(array_column($organizations, 'Basic_school'));?>
            </div>
        </div>
        <div class="stat-card">
            <h3>Средние общеобразовательные школы</h3>
            <div class="stat-value">
                <?php echo array_sum(array_column($organizations, 'sec_sc_sum'));?>
            </div>
        </div>
    </div>

    <!-- Четвертая строка (две карточки) -->
    <div class="statistics">
        <div class="stat-card">
            <h3>Санаторные общеобразовательные организации</h3>
            <div class="stat-value">
                <?php echo array_sum(array_column($organizations, 'Sanatorium_schools'));?>
            </div>
        </div>
        <div class="stat-card">
            <h3 style="font-size: 14px;">Школы для детей с ограниченными возможностями здоровья</h3>
            <div class="stat-value">
                <?php echo array_sum(array_column($organizations, 'Special_needs_schools'));?>
            </div>
        </div>
    </div>

    <!-- Пятая строка (две карточки) -->
    <div class="statistics">
        <div class="stat-card">
            <h3>Вечерние общеобразовательные организации</h3>
            <div class="stat-value">
                <?php echo array_sum(array_column($organizations, 'Evening_schools'));?>
            </div>
        </div>
        <div class="stat-card">
            <h3>Филиалы</h3>
            <div class="stat-value">
                <?php echo array_sum(array_column($organizations, 'Branches'));?>
            </div>
        </div>
    </div>

<!-- ГРАФИКИ -->
<div class="chart-container">
    <div class="chart-box">
        <div class="chart-header">
            <h3>Структура по типам <?= $show_single_year_charts ? "($years[0])" : '(суммарно)' ?></h3>
        </div>
        <div class="chart-wrap chart-wrap--big no-hover">
            <canvas id="pieChart"></canvas>
        </div>
    </div>
</div>

<!-- Таблица -->
<div class="results" id="tableView" style="margin-top: 20px; display: none;">
    <table>
        <thead>
            <tr>
                <th style="font-weight: bold;">Образовательные организации</th>
                <?php foreach ($yearsTable as $y): ?>
                    <th style="text-align:center; font-weight:bold;"><?= safeEcho($y) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td style="font-weight:bold; padding-left: 9%;">НОШ д/сад</td>
                <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Nursery_school_primary'] ?></td><?php endforeach; ?>
            </tr>

            <tr>
                <td style="font-weight:bold; padding-left: 9%;">НОШ</td>
                <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Primary_school'] ?></td><?php endforeach; ?>
            </tr>

            <tr>
                <td style="font-weight:bold; padding-left: 9%;">ООШ</td>
                <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Basic_school'] ?></td><?php endforeach; ?>
            </tr>

            <tr>
                <td style="font-weight:bold; padding-left: 9%;">всего СОШ</td>
                <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['sec_sc_sum'] ?></td><?php endforeach; ?>
            </tr>

            <tr>
                <td style="font-weight:bold; padding-left: 9%;">СОШ</td>
                <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Secondary_school'] ?></td><?php endforeach; ?>
            </tr>

            <tr>
                <td style="font-weight:bold; padding-left: 9%;">СОШ с УИОП</td>
                <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Secondary_school_special'] ?></td><?php endforeach; ?>
            </tr>

            <tr>
                <td style="font-weight:bold; padding-left: 9%;">гимназии</td>
                <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Gymnasium'] ?></td><?php endforeach; ?>
            </tr>

            <tr>
                <td style="font-weight:bold; padding-left: 9%;">лицеи</td>
                <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Lyceum'] ?></td><?php endforeach; ?>
            </tr>

            <tr>
                <td style="font-weight:bold; padding-left: 9%;">кадетские корпуса</td>
                <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Cadet_corps'] ?></td><?php endforeach; ?>
            </tr>

            <tr>
                <td style="font-weight:bold; padding-left: 9%;">филиалы</td>
                <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Branches'] ?></td><?php endforeach; ?>
            </tr>

            <tr style="background-color:#6d444b; color:#fff; font-weight:bold;">
                <td style="padding-left:9%;">итого ОО</td>
                <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Total_organizations'] ?></td><?php endforeach; ?>
            </tr>

            <tr>
                <td style="font-weight:bold; padding-left: 9%;">санаторные ОО</td>
                <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Sanatorium_schools'] ?></td><?php endforeach; ?>
            </tr>

            <tr>
                <td style="font-weight:bold; padding-left: 9%;">ОО для детей с ОВЗ</td>
                <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Special_needs_schools'] ?></td><?php endforeach; ?>
            </tr>

            <tr style="background-color:#6d444b; color:#fff; font-weight:bold;">
                <td style="padding-left:9%;">итого дневные ОО</td>
                <?php foreach ($yearsTable as $y): ?>
                    <td style="text-align:center;"><?= (int)$tableByYear[$y]['Total_organizations'] - (int)$tableByYear[$y]['Evening_schools'] ?></td>
                <?php endforeach; ?>
            </tr>

            <tr>
                <td style="font-weight:bold; padding-left: 9%;">вечерние ОО</td>
                <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Evening_schools'] ?></td><?php endforeach; ?>
            </tr>
        </tbody>
    </table>

    </div>
        <?php else: ?>
            <?php
                $emptyIcon = '📝';
                $emptyTitle = 'Организации не найдены';
                $emptyMessage = 'Измените параметры фильтрации или добавьте данные в систему.';
                include $docRoot . '/v3/pages/shared/empty_state.php';
            ?>
        <?php endif; ?>
    </div> <!-- закрыли .container -->

    </div> <!-- закрыли .content-area -->

    <?php
        include $docRoot . '/v3/pages/shared/footer.php';
        include $docRoot . '/v3/scripts/index/index_script.php';
    ?>
</body>
</html>