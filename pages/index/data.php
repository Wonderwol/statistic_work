<?php
declare(strict_types=1);

/**
 * data.php — только логика и подготовка массивов.
 * Ничего не выводит (никаких echo).
 *
 * Требования:
 * - $pdo уже существует (подключён config.php в index.php)
 * - sql.php лежит рядом и содержит функции index_fetch_*
 */
require_once __DIR__ . '/sql.php';

/** @var PDO $pdo */

// 1) Забираем фильтры из GET (сохраняем те же имена переменных)
$org_types = $_GET['org_type'] ?? [];
$year_ids = $_GET['year_id'] ?? [];
$locality_types = $_GET['locality_type'] ?? [];

// приводим к массивам (у radio обычно строка)
if (!is_array($org_types) && $org_types !== '') $org_types = [$org_types];
if (!is_array($year_ids) && $year_ids !== '') $year_ids = [$year_ids];
if (!is_array($locality_types) && $locality_types !== '') $locality_types = [$locality_types];

// 2) Данные для селекторов фильтров
$years_data = index_fetch_years_data($pdo);
// Если фильтры по году не применялись (year_id[] не пришёл) — показываем текущий год по умолчанию
if (!isset($_GET['year_id']) || empty($year_ids)) {
    try {
        $defaultYear = index_fetch_default_year_period($pdo);
        if ($defaultYear !== null) {
            $year_ids = [$defaultYear];
        }
    } catch (Throwable $e) {
        error_log('Ошибка получения default Year_period: ' . $e->getMessage());
    }
}
$org_types_data = index_fetch_org_types_data($pdo);
$locality_types_data = index_fetch_locality_types_data($pdo);

// 3) Основные данные (организации)
$organizations = index_fetch_organizations($pdo, [
    'org_type'      => $org_types,        // radio -> 1 элемент, но пусть будет массив
    'year_id'       => $year_ids,         // checkbox[]
    'locality_type' => $locality_types    // radio -> 1 элемент, но пусть будет массив
]);

$hasOrganizations = !empty($organizations);

// 4) Нормализация и дополнительные поля (чтобы дальше расчёты были стабильны)
foreach ($organizations as &$org) {
    $org['Nursery_school_primary']    = (int)($org['Nursery_school_primary'] ?? 0);
    $org['Primary_school']            = (int)($org['Primary_school'] ?? 0);
    $org['Basic_school']              = (int)($org['Basic_school'] ?? 0);

    $org['Secondary_school_sum']      = (int)($org['Secondary_school_sum'] ?? 0);
    $org['Secondary_school']          = (int)($org['Secondary_school'] ?? 0);
    $org['Secondary_school_special']  = (int)($org['Secondary_school_special'] ?? 0);
    $org['Gymnasium']                 = (int)($org['Gymnasium'] ?? 0);
    $org['Lyceum']                    = (int)($org['Lyceum'] ?? 0);
    $org['Cadet_corps']               = (int)($org['Cadet_corps'] ?? 0);

    $org['Branches']                  = (int)($org['Branches'] ?? 0);
    $org['Sanatorium_schools']        = (int)($org['Sanatorium_schools'] ?? 0);
    $org['Special_needs_schools']     = (int)($org['Special_needs_schools'] ?? 0);
    $org['Evening_schools']           = (int)($org['Evening_schools'] ?? 0);

    $org['Total_organizations']       = (int)($org['Total_organizations'] ?? 0);

    // как у тебя было: sec_sc_sum
    $org['sec_sc_sum'] = $org['Secondary_school_sum'];
}
unset($org);

// 5) Таблица по годам (yearsTable + tableByYear)
$tableByYear = [];
$yearsTable = [];

foreach ($organizations as $row) {
    $y = (string)($row['Year_period'] ?? '');
    if ($y === '') continue;

    if (!isset($tableByYear[$y])) {
        $tableByYear[$y] = [
            'Nursery_school_primary'   => 0,
            'Primary_school'           => 0,
            'Basic_school'             => 0,
            'sec_sc_sum'               => 0,
            'Secondary_school'         => 0,
            'Secondary_school_special' => 0,
            'Gymnasium'                => 0,
            'Lyceum'                   => 0,
            'Cadet_corps'              => 0,
            'Branches'                 => 0,
            'Total_organizations'      => 0,
            'Sanatorium_schools'       => 0,
            'Special_needs_schools'    => 0,
            'Evening_schools'          => 0,
        ];
        $yearsTable[] = $y;
    }

    $tableByYear[$y]['Nursery_school_primary']   += (int)$row['Nursery_school_primary'];
    $tableByYear[$y]['Primary_school']           += (int)$row['Primary_school'];
    $tableByYear[$y]['Basic_school']             += (int)$row['Basic_school'];
    $tableByYear[$y]['sec_sc_sum']               += (int)$row['sec_sc_sum'];

    $tableByYear[$y]['Secondary_school']         += (int)$row['Secondary_school'];
    $tableByYear[$y]['Secondary_school_special'] += (int)$row['Secondary_school_special'];
    $tableByYear[$y]['Gymnasium']                += (int)$row['Gymnasium'];
    $tableByYear[$y]['Lyceum']                   += (int)$row['Lyceum'];
    $tableByYear[$y]['Cadet_corps']              += (int)$row['Cadet_corps'];

    $tableByYear[$y]['Branches']                 += (int)$row['Branches'];
    $tableByYear[$y]['Total_organizations']      += (int)$row['Total_organizations'];
    $tableByYear[$y]['Sanatorium_schools']       += (int)$row['Sanatorium_schools'];
    $tableByYear[$y]['Special_needs_schools']    += (int)$row['Special_needs_schools'];
    $tableByYear[$y]['Evening_schools']          += (int)$row['Evening_schools'];
}
sort($yearsTable);

// 6) Данные для графиков (years + массивы под window.*)
$years = [];
$dataByYear = [];

foreach ($organizations as $org) {
    $y = (string)$org['Year_period'];
    if ($y === '') continue;

    if (!isset($dataByYear[$y])) {
        $dataByYear[$y] = [
            'total' => 0,
            'nursery' => 0,
            'basic' => 0,
            'special' => 0,
            'school_types' => [0, 0, 0, 0, 0],
            'pie' => [0, 0, 0, 0, 0, 0, 0],
        ];
        $years[] = $y;
    }

    $dataByYear[$y]['total']   += $org['Total_organizations'];
    $dataByYear[$y]['nursery'] += $org['Nursery_school_primary'];
    $dataByYear[$y]['basic']   += $org['Basic_school'];
    $dataByYear[$y]['special'] += $org['Special_needs_schools'];

    // bar/legend types
    $dataByYear[$y]['school_types'][0] += $org['Secondary_school'];
    $dataByYear[$y]['school_types'][1] += $org['Secondary_school_special'];
    $dataByYear[$y]['school_types'][2] += $org['Gymnasium'];
    $dataByYear[$y]['school_types'][3] += $org['Lyceum'];
    $dataByYear[$y]['school_types'][4] += $org['Cadet_corps'];

    // pie (структура)
    $dataByYear[$y]['pie'][0] += $org['Nursery_school_primary'];
    $dataByYear[$y]['pie'][1] += $org['Primary_school'];
    $dataByYear[$y]['pie'][2] += $org['Basic_school'];
    $dataByYear[$y]['pie'][3] += $org['sec_sc_sum'];
    $dataByYear[$y]['pie'][4] += $org['Sanatorium_schools'];
    $dataByYear[$y]['pie'][5] += $org['Special_needs_schools'];
    $dataByYear[$y]['pie'][6] += $org['Evening_schools'];
}

sort($years);

$show_single_year_charts = (count($years) === 1);

// линии/столбцы по годам
$totalOrganizations = [];
$nurseryData = [];
$basicData = [];
$specialData = [];

foreach ($years as $y) {
    $totalOrganizations[] = $dataByYear[$y]['total'];
    $nurseryData[] = $dataByYear[$y]['nursery'];
    $basicData[] = $dataByYear[$y]['basic'];
    $specialData[] = $dataByYear[$y]['special'];
}

// агрегаты “суммарно” (для pie / schoolTypes)
$schoolTypesData = [0, 0, 0, 0, 0];
$pieData = [0, 0, 0, 0, 0, 0, 0];

foreach ($dataByYear as $yd) {
    for ($i = 0; $i < 5; $i++) $schoolTypesData[$i] += $yd['school_types'][$i];
    for ($i = 0; $i < 7; $i++) $pieData[$i] += $yd['pie'][$i];
}

$schoolTypesLabels = ['СОШ', 'СОШ с УИОП', 'Гимназии', 'Лицеи', 'Кадетские корпуса'];
$pieLabels = ['НОШ д/сад', 'НОШ', 'Основные школы', 'Средние школы', 'Санаторные', 'ОВЗ школы', 'Вечерние'];

$pieSeries = []; // [тип][год] => значения по каждому году
for ($i = 0; $i < count($pieLabels); $i++) {
    $row = [];
    foreach ($years as $y) {
        $row[] = (int)($dataByYear[$y]['pie'][$i] ?? 0);
    }
    $pieSeries[] = $row;
}


// 7) Карточки (если у тебя их расчёт делался в PHP)
$cards = [
    'total_all'     => array_sum(array_column($organizations, 'Total_organizations')),
    'nursery'       => array_sum(array_column($organizations, 'Nursery_school_primary')),
    'primary'       => array_sum(array_column($organizations, 'Primary_school')),
    'basic'         => array_sum(array_column($organizations, 'Basic_school')),
    'secondary_sum' => array_sum(array_column($organizations, 'sec_sc_sum')),
    'sanatorium'    => array_sum(array_column($organizations, 'Sanatorium_schools')),
    'ovz'           => array_sum(array_column($organizations, 'Special_needs_schools')),
    'evening'       => array_sum(array_column($organizations, 'Evening_schools')),
    'branches'      => array_sum(array_column($organizations, 'Branches')),
];

// 8) Время обновления (displayTime как раньше)
try {
    $lastUpdate = index_fetch_last_update($pdo);
    $displayTime = $lastUpdate ? date('H:i d.m.Y', strtotime($lastUpdate)) : date('H:i d.m.Y');
} catch (Throwable $e) {
    error_log("Ошибка получения времени обновления: " . $e->getMessage());
    $displayTime = date('H:i d.m.Y');
}
