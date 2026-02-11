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

@keyframes fadeInUp {
        to {
            opacity: 1;
            transform: none; /* важно: убираем stacking context после анимации */
        }
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