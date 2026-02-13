.dropdown-search-container {
  position: relative;
}

/* Стрелка справа (визуальная, не часть текста) */
.dropdown-search-container::after{
  content: "";
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  width: 0;
  height: 0;
  border-left: 6px solid transparent;
  border-right: 6px solid transparent;
  border-top: 7px solid rgba(109, 68, 75, 0.9);
  pointer-events: none;
}

/* Когда открыт дропдаун — можно чуть повернуть */
.dropdown-search-container.active::after{
  transform: translateY(-50%) rotate(180deg);
}

/* Поле "поиска/выбора" */
.dropdown-search-input {
  width: 100%;
  /* + место под стрелку справа */
  padding: clamp(10px, 0.6vw + 8px, 12px) 40px clamp(10px, 0.6vw + 8px, 12px) clamp(12px, 0.8vw + 10px, 15px);
  border: 2px solid var(--medium-gray);
  border-radius: var(--border-radius);
  font-size: clamp(13px, 0.3vw + 12px, 14px);
  color: var(--dark-gray);
  background-color: var(--white);
  cursor: pointer;
  transition: var(--transition);
}

/* Не даём выделять текст в readonly-инпутах фильтров */
.dropdown-search-input[readonly]{
  -webkit-user-select: none;
  user-select: none;
  caret-color: transparent;
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

/* Панель выпадающего списка */
.dropdown-checkbox-group {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;

  max-height: 350px;
  overflow-y: auto;

  background: var(--white);
  border: 2px solid var(--primary-color);
  border-radius: var(--border-radius);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);

  z-index: 6000;
  margin-top: 5px;

  display: none;
  animation: dropdownFadeIn 0.2s ease;
}

@keyframes dropdownFadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to   { opacity: 1; transform: translateY(0); }
}

.dropdown-checkbox-group.active {
  display: block;
}

/* Элемент списка */
.checkbox-item {
  padding: clamp(10px, 0.6vw + 8px, 12px) clamp(12px, 0.8vw + 10px, 15px);
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

/* Счетчик выбранных */
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
  gap: 10px;
}

.selected-count span {
  font-weight: 600;
  color: var(--primary-color);
}

/* Кнопки управления в выпадашке */
.clear-selection,
.select-all {
  background: var(--primary-color);
  color: #fff !important;
  padding: 4px 10px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 600;
  transition: all 0.2s ease;
  text-decoration: none !important;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.clear-selection:hover,
.select-all:hover {
  background: var(--primary-light);
  color: var(--primary-color) !important;
  transform: translateY(-1px);
}

/* === Выпадающие списки без чекбоксов === */
.dropdown-checkbox-group input[type="checkbox"],
.dropdown-checkbox-group input[type="radio"] {
  display: none !important;
}

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
  border-bottom: none;
}

.dropdown-checkbox-group .checkbox-item:hover {
  background-color: #f0f0f0;
  border-left-color: var(--primary-color);
}

.dropdown-checkbox-group .checkbox-item.selected {
  background-color: var(--primary-light);
  border-left: 3px solid var(--primary-color);
  font-weight: 600;
  color: var(--primary-color);
}

.dropdown-checkbox-group .checkbox-item.selected::after {
  content: "✓";
  position: absolute;
  right: 15px;
  color: var(--primary-color);
  font-weight: 700;
}

/* Исключение для учебных годов - оставляем чекбоксы видимыми */
#year-group input[type="checkbox"] {
  display: inline-block !important;
  margin-right: 10px;
  width: 16px;
  height: 16px;
  accent-color: var(--primary-color);
  cursor: pointer;
}

/* Специальные стили для группы учебных годов */
#year-group .checkbox-item {
  cursor: default;
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
  background-color: var(--primary-light);
  border-left: 3px solid var(--primary-color);
}

/* Нет результатов внутри выпадашки */
.dropdown-no-results {
  padding: 15px;
  text-align: center;
  color: #6c757d;
  font-style: italic;
  font-size: 13px;
  display: none;
}
