<?php
declare(strict_types=1);

require_once __DIR__ . '/sql.php';

/** @var PDO $pdo */

function ch3_norm_year(string $s): string
{
    $s = str_replace(["\u{2013}", "\u{2014}", '–', '—'], '-', $s);
    $s = preg_replace('/\s+/u', '', $s);
    return trim($s);
}

function ch3_year_key(string $s): int
{
    $n = ch3_norm_year($s);
    if (preg_match('/^(\d{4})(?:[-\/](\d{4}))?$/', $n, $m)) {
        $a = (int)$m[1];
        $b = isset($m[2]) ? (int)$m[2] : $a;
        return $a * 10000 + $b;
    }
    return 0;
}

function ch3_year_label(string $yearPeriod): string
{
    $n = ch3_norm_year($yearPeriod);
    if (preg_match('/^(\d{4})/', $n, $m)) return (string)$m[1];
    return $yearPeriod;
}

function ch3_detect_total_area(PDO $pdo, string $yearPeriod, int $areaTypeCode): ?array
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

// ========================
// GET-фильтры (год убран)
// ========================
$org_types      = $_GET['org_type'] ?? [];
$locality_types = $_GET['locality_type'] ?? [];

// Важно: фильтр по годам убран из 3 раздела.
// Чтобы не ломать существующие переменные/пейлоад — оставляем пустым массивом.
$year_ids = [];

if (!is_array($org_types) && $org_types !== '') $org_types = [$org_types];
if (!is_array($locality_types) && $locality_types !== '') $locality_types = [$locality_types];

// данные для селекторов
$years_data          = ch3_fetch_years_data($pdo);
$org_types_data      = ch3_fetch_org_types_data($pdo);
$locality_types_data = ch3_fetch_locality_types_data($pdo);

// дефолт местности: "Всего" = 3
if (!isset($_GET['locality_type']) || empty($locality_types)) $locality_types = [3];
$areaTypeCode = (int)reset($locality_types);
if ($areaTypeCode <= 0) $areaTypeCode = 3;

// самый свежий год из БД — для детекта "Итого по НСО"
$allYears = [];
foreach ($years_data as $r) {
    $id = (string)($r['id'] ?? '');
    if ($id !== '') $allYears[] = $id;
}
$allYears = array_values(array_unique($allYears));
usort($allYears, static fn(string $a, string $b): int => ch3_year_key($a) <=> ch3_year_key($b));

$yearForTotal = '';
if (!empty($allYears)) {
    $yearForTotal = (string)end($allYears);
    reset($allYears);
}

// детект итога
$totalAreaCode = null;
$totalAreaName = null;

if ($yearForTotal !== '') {
    try {
        $total = ch3_detect_total_area($pdo, $yearForTotal, $areaTypeCode);
        if ($total) {
            $totalAreaCode = (string)($total['Area_code'] ?? '');
            $totalAreaName = (string)($total['Area_name'] ?? '');
            if ($totalAreaCode === '') $totalAreaCode = null;
        }
    } catch (Throwable $e) {
        error_log('ch3_detect_total_area error: ' . $e->getMessage());
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

// основная выборка (без ограничения по годам)
$organizations = ch3_fetch_organizations($pdo, [
    'org_type'      => $org_types,
    'year_id'       => [],
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

// агрегируем по годам
$byYear = [];
foreach ($organizations as $row) {
    $y = (string)($row['Year_period'] ?? '');
    if ($y === '') continue;

    if (!isset($byYear[$y])) {
        $byYear[$y] = [
            'total'     => 0,
            'nursery'   => 0,
            'primary'   => 0,
            'basic'     => 0,
            'secondary' => 0,
            'ovz'       => 0,
            'sanat'     => 0,
            'evening'   => 0,
            'branches'  => 0,
        ];
    }

    $byYear[$y]['total']     += (int)($row['Total_organizations'] ?? 0);
    $byYear[$y]['nursery']   += (int)($row['Nursery_school_primary'] ?? 0);
    $byYear[$y]['primary']   += (int)($row['Primary_school'] ?? 0);
    $byYear[$y]['basic']     += (int)($row['Basic_school'] ?? 0);
    $byYear[$y]['secondary'] += (int)($row['Secondary_school_sum'] ?? 0);
    $byYear[$y]['ovz']       += (int)($row['Special_needs_schools'] ?? 0);
    $byYear[$y]['sanat']     += (int)($row['Sanatorium_schools'] ?? 0);
    $byYear[$y]['evening']   += (int)($row['Evening_schools'] ?? 0);
    $byYear[$y]['branches']  += (int)($row['Branches'] ?? 0);
}

// годы для графика = все годы, которые реально есть в данных выборки
$chartYearPeriods = array_keys($byYear);
usort($chartYearPeriods, static fn(string $a, string $b): int => ch3_year_key($a) <=> ch3_year_key($b));

$chartLabels = array_map('ch3_year_label', $chartYearPeriods);

$series = [
    'total'     => [],
    'nursery'   => [],
    'primary'   => [],
    'basic'     => [],
    'secondary' => [],
    'ovz'       => [],
    'sanat'     => [],
    'evening'   => [],
    'branches'  => [],
];

foreach ($chartYearPeriods as $yp) {
    $r = $byYear[$yp] ?? null;
    $series['total'][]     = (int)($r['total'] ?? 0);
    $series['nursery'][]   = (int)($r['nursery'] ?? 0);
    $series['primary'][]   = (int)($r['primary'] ?? 0);
    $series['basic'][]     = (int)($r['basic'] ?? 0);
    $series['secondary'][] = (int)($r['secondary'] ?? 0);
    $series['ovz'][]       = (int)($r['ovz'] ?? 0);
    $series['sanat'][]     = (int)($r['sanat'] ?? 0);
    $series['evening'][]   = (int)($r['evening'] ?? 0);
    $series['branches'][]  = (int)($r['branches'] ?? 0);
}

$seriesLabels = [
    'total'     => 'Все ОО',
    'nursery'   => 'НШ д/с',
    'primary'   => 'НОШ',
    'basic'     => 'ООШ',
    'secondary' => 'СОШ',
    'ovz'       => 'ОО для детей с ОВЗ',
    'sanat'     => 'Санаторные ОО',
    'evening'   => 'Вечерние ОО',
    'branches'  => 'Филиалы',
];

// дата актуальности (20.09 раз в год)
$now = new DateTimeImmutable('now');
$year = (int)$now->format('Y');
$anchorThisYear = DateTimeImmutable::createFromFormat('!d.m.Y', '20.09.' . $year);
if ($anchorThisYear instanceof DateTimeImmutable && $now < $anchorThisYear) $year--;
$displayTime = sprintf('20.09.%04d', $year);
