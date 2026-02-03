<script>
document.addEventListener('DOMContentLoaded', function() {
    // Конфигурация всех фильтров
    const filtersConfig = {
        'org_type': {
            isRadio: true,
            searchId: 'org_type-search',
            groupId: 'org_type-group',
            countId: 'org_type-count',
            clearId: 'org_type-clear',
            containerId: 'org_type-container',
            placeholder: 'Уровень представления данных'
        },
        'year': {
            isRadio: false,
            searchId: 'year-search',
            groupId: 'year-group',
            countId: 'year-count',
            clearId: 'year-clear',
            containerId: 'year-container',
            selectAllId: 'year-select-all',
            placeholder: 'Учебный год'
        },
        'locality': {
            isRadio: true,
            searchId: 'locality-search',
            groupId: 'locality-group',
            countId: 'locality-count',
            clearId: 'locality-clear',
            containerId: 'locality-container',
            placeholder: 'Тип местности'
        }
    };
    
    // Инициализация всех фильтров
    Object.keys(filtersConfig).forEach(filterName => {
        const config = filtersConfig[filterName];
        initFilter(filterName, config);
    });
    
    // Закрытие всех выпадающих списков при клике вне их
    document.addEventListener('click', function(event) {
        let shouldCloseAll = true;
        
        Object.keys(filtersConfig).forEach(filterName => {
            const container = document.getElementById(filtersConfig[filterName].containerId);
            if (container && container.contains(event.target)) {
                shouldCloseAll = false;
            }
        });
        
        if (shouldCloseAll) {
            Object.keys(filtersConfig).forEach(filterName => {
                const group = document.getElementById(filtersConfig[filterName].groupId);
                group.classList.remove('active');
            });
        }
    });
    
    // Инициализация фильтра
    function initFilter(filterName, config) {
        const searchInput = document.getElementById(config.searchId);
        const checkboxGroup = document.getElementById(config.groupId);
        const countSpan = document.getElementById(config.countId);
        const clearBtn = document.getElementById(config.clearId);
        const container = document.getElementById(config.containerId);
        const selectAllBtn = config.selectAllId ? document.getElementById(config.selectAllId) : null;
        
        if (!searchInput || !checkboxGroup) return;
        
        const checkboxes = checkboxGroup.querySelectorAll('input[type="' + (config.isRadio ? 'radio' : 'checkbox') + '"]');
        const checkboxItems = checkboxGroup.querySelectorAll('.checkbox-item');
        const noResults = checkboxGroup.querySelector('.no-results');
        
        // Инициализация текста в поле поиска
        updateSearchInputText(filterName);
        
        // Показать/скрыть список при клике
        searchInput.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Закрыть другие открытые списки
            Object.keys(filtersConfig).forEach(otherFilter => {
                if (otherFilter !== filterName) {
                    const otherGroup = document.getElementById(filtersConfig[otherFilter].groupId);
                    otherGroup.classList.remove('active');
                }
            });
            
            // Переключить текущий
            checkboxGroup.classList.toggle('active');
        });
        
        // Поиск
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            let hasVisibleItems = false;
            
            checkboxItems.forEach(item => {
                const label = item.querySelector('label');
                const text = label.textContent.toLowerCase();
                
                if (text.includes(searchTerm)) {
                    item.style.display = 'flex';
                    hasVisibleItems = true;
                } else {
                    item.style.display = 'none';
                }
            });
            
            if (noResults) {
                noResults.style.display = hasVisibleItems ? 'none' : 'block';
            }
        });
        
        // Обновление счетчика и текста
        function updateFilterState() {
            updateSelectedCount(filterName);
            updateSearchInputText(filterName);
        }
        
        // Обновление счетчика
        function updateSelectedCount(filterName) {
            const config = filtersConfig[filterName];
            const countSpan = document.getElementById(config.countId);
            const checkboxes = document.querySelectorAll(`#${config.groupId} input[type="${config.isRadio ? 'radio' : 'checkbox'}"]`);
            
            if (!countSpan) return;
            
            let checkedCount;
            if (config.isRadio) {
                checkedCount = Array.from(checkboxes).some(cb => cb.checked) ? 1 : 0;
            } else {
                checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
            }
            
            countSpan.textContent = checkedCount;
            
            // Обновить текст кнопки "Выбрать все"
            if (selectAllBtn && filterName === 'year') {
                selectAllBtn.textContent = checkedCount === checkboxes.length ? 'Снять все' : 'Выбрать все';
            }
        }
        
        // Обновление текста в поле поиска
        function updateSearchInputText(filterName) {
            const config = filtersConfig[filterName];
            const searchInput = document.getElementById(config.searchId);
            const checkboxes = document.querySelectorAll(`#${config.groupId} input[type="${config.isRadio ? 'radio' : 'checkbox'}"]`);
            
            if (!searchInput) return;
            
            let selectedText = [];
            
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const label = document.querySelector(`label[for="${checkbox.id}"]`);
                    if (label) selectedText.push(label.textContent);
                }
            });
            
            if (selectedText.length > 0) {
                if (filterName === 'year' && selectedText.length > 3) {
                    searchInput.value = `Выбрано ${selectedText.length} лет ▼`;
                } else {
                    searchInput.value = selectedText.join(', ') + ' ▼';
                }
            } else {
                searchInput.value = config.placeholder;
            }
        }
        
        // Обновление при изменении чекбоксов
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (config.isRadio && this.checked) {
                    // Закрыть dropdown после выбора радиокнопки
                    setTimeout(() => {
                        checkboxGroup.classList.remove('active');
                    }, 300);
                }
                updateFilterState();
            });
        });
        
        // Обработка клика на элементе
        checkboxItems.forEach(item => {
            item.addEventListener('click', function(e) {
                const input = this.querySelector('input');
                if (e.target !== input && !e.target.classList.contains('clear-selection') && !e.target.classList.contains('select-all')) {
                    if (config.isRadio) {
                        input.checked = true;
                    } else {
                        input.checked = !input.checked;
                    }
                    input.dispatchEvent(new Event('change'));
                }
            });
        });
        
        // Очистка выбора
        if (clearBtn) {
            clearBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateFilterState();
            });
        }
        
        // Кнопка "Выбрать все" для чекбоксов
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                
                checkboxes.forEach(checkbox => {
                    checkbox.checked = !allChecked;
                });
                
                updateFilterState();
            });
        }
        
        // Инициализация начального состояния
        updateFilterState();
    }
    
    // ========== ФУНКЦИИ ДЛЯ ГРАФИКОВ И ТАБЛИЦ ==========
    
    // Функции showCards() и showTable() уже есть у вас
    // Функция initializeCharts() уже есть у вас
    
    // По умолчанию показываем карточки
    showCards();
    
    // Инициализируем графики
    initializeCharts();
});

// Функции для переключения между карточками и таблицей
function showCards() {
    const statBlocks = document.querySelectorAll('.statistics');
    statBlocks.forEach(function(block) {
        block.style.display = 'block';
    });
    
    const tableView = document.getElementById('tableView');
    if (tableView) tableView.style.display = 'none';
    
    const showCardsBtn = document.getElementById('showCardsBtn');
    const showTableBtn = document.getElementById('showTableBtn');
    
    if (showCardsBtn) showCardsBtn.classList.add('active');
    if (showTableBtn) showTableBtn.classList.remove('active');
}

function showTable() {
    const statBlocks = document.querySelectorAll('.statistics');
    statBlocks.forEach(function(block) {
        block.style.display = 'none';
    });
    
    const tableView = document.getElementById('tableView');
    if (tableView) tableView.style.display = 'block';
    
    const showCardsBtn = document.getElementById('showCardsBtn');
    const showTableBtn = document.getElementById('showTableBtn');
    
    if (showTableBtn) showTableBtn.classList.add('active');
    if (showCardsBtn) showCardsBtn.classList.remove('active');
}

// Функция для инициализации графиков
function initializeCharts() {
    // График 1: Общая динамика
    const totalChartCanvas = document.getElementById('totalChart');
    if (totalChartCanvas) {
        const totalCtx = totalChartCanvas.getContext('2d');
        new Chart(totalCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($years ?? []); ?>,
                datasets: [
                    {
                        label: 'Всего организаций',
                        data: <?php echo json_encode($totalOrganizations ?? []); ?>,
                        borderColor: '#6d444b', // Ваш основной цвет
                        backgroundColor: 'rgba(109, 68, 75, 0.1)', // Прозрачная версия
                        borderWidth: 3,
                        tension: 0.1,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'Количество'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Учебный год'
                        }
                    }
                }
            }
        });
    }

    // График 2: Типы школ
    const schoolTypesChartCanvas = document.getElementById('schoolTypesChart');
    if (schoolTypesChartCanvas) {
        const typesCtx = schoolTypesChartCanvas.getContext('2d');
        new Chart(typesCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($schoolTypesLabels ?? []); ?>,
                datasets: [{
                    label: 'Количество организаций',
                    data: <?php echo json_encode($schoolTypesData ?? []); ?>,
                    backgroundColor: [
                        'rgba(109, 68, 75, 0.7)',    // Ваш цвет
                        'rgba(152, 251, 152, 0.7)',  // Ваш hover цвет
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)'
                    ],
                    borderColor: [
                        'rgb(109, 68, 75)',
                        'rgb(152, 251, 152)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 206, 86)',
                        'rgb(75, 192, 192)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Количество'
                        }
                    }
                }
            }
        });
    }

    // График 3: Сравнение по годам
    const comparisonChartCanvas = document.getElementById('comparisonChart');
    if (comparisonChartCanvas && window.nurseryData && window.basicData) {
        const compareCtx = comparisonChartCanvas.getContext('2d');
        new Chart(compareCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($years ?? []); ?>,
                datasets: [
                    {
                        label: 'НОШ д/сад',
                        data: window.nurseryData,
                        backgroundColor: 'rgba(109, 68, 75, 0.7)'
                    },
                    {
                        label: 'Основные школы',
                        data: window.basicData,
                        backgroundColor: 'rgba(152, 251, 152, 0.7)'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Количество'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Учебный год'
                        }
                    }
                }
            }
        });
    }

    // График 4: Круговая диаграмма
    const pieChartCanvas = document.getElementById('pieChart');
    if (pieChartCanvas) {
        const pieCtx = pieChartCanvas.getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($pieLabels ?? []); ?>,
                datasets: [{
                    data: <?php echo json_encode($pieData ?? []); ?>,
                    backgroundColor: [
                        'rgba(109, 68, 75, 0.7)',
                        'rgba(152, 251, 152, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)'
                    ],
                    borderColor: [
                        'rgb(109, 68, 75)',
                        'rgb(152, 251, 152)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 206, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(153, 102, 255)',
                        'rgb(255, 159, 64)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            padding: 15
                        }
                    }
                }
            }
        });
    }
}

// === ДОБАВЬТЕ ЭТОТ КОД В КОНЕЦ ВАШЕГО СКРИПТА ===

// Простая функция экспорта таблицы в Excel
window.exportToExcel = function() {
    try {
        const table = document.querySelector('#tableView table');
        if (!table) {
            alert('Таблица не найдена!');
            return;
        }
        
        // Создаем HTML таблицу для Excel
        let html = '<html><head><meta charset="UTF-8"></head><body>';
        html += '<table border="1">' + table.innerHTML + '</table>';
        html += '</body></html>';
        
        // Создаем и скачиваем файл
        const blob = new Blob([html], {type: 'application/vnd.ms-excel'});
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        
        a.href = url;
        a.download = 'статистика_образования_' + new Date().toLocaleDateString('ru-RU') + '.xls';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        
    } catch (error) {
        console.error('Ошибка при экспорте:', error);
        alert('Ошибка при экспорте таблицы');
    }
};

// Добавляем кнопку экспорта при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    const viewControls = document.querySelector('.view-controls');
    if (viewControls && !document.querySelector('.export-btn')) {
        const exportBtn = document.createElement('button');
        exportBtn.className = 'view-btn';
        exportBtn.textContent = 'экспорт';
        exportBtn.onclick = exportToExcel;
        exportBtn.title = 'Экспорт таблицы в Excel';
        viewControls.appendChild(exportBtn);
    }
    
    // Простая функция для показа пояснений
    const infoLink = document.querySelector('a[href="#"]');
    if (infoLink) {
        infoLink.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Справка по данным:\n\n' +
                  '• НОШ д/сад - Начальные школы-детские сады\n' +
                  '• НОШ - Начальные общеобразовательные школы\n' +
                  '• ООШ - Основные общеобразовательные школы\n' +
                  '• СОШ - Средние общеобразовательные школы\n' +
                  '• УИОП - Углубленное изучение отдельных предметов\n' +
                  '• ОВЗ - Ограниченные возможности здоровья');
        });
    }
});

// Скрываем preloader когда все загружено
document.addEventListener('DOMContentLoaded', function() {
    // Ждем полной загрузки страницы
    window.addEventListener('load', function() {
        setTimeout(function() {
            const preloader = document.getElementById('preloader');
            if (preloader) {
                preloader.style.transition = 'opacity 0.3s';
                preloader.style.opacity = '0';
                setTimeout(() => {
                    preloader.style.display = 'none';
                }, 300);
            }
        }, 300); // Минимальная задержка
    });
});
</script>