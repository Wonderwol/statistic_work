<?php
declare(strict_types=1);

require_once __DIR__ . '/sql.php';

/** @var PDO $pdo */

function st_norm_year(string $s): string
{
    $s = str_replace(["\u{2013}", "\u{2014}", '–', '—'], '-', $s);
    $s = preg_replace('/\s+/u', '', $s);
    return trim($s);
}

function st_nf_int(float|int $v): string
{
    return number_format((int)round((float)$v), 0, '.', ' ');
}

// 1) GET-фильтры (имена оставляем такими же, как в разделе edu_orgs)
$org_types = $_GET['org_type'] ?? [];
$year_ids = $_GET['year_id'] ?? [];
$locality_types = $_GET['locality_type'] ?? [];
$chart_year_id = (string)($_GET['chart_year_id'] ?? '');

if (!is_array($org_types) && $org_types !== '') $org_types = [$org_types];
if (!is_array($year_ids) && $year_ids !== '') $year_ids = [$year_ids];
if (!is_array($locality_types) && $locality_types !== '') $locality_types = [$locality_types];

// 2) Справочники для фильтров
$studentsDataFatal = false;
$studentsDataError = '';

try {
    $years_data = st_fetch_years_data($pdo);
    $org_types_data = st_fetch_org_types_data($pdo);
    $locality_types_data = st_fetch_locality_types_data($pdo);
} catch (Throwable $e) {
    $studentsDataFatal = true;
    $studentsDataError = $e->getMessage();

    // Минимальные значения, чтобы страница не упала 500.
    $years_data = [];
    $org_types_data = [];
    $locality_types_data = [];

    $table = [];
    $areaKeys = [];
    $yearsTable = [];

    $lineLabels = [];
    $lineValues = [];

    $areaRankLabels = [];
    $areaRankValues = [];
    $areaRankCodes  = [];

    $highlightAreaCode = '';
    $rankYearLabel = '';

    $displayTime = '';

    return;
}

// 3) Дефолт по местности: "Всего" = 3
if (!isset($_GET['locality_type']) || empty($locality_types)) {
    $locality_types = [3];
}
$areaTypeCode = (int)reset($locality_types);
if ($areaTypeCode <= 0) $areaTypeCode = 3;

// 4) Дефолт по году: 2025-2026 если есть, иначе самый свежий
if (!isset($_GET['year_id']) || empty($year_ids)) {
    $preferredYear = null;
    foreach ($years_data as $yRow) {
        $id = (string)($yRow['id'] ?? '');
        $n = st_norm_year($id);
        if ($n === '2025-2026' || $n === '2025/2026') {
            $preferredYear = $id;
            break;
        }
    }

    if ($preferredYear !== null) {
        $year_ids = [$preferredYear];
    } else {
        $defaultYear = st_fetch_default_year_period($pdo);
        if ($defaultYear !== null) $year_ids = [$defaultYear];
    }
}

$yearPeriod = (string)reset($year_ids);

// 5) Определяем "итого" по данным (по выбранному году + типу местности)
$totalAreaCode = null;
$totalAreaName = null;

if ($yearPeriod !== '') {
    try {
        $total = st_detect_total_area($pdo, $yearPeriod, $areaTypeCode);
        if ($total) {
            $totalAreaCode = (string)($total['Area_code'] ?? '');
            $totalAreaName = (string)($total['Area_name'] ?? '');
            if ($totalAreaCode === '') $totalAreaCode = null;
        }
    } catch (Throwable $e) {
        error_log('students/ch1: st_detect_total_area error: ' . $e->getMessage());
    }
}

// Переименовываем итоговую строку только для UI + поднимаем наверх
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

// Дефолт по району: Итого по НСО
if ((!isset($_GET['org_type']) || empty($org_types)) && $totalAreaCode !== null) {
    $org_types = [$totalAreaCode];
}

$selectedAreaCode = (!empty($org_types) ? (string)reset($org_types) : '');

// Для запросов: добавляем chart_year_id, если его нет среди year_id[]
$year_ids_query = $year_ids;
if ($chart_year_id !== '' && !in_array($chart_year_id, $year_ids_query, true)) {
    $year_ids_query[] = $chart_year_id;
}

// 6) Данные для таблицы (все выбранные годы)
$rows = [];
$hasData = false;

try {
    $rows = st_fetch_students_totals($pdo, [
        'org_type' => null,                 // для таблицы берём все районы
        'year_id' => $year_ids_query,
        'locality_type' => $locality_types,
    ]);
    $hasData = !empty($rows);
} catch (Throwable $e) {
    error_log('students/ch1: st_fetch_students_totals error: ' . $e->getMessage());
    $rows = [];
    $hasData = false;
}

// Если выбран конкретный район (не итог) — вырезаем "итого" из таблицы, чтобы не было двойного учёта
if ($hasData && $totalAreaCode !== null && $selectedAreaCode !== '' && $selectedAreaCode !== $totalAreaCode) {
    $rows = array_values(array_filter($rows, static function (array $r) use ($totalAreaCode, $totalAreaName): bool {
        if (isset($r['Area_code']) && (string)$r['Area_code'] === (string)$totalAreaCode) return false;
        if ($totalAreaName !== null && isset($r['Area_name']) && (string)$r['Area_name'] === (string)$totalAreaName) return false;
        return true;
    }));
}

// 7) Годы для таблицы (из чекбоксов)
$yearsTable = array_values(array_unique(array_map('strval', (array)$year_ids)));
sort($yearsTable, SORT_NATURAL);

// Если chart_year_id не выбран — по умолчанию берём самый свежий из выбранных
if ($chart_year_id === '' && !empty($yearsTable)) {
    $chart_year_id = (string)end($yearsTable);
    reset($yearsTable);
}

$rankYearLabel = $chart_year_id !== '' ? $chart_year_id : ($yearsTable[0] ?? '');

// 8) Пивот для таблицы: строки = районы, колонки = годы
$table = []; // [Area_code => ['name'=>..., 'years'=>[Year_period=>val]]]

foreach ($rows as $r) {
    $ac = (string)($r['Area_code'] ?? '');
    $an = (string)($r['Area_name'] ?? '');
    $y = (string)($r['Year_period'] ?? '');
    $v = (float)($r['Total_students'] ?? 0);

    if ($ac === '' || $y === '') continue;

    if (!isset($table[$ac])) {
        $table[$ac] = [
            'name' => $an,
            'years' => [],
            'total' => 0.0,
        ];
    }

    // переименование для UI
    if ($totalAreaCode !== null && $ac === (string)$totalAreaCode) {
        $table[$ac]['name'] = 'Итого по НСО';
    }

    $table[$ac]['years'][$y] = ($table[$ac]['years'][$y] ?? 0.0) + $v;
    $table[$ac]['total'] += $v;
}

// Сортировка строк: Итого по НСО (если есть) сверху, потом по алфавиту
$areaKeys = array_keys($table);
usort($areaKeys, static function (string $a, string $b) use ($table, $totalAreaCode): int {
    $aIsTotal = ($totalAreaCode !== null && $a === (string)$totalAreaCode) ? 0 : 1;
    $bIsTotal = ($totalAreaCode !== null && $b === (string)$totalAreaCode) ? 0 : 1;
    if ($aIsTotal !== $bIsTotal) return $aIsTotal <=> $bIsTotal;
    return strcmp($table[$a]['name'] ?? '', $table[$b]['name'] ?? '');
});

// 9) Данные для line chart: выбранный район по всем доступным годам (из справочника)
$allYears = array_map(static fn($r) => (string)($r['id'] ?? ''), $years_data);
$allYears = array_values(array_filter($allYears, static fn($s) => $s !== ''));
sort($allYears, SORT_NATURAL);

$lineLabels = [];
$lineValues = [];

if ($selectedAreaCode !== '') {
    try {
        $lineRows = st_fetch_students_totals($pdo, [
            'org_type' => [$selectedAreaCode],
            'year_id' => $allYears,
            'locality_type' => $locality_types,
        ]);

        $map = [];
        foreach ($lineRows as $r) {
            $y = (string)($r['Year_period'] ?? '');
            if ($y === '') continue;
            $map[$y] = (float)($r['Total_students'] ?? 0);
        }

        foreach ($allYears as $y) {
            $lineLabels[] = $y;
            $lineValues[] = (float)($map[$y] ?? 0);
        }

    } catch (Throwable $e) {
        error_log('students/ch1: lineRows error: ' . $e->getMessage());
    }
}

// 10) Данные для рейтинга районов (bar) за выбранный год
$areaRankLabels = [];
$areaRankValues = [];
$areaRankCodes  = [];

if ($rankYearLabel !== '') {
    try {
        $rankRows = st_fetch_students_totals($pdo, [
            'org_type' => null,
            'year_id' => [$rankYearLabel],
            'locality_type' => $locality_types,
        ]);

        // агрегируем по району (на всякий случай) + сортируем по убыванию
        $tmp = [];
        foreach ($rankRows as $r) {
            $ac = (string)($r['Area_code'] ?? '');
            $an = (string)($r['Area_name'] ?? '');
            $v = (float)($r['Total_students'] ?? 0);
            if ($ac === '') continue;

            if (!isset($tmp[$ac])) $tmp[$ac] = ['code' => $ac, 'name' => $an, 'val' => 0.0];
            $tmp[$ac]['val'] += $v;
        }

        $items = array_values($tmp);
        usort($items, static function (array $a, array $b): int {
            $d = ($b['val'] <=> $a['val']);
            if ($d !== 0) return $d;
            return strcmp((string)$a['name'], (string)$b['name']);
        });

        // Берём топ-20, чтобы график был читаемый
        $items = array_slice($items, 0, 20);

        foreach ($items as $it) {
            $areaRankCodes[]  = (string)$it['code'];
            $areaRankLabels[] = (string)$it['name'];
            $areaRankValues[] = (float)$it['val'];
        }

        // Переименовываем итог в UI
        if ($totalAreaCode !== null) {
            foreach ($areaRankLabels as $i => $name) {
                if ((string)($areaRankCodes[$i] ?? '') === (string)$totalAreaCode) {
                    $areaRankLabels[$i] = 'Итого по НСО';
                }
            }
        }

    } catch (Throwable $e) {
        error_log('students/ch1: rankRows error: ' . $e->getMessage());
    }
}

// 11) Карточки (для выбранного года chart_year_id и выбранного района)
$cardTotalStudents = 0;
if ($selectedAreaCode !== '' && $rankYearLabel !== '') {
    foreach ($rows as $r) {
        if ((string)($r['Area_code'] ?? '') === $selectedAreaCode && (string)($r['Year_period'] ?? '') === $rankYearLabel) {
            $cardTotalStudents += (int)round((float)($r['Total_students'] ?? 0));
        }
    }
    // если выбран район и мы вырезали итого, то rows может не содержать выбранный район за rankYearLabel
    if ($cardTotalStudents === 0) {
        try {
            $one = st_fetch_students_totals($pdo, [
                'org_type' => [$selectedAreaCode],
                'year_id' => [$rankYearLabel],
                'locality_type' => $locality_types,
            ]);
            foreach ($one as $r) {
                $cardTotalStudents += (int)round((float)($r['Total_students'] ?? 0));
            }
        } catch (Throwable $e) {
            // ignore
        }
    }
}

// 12) Дата актуальности данных (20.09)
$now = new DateTimeImmutable('now');
$year = (int)$now->format('Y');
$anchorThisYear = DateTimeImmutable::createFromFormat('!d.m.Y', '20.09.' . $year);
if ($anchorThisYear instanceof DateTimeImmutable && $now < $anchorThisYear) {
    $year--;
}
$displayTime = sprintf('20.09.%04d', $year);

// Для подсветки выбранного района на графике
$highlightAreaCode = $selectedAreaCode;