<?php
declare(strict_types=1);
?>
<div class="chart-container">
    <div class="chart-box">
        <div class="chart-header">
            <h3>Структура по типам <?= $show_single_year_charts ? "($years[0])" : '(суммарно)' ?></h3>
        </div>
        <div class="chart-wrap chart-wrap--big no-hover">
            <canvas id="pieChart"></canvas>
        </div>
    </div>
</div>
