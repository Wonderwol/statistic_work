<?php

require_once '../config/config.php';

// Получаем параметры фильтрации как массивы
$org_types = $_GET['org_type'] ?? [];
$year_ids = $_GET['year_id'] ?? [];
$locality_types = $_GET['locality_type'] ?? [];

// Преобразуем в массивы, если пришло одиночное значение
if (!is_array($org_types) && !empty($org_types)) $org_types = [$org_types];
if (!is_array($year_ids) && !empty($year_ids)) $year_ids = [$year_ids];
if (!is_array($locality_types) && !empty($locality_types)) $locality_types = [$locality_types];

// Определяем сколько лет выбрано
$selected_years_count = count($year_ids);
$show_multiple_years_charts = $selected_years_count > 1;
$show_single_year_charts = $selected_years_count == 1;

// Получаем списки для фильтров
$years_data = $pdo->query("select distinct Year_period as id, Year_period as name from Area_organizations")->fetchAll();
$org_types_data = $pdo->query("select Area_code as id, Area_name as name from dat_Area")->fetchAll();
$locality_types_data = $pdo->query("select Area_type_code as id, Area_type_name as name from dat_Area_types")->fetchAll();

// ОСНОВНОЙ ЗАПРОС с правильными кодами
$sql = "SELECT 
    da.Area_name,
    ao.Year_period,
    -- Основные типы организаций
    SUM(CASE WHEN do.Organization_type_code = 1 THEN ao.Area_organizations_count ELSE 0 END) AS Nursery_school_primary,
    SUM(CASE WHEN do.Organization_type_code = 2 THEN ao.Area_organizations_count ELSE 0 END) AS Primary_school,
    SUM(CASE WHEN do.Organization_type_code = 3 THEN ao.Area_organizations_count ELSE 0 END) AS Basic_school,
    
    -- Средние школы (суммарно - тип 4)
    SUM(CASE WHEN do.Organization_type_code = 4 THEN ao.Area_organizations_count ELSE 0 END) AS Secondary_school_sum,
    
    -- Средние школы (отдельные типы 5-9)
    SUM(CASE WHEN do.Organization_type_code = 5 THEN ao.Area_organizations_count ELSE 0 END) AS Secondary_school,
    SUM(CASE WHEN do.Organization_type_code = 6 THEN ao.Area_organizations_count ELSE 0 END) AS Secondary_school_special,
    SUM(CASE WHEN do.Organization_type_code = 7 THEN ao.Area_organizations_count ELSE 0 END) AS Gymnasium,
    SUM(CASE WHEN do.Organization_type_code = 8 THEN ao.Area_organizations_count ELSE 0 END) AS Lyceum,
    SUM(CASE WHEN do.Organization_type_code = 9 THEN ao.Area_organizations_count ELSE 0 END) AS Cadet_corps,
    
    -- Другие типы
    SUM(CASE WHEN do.Organization_type_code = 10 THEN ao.Area_organizations_count ELSE 0 END) AS Branches,
    SUM(CASE WHEN do.Organization_type_code = 11 THEN ao.Area_organizations_count ELSE 0 END) AS Sanatorium_schools,
    SUM(CASE WHEN do.Organization_type_code = 12 THEN ao.Area_organizations_count ELSE 0 END) AS Special_needs_schools,
    SUM(CASE WHEN do.Organization_type_code = 13 THEN ao.Area_organizations_count ELSE 0 END) AS Evening_schools,
    
    -- ИТОГО (тип 14)
    SUM(CASE WHEN do.Organization_type_code = 14 THEN ao.Area_organizations_count ELSE 0 END) AS Total_organizations
    FROM Area_organizations ao
    JOIN dat_Area da ON ao.Area_code = da.Area_code
    JOIN dat_Organizations do ON ao.Organization_type_code = do.Organization_type_code
    WHERE ao.deleted = 0";





$params = [];

if (empty($locality_types)) {
    //тип местности не выбран - показываем Всего (код 3)
    $sql .= " AND ao.Area_type_code = 3";
} else {
    // Если выбран - фильтруем по выбранному
    $placeholders = str_repeat('?,', count($locality_types) - 1) . '?';
    $sql .= " AND ao.Area_type_code IN ($placeholders)";
    $params = array_merge($params, $locality_types);
}

if (!empty($org_types)) {
    $placeholders = str_repeat('?,', count($org_types) - 1) . '?';
    $sql .= " AND ao.Area_code IN ($placeholders)";
    $params = array_merge($params, $org_types);
}

if (!empty($year_ids)) {
    $placeholders = str_repeat('?,', count($year_ids) - 1) . '?';
    $sql .= " AND ao.Year_period IN ($placeholders)";
    $params = array_merge($params, $year_ids);
}

$sql .= " GROUP BY da.Area_name, ao.Year_period
          ORDER BY da.Area_name, ao.Year_period";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$organizations = $stmt->fetchAll();



// ================ ИСПРАВЛЕННАЯ ЛОГИКА ДЛЯ ГРАФИКОВ ================

// 1. Данные для графика общей динамики
$years = [];
$totalOrganizations = [];
$totalByYear = [];

// ================ ПРАВИЛЬНАЯ ЛОГИКА ДЛЯ ГРАФИКОВ ================

// 1. Предварительные вычисления (ДО всех расчетов!)
foreach ($organizations as &$org) {
    $org['sec_sc_sum'] = $org['Secondary_school_sum'] ?? 0;
}
unset($org);

// 2. Инициализация структур для хранения данных
$years = [];
$dataByYear = [];

// 3. ОДИН проход для всех данных
foreach ($organizations as $org) {
    $year = $org['Year_period'];
    
    if (!isset($dataByYear[$year])) {
        $dataByYear[$year] = [
            'total' => 0,
            'school_types' => [0, 0, 0, 0, 0],  // 5 типов средних школ
            'pie_data' => [0, 0, 0, 0, 0, 0, 0], // 7 категорий для круговой
            'nursery' => 0,
            'basic' => 0,
            'special' => 0
        ];
        $years[] = $year;
    }
    
    // 3.1 ГРАФИК 1: Общая динамика (Total_organizations)
    $dataByYear[$year]['total'] += $org['Total_organizations'] ?? 0;
    
    // 3.2 ГРАФИК 2: Типы средних школ
    $dataByYear[$year]['school_types'][0] += $org['Secondary_school'] ?? 0;
    $dataByYear[$year]['school_types'][1] += $org['Secondary_school_special'] ?? 0;
    $dataByYear[$year]['school_types'][2] += $org['Gymnasium'] ?? 0;
    $dataByYear[$year]['school_types'][3] += $org['Lyceum'] ?? 0;
    $dataByYear[$year]['school_types'][4] += $org['Cadet_corps'] ?? 0;
    
    // 3.3 ГРАФИК 3: Сравнение по годам
    $dataByYear[$year]['nursery'] += $org['Nursery_school_primary'] ?? 0;
    $dataByYear[$year]['basic'] += $org['Basic_school'] ?? 0;
    $dataByYear[$year]['special'] += $org['Special_needs_schools'] ?? 0;
    
    // 3.4 ГРАФИК 4: Круговая диаграмма (ВСЕ типы организаций)
    $dataByYear[$year]['pie_data'][0] += $org['Nursery_school_primary'] ?? 0;
    $dataByYear[$year]['pie_data'][1] += $org['Basic_school'] ?? 0;
    $dataByYear[$year]['pie_data'][2] += $org['sec_sc_sum'] ?? 0;  // ВСЕ средние школы
    $dataByYear[$year]['pie_data'][3] += $org['Sanatorium_schools'] ?? 0;
    $dataByYear[$year]['pie_data'][4] += $org['Special_needs_schools'] ?? 0;
    $dataByYear[$year]['pie_data'][5] += $org['Evening_schools'] ?? 0;
    $dataByYear[$year]['pie_data'][6] += $org['Branches'] ?? 0;
}

// 4. Сортируем годы
sort($years);

// 5. Определяем, какие графики показывать (по ВЫБРАННЫМ годам)
$showYearsCount = count($years);
$showMultipleYears = $showYearsCount > 1;
$showSingleYear = $showYearsCount == 1;


// ИСПРАВЛЕНИЕ 
$actual_years_count = count($years); // Уникальные годы в полученных данных

// Если пользователь выбрал годы - учитываем это
if ($selected_years_count > 0) {
    $show_multiple_years_charts = $selected_years_count > 1;
    $show_single_year_charts = $selected_years_count == 1;
} else {
    // Если годы не выбраны - смотрим что пришло из БД
    $show_multiple_years_charts = $actual_years_count > 1;
    $show_single_year_charts = $actual_years_count == 1;
}

echo "<!-- === ИНФОРМАЦИЯ О ГРАФИКАХ === -->";
echo "<!-- Выбрано лет пользователем: $selected_years_count -->";
echo "<!-- Фактически лет в данных: $actual_years_count -->";
echo "<!-- Показывать несколько лет: " . ($show_multiple_years_charts ? 'да' : 'нет') . " -->";
echo "<!-- Показывать один год: " . ($show_single_year_charts ? 'да' : 'нет') . " -->";
echo "<!-- === КОНЕЦ ИНФОРМАЦИИ === -->";



// ============ КОНЕЦ ИСПРАВЛЕНИЯ ============

// 6. Формируем массивы для графиков
$totalOrganizations = []; // График 1
$nurseryData = [];        // График 3
$basicData = [];          // График 3
$specialData = [];        // График 3
$schoolTypesData = [0, 0, 0, 0, 0]; // График 2 (сумма за ВСЕ выбранные годы)
$pieData = [0, 0, 0, 0, 0, 0, 0];   // График 4 (сумма за ВСЕ выбранные годы)

// 6.1 Графики 1 и 3: данные по годам
foreach ($years as $year) {
    $totalOrganizations[] = $dataByYear[$year]['total'] ?? 0;
    $nurseryData[] = $dataByYear[$year]['nursery'] ?? 0;
    $basicData[] = $dataByYear[$year]['basic'] ?? 0;
    $specialData[] = $dataByYear[$year]['special'] ?? 0;
}

// 6.2 Графики 2 и 4: СУММА за ВСЕ выбранные годы (как таблица)
foreach ($dataByYear as $yearData) {
    $schoolTypesData[0] += $yearData['school_types'][0] ?? 0;
    $schoolTypesData[1] += $yearData['school_types'][1] ?? 0;
    $schoolTypesData[2] += $yearData['school_types'][2] ?? 0;
    $schoolTypesData[3] += $yearData['school_types'][3] ?? 0;
    $schoolTypesData[4] += $yearData['school_types'][4] ?? 0;
    
    $pieData[0] += $yearData['pie_data'][0] ?? 0;
    $pieData[1] += $yearData['pie_data'][1] ?? 0;
    $pieData[2] += $yearData['pie_data'][2] ?? 0;
    $pieData[3] += $yearData['pie_data'][3] ?? 0;
    $pieData[4] += $yearData['pie_data'][4] ?? 0;
    $pieData[5] += $yearData['pie_data'][5] ?? 0;
    $pieData[6] += $yearData['pie_data'][6] ?? 0;
}

// 7. Метки (оставляем как есть)
$schoolTypesLabels = ['СОШ', 'СОШ с УИОП', 'Гимназии', 'Лицеи', 'Кадетские корпуса'];
$pieLabels = ['НОШ д/сад', 'Основные школы', 'Средние школы', 'Санаторные', 'ОВЗ школы', 'Вечерние', 'Филиалы'];




// 8. Передаем в JavaScript
echo "<script>";
echo "window.nurseryData = " . json_encode($nurseryData) . ";";
echo "window.basicData = " . json_encode($basicData) . ";";
echo "window.specialData = " . json_encode($specialData) . ";";
echo "window.showMultipleYears = " . ($showMultipleYears ? 'true' : 'false') . ";";
echo "</script>";

// Получаем время обновления
try {
    $query = "SELECT MAX(Updated_date) as last_update FROM Area_organizations WHERE deleted = 0";
    $stmt = $pdo->query($query);
    $lastUpdate = $stmt->fetchColumn();
    $displayTime = $lastUpdate ? date('H:i d.m.Y', strtotime($lastUpdate)) : date('H:i d.m.Y');
} catch (Exception $e) {
    error_log("Ошибка получения времени обновления: " . $e->getMessage());
    $displayTime = date('H:i d.m.Y');
}

// Дополнительные расчеты для организаций
foreach ($organizations as &$org) {
    $org['sec_sc_sum'] = $org['Secondary_school_sum'] ?? 0;
    
    $org['total_oo'] = ($org['Nursery_school_primary'] ?? 0) +
                       ($org['Primary_school'] ?? 0) +
                       ($org['Basic_school'] ?? 0) +
                       ($org['Secondary_school_sum'] ?? 0) +
                       ($org['Branches'] ?? 0);
    
    $org['total_day_oo'] = $org['total_oo'] +
                           ($org['Sanatorium_schools'] ?? 0) +
                           ($org['Special_needs_schools'] ?? 0);
}
unset($org);

// Корректировка расхождений
foreach ($organizations as &$org) {
    $calculated_total = $org['total_day_oo'] + ($org['Evening_schools'] ?? 0);
    $db_total = $org['Total_organizations'] ?? 0;
    
    if ($calculated_total != $db_total) {
        $org['total_day_oo'] = $db_total - ($org['Evening_schools'] ?? 0);
    }
}
unset($org);

?>




<!-- HTML -->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Открытая статистика образовательных организаций</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>   
    <?php include '../styles/style_index.php'; ?>

    <link rel="icon" type="image/png" sizes="16x16" href="\v3\src\img\favicon16x16.png"> <!-- Иконка вкладки браузера -->
</head>
<body>
    <div id="preloader" style="
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: white;
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    ">
        <div style="text-align: center;">
            <div style="
                width: 50px;
                height: 50px;
                border: 3px solid #f3f3f3;
                border-top: 3px solid #6d444b;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 20px;
            "></div>
            <p>Загрузка данных...</p>
        </div>
    </div>
    <?php include '../header/header.php'; ?>  
    <?php include '../nav/nav_left.php'; ?> 
     
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
    <!------------------------------------------------------------->

    <div class="container">
        <div class="filters">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                    <h1 style="color: #2c3e50; font-weight: bold; margin: 0; flex: 1;">Сеть образовательных организаций Новосибирской области
                </h1>
                    <a href="info.php" style="margin-top: 6px; margin-right: 18px;">
                        <img src="../src/img/info.png" alt="Информация">
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
<div class="stat-card" style="width: 99%;">
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
        <div class="stat-card" style="display: inline-block; width: 49%;">
            <h3>Начальные школы - детские сады</h3>
            <div class="stat-value">
                <?php echo array_sum(array_column($organizations, 'Nursery_school_primary'));?>
            </div>
        </div>
        <div class="stat-card" style="display: inline-block; width: 50%;">
            <h3>Начальные общеобразовательные школы</h3>
            <div class="stat-value">
                <?php echo array_sum(array_column($organizations, 'Primary_school')); ?>
            </div>
        </div>
    </div>

    <!-- Третья строка (две карточки) -->
    <div class="statistics">
        <div class="stat-card" style="display: inline-block; width: 49%;">
            <h3>Основные общеобразовательные школы</h3>
            <div class="stat-value" style="display: inline-block;">
                <?php echo array_sum(array_column($organizations, 'Basic_school'));?>
                <!-- ИСПРАВЛЕНО: было Primary_school, стало Basic_school -->
            </div>
        </div>
        <div class="stat-card" style="display: inline-block; width: 50%;">
            <h3>Средние общеобразовательные школы</h3>
            <div class="stat-value">
                <?php echo array_sum(array_column($organizations, 'sec_sc_sum'));?>
            </div>
        </div>
    </div>

    <!-- Четвертая строка (две карточки) -->
    <div class="statistics">
        <div class="stat-card" style="display: inline-block; width: 49%;">
            <h3>Санаторные общеобразовательные организации</h3>
            <div class="stat-value">
                <?php echo array_sum(array_column($organizations, 'Sanatorium_schools'));?>
            </div>
        </div>
        <div class="stat-card" style="display: inline-block; width: 50%;">
            <h3 style="font-size: 14px;">Школы для детей с ограниченными возможностями здоровья</h3>
            <div class="stat-value">
                <?php echo array_sum(array_column($organizations, 'Special_needs_schools'));?>
            </div>
        </div>
    </div>

    <!-- Пятая строка (две карточки) -->
    <div class="statistics">
        <div class="stat-card" style="display: inline-block; width: 49%;">
            <h3>Вечерние общеобразовательные организации</h3>
            <div class="stat-value">
                <?php echo array_sum(array_column($organizations, 'Evening_schools'));?>
            </div>
        </div>
        <div class="stat-card" style="display: inline-block; width: 50%;">
            <h3>Филиалы</h3>
            <div class="stat-value">
                <?php echo array_sum(array_column($organizations, 'Branches'));?>
            </div>
        </div>
    </div>
</div> 



<!-- ГРАФИКИ -->
<div class="chart-container">
    <!-- ВСЕГДА показываем эти графики -->
    <div class="chart-box">
        <div class="chart-header">
            <h3>Общее количество организаций <?= count($years) > 1 ? 'по годам' : "($years[0])" ?></h3>
        </div>
        <canvas id="totalChart"></canvas>
    </div>
    
    <div class="chart-box">
        <div class="chart-header">
            <h3>Среднеобразовательные организации <?= $show_single_year_charts ? "($years[0])" : '(суммарно)' ?></h3>
        </div>
        <canvas id="schoolTypesChart"></canvas>
    </div>
    
    <div class="chart-box">
        <div class="chart-header">
            <h3>Структура по типам <?= $show_single_year_charts ? "($years[0])" : '(суммарно)' ?></h3>
        </div>
        <canvas id="pieChart"></canvas>
    </div>
    
    <!-- График сравнения показываем ТОЛЬКО если есть данные за несколько лет -->
    <?php if (count($years) > 1): ?>
    <div class="chart-box">
        <div class="chart-header">
            <h3>Сравнение основных типов по годам</h3>
        </div>
        <canvas id="comparisonChart"></canvas>
    </div>
    <?php endif; ?>
</div>

<!---------------------------------------------------->



<!-- Таблица -->

<div class="results" id="tableView" style="margin-top: 20px; display: none;">
    <table>
        <thead>
            <tr>
                <th style="font-weight: bold;">Образовательные организации</th>
                <?php foreach ($organizations as $org): ?>
                <th style="text-align: center; font-weight: bold;"><?= safeEcho($org['Year_period']) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight: bold; padding-left: 9%;">НОШ д/сад</td>
                <?php foreach ($organizations as $org): ?>
                <td style="text-align: center;"><?= safeEcho($org['Nursery_school_primary'] ?? 0) ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td style="font-weight: bold; padding-left: 9%;">НОШ</td>
                <?php foreach ($organizations as $org): ?>
                <td style="text-align: center;"><?= safeEcho($org['Primary_school'] ?? 0) ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td style="font-weight: bold; padding-left: 9%;">ООШ</td>
                <?php foreach ($organizations as $org): ?>
                <td style="text-align: center;"><?= safeEcho($org['Basic_school'] ?? 0) ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td style="font-weight: bold; padding-left: 9%;">всего СОШ</td>
                <?php foreach ($organizations as $org): ?>
                <td style="text-align: center;"><?= safeEcho($org['sec_sc_sum'] ?? 0) ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td style="font-weight: bold; padding-left: 9%;">СОШ</td>
                <?php foreach ($organizations as $org): ?>
                <td style="text-align: center;"><?= safeEcho($org['Secondary_school'] ?? 0) ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td style="font-weight: bold; padding-left: 9%;">СОШ с УИОП</td>
                <?php foreach ($organizations as $org): ?>
                <td style="text-align: center;"><?= safeEcho($org['Secondary_school_special'] ?? 0) ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td style="font-weight: bold; padding-left: 9%;">гимназии</td>
                <?php foreach ($organizations as $org): ?>
                <td style="text-align: center;"><?= safeEcho($org['Gymnasium'] ?? 0) ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td style="font-weight: bold; padding-left: 9%;">лицеи</td>
                <?php foreach ($organizations as $org): ?>
                <td style="text-align: center;"><?= safeEcho($org['Lyceum'] ?? 0) ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td style="font-weight: bold; padding-left: 9%;">кадетские корпуса</td>
                <?php foreach ($organizations as $org): ?>
                <td style="text-align: center;"><?= safeEcho($org['Cadet_corps'] ?? 0) ?></td>
                <?php endforeach; ?>
            </tr>
            <tr >
                <td style="font-weight: bold; padding-left: 9%;">филиалы</td>
                <?php foreach ($organizations as $org): ?>
                <td style="text-align: center;"><?= safeEcho($org['Branches'] ?? 0) ?></td>
                <?php endforeach; ?>
            </tr>
            <tr style="background-color: #6d444b; color: white; font-weight: bold;">
                <td style="font-weight: bold; padding-left: 9%;">итого ОО</td>
                <?php foreach ($organizations as $org): ?>
                <td style="text-align: center;"><?= safeEcho($org['total_oo'] ?? 0) ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td style="font-weight: bold; padding-left: 9%;">санаторные ОО</td>
                <?php foreach ($organizations as $org): ?>
                <td style="text-align: center;"><?= safeEcho($org['Sanatorium_schools'] ?? 0) ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td style="font-weight: bold; padding-left: 9%;">ОО для детей с ОВЗ</td>
                <?php foreach ($organizations as $org): ?>
                <td style="text-align: center;"><?= safeEcho($org['Special_needs_schools'] ?? 0) ?></td>
                <?php endforeach; ?>
            </tr>
            <tr style="background-color: #6d444b; color: white; font-weight: bold;">
                <td style="font-weight: bold; padding-left: 9%;">итого дневные ОО</td>
                <?php foreach ($organizations as $org): ?>
                <td style="text-align: center;"><?= safeEcho($org['total_day_oo'] ?? 0) ?></td>
                <?php endforeach; ?>
            </tr>
            <tr>
                <td style="font-weight: bold; padding-left: 9%;">вечерние ОО</td>
                <?php foreach ($organizations as $org): ?>
                <td style="text-align: center;"><?= safeEcho($org['Evening_schools'] ?? 0) ?></td>
                <?php endforeach; ?>
            </tr>
            <tr  style="background-color: #6d444b; color: white; font-weight: bold;">
                <td>итого ОО по району</td>
                <?php foreach ($organizations as $org): ?>
                <td style="text-align: center;"><?= safeEcho($org['Total_organizations'] ?? 0) ?></td>
                <?php endforeach; ?>
            </tr>
        </tbody>
    </table>
</div>


        <?php else: ?>
            <div class="no-results">
                <h2>📝 Организации не найдены</h2>
                <p>Измените параметры фильтрации или добавьте данные в систему.</p>
            </div>
        <?php endif; ?>
        

    </div>
    
    <?php include '../scripts/index_script.php'; ?>

        </div>
    </div>
    <?php include '../footer/footer.php'; ?>
    <?php include '../styles/style_index.php'; ?>
</body>
</html>