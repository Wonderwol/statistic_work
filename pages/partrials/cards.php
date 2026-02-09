<?php
declare(strict_types=1);
?>
<div class="stat-card" style="width: 100%;">
    <h3>Общеобразовательных организаций - всего</h3>
    <div class="stat-value" style="display: inline-block;">
        <?= (int)$cards['total_all'] ?>
    </div>
</div>

<div class="statistics">
    <div class="stat-card">
        <h3>Начальные школы - детские сады</h3>
        <div class="stat-value"><?= (int)$cards['nursery'] ?></div>
    </div>
    <div class="stat-card">
        <h3>Начальные общеобразовательные школы</h3>
        <div class="stat-value"><?= (int)$cards['primary'] ?></div>
    </div>
</div>

<div class="statistics">
    <div class="stat-card">
        <h3>Основные общеобразовательные школы</h3>
        <div class="stat-value"><?= (int)$cards['basic'] ?></div>
    </div>
    <div class="stat-card">
        <h3>Средние общеобразовательные школы</h3>
        <div class="stat-value"><?= (int)$cards['secondary_sum'] ?></div>
    </div>
</div>

<div class="statistics">
    <div class="stat-card">
        <h3>Санаторные общеобразовательные организации</h3>
        <div class="stat-value"><?= (int)$cards['sanatorium'] ?></div>
    </div>
    <div class="stat-card">
        <h3 style="font-size: 14px;">Школы для детей с ограниченными возможностями здоровья</h3>
        <div class="stat-value"><?= (int)$cards['special_needs'] ?></div>
    </div>
</div>

<div class="statistics">
    <div class="stat-card">
        <h3>Вечерние общеобразовательные организации</h3>
        <div class="stat-value"><?= (int)$cards['evening'] ?></div>
    </div>
    <div class="stat-card">
        <h3>Филиалы</h3>
        <div class="stat-value"><?= (int)$cards['branches'] ?></div>
    </div>
</div>
