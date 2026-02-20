<?php
declare(strict_types=1);

require_once __DIR__ . '/sql.php';

/** @var PDO $pdo */

function nimro_norm_year(string $s): string
{
    $s = str_replace(["\u{2013}", "\u{2014}", '–', '—'], '-', $s);
    $s = preg_replace('/\s+/u', '', $s);
    return trim($s);
}

function nimro_detect_total_area(PDO $pdo, string $yearPeriod, int $areaTypeCode): ?array
{
    $sql = "
        SELECT TOP 1
            ao.Area_code,
            da.Area_name,
            SUM(CASE WHEN ao.Organization_type_code IN (1,2,3,5,6,7,8,9,11,12,13)
                     THEN ao.Area_organizations_count ELSE 0 END) AS total_orgs
        FROM Area_organizations ao
        JOIN dat_Area da ON da.Area_code = ao.Area_code
        WHERE ao.deleted = 0
          AND ao.Year_period = ?
          AND ao.Area_type_code = ?
        GROUP BY ao.Area_code, da.Area_name
        ORDER BY total_orgs DESC, ao.Area_code ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$yearPeriod, $areaTypeCode]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}

// 1) Забираем фильтры из GET (сохраняем те же имена переменных)
$org_types = $_GET['org_type'] ?? [];
$year_ids = $_GET['year_id'] ?? [];
$locality_types = $_GET['locality_type'] ?? [];
$chart_year_id = (string)($_GET['chart_year_id'] ?? '');

// приводим к массивам (у radio обычно строка)
if (!is_array($org_types) && $org_types !== '') $org_types = [$org_types];
if (!is_array($year_ids) && $year_ids !== '') $year_ids = [$year_ids];
if (!is_array($locality_types) && $locality_types !== '') $locality_types = [$locality_types];

// 2) Данные для селекторов фильтров
$years_data = index_fetch_years_data($pdo);
$org_types_data = index_fetch_org_types_data($pdo);
$locality_types_data = index_fetch_locality_types_data($pdo);

/**
 * Дефолт по местности: "Всего" = 3 (чтобы radio был отмечен визуально).
 */
if (!isset($_GET['locality_type']) || empty($locality_types)) {
    $locality_types = [3];
}
$areaTypeCode = (int)reset($locality_types);
if ($areaTypeCode <= 0) $areaTypeCode = 3;

/**
 * Дефолт по году: 2025-2026 (если есть), иначе самый свежий год.
 */
if (!isset($_GET['year_id']) || empty($year_ids)) {
    $preferredYear = null;
    foreach ($years_data as $yRow) {
        $id = (string)($yRow['id'] ?? '');
        $n = nimro_norm_year($id);
        if ($n === '2025-2026' || $n === '2025/2026') {
            $preferredYear = $id;
            break;
        }
    }

    if ($preferredYear !== null) {
        $year_ids = [$preferredYear];
    } else {
        try {
            $defaultYear = index_fetch_default_year_period($pdo);
            if ($defaultYear !== null) $year_ids = [$defaultYear];
        } catch (Throwable $e) {
            error_log('Ошибка получения default Year_period: ' . $e->getMessage());
        }
    }
}

$yearPeriod = (string)reset($year_ids);

/**
 * Определяем итоговую строку НСО по данным.
 */
$totalAreaCode = null;
$totalAreaName = null;

if ($yearPeriod !== '') {
    try {
        $total = nimro_detect_total_area($pdo, $yearPeriod, $areaTypeCode);
        if ($total) {
            $totalAreaCode = (string)($total['Area_code'] ?? '');
            $totalAreaName = (string)($total['Area_name'] ?? '');
            if ($totalAreaCode === '') $totalAreaCode = null;
        }
    } catch (Throwable $e) {
        error_log('Ошибка nimro_detect_total_area: ' . $e->getMessage());
    }
}

/**
 * Переименовываем итоговую строку только для UI.
 */
if ($totalAreaCode !== null) {
    foreach ($org_types_data as &$t) {
        if ((string)($t['id'] ?? '') === $totalAreaCode) {
            $t['name'] = 'Итого по НСО';
            break;
        }
    }
    unset($t);

    // поднимаем "Итого по НСО" наверх списка
    usort($org_types_data, static function (array $a, array $b): int {
        $aIs = ((string)($a['name'] ?? '') === 'Итого по НСО') ? 0 : 1;
        $bIs = ((string)($b['name'] ?? '') === 'Итого по НСО') ? 0 : 1;
        if ($aIs !== $bIs) return $aIs <=> $bIs;
        return strcmp((string)($a['name'] ?? ''), (string)($b['name'] ?? ''));
    });
}

/**
 * Дефолт по 1-му фильтру: Итого по НСО (Area_code итога).
 */
if ((!isset($_GET['org_type']) || empty($org_types)) && $totalAreaCode !== null) {
    $org_types = [$totalAreaCode];
}

// Для запроса в БД добавляем ещё chart_year_id (если его нет среди year_id[]),
$year_ids_query = $year_ids;
if ($chart_year_id !== '' && !in_array($chart_year_id, $year_ids_query, true)) {
    $year_ids_query[] = $chart_year_id;
}

$organizations = index_fetch_organizations($pdo, [
    'org_type'      => $org_types,
    'year_id'       => $year_ids_query,
    'locality_type' => $locality_types
]);

/**
 * Анти-двойной учёт:
 * - если выбран итог — оставляем только строку итога;
 * - если выбран не итог — вырезаем строку итога, если она вдруг попала.
 */
if (!empty($organizations) && $totalAreaCode !== null) {
    $selectedOrg = (!empty($org_types) ? (string)reset($org_types) : '');

    if ($selectedOrg === $totalAreaCode) {
        $organizations = array_values(array_filter($organizations, static function (array $r) use ($totalAreaCode): bool {
            return isset($r['Area_code']) && (string)$r['Area_code'] === $totalAreaCode;
        }));
        foreach ($organizations as &$r) {
            $r['Area_name'] = 'Итого по НСО';
        }
        unset($r);
    } else {
        $organizations = array_values(array_filter($organizations, static function (array $r) use ($totalAreaCode, $totalAreaName): bool {
            if (isset($r['Area_code'])) return (string)$r['Area_code'] !== $totalAreaCode;
            if ($totalAreaName !== null && isset($r['Area_name'])) return (string)$r['Area_name'] !== $totalAreaName;
            return true;
        }));
    }
}

$hasOrganizations = !empty($organizations);

// 4) Нормализация и дополнительные поля
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

    $org['sec_sc_sum'] = $org['Secondary_school_sum'];
}
unset($org);

// 5) Таблица по годам
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

// Берём годы для таблицы из выбранных чекбоксов
$yearsTable = array_values(array_unique(array_map('strval', (array)$year_ids)));
sort($yearsTable);

// Если chart_year_id не выбран — по умолчанию берём самый свежий из выбранных для таблицы
if ($chart_year_id === '' && !empty($yearsTable)) {
    $chart_year_id = (string)end($yearsTable);
    reset($yearsTable);
}

// 6) Данные для графиков
$years = ($chart_year_id !== '') ? [$chart_year_id] : $yearsTable;
$show_single_year_charts = true;

$pieLabels = ['НОШ д/сад', 'НОШ', 'Основные школы', 'Средние школы', 'Санаторные', 'ОВЗ школы', 'Вечерние'];
$pieData = [0, 0, 0, 0, 0, 0, 0];

foreach ($years as $y) {
    $pieData[0] += (int)($tableByYear[$y]['Nursery_school_primary'] ?? 0);
    $pieData[1] += (int)($tableByYear[$y]['Primary_school'] ?? 0);
    $pieData[2] += (int)($tableByYear[$y]['Basic_school'] ?? 0);
    $pieData[3] += (int)($tableByYear[$y]['sec_sc_sum'] ?? 0);
    $pieData[4] += (int)($tableByYear[$y]['Sanatorium_schools'] ?? 0);
    $pieData[5] += (int)($tableByYear[$y]['Special_needs_schools'] ?? 0);
    $pieData[6] += (int)($tableByYear[$y]['Evening_schools'] ?? 0);
}

// =========================================================
// 7) График: "Количество ОО по районам" (Total_organizations)
// =========================================================

$selectedAreaCode = (!empty($org_types) ? (string)reset($org_types) : '');
// если выбран "Итого по НСО" — подсветку выключаем
if ($totalAreaCode !== null && $selectedAreaCode !== '' && $selectedAreaCode === (string)$totalAreaCode) {
    $selectedAreaCode = '';
}

$rankYear = (string)($chart_year_id !== '' ? $chart_year_id : $yearPeriod);
$areaRankLabels = [];
$areaRankValues = [];
$areaRankCodes  = [];

try {
    $rows = ($rankYear !== '') ? index_fetch_area_totals($pdo, $rankYear, $areaTypeCode) : [];

    $totalRow = null;
    $items = [];

    foreach ($rows as $r) {
        $code = (string)($r['Area_code'] ?? '');
        $name = (string)($r['Area_name'] ?? '');
        $val  = (int)($r['Total_organizations'] ?? 0);

        if ($val <= 0) continue;
        if ($code === '') continue;

        if ($totalAreaCode !== null && $code === (string)$totalAreaCode) {
            $totalRow = ['code' => $code, 'name' => 'Итого', 'val' => $val];
            continue;
        }

        $items[] = ['code' => $code, 'name' => $name, 'val' => $val];
    }

    usort($items, static function (array $a, array $b): int {
        if (($b['val'] ?? 0) !== ($a['val'] ?? 0)) return ($b['val'] ?? 0) <=> ($a['val'] ?? 0);
        return strcmp((string)($a['name'] ?? ''), (string)($b['name'] ?? ''));
    });

    if (is_array($totalRow)) {
        $items[] = $totalRow;
    }

    foreach ($items as $it) {
        $areaRankCodes[]  = (string)$it['code'];
        $areaRankLabels[] = (string)$it['name'];
        $areaRankValues[] = (int)$it['val'];
    }
} catch (Throwable $e) {
    error_log('Ошибка построения area-rank: ' . $e->getMessage());
}

// 8) Дата актуальности данных (20.09)
$now = new DateTimeImmutable('now');
$year = (int)$now->format('Y');

$anchorThisYear = DateTimeImmutable::createFromFormat('!d.m.Y', '20.09.' . $year);
if ($anchorThisYear instanceof DateTimeImmutable && $now < $anchorThisYear) {
    $year--;
}
$displayTime = sprintf('20.09.%04d', $year);
