<?php
declare(strict_types=1);

function ch3_norm_list(mixed $v): array
{
    if ($v === null) return [];
    if (is_array($v)) return $v;
    if ($v === '') return [];
    return [$v];
}

function ch3_only_ints(array $arr): array
{
    $out = [];
    foreach ($arr as $v) {
        $s = trim((string)$v);
        if ($s !== '' && preg_match('/^\d+$/', $s)) $out[] = (int)$s;
    }
    $out = array_values(array_unique($out));
    sort($out);
    return $out;
}

function ch3_only_year_periods(array $arr): array
{
    $out = [];
    foreach ($arr as $v) {
        $s = trim((string)$v);
        if ($s === '') continue;

        $s = str_replace(["\u{2013}", "\u{2014}", '–', '—'], '-', $s);
        $s = preg_replace('/\s+/u', '', $s);

        if (preg_match('/^\d{4}([\-\/]\d{4})?$/', $s)) $out[] = $s;
    }
    $out = array_values(array_unique($out));
    sort($out, SORT_NATURAL);
    return $out;
}

function ch3_fetch_years_data(PDO $pdo): array
{
    $sql = "
        SELECT DISTINCT
            ao.Year_period AS id,
            ao.Year_period AS name
        FROM Area_organizations ao
        WHERE ao.deleted = 0
        ORDER BY ao.Year_period
    ";
    return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

function ch3_fetch_org_types_data(PDO $pdo): array
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

function ch3_fetch_locality_types_data(PDO $pdo): array
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

function ch3_fetch_organizations(PDO $pdo, array $filters): array
{
    $org_type_raw = $filters['org_type'] ?? null;
    $year_ids_raw = $filters['year_id'] ?? [];
    $locality_raw = $filters['locality_type'] ?? null;

    $org_type_list = ch3_only_ints(ch3_norm_list($org_type_raw));
    $year_ids_list = ch3_only_year_periods(ch3_norm_list($year_ids_raw));
    $locality_list = ch3_only_ints(ch3_norm_list($locality_raw));

    if (empty($locality_list)) $locality_list = [3];

    $sql = "
        SELECT
            ao.Area_code AS Area_code,
            da.Area_name,
            ao.Year_period,

            SUM(CASE WHEN ao.Organization_type_code = 1  THEN ao.Area_organizations_count ELSE 0 END) AS Nursery_school_primary,
            SUM(CASE WHEN ao.Organization_type_code = 2  THEN ao.Area_organizations_count ELSE 0 END) AS Primary_school,
            SUM(CASE WHEN ao.Organization_type_code = 3  THEN ao.Area_organizations_count ELSE 0 END) AS Basic_school,

            SUM(CASE WHEN ao.Organization_type_code BETWEEN 5 AND 9 THEN ao.Area_organizations_count ELSE 0 END) AS Secondary_school_sum,
            SUM(CASE WHEN ao.Organization_type_code = 10 THEN ao.Area_organizations_count ELSE 0 END) AS Branches,
            SUM(CASE WHEN ao.Organization_type_code = 11 THEN ao.Area_organizations_count ELSE 0 END) AS Sanatorium_schools,
            SUM(CASE WHEN ao.Organization_type_code = 12 THEN ao.Area_organizations_count ELSE 0 END) AS Special_needs_schools,
            SUM(CASE WHEN ao.Organization_type_code = 13 THEN ao.Area_organizations_count ELSE 0 END) AS Evening_schools,

            SUM(CASE WHEN ao.Organization_type_code IN (1,2,3,5,6,7,8,9,11,12,13)
                     THEN ao.Area_organizations_count ELSE 0 END) AS Total_organizations

        FROM Area_organizations ao
        JOIN dat_Area da ON da.Area_code = ao.Area_code
        WHERE ao.deleted = 0
    ";

    $params = [];

    $ph = implode(',', array_fill(0, count($locality_list), '?'));
    $sql .= " AND ao.Area_type_code IN ($ph)";
    $params = array_merge($params, $locality_list);

    if (!empty($org_type_list)) {
        $ph = implode(',', array_fill(0, count($org_type_list), '?'));
        $sql .= " AND ao.Area_code IN ($ph)";
        $params = array_merge($params, $org_type_list);
    }

    if (!empty($year_ids_list)) {
        $ph = implode(',', array_fill(0, count($year_ids_list), '?'));
        $sql .= " AND ao.Year_period IN ($ph)";
        $params = array_merge($params, $year_ids_list);
    }

    $sql .= "
        GROUP BY ao.Area_code, da.Area_name, ao.Year_period
        ORDER BY da.Area_name, ao.Year_period
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
