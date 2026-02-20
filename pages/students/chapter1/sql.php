<?php
declare(strict_types=1);

/**
 * Раздел: Обучающиеся образовательных организаций
 * Глава 1: Численность обучающихся (агрегация по территориям/годам)
 *
 * В проекте уже есть аналогичные функции для edu_orgs. Здесь — отдельный набор,
 * но с теми же принципами безопасности (валидация IN-списков и Year_period).
 */

function st_norm_list(mixed $v): array
{
    if ($v === null) return [];
    if (is_array($v)) return $v;
    if ($v === '') return [];
    return [$v];
}

function st_only_ints(array $arr): array
{
    $out = [];
    foreach ($arr as $v) {
        $s = trim((string)$v);
        if ($s !== '' && preg_match('/^\d+$/', $s)) {
            $out[] = (int)$s;
        }
    }
    $out = array_values(array_unique($out));
    sort($out);
    return $out;
}

function st_only_year_periods(array $arr): array
{
    $out = [];
    foreach ($arr as $v) {
        $s = trim((string)$v);
        if ($s === '') continue;

        $s = str_replace(["\u{2013}", "\u{2014}", '–', '—'], '-', $s);
        $s = preg_replace('/\s+/u', '', $s);

        if (preg_match('/^\d{4}([\-\/]\d{4})?$/', $s)) {
            $out[] = $s;
        }
    }

    $out = array_values(array_unique($out));
    sort($out, SORT_NATURAL);
    return $out;
}

function st_table_exists(PDO $pdo, string $tableName, string $schema = 'dbo'): bool
{
    $sql = "
        SELECT 1
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$schema, $tableName]);
    return (bool)$stmt->fetchColumn();
}

function st_column_exists(PDO $pdo, string $tableName, string $columnName, string $schema = 'dbo'): bool
{
    $sql = "
        SELECT 1
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$schema, $tableName, $columnName]);
    return (bool)$stmt->fetchColumn();
}

/**
 * Возвращает конфиг реальной таблицы обучающихся и нужных колонок.
 * Делается с подстраховками, чтобы код не падал при небольших отличиях схемы.
 */
function st_resolve_students_source(PDO $pdo): array
{
    $schema = 'dbo';

    $candidates = [
        'Area_students',
        'Area_Students',
        'area_students',
    ];

    $table = null;
    foreach ($candidates as $t) {
        if (st_table_exists($pdo, $t, $schema)) {
            $table = $t;
            break;
        }
    }

    if ($table === null) {
        // Последняя попытка — любой стол/вью с "students" в имени.
        $sql = "
            SELECT TOP 1 TABLE_NAME
            FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA = ?
              AND TABLE_NAME LIKE '%students%'
            ORDER BY TABLE_NAME
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$schema]);
        $maybe = $stmt->fetchColumn();
        if ($maybe) $table = (string)$maybe;
    }

    if ($table === null) {
        throw new RuntimeException('Не найдена таблица с данными обучающихся (ожидалась Area_students).');
    }

    $areaCol = st_column_exists($pdo, $table, 'Area_code', $schema) ? 'Area_code' : null;
    $areaTypeCol = st_column_exists($pdo, $table, 'Area_type_code', $schema) ? 'Area_type_code' : null;
    $yearCol = st_column_exists($pdo, $table, 'Year_period', $schema) ? 'Year_period' : null;

    if ($areaCol === null || $yearCol === null) {
        throw new RuntimeException('В таблице ' . $table . ' не найдены обязательные колонки Area_code и/или Year_period.');
    }

    $measureCandidates = [
        'Area_students_count',
        'Area_students',
        'Students_count',
        'Students_amount',
        'Students_total',
        'Students',
    ];
    $measureCol = null;
    foreach ($measureCandidates as $c) {
        if (st_column_exists($pdo, $table, $c, $schema)) {
            $measureCol = $c;
            break;
        }
    }

    if ($measureCol === null) {
        // Берём первую числовую колонку, похожую на "count"/"amount".
        $sql = "
            SELECT TOP 1 COLUMN_NAME
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
              AND DATA_TYPE IN ('int','bigint','numeric','decimal','float','real')
              AND (COLUMN_NAME LIKE '%count%' OR COLUMN_NAME LIKE '%amount%' OR COLUMN_NAME LIKE '%total%')
            ORDER BY
              CASE
                WHEN COLUMN_NAME LIKE '%count%' THEN 1
                WHEN COLUMN_NAME LIKE '%amount%' THEN 2
                WHEN COLUMN_NAME LIKE '%total%' THEN 3
                ELSE 4
              END,
              COLUMN_NAME
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$schema, $table]);
        $maybe = $stmt->fetchColumn();
        if ($maybe) $measureCol = (string)$maybe;
    }

    if ($measureCol === null) {
        throw new RuntimeException('Не найдена колонка с количеством обучающихся в таблице ' . $table . '.');
    }

    $deletedCol = st_column_exists($pdo, $table, 'deleted', $schema) ? 'deleted' : null;

    return [
        'schema' => $schema,
        'table' => $table,
        'col_area' => $areaCol,
        'col_area_type' => $areaTypeCol,   // может быть null
        'col_year' => $yearCol,
        'col_measure' => $measureCol,
        'col_deleted' => $deletedCol,      // может быть null
    ];
}

function st_fetch_years_data(PDO $pdo): array
{
    $src = st_resolve_students_source($pdo);

    $table = $src['table'];
    $schema = $src['schema'];
    $yearCol = $src['col_year'];

    $where = [];
    $params = [];

    if (!empty($src['col_deleted'])) {
        $where[] = "s.[{$src['col_deleted']}] = 0";
    }

    $sql = "
        SELECT DISTINCT
            s.[$yearCol] AS id,
            s.[$yearCol] AS name
        FROM [$schema].[$table] s
        " . (!empty($where) ? ('WHERE ' . implode(' AND ', $where)) : '') . "
        ORDER BY s.[$yearCol]
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function st_fetch_default_year_period(PDO $pdo): ?string
{
    $src = st_resolve_students_source($pdo);

    $table = $src['table'];
    $schema = $src['schema'];
    $yearCol = $src['col_year'];

    $where = ["s.[$yearCol] IS NOT NULL", "LTRIM(RTRIM(s.[$yearCol])) <> ''"];
    $params = [];

    if (!empty($src['col_deleted'])) {
        $where[] = "s.[{$src['col_deleted']}] = 0";
    }

    $sql = "
        SELECT TOP 1
            s.[$yearCol]
        FROM [$schema].[$table] s
        WHERE " . implode(' AND ', $where) . "
        ORDER BY
            TRY_CONVERT(int, LEFT(REPLACE(REPLACE(REPLACE(LTRIM(RTRIM(s.[$yearCol])),'–','-'),'—','-'),' ',''), 4)) DESC,
            TRY_CONVERT(int, RIGHT(REPLACE(REPLACE(REPLACE(LTRIM(RTRIM(s.[$yearCol])),'–','-'),'—','-'),' ',''), 4)) DESC,
            s.[$yearCol] DESC
    ";

    $v = $pdo->query($sql)->fetchColumn();
    if ($v === false || $v === null) return null;

    $v = trim((string)$v);
    return $v !== '' ? $v : null;
}

function st_fetch_org_types_data(PDO $pdo): array
{
    $sql = "
        SELECT
            da.Area_code AS id,
            da.Area_name AS name
        FROM dat_Area da
        ORDER BY da.Area_name
    ";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

function st_fetch_locality_types_data(PDO $pdo): array
{
    $sql = "
        SELECT
            at.Area_type_code AS id,
            at.Area_type_name AS name
        FROM dat_Area_types at
        ORDER BY at.Area_type_code
    ";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Базовая выборка: totals обучающихся по Area/Year.
 * Возвращает строки: Area_code, Area_name, Year_period, Total_students.
 */
function st_fetch_students_totals(PDO $pdo, array $filters): array
{
    $src = st_resolve_students_source($pdo);

    $schema = $src['schema'];
    $table = $src['table'];

    $areaCol = $src['col_area'];
    $areaTypeCol = $src['col_area_type'];
    $yearCol = $src['col_year'];
    $measureCol = $src['col_measure'];

    $org_type_raw = $filters['org_type'] ?? null;
    $year_ids_raw = $filters['year_id'] ?? [];
    $locality_raw = $filters['locality_type'] ?? null;

    $org_type_list = st_only_ints(st_norm_list($org_type_raw));
    $year_ids_list = st_only_year_periods(st_norm_list($year_ids_raw));
    $locality_list = st_only_ints(st_norm_list($locality_raw));

    $where = [];
    $params = [];

    if (!empty($src['col_deleted'])) {
        $where[] = "s.[{$src['col_deleted']}] = 0";
    }

    if (!empty($locality_list) && $areaTypeCol !== null) {
        $ph = implode(',', array_fill(0, count($locality_list), '?'));
        $where[] = "s.[$areaTypeCol] IN ($ph)";
        $params = array_merge($params, $locality_list);
    }

    if (!empty($org_type_list)) {
        $ph = implode(',', array_fill(0, count($org_type_list), '?'));
        $where[] = "s.[$areaCol] IN ($ph)";
        $params = array_merge($params, $org_type_list);
    }

    if (!empty($year_ids_list)) {
        $ph = implode(',', array_fill(0, count($year_ids_list), '?'));
        $where[] = "s.[$yearCol] IN ($ph)";
        $params = array_merge($params, $year_ids_list);
    }

    $sql = "
        SELECT
            s.[$areaCol] AS Area_code,
            da.Area_name,
            s.[$yearCol] AS Year_period,
            SUM(COALESCE(TRY_CONVERT(float, s.[$measureCol]), 0)) AS Total_students
        FROM [$schema].[$table] s
        JOIN dat_Area da ON da.Area_code = s.[$areaCol]
        " . (!empty($where) ? ('WHERE ' . implode(' AND ', $where)) : '') . "
        GROUP BY s.[$areaCol], da.Area_name, s.[$yearCol]
        ORDER BY da.Area_name, s.[$yearCol]
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Определяем "итого по области" как строку с максимальным суммарным количеством обучающихся.
 */
function st_detect_total_area(PDO $pdo, string $yearPeriod, int $areaTypeCode): ?array
{
    $src = st_resolve_students_source($pdo);

    $schema = $src['schema'];
    $table = $src['table'];

    $areaCol = $src['col_area'];
    $areaTypeCol = $src['col_area_type'];
    $yearCol = $src['col_year'];
    $measureCol = $src['col_measure'];

    $where = ["s.[$yearCol] = ?"];
    $params = [$yearPeriod];

    if (!empty($src['col_deleted'])) {
        $where[] = "s.[{$src['col_deleted']}] = 0";
    }

    if ($areaTypeCol !== null) {
        $where[] = "s.[$areaTypeCol] = ?";
        $params[] = $areaTypeCode;
    }

    $sql = "
        SELECT TOP 1
            s.[$areaCol] AS Area_code,
            da.Area_name,
            SUM(COALESCE(TRY_CONVERT(float, s.[$measureCol]), 0)) AS total_students
        FROM [$schema].[$table] s
        JOIN dat_Area da ON da.Area_code = s.[$areaCol]
        WHERE " . implode(' AND ', $where) . "
        GROUP BY s.[$areaCol], da.Area_name
        ORDER BY total_students DESC, s.[$areaCol] ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}