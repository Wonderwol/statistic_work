<?php
require_once 'config.php';

// Получаем сводные данные
$reports = [
    'Организации по типам' => $pdo->query("
        SELECT type, COUNT(*) as count 
        FROM organizations 
        GROUP BY type 
        ORDER BY count DESC
    ")->fetchAll(),
    
    'Обучающиеся по территориям' => $pdo->query("
        SELECT t.name as territory, SUM(s.count) as total_students
        FROM students s
        JOIN organizations o ON s.organization_id = o.id
        JOIN territories t ON o.territory_id = t.id
        GROUP BY t.name
        ORDER BY total_students DESC
    ")->fetchAll(),
    
    'Сотрудники по возрасту' => $pdo->query("
        SELECT age_group, COUNT(*) as count
        FROM employees
        GROUP BY age_group
        ORDER BY 
            CASE age_group
                WHEN 'до 25' THEN 1
                WHEN '25-35' THEN 2
                WHEN '35-45' THEN 3
                WHEN '45-55' THEN 4
                WHEN '55-65' THEN 5
                WHEN '65+' THEN 6
                ELSE 7
            END
    ")->fetchAll(),
    
    'Организации с доступом для маломобильных' => $pdo->query("
        SELECT p.mobility_access, COUNT(DISTINCT p.organization_id) as count
        FROM property p
        GROUP BY p.mobility_access
        ORDER BY 
            CASE p.mobility_access
                WHEN 'полная' THEN 1
                WHEN 'частичная' THEN 2
                WHEN 'отсутствует' THEN 3
                ELSE 4
            END
    ")->fetchAll()
];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Отчеты</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        .report-section { margin-bottom: 40px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #28a745; color: white; }
        .back-link { display: block; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>📈 Отчеты и аналитика</h1>
    <a href="by_type.php" class="back-link">← Назад к списку организаций</a>
    
    <?php foreach ($reports as $title => $data): ?>
    <div class="report-section">
        <h2><?= safeEcho($title) ?></h2>
        <?php if (!empty($data)): ?>
        <table>
            <thead>
                <tr>
                    <?php foreach (array_keys($data[0]) as $column): ?>
                        <th><?= safeEcho(ucfirst(str_replace('_', ' ', $column))) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                <tr>
                    <?php foreach ($row as $value): ?>
                        <td><?= safeEcho($value) ?></td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>Нет данных для отображения.</p>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</body>
</html>