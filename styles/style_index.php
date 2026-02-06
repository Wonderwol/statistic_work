<style>
:root {
    --primary-color: #6d444b;
    --primary-light: #eadee0;
    --primary-hover: #98fb98;
    --secondary-color: #3498db;
    --success-color: #2ecc71;
    --danger-color: #e74c3c;
    --light-gray: #f5f5f5;
    --medium-gray: #ddd;
    --dark-gray: #333;
    --white: #ffffff;
    --shadow: 0 2px 10px rgba(0,0,0,0.1);
    --border-radius: 8px;
    --transition: all 0.3s ease;
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #ffffff;
    color: var(--dark-gray);
    line-height: 1.6;
    margin-left: 20%;
    margin-right: 20%;
}

/* Основная структура */
.main-wrapper {
    display: flex;
    min-height: calc(100vh - 120px);
    max-width: 1600px;
    margin: 0 auto;
    padding: 0 20px;
    gap: 30px;
}

.container {
    flex: 1;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px 0;
}

/* Заголовок */
header {
    background-color: var(--primary-color);
    color: var(--white);
    padding: 15px 0;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.header-content {
    max-width: 1600px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Левая навигация */
.left-navigation {
    width: 280px;
    flex-shrink: 0;
    margin-top: 20px;
}

.nav-panel {
    background-color: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    padding: 20px;
    margin-bottom: 30px;
    position: sticky;
    top: 20px;
}

.nav-panel h2 {
    color: var(--primary-color);
    margin-bottom: 20px;
    font-size: 18px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--primary-light);
}

.nav-section {
    margin-bottom: 25px;
}

.nav-section:last-child {
    margin-bottom: 0;
}

.nav-section-title {
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 10px;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.nav-menu {
    list-style: none;
}

.nav-menu li {
    margin-bottom: 5px;
}

.nav-menu a {
    display: block;
    padding: 10px 12px;
    text-decoration: none;
    color: var(--dark-gray);
    border-radius: 4px;
    transition: var(--transition);
    border-left: 3px solid transparent;
    font-size: 14px;
}

.nav-menu a:hover {
    background-color: var(--primary-hover);
    color: black;
    border-left-color: var(--primary-color);
}

.nav-menu a.active {
    background-color: var(--primary-color);
    color: var(--white);
    border-left-color: var(--primary-hover);
}

/* Основная область контента */
.content-area {
    flex: 1;
}

/* Заголовки */
h1 {
    color: var(--primary-color);
    font-weight: 600;
    font-size: 24px;
    margin-bottom: 10px;
}

.subtitle {
    color: #666;
    font-size: 14px;
    margin-bottom: 20px;
}

/* Панель фильтров */
.filters {
    background-color: var(--white);
    padding: 25px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    margin-bottom: 30px;
}

.filter-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 20px;
}

.filter-group {
    position: relative;
}

.filter-label {
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 8px;
    display: block;
    font-size: 14px;
}

/* Выпадающие списки */
.dropdown-search-container {
    position: relative;
}

.dropdown-search-input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid var(--medium-gray);
    border-radius: var(--border-radius);
    font-size: 14px;
    color: var(--dark-gray);
    background-color: var(--white);
    cursor: pointer;
    transition: var(--transition);
}

.dropdown-search-input:hover {
    border-color: var(--primary-color);
    background-color: #f9f9f9;
}

.dropdown-search-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(109, 68, 75, 0.2);
}

.dropdown-checkbox-group {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    max-height: 350px;
    overflow-y: auto;
    background-color: var(--white);
    border: 2px solid var(--medium-gray);
    border-radius: var(--border-radius);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    z-index: 1000;
    margin-top: 5px;
    display: none;
    animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.dropdown-checkbox-group.active {
    display: block;
}

.checkbox-item {
    padding: 12px 15px;
    display: flex;
    align-items: center;
    transition: var(--transition);
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
}

.checkbox-item:last-child {
    border-bottom: none;
}

.checkbox-item:hover {
    background-color: #f8f8f8;
}

.checkbox-item.selected {
    background-color: var(--primary-light);
    border-left: 4px solid var(--primary-color);
}

.checkbox-item input[type="checkbox"],
.checkbox-item input[type="radio"] {
    margin-right: 12px;
    cursor: pointer;
}

.checkbox-item label {
    cursor: pointer;
    flex: 1;
    font-size: 14px;
    color: var(--dark-gray);
}

/* Стили для кнопок управления фильтрами */
.clear-selection {
    color: #6d444b;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.2s ease;
    padding: 2px 6px;
    border-radius: 4px;
    text-decoration: underline;
    opacity: 0.8;
}

.clear-selection:hover {
    color: #98fb98;
    background-color: rgba(109, 68, 75, 0.1);
    text-decoration: none;
}

/* Скрываем "Выбрано: 0" для негодовых фильтров */
#org_type-count,
#locality-count {
    display: none;
}

/* Показываем "Выбрано: X" только для года */
#year-count {
    font-weight: 600;
    color: #6d444b;
}

/* Контейнеры с кнопками управления */
.selected-count {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.no-results {
    padding: 20px;
    text-align: center;
    color: #999;
    font-style: italic;
    font-size: 14px;
    display: none;
}

/* Кнопки управления видом */
.view-controls {
    display: flex;
    gap: 10px;
    margin-left: auto;
}

.view-btn {
    padding: 10px 20px;
    border: 2px solid var(--medium-gray);
    background-color: var(--white);
    color: var(--dark-gray);
    cursor: pointer;
    border-radius: var(--border-radius);
    font-size: 14px;
    font-weight: 600;
    transition: var(--transition);
    min-width: 100px;
    text-align: center;
}

.view-btn:hover {
    background-color: var(--primary-hover);
    color: black;
    border-color: var(--primary-hover);
}

.view-btn.active {
    background-color: var(--primary-color);
    color: var(--white);
    border-color: var(--primary-color);
}

.view-btn.active:hover {
    background-color: #5a373d;
    border-color: #5a373d;
}

/* Кнопки фильтров */
.buttons {
    display: flex;
    gap: 15px;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid var(--medium-gray);
}

.btn-primary,
.btn-secondary {
    padding: 12px 30px;
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-size: 15px;
    font-weight: 600;
    transition: var(--transition);
    flex: 1;
    max-width: 200px;
}

.btn-primary {
    background-color: var(--primary-color);
    color: var(--white);
}

.btn-primary:hover {
    background-color: var(--primary-hover);
    color: black;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-secondary {
    background-color: #e0e0e0;
    color: var(--dark-gray);
}

.btn-secondary:hover {
    background-color: #d0d0d0;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Статистические карточки */
.statistics {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 12px;
    margin-top: 12px;
}

.stat-card {
    background-color: var(--white);
    padding: 25px;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    display: flex;
    flex-direction: column;
    transition: var(--transition);
    border-top: 4px solid var(--primary-color);
    align-items: center;     /* по горизонтали */
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stat-card h3 {
    color: var(--primary-color);
    margin-bottom: 15px;
    font-size: 16px;
    font-weight: 600;
    line-height: 1.4;
}

.stat-value {
    font-size: 42px;
    font-weight: 700;
    color: var(--primary-color);
    margin-top: auto;

}

/* График */
.chart-container {
  width: 100%;
  min-height: 520px;   /* увеличь/уменьши по вкусу */
  height: 520px;
  margin-top: 12px;
}

/* canvas всегда занимает контейнер */
.chart-container canvas {
  width: 100% !important;
  height: 100% !important;
  display: block;
}

.chart-box h3 {
    color: var(--primary-color);
    margin-bottom: 20px;
    font-size: 18px;
    font-weight: 600;
}

/* Canvas занимает всё доступное место */
.chart-box canvas {
    flex: 1;
    width: 100% !important;
    height: 100% !important;
    max-height: none !important;  /* критично: убираем лимит 300px */
    display: block;
}

/* Таблица */
.results {
    background-color: var(--white);
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--shadow);
    margin-top: 30px;
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

thead {
    background-color: var(--primary-color);
    color: var(--white);
}

th {
    padding: 16px 20px;
    text-align: left;
    font-weight: 600;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

td {
    padding: 14px 20px;
    border-bottom: 1px solid var(--medium-gray);
    font-size: 14px;
}

tbody tr {
    background-color: var(--white);
    transition: var(--transition);
}

tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

tbody tr:hover {
    background-color: var(--primary-light);
}

/* Сообщение об отсутствии данных */
.no-results-message {
    text-align: center;
    padding: 60px 20px;
    color: var(--primary-color);
}

.no-results-message h2 {
    margin-bottom: 15px;
    font-size: 24px;
}

.no-results-message p {
    color: #666;
    font-size: 16px;
}

/* Информационные иконки */
.info-link {
    display: inline-flex;
    align-items: center;
    color: var(--primary-color);
    text-decoration: none;
    font-size: 14px;
    transition: var(--transition);
}

.info-link:hover {
    color: var(--primary-hover);
}

/* Скроллбар */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--primary-hover);
}

/* Адаптивность */
@media (max-width: 1200px) {
    .main-wrapper {
        flex-direction: column;
        gap: 20px;
    }
}
    
    .left-navigation {
        width: 100%;
        margin-top: 0;
    }
    
    .nav-panel {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .nav-section {
        margin-bottom: 0;
    }
    
    .chart-container {
        grid-template-columns: 1fr;
    }
    

@media (max-width: 768px) {
    .main-wrapper {
        padding: 0 15px;
    }
    
    .container {
        padding: 15px 0;
    }
    
    .filter-row {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .buttons {
        flex-direction: column;
    }
    
    .btn-primary,
    .btn-secondary {
        max-width: 100%;
    }
    
    .statistics {
    grid-template-columns: 1fr;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .view-controls {
        margin-left: 0;
        width: 100%;
        justify-content: center;
    }
    
    table {
        display: block;
        overflow-x: auto;
    }
    
    th, td {
        padding: 12px 15px;
        white-space: nowrap;
    }
}

@media (max-width: 480px) {
    .filters {
        padding: 20px;
    }
    
    .stat-card {
        padding: 20px;
    }
    
    .stat-value {
        font-size: 36px;
    }
    
    .view-btn {
        min-width: 80px;
        padding: 8px 15px;
    }
}

/* === ДОБАВЬТЕ ТОЛЬКО ЭТО В КОНЕЦ ВАШЕГО CSS === */

/* Спиннер для кнопки загрузки */
.btn-primary.loading {
    position: relative;
    color: transparent !important;
}

.btn-primary.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid rgba(255,255,255,0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Исправление для адаптивности карточек */
@media (max-width: 1200px) {
    .stat-card[style*="width: 49%"],
    .stat-card[style*="width: 50%"] {
        width: 48% !important;
    }
}

@media (max-width: 768px) {
    .stat-card[style*="width: 49%"],
    .stat-card[style*="width: 50%"] {
        width: 100% !important;
        margin-bottom: 15px;
    }
    
    
    .filter-row {
        flex-direction: column !important;
    }
}

/* Исправление для таблицы на мобильных */
@media (max-width: 768px) {
    .results table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }
}

/* === СТИЛИ ДЛЯ ВЫПАДАЮЩИХ СПИСКОВ БЕЗ ЧЕКБОКСОВ === */

/* Скрываем чекбоксы и радиокнопки */
.dropdown-checkbox-group input[type="checkbox"],
.dropdown-checkbox-group input[type="radio"] {
    display: none !important;
}

/* Стили для элементов списка без чекбоксов */
.dropdown-checkbox-group .checkbox-item {
    cursor: pointer;
    padding: 12px 15px;
    margin: 3px 0;
    border-radius: 6px;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
    background-color: #f9f9f9;
    display: flex;
    align-items: center;
    position: relative;
}

.dropdown-checkbox-group .checkbox-item:hover {
    background-color: #f0f0f0;
    border-left-color: #98fb98;
}

/* Стиль для выбранных элементов */
.dropdown-checkbox-group .checkbox-item.selected {
    background-color: #e8f5e9;
    border-left: 3px solid #6d444b;
    font-weight: 600;
    color: #6d444b;
}

/* Галочка для выбранных элементов */
.dropdown-checkbox-group .checkbox-item.selected::after {
    content: "✓";
    position: absolute;
    right: 15px;
    color: #6d444b;
    font-weight: bold;
}

/* Исключение для учебных годов - оставляем чекбоксы видимыми */
#year-group input[type="checkbox"] {
    display: inline-block !important;
    margin-right: 10px;
    width: 16px;
    height: 16px;
    accent-color: #6d444b;
    cursor: pointer;
}

/* Специальные стили для группы учебных годов */
#year-group .checkbox-item {
    cursor: default; /* Отключаем клик на весь элемент */
    background-color: #f9f9f9;
}

#year-group .checkbox-item:hover {
    background-color: #f0f0f0;
}

#year-group .checkbox-item label {
    cursor: pointer;
    display: flex;
    align-items: center;
    font-size: 14px;
    color: #333;
    width: 100%;
}

#year-group .checkbox-item.selected {
    background-color: #e8f5e9;
    border-left: 3px solid #6d444b;
}

/* Иконка для учебных годов */
#year-search::before {
    content: "📅 ";
    margin-right: 5px;
}

/* Иконки для других фильтров */
#org_type-search::before {
    content: "📊 ";
    margin-right: 5px;
}

#locality-search::before {
    content: "📍 ";
    margin-right: 5px;
}

/* === УЛУЧШЕНИЯ ДИЗАЙНА ФИЛЬТРОВ === */

/* Контейнер фильтров */
.filter-row {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #dee2e6;
    margin-bottom: 25px;
}

/* Заголовки фильтров */
.filter-header {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
}

.filter-header::before {
    content: "";
    display: inline-block;
    width: 4px;
    height: 16px;
    background: #6d444b;
    margin-right: 8px;
    border-radius: 2px;
}

/* Поля поиска в выпадающих списках */
.dropdown-search-input {
    background: white;
    border: 2px solid #ced4da;
    border-radius: 8px;
    padding: 12px 15px;
    font-size: 14px;
    color: #495057;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.dropdown-search-input:hover {
    border-color: #6d444b;
    box-shadow: 0 4px 8px rgba(109, 68, 75, 0.1);
}

.dropdown-search-input:focus {
    outline: none;
    border-color: #6d444b;
    box-shadow: 0 0 0 3px rgba(109, 68, 75, 0.2);
}

/* Выпадающие списки */
.dropdown-checkbox-group {
    border: 2px solid #6d444b;
    border-radius: 8px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    max-height: 300px;
    background: white;
}

/* Счетчик выбранных элементов */
.selected-count {
    background: #e9ecef;
    padding: 8px 12px;
    border-radius: 6px;
    margin-top: 8px;
    font-size: 12px;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.selected-count span {
    font-weight: 600;
    color: #6d444b;
}

/* Кнопки "очистить" и "выбрать все" */
.clear-selection, .select-all {
    background: #6d444b;
    color: white !important;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    transition: all 0.2s ease;
    text-decoration: none !important;
}

.clear-selection:hover, .select-all:hover {
    background: #98fb98;
    color: #000 !important;
    transform: translateY(-1px);
}

/* Анимация появления */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-15px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.dropdown-checkbox-group.active {
    animation: slideDown 0.2s ease-out;
}

/* Подсказка для учебных годов */
#year-group::before {
    content: "Можно выбрать несколько лет";
    display: block;
    padding: 10px 15px;
    font-size: 12px;
    color: #6c757d;
    font-style: italic;
    border-bottom: 1px solid #dee2e6;
    background: #f8f9fa;
}

/* === ФИНАЛЬНЫЕ ШТРИХИ === */

/* Плавное появление фильтров при загрузке */
.filter-group {
    opacity: 0;
    transform: translateY(10px);
    animation: fadeInUp 0.5s ease forwards;
}

.filter-group:nth-child(1) { animation-delay: 0.1s; }
.filter-group:nth-child(2) { animation-delay: 0.2s; }
.filter-group:nth-child(3) { animation-delay: 0.3s; }

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: none; /* важно: убираем stacking context после анимации */
    }
}

/* Эффект при активном фильтре */
.dropdown-search-container.active .dropdown-search-input {
    border-color: #6d444b;
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    box-shadow: 0 0 0 4px rgba(109, 68, 75, 0.1);
}

/* Адаптивность */
@media (max-width: 768px) {
    .filter-row {
        padding: 15px;
    }
    
    .dropdown-search-input {
        padding: 10px 12px;
        font-size: 13px;
    }
    
    .checkbox-item {
        padding: 10px 12px;
    }
    
    .selected-count {
        padding: 6px 10px;
        font-size: 11px;
    }
}

/* === УПРОЩЕННЫЕ ФИЛЬТРЫ === */

/* Основной контейнер фильтра */
.dropdown-container {
    position: relative;
    width: 100%;
}

/* Триггер для открытия выпадающего списка */
.dropdown-trigger {
    background: white;
    border: 2px solid #ced4da;
    border-radius: 8px;
    padding: 12px 15px;
    font-size: 14px;
    color: #495057;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    justify-content: space-between;
    align-items: center;
    user-select: none;
}

.dropdown-trigger:hover {
    border-color: #6d444b;
    background-color: #f8f9fa;
}

.dropdown-trigger.active {
    border-color: #6d444b;
    background-color: #fff;
    box-shadow: 0 0 0 3px rgba(109, 68, 75, 0.1);
}

.dropdown-arrow {
    color: #6c757d;
    font-size: 12px;
    transition: transform 0.2s ease;
}

.dropdown-trigger.active .dropdown-arrow {
    transform: rotate(180deg);
    color: #6d444b;
}

/* Выбранные элементы */
.dropdown-selected-count {
    font-size: 12px;
    color: #6c757d;
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 10px;
    opacity: 0;
    height: 0;
    overflow: hidden;
    transition: all 0.2s ease;
}

.dropdown-selected-count.visible {
    opacity: 1;
    height: auto;
    margin-top: 8px;
}

.dropdown-selected-count span:first-child {
    font-weight: 600;
    color: #6d444b;
    background: #e8f5e9;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 11px;
}

/* Кнопки управления */
.dropdown-clear,
.dropdown-select-all {
    font-size: 11px;
    color: #6d444b;
    cursor: pointer;
    padding: 2px 6px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.dropdown-clear:hover {
    background: #f8d7da;
    color: #721c24;
}

.dropdown-select-all:hover {
    background: #d1ecf1;
    color: #0c5460;
}

/* Выпадающий список */
.dropdown-options {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    max-height: 250px;
    overflow-y: auto;
    background: white;
    border: 2px solid #6d444b;
    border-radius: 8px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    z-index: 1000;
    margin-top: 5px;
    display: none;
    animation: slideDown 0.2s ease-out;
}

.dropdown-options.active {
    display: block;
}

.dropdown-option {
    padding: 10px 15px;
    cursor: pointer;
    transition: all 0.2s ease;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
}

.dropdown-option:last-child {
    border-bottom: none;
}

.dropdown-option:hover {
    background-color: #f8f9fa;
}

.dropdown-option.selected {
    background-color: #e8f5e9;
    border-left: 3px solid #6d444b;
    font-weight: 500;
}

/* Скрываем чекбоксы */
.dropdown-option input[type="checkbox"],
.dropdown-option input[type="radio"] {
    display: none;
}

.dropdown-option label {
    cursor: pointer;
    flex: 1;
    font-size: 14px;
    color: #212529;
    padding: 2px 0;
}

/* Галочка для выбранных элементов */
.dropdown-option.selected::after {
    content: "✓";
    color: #6d444b;
    font-weight: bold;
    margin-left: 8px;
}

/* Нет результатов */
.dropdown-no-results {
    padding: 15px;
    text-align: center;
    color: #6c757d;
    font-style: italic;
    font-size: 13px;
    display: none;
}

/* Анимация */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Упрощенные заголовки */
.filter-header {
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
    font-size: 13px;
}

.filter-header span:first-child {
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

/* Подсказка */
.filter-hint {
    display: inline-block;
    margin-left: 5px;
    color: #6d444b;
    cursor: help;
    font-size: 12px;
}

/* Компактная сетка фильтров */
.filter-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

/* Адаптивность */
@media (max-width: 768px) {
    .filter-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .dropdown-trigger {
        padding: 10px 12px;
        font-size: 13px;
    }
    
    .dropdown-option {
        padding: 8px 12px;
    }
}


/* === СТИЛИ ДЛЯ ГРАФИКОВ === */

.chart-box {
    min-height: 620px;      /* регулируй: 560–750 */
}


.chart-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.chart-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.chart-header h3 {
    color: var(--primary-color);
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    line-height: 1.4;
    flex: 1;
}

.chart-controls {
    display: flex;
    gap: 8px;
}

.chart-btn {
    background: white;
    border: 1px solid var(--medium-gray);
    border-radius: 4px;
    padding: 6px 10px;
    cursor: pointer;
    font-size: 14px;
    color: var(--dark-gray);
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
}

.chart-btn:hover {
    background: var(--primary-hover);
    border-color: var(--primary-color);
    color: black;
    transform: translateY(-1px);
}

.chart-btn svg {
    width: 16px;
    height: 16px;
}


/* Адаптивность графиков */
@media (max-width: 1200px) {
    .chart-container {
        grid-template-columns: 1fr;
    }
    
    .chart-box {
        height: 350px;
    }
}

@media (max-width: 768px) {
    .chart-box {
        padding: 20px;
        height: 300px;
    }
    
    .chart-header {
        flex-direction: column;
        gap: 10px;
    }
    
    .chart-header h3 {
        font-size: 16px;
    }
    
    .chart-controls {
        align-self: flex-end;
    }
}

@media (max-width: 480px) {
    .chart-box {
        padding: 15px;
        height: 280px;
    }
}

/* Улучшаем таблицу */
.results {
    margin-top: 30px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.results table {
    width: 100%;
    border-collapse: collapse;
}

.results th {
    background: #6d444b;
    color: white;
    padding: 12px 15px;
    text-align: center;
    font-weight: 600;
    font-size: 14px;
    position: sticky;
    top: 0;
}

.results td {
    padding: 10px 15px;
    border-bottom: 1px solid #eee;
    text-align: center;
    font-size: 14px;
}

/* Чередование строк */
.results tbody tr:nth-child(even) {
    background: #f9f9f9;
}

/* Итоговые строки */
.results tr[style*="background-color: #6d444b"] {
    background: #6d444b !important;
    color: white;
    font-weight: bold;
}

/* Заголовки строк слева */
.results td:first-child {
    text-align: left;
    font-weight: 600;
    color: #333;
}

/* Адаптивность таблицы */
@media (max-width: 768px) {
    .results {
        overflow-x: auto;
    }
    
    .results table {
        min-width: 800px;
    }
    
    .results th,
    .results td {
        padding: 8px 10px;
        font-size: 13px;
    }
}

/* Открытый фильтр должен быть выше соседей */
.filter-group.dropdown-open{
    z-index: 5000;
}

/* Сам список ещё выше */
.dropdown-checkbox-group{
    z-index: 6000;
}

.chart-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
    gap: 20px;
    margin-top: 18px;
}

.chart-box {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    padding: 16px 16px 10px 16px;
    min-height: 360px;
    position: relative;
}

.chart-header h3 {
    margin: 0 0 10px 0;
    color: #2c3e50;
    font-weight: 700;
    font-size: 15px;
}



/* Ряд карточек: две в строку */
.statistics{
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 12px;
  align-items: stretch;
}

/* Карточка: ровно половина строки (с учетом gap) */
.statistics .stat-card{
  box-sizing: border-box;
  flex: 0 0 calc(50% - 6px);
  max-width: calc(50% - 6px);
  min-width: 320px; /* можно 280-360 по вкусу */
}

/* На узких экранах — одна в строку */
@media (max-width: 900px){
  .statistics .stat-card{
    flex: 0 0 100%;
    max-width: 100%;
    min-width: 0;
  }
}

  .chart-box{
  background: #fff;
  border-radius: 14px;
  box-shadow: 0 8px 22px rgba(0,0,0,0.08);
  padding: 16px 16px 10px 16px;
  border: 1px solid rgba(0,0,0,0.06);
}

.chart-header h3{
  margin: 0 0 10px 0;
  font-weight: 700;
  font-size: 15px;
  color: #2c3e50;
}



/* Узкий и высокий stacked-график структуры */
#pieChart{
    height: 520px !important;   /* ДЛИННЫЙ */
    max-height: 520px;
}

.chart-header--with-actions{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
}

.chart-actions{
  display:flex;
  gap:8px;
  flex-wrap:wrap;
}

.chart-btn{
  appearance:none;
  border:1px solid rgba(15,23,42,.14);
  background:#fff;
  border-radius:10px;
  padding:7px 10px;
  font-size:12px;
  font-weight:700;
  color:rgba(15,23,42,.78);
  cursor:pointer;
  transition:transform .12s ease, background .12s ease, border-color .12s ease;
}
.chart-btn:hover{ transform:translateY(-1px); background:rgba(2,6,23,.02); border-color:rgba(15,23,42,.22); }
.chart-btn:active{ transform:translateY(0); }

.chart-wrap{
  position:relative;
  width:100%;
  min-height:320px;
}

.chart-tooltip{
  position:absolute;
  pointer-events:none;
  transform:translate(-50%, -110%);
  min-width:160px;
  max-width:260px;
  padding:10px;
  border-radius:12px;
  background:rgba(15,23,42,.92);
  color:#fff;
  box-shadow:0 18px 50px rgba(2,6,23,.35);
  opacity:0;
  transition:opacity .08s ease;
  z-index:5;
  font-size:12px;
  line-height:1.25;
}
.chart-tooltip.is-visible{ opacity:1; }

.chart-box.is-fullscreen{
  position:fixed;
  inset:12px;
  z-index:9999;
  margin:0;
  background:#fff;
  border-radius:16px;
  padding:14px;
  display:flex;
  flex-direction:column;
}
.chart-box.is-fullscreen .chart-wrap{ flex:1; min-height:0; }


/* Дать графику больше места: фиксируем высоту контейнера */
.chart-wrap.chart-wrap--big {
  position: relative;
  height: 560px;              /* увеличивай: 520/600/700 */
  width: 100%;
}

/* Canvas занимает весь контейнер */
.chart-wrap.chart-wrap--big > canvas {
  width: 100% !important;
  height: 100% !important;
  display: block;
}

/* Убрать hover у блока, где находится график */
.no-hover:hover {
  transform: none !important;
  box-shadow: none !important;
  filter: none !important;
  background: inherit !important;
}

/* Если hover повешен на карточку/контейнер выше уровнем — гасим и его */
.chart-container:hover,
.chart-container .card:hover,
.chart-container .chart-card:hover,
.chart-container .chart-box:hover {
  transform: none !important;
  box-shadow: none !important;
  filter: none !important;
}

#pieChart {
  max-height: none !important;
}

</style>
