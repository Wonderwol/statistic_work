<?php
declare(strict_types=1);

/**
 * Нормализуем вход: из GET может прийти строка, массив, пусто.
 */
function index_norm_list(mixed $v): array
{
    if ($v === null) return [];
    if (is_array($v)) return $v;
    if ($v === '') return [];
    return [$v];
}

/**
 * Оставляем только целые числа (для IN (...)).
 */
function index_only_ints(array $arr): array
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

/**
 * DISTINCT Years
 */
function index_fetch_years_data(PDO $pdo): array
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

/**
 * Районы/территории (dat_Area)
 */
function index_fetch_org_types_data(PDO $pdo): array
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

/**
 * Тип местности (dat_Area_types)
 */
function index_fetch_locality_types_data(PDO $pdo): array
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
 * Последнее обновление данных
 */
function index_fetch_last_update(PDO $pdo): ?string
{
    $sql = "
        SELECT MAX(ao.Updated_date) AS last_update
        FROM Area_organizations ao
        WHERE ao.deleted = 0
    ";
    $v = $pdo->query($sql)->fetchColumn();
    return $v ? (string)$v : null;
}

/**
 * Основная выборка организаций с фильтрами.
 * Важно: возвращает те же поля-агрегаты, которые у тебя уже используются в index.php.
 *
 * $filters:
 * - org_type (Area_code) — одно значение (radio)
 * - year_id[] (Year_period) — массив (checkbox)
 * - locality_type (Area_type_code) — одно значение (radio), по умолчанию 3 если не задано
 */
function index_fetch_organizations(PDO $pdo, array $filters): array
{
    $org_type_raw = $filters['org_type'] ?? null;         // radio: одно значение
    $year_ids_raw = $filters['year_id'] ?? [];            // checkbox: массив
    $locality_raw = $filters['locality_type'] ?? null;    // radio: одно значение

    $org_type_list = index_only_ints(index_norm_list($org_type_raw));
    $year_ids_list = index_only_ints(index_norm_list($year_ids_raw));
    $locality_list = index_only_ints(index_norm_list($locality_raw));

    // По умолчанию "Всего" (как у тебя было раньше)
    if (empty($locality_list)) {
        $locality_list = [3];
    }

    $sql = "
        SELECT
            da.Area_name,
            ao.Year_period,

            SUM(CASE WHEN ao.Organization_type_code = 1  THEN ao.Area_organizations_count ELSE 0 END) AS Nursery_school_primary,
            SUM(CASE WHEN ao.Organization_type_code = 2  THEN ao.Area_organizations_count ELSE 0 END) AS Primary_school,
            SUM(CASE WHEN ao.Organization_type_code = 3  THEN ao.Area_organizations_count ELSE 0 END) AS Basic_school,

            SUM(CASE WHEN ao.Organization_type_code BETWEEN 5 AND 9 THEN ao.Area_organizations_count ELSE 0 END) AS Secondary_school_sum,
            SUM(CASE WHEN ao.Organization_type_code = 5  THEN ao.Area_organizations_count ELSE 0 END) AS Secondary_school,
            SUM(CASE WHEN ao.Organization_type_code = 6  THEN ao.Area_organizations_count ELSE 0 END) AS Secondary_school_special,
            SUM(CASE WHEN ao.Organization_type_code = 7  THEN ao.Area_organizations_count ELSE 0 END) AS Gymnasium,
            SUM(CASE WHEN ao.Organization_type_code = 8  THEN ao.Area_organizations_count ELSE 0 END) AS Lyceum,
            SUM(CASE WHEN ao.Organization_type_code = 9  THEN ao.Area_organizations_count ELSE 0 END) AS Cadet_corps,

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

    // locality_type (всегда есть, минимум [3])
    $ph = implode(',', array_fill(0, count($locality_list), '?'));
    $sql .= " AND ao.Area_type_code IN ($ph)";
    $params = array_merge($params, $locality_list);

    // org_type (radio) — если выбран
    if (!empty($org_type_list)) {
        // обычно radio -> одно значение, но обработаем и массив
        $ph = implode(',', array_fill(0, count($org_type_list), '?'));
        $sql .= " AND ao.Area_code IN ($ph)";
        $params = array_merge($params, $org_type_list);
    }

    // year_id[] — если выбраны годы
    if (!empty($year_ids_list)) {
        $ph = implode(',', array_fill(0, count($year_ids_list), '?'));
        $sql .= " AND ao.Year_period IN ($ph)";
        $params = array_merge($params, $year_ids_list);
    }

    $sql .= "
        GROUP BY da.Area_name, ao.Year_period
        ORDER BY da.Area_name, ao.Year_period
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
