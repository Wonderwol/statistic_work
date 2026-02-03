<?php
require_once 'config.php';

// Получаем параметры фильтрации
$organization_id = $_GET['organization_id'] ?? '';
$grade = $_GET['grade'] ?? '';
$has_disabilities = $_GET['has_disabilities'] ?? '';
$year_id = $_GET['year_id'] ?? '';

// Получаем список организаций для фильтра
$organizations = $pdo->query("SELECT id, name FROM organizations ORDER BY name")->fetchAll();
$years = $pdo->query("SELECT id, name FROM educational_years ORDER BY start_year DESC")->fetchAll();
$grades = range(1, 11);

// Формируем запрос
$sql = "SELECT 
            s.*,
            o.name as organization_name,
            t.name as territory_name,
            ey.name as educational_year
        FROM students s
        JOIN organizations o ON s.organization_id = o.id
        JOIN territories t ON o.territory_id = t.id
        JOIN educational_years ey ON s.educational_year_id = ey.id
        WHERE 1=1";

$params = [];

if (!empty($organization_id)) {
    $sql .= " AND s.organization_id = ?";
    $params[] = $organization_id;
}

if (!empty($grade)) {
    $sql .= " AND s.grade = ?";
    $params[] = $grade;
}

if ($has_disabilities !== '') {
    $sql .= " AND s.has_disabilities = ?";
    $params[] = (int)$has_disabilities;
}

if (!empty($year_id)) {
    $sql .= " AND s.educational_year_id = ?";
    $params[] = $year_id;
}

$sql .= " ORDER BY t.name, o.name, s.grade";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students_data = $stmt->fetchAll();

// Агрегированные данные
$total_students = array_sum(array_column($students_data, 'count'));
$total_with_disabilities = array_sum(array_column(
    array_filter($students_data, fn($s) => $s['has_disabilities'] == 1),
    'count'
));
$total_foreign = array_sum(array_column(
    array_filter($students_data, fn($s) => $s['is_foreign'] == 1),
    'count'
));
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Обучающиеся</title>
    <style>
        /* Стили из index.php */
        body { font-family: Arial, sans-serif; margin: 20px; }
        .filters { background: #f4f4f4; padding: 20px; margin-bottom: 20px; border-radius: 5px; }
        .filter-group { margin-bottom: 10px; }
        label { display: inline-block; width: 200px; }
        select, input { padding: 5px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #007bff; color: white; }
        .stats { display: flex; gap: 20px; margin: 20px 0; }
        .stat-box { background: #e9ecef; padding: 15px; border-radius: 5px; flex: 1; }
        .stat-value { font-size: 24px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>👨‍🎓 Обучающиеся</h1>
    
    <div class="filters">
        <form method="GET">
            <div class="filter-group">
                <label>Организация:</label>
                <select name="organization_id">
                    <option value="">Все организации</option>
                    <?php foreach ($organizations as $org): ?>
                        <option value="<?= safeEcho($org['id']) ?>" <?= ($organization_id == $org['id']) ? 'selected' : '' ?>>
                            <?= safeEcho($org['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label>Класс:</label>
                <select name="grade">
                    <option value="">Все классы</option>
                    <?php foreach ($grades as $g): ?>
                        <option value="<?= $g ?>" <?= ($grade == $g) ? 'selected' : '' ?>><?= $g ?> класс</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label>ОВЗ:</label>
                <select name="has_disabilities">
                    <option value="">Все</option>
                    <option value="1" <?= ($has_disabilities === '1') ? 'selected' : '' ?>>Только с ОВЗ</option>
                    <option value="0" <?= ($has_disabilities === '0') ? 'selected' : '' ?>>Без ОВЗ</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label>Учебный год:</label>
                <select name="year_id">
                    <option value="">Все годы</option>
                    <?php foreach ($years as $year): ?>
                        <option value="<?= safeEcho($year['id']) ?>" <?= ($year_id == $year['id']) ? 'selected' : '' ?>>
                            <?= safeEcho($year['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit">Применить фильтры</button>
            <button type="button" onclick="window.location.href='students.php'">Сбросить</button>
            <a href="index.php" style="margin-left: 20px;">← Назад</a>
        </form>
    </div>
    
    <div class="stats">
        <div class="stat-box">
            <div>Всего обучающихся</div>
            <div class="stat-value"><?= number_format($total_students) ?></div>
        </div>
        <div class="stat-box">
            <div>С ОВЗ</div>
            <div class="stat-value"><?= number_format($total_with_disabilities) ?></div>
        </div>
        <div class="stat-box">
            <div>Иностранные граждане</div>
            <div class="stat-value"><?= number_format($total_foreign) ?></div>
        </div>
    </div>
    
    <?php if (!empty($students_data)): ?>
    <table>
        <thead>
            <tr>
                <th>Организация</th>
                <th>Территория</th>
                <th>Класс</th>
                <th>Учебный год</th>
                <th>Количество</th>
                <th>ОВЗ</th>
                <th>Иностранцы</th>
                <th>Смена</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students_data as $student): ?>
            <tr>
                <td><?= safeEcho($student['organization_name']) ?></td>
                <td><?= safeEcho($student['territory_name']) ?></td>
                <td><?= safeEcho($student['grade'] ?? '-') ?></td>
                <td><?= safeEcho($student['educational_year']) ?></td>
                <td><?= safeEcho($student['count']) ?></td>
                <td><?= $student['has_disabilities'] ? 'Да' : 'Нет' ?></td>
                <td><?= $student['is_foreign'] ? 'Да' : 'Нет' ?></td>
                <td><?= safeEcho($student['shift'] ?? '-') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>Нет данных по заданным фильтрам.</p>
    <?php endif; ?>
</body>
</html>