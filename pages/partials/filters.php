<?php
declare(strict_types=1);
?>
<form method="GET" action="">
    <div class="filter-row">
        <!-- Тип организации (радиокнопки) -->
        <div class="filter-group">
            <div class="dropdown-search-container" id="org_type-container">
                <input type="text"
                       class="dropdown-search-input"
                       placeholder="Выберите уровень..."
                       id="org_type-search"
                       readonly
                       style="cursor: pointer;">

                <div class="selected-count" id="org_type-selected-count">
                    <span class="clear-selection" id="org_type-clear">(очистить)</span>
                </div>

                <div class="dropdown-checkbox-group" id="org_type-group">
                    <?php foreach ($org_types_data as $type): ?>
                        <div class="checkbox-item" data-org-type-id="<?= safeEcho($type['id']) ?>">
                            <input type="radio"
                                   id="org_type_<?= safeEcho($type['id']) ?>"
                                   name="org_type"
                                   value="<?= safeEcho($type['id']) ?>"
                                <?= (!empty($org_types) && in_array($type['id'], (array)$org_types)) ? 'checked' : '' ?>>
                            <label for="org_type_<?= safeEcho($type['id']) ?>">
                                <?= safeEcho($type['name']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                    <div class="no-results">Ничего не найдено</div>
                </div>
            </div>
        </div>

        <!-- Учебный год (чекбоксы) -->
        <div class="filter-group">
            <div class="dropdown-search-container" id="year-container">
                <input type="text"
                       class="dropdown-search-input"
                       placeholder="Выберите год/годы..."
                       id="year-search"
                       readonly
                       style="cursor: pointer;">

                <div class="selected-count" id="year-selected-count">
                    Выбрано: <span id="year-count">0</span>
                    <span class="clear-selection" id="year-clear">(очистить)</span>
                    <span style="float: right;" class="select-all" id="year-select-all">Выбрать все</span>
                </div>

                <div class="dropdown-checkbox-group" id="year-group">
                    <?php foreach ($years_data as $year): ?>
                        <div class="checkbox-item" data-year-id="<?= safeEcho($year['id']) ?>">
                            <input type="checkbox"
                                   id="year_<?= safeEcho($year['id']) ?>"
                                   name="year_id[]"
                                   value="<?= safeEcho($year['id']) ?>"
                                <?= (is_array($year_ids) && in_array($year['id'], $year_ids)) ? 'checked' : '' ?>>
                            <label for="year_<?= safeEcho($year['id']) ?>">
                                <?= safeEcho($year['name']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                    <div class="no-results">Ничего не найдено</div>
                </div>
            </div>
        </div>

        <!-- Тип местности (радиокнопки) -->
        <div class="filter-group">
            <div class="dropdown-search-container" id="locality-container">
                <input type="text"
                       class="dropdown-search-input"
                       placeholder="Выберите тип..."
                       id="locality-search"
                       readonly
                       style="cursor: pointer;">

                <div class="selected-count" id="locality-selected-count">
                    <span class="clear-selection" id="locality-clear">(очистить)</span>
                </div>

                <div class="dropdown-checkbox-group" id="locality-group">
                    <?php foreach ($locality_types_data as $type): ?>
                        <div class="checkbox-item" data-locality-id="<?= safeEcho($type['id']) ?>">
                            <input type="radio"
                                   id="locality_<?= safeEcho($type['id']) ?>"
                                   name="locality_type"
                                   value="<?= safeEcho($type['id']) ?>"
                                <?= (!empty($locality_types) && in_array($type['id'], (array)$locality_types)) ? 'checked' : '' ?>>
                            <label for="locality_<?= safeEcho($type['id']) ?>">
                                <?= safeEcho($type['name']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                    <div class="no-results">Ничего не найдено</div>
                </div>
            </div>
        </div>
    </div>

    <div class="buttons">
        <button type="submit" class="btn-primary">Применить фильтры</button>
        <button type="button" class="btn-secondary" onclick="window.location.href='by_type.php'">Сбросить</button>
    </div>
</form>
