<?php
declare(strict_types=1);

require_once __DIR__ . '/sql.php';

/** @var PDO $pdo */

function dyn_norm_year(string $s): string
{
    $s = str_replace(["\u{2013}", "\u{2014}", '–', '—'], '-', $s);
    $s = preg_replace('/\s+/u', '', $s);
    return trim($s);
}

function dyn_year_key(string $s): int
{
    $n = dyn_norm_year($s);
    if (preg_match('/^(\d{4})(?:[-\/](\d{4}))?$/', $n, $m)) {
        $a = (int)$m[1];
        $b = isset($m[2]) ? (int)$m[2] : $a;
        return $a * 10000 + $b;
    }
    return 0;
}

function dyn_detect_total_area(PDO $pdo, string $yearPeriod, int $areaTypeCode): ?array
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

// GET-фильтры
$org_types      = $_GET['org_type'] ?? [];
$year_ids       = $_GET['year_id'] ?? [];
$locality_types = $_GET['locality_type'] ?? [];

if (!is_array($org_types) && $org_types !== '') $org_types = [$org_types];
if (!is_array($year_ids) && $year_ids !== '') $year_ids = [$year_ids];
if (!is_array($locality_types) && $locality_types !== '') $locality_types = [$locality_types];

// данные для селекторов
$years_data          = dyn_fetch_years_data($pdo);

$year_ids = [];
foreach ($years_data as $r) {
    $id = (string)($r['id'] ?? '');
    if ($id !== '') $year_ids[] = $id;
}

// сортировка как у тебя (через dyn_year_key / dyn_norm_year)
$year_ids = array_values(array_unique($year_ids));
usort($year_ids, static function(string $a, string $b): int {
    return dyn_year_key($a) <=> dyn_year_key($b);
});
$org_types_data      = dyn_fetch_org_types_data($pdo);
$locality_types_data = dyn_fetch_locality_types_data($pdo);

// дефолт местности: "Всего" = 3
if (!isset($_GET['locality_type']) || empty($locality_types)) $locality_types = [3];
$areaTypeCode = (int)reset($locality_types);
if ($areaTypeCode <= 0) $areaTypeCode = 3;

// дефолт годов: последние 5 (если пользователь не выбирал)
if (!isset($_GET['year_id']) || empty($year_ids)) {
    $tmp = [];
    foreach ($years_data as $r) {
        $id = (string)($r['id'] ?? '');
        if ($id !== '') $tmp[] = $id;
    }

    usort($tmp, static function(string $a, string $b): int {
        return dyn_year_key($a) <=> dyn_year_key($b);
    });

    $tmp = array_values(array_unique($tmp));

    $take = 5;
    if (count($tmp) > $take) $tmp = array_slice($tmp, -$take);

    $year_ids = $tmp;
}

// сортируем выбранные годы правильно
$year_ids = array_values(array_unique(array_map('strval', $year_ids)));
usort($year_ids, static function(string $a, string $b): int {
    return dyn_year_key($a) <=> dyn_year_key($b);
});

// год для детекта "Итого по НСО" берём самый свежий из выбранных
$yearForTotal = '';
if (!empty($year_ids)) {
    $yearForTotal = (string)end($year_ids);
    reset($year_ids);
}

// детект итога
$totalAreaCode = null;
$totalAreaName = null;

if ($yearForTotal !== '') {
    try {
        $total = dyn_detect_total_area($pdo, $yearForTotal, $areaTypeCode);
        if ($total) {
            $totalAreaCode = (string)($total['Area_code'] ?? '');
            $totalAreaName = (string)($total['Area_name'] ?? '');
            if ($totalAreaCode === '') $totalAreaCode = null;
        }
    } catch (Throwable $e) {
        error_log('dyn_detect_total_area error: ' . $e->getMessage());
    }
}

// переименование итога только для UI + поднять вверх
if ($totalAreaCode !== null) {
    foreach ($org_types_data as &$t) {
        if ((string)($t['id'] ?? '') === $totalAreaCode) {
            $t['name'] = 'Итого по НСО';
            break;
        }
    }
    unset($t);

    usort($org_types_data, static function (array $a, array $b): int {
        $aIs = ((string)($a['name'] ?? '') === 'Итого по НСО') ? 0 : 1;
        $bIs = ((string)($b['name'] ?? '') === 'Итого по НСО') ? 0 : 1;
        if ($aIs !== $bIs) return $aIs <=> $bIs;
        return strcmp((string)($a['name'] ?? ''), (string)($b['name'] ?? ''));
    });
}

// дефолт org_type: итог
if ((!isset($_GET['org_type']) || empty($org_types)) && $totalAreaCode !== null) {
    $org_types = [$totalAreaCode];
}

// основная выборка
$organizations = dyn_fetch_organizations($pdo, [
    'org_type'      => $org_types,
    'year_id'       => $year_ids,
    'locality_type' => $locality_types,
]);

// анти-двойной учёт итога
if (!empty($organizations) && $totalAreaCode !== null) {
    $selectedOrg = (!empty($org_types) ? (string)reset($org_types) : '');

    if ($selectedOrg === $totalAreaCode) {
        $organizations = array_values(array_filter($organizations, static function (array $r) use ($totalAreaCode): bool {
            return isset($r['Area_code']) && (string)$r['Area_code'] === $totalAreaCode;
        }));
        foreach ($organizations as &$r) $r['Area_name'] = 'Итого по НСО';
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

// агрегаты по годам (суммируем, потому что строка может быть по району)
$tableByYear = [];
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
            'Sanatorium_schools'       => 0,
            'Special_needs_schools'    => 0,
            'Evening_schools'          => 0,
            'Total_organizations'      => 0,
        ];
    }

    $tableByYear[$y]['Nursery_school_primary']   += (int)($row['Nursery_school_primary'] ?? 0);
    $tableByYear[$y]['Primary_school']           += (int)($row['Primary_school'] ?? 0);
    $tableByYear[$y]['Basic_school']             += (int)($row['Basic_school'] ?? 0);

    $tableByYear[$y]['sec_sc_sum']               += (int)($row['Secondary_school_sum'] ?? 0);
    $tableByYear[$y]['Secondary_school']         += (int)($row['Secondary_school'] ?? 0);
    $tableByYear[$y]['Secondary_school_special'] += (int)($row['Secondary_school_special'] ?? 0);
    $tableByYear[$y]['Gymnasium']                += (int)($row['Gymnasium'] ?? 0);
    $tableByYear[$y]['Lyceum']                   += (int)($row['Lyceum'] ?? 0);
    $tableByYear[$y]['Cadet_corps']              += (int)($row['Cadet_corps'] ?? 0);

    $tableByYear[$y]['Branches']                 += (int)($row['Branches'] ?? 0);
    $tableByYear[$y]['Sanatorium_schools']       += (int)($row['Sanatorium_schools'] ?? 0);
    $tableByYear[$y]['Special_needs_schools']    += (int)($row['Special_needs_schools'] ?? 0);
    $tableByYear[$y]['Evening_schools']          += (int)($row['Evening_schools'] ?? 0);

    $tableByYear[$y]['Total_organizations']      += (int)($row['Total_organizations'] ?? 0);
}

// серии для графиков (по выбранным годам)
$chartYears = $year_ids;

$networkLabels = ['НОШ д/сад', 'НОШ', 'ООШ', 'СОШ', 'санаторные ОО', 'ОО для детей с ОВЗ', 'вечерние ОО'];
$networkSeries = array_fill(0, count($networkLabels), []);
$networkTotals = [];

$branchesSeries = [];

$secLabels = ['СОШ', 'СОШ с УИОП', 'Гимназии', 'Лицеи', 'кадетские корпуса'];
$secSeries = array_fill(0, count($secLabels), []);
$secTotals = [];

foreach ($chartYears as $y) {
    $r = $tableByYear[$y] ?? [
        'Nursery_school_primary'=>0,'Primary_school'=>0,'Basic_school'=>0,'sec_sc_sum'=>0,
        'Sanatorium_schools'=>0,'Special_needs_schools'=>0,'Evening_schools'=>0,
        'Secondary_school'=>0,'Secondary_school_special'=>0,'Gymnasium'=>0,'Lyceum'=>0,'Cadet_corps'=>0,
        'Branches'=>0,'Total_organizations'=>0
    ];

    $networkSeries[0][] = (int)$r['Nursery_school_primary'];
    $networkSeries[1][] = (int)$r['Primary_school'];
    $networkSeries[2][] = (int)$r['Basic_school'];
    $networkSeries[3][] = (int)$r['sec_sc_sum'];
    $networkSeries[4][] = (int)$r['Sanatorium_schools'];
    $networkSeries[5][] = (int)$r['Special_needs_schools'];
    $networkSeries[6][] = (int)$r['Evening_schools'];
    $networkTotals[]    = (int)$r['Total_organizations'];

    $branchesSeries[] = (int)$r['Branches'];

    $secSeries[0][] = (int)$r['Secondary_school'];
    $secSeries[1][] = (int)$r['Secondary_school_special'];
    $secSeries[2][] = (int)$r['Gymnasium'];
    $secSeries[3][] = (int)$r['Lyceum'];
    $secSeries[4][] = (int)$r['Cadet_corps'];
    $secTotals[]    = (int)$r['sec_sc_sum'];
}

// дата актуальности (20.09 раз в год)
$now = new DateTimeImmutable('now');
$year = (int)$now->format('Y');
$anchorThisYear = DateTimeImmutable::createFromFormat('!d.m.Y', '20.09.' . $year);
if ($anchorThisYear instanceof DateTimeImmutable && $now < $anchorThisYear) $year--;
$displayTime = sprintf('20.09.%04d', $year);
