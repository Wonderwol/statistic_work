<?php
declare(strict_types=1);
?>
<div class="results" id="tableView" style="margin-top: 20px; display: none;">
    <table>
        <thead>
        <tr>
            <th style="font-weight: bold;">Образовательные организации</th>
            <?php foreach ($yearsTable as $y): ?>
                <th style="text-align:center; font-weight:bold;"><?= safeEcho($y) ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>

        <tbody>
        <tr>
            <td style="font-weight:bold; padding-left: 9%;">НОШ д/сад</td>
            <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Nursery_school_primary'] ?></td><?php endforeach; ?>
        </tr>

        <tr>
            <td style="font-weight:bold; padding-left: 9%;">НОШ</td>
            <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Primary_school'] ?></td><?php endforeach; ?>
        </tr>

        <tr>
            <td style="font-weight:bold; padding-left: 9%;">ООШ</td>
            <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Basic_school'] ?></td><?php endforeach; ?>
        </tr>

        <tr>
            <td style="font-weight:bold; padding-left: 9%;">всего СОШ</td>
            <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['sec_sc_sum'] ?></td><?php endforeach; ?>
        </tr>

        <tr>
            <td style="font-weight:bold; padding-left: 9%;">СОШ</td>
            <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Secondary_school'] ?></td><?php endforeach; ?>
        </tr>

        <tr>
            <td style="font-weight:bold; padding-left: 9%;">СОШ с УИОП</td>
            <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Secondary_school_special'] ?></td><?php endforeach; ?>
        </tr>

        <tr>
            <td style="font-weight:bold; padding-left: 9%;">гимназии</td>
            <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Gymnasium'] ?></td><?php endforeach; ?>
        </tr>

        <tr>
            <td style="font-weight:bold; padding-left: 9%;">лицеи</td>
            <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Lyceum'] ?></td><?php endforeach; ?>
        </tr>

        <tr>
            <td style="font-weight:bold; padding-left: 9%;">кадетские корпуса</td>
            <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Cadet_corps'] ?></td><?php endforeach; ?>
        </tr>

        <tr>
            <td style="font-weight:bold; padding-left: 9%;">филиалы</td>
            <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Branches'] ?></td><?php endforeach; ?>
        </tr>

        <tr class="rowbr" style="background-color:#6d444b; color:#fff; font-weight:bold;">
            <td style="padding-left:9%;">итого ОО</td>
            <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Total_organizations'] ?></td><?php endforeach; ?>
        </tr>

        <tr>
            <td style="font-weight:bold; padding-left: 9%;">санаторные ОО</td>
            <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Sanatorium_schools'] ?></td><?php endforeach; ?>
        </tr>

        <tr>
            <td style="font-weight:bold; padding-left: 9%;">ОО для детей с ОВЗ</td>
            <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Special_needs_schools'] ?></td><?php endforeach; ?>
        </tr>

        <tr class='rowbr' style="background-color:#6d444b; color:#fff; font-weight:bold;">
            <td style="padding-left:9%;">итого дневные ОО</td>
            <?php foreach ($yearsTable as $y): ?>
                <td style="text-align:center;"><?= (int)$tableByYear[$y]['Total_organizations'] - (int)$tableByYear[$y]['Evening_schools'] ?></td>
            <?php endforeach; ?>
        </tr>

        <tr>
            <td style="font-weight:bold; padding-left: 9%;">вечерние ОО</td>
            <?php foreach ($yearsTable as $y): ?><td style="text-align:center;"><?= (int)$tableByYear[$y]['Evening_schools'] ?></td><?php endforeach; ?>
        </tr>
        </tbody>
    </table>
</div>
