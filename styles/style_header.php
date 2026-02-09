<style>
/* =========================================================
   HEADER (глобальная шапка сайта НИМРО)
   ========================================================= */

/* Контейнер шапки */
@import url('https://fonts.googleapis.com/css2?family=PT+Sans+Narrow:wght@400;700&display=swap');

#header {
    width: 100%;
    height: 255px;
    background-image: url('/v3/src/img/header_bg.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;

    position: relative;
    box-sizing: border-box;
    padding: 0 40px;

    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
}

#header .logo {
    position: absolute;
    left: 63px;
    top: 45.5%;

    transform: translateY(-50%);
    display: flex;
    align-items: center;
    justify-content: flex-start;

    z-index: 3;
    text-decoration: none;
}

#header .logo img {
    height: 118px;
    width: auto;
    object-fit: contain;
    display: block;
}

/* Нижняя полоса меню */
.header-menu-block {
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: auto;
        padding: 8px 0;

    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;

    background: rgba(109, 68, 75, 0.92);
    z-index: 4;
}

/* Меню */
.header-menu {
    list-style: none;
    margin: 0;
    padding: 0 12px;

    display: flex;
    align-items: center;
    gap: 22px;
}

/* Пункты меню */
.header-menu li a {
    text-decoration: none;
    color: #fff;
    font-weight: 600;
    font-size: 14px;
    white-space: nowrap;
    padding: 6px 6px;
    font-family: "PT Sans Narrow", "Arial Narrow", Arial, sans-serif;
}

/* Активный пункт */
.header-menu li a.selected,
.header-menu li a[aria-current="page"] {
    text-decoration: underline;
    text-underline-offset: 4px;
}

/* ===== Адаптивность ===== */

@media (max-width: 1100px) {
    #header {
        padding: 0 20px;
    }

    #header .logo {
        left: 20px;
    }

    .header-menu {
        gap: 14px;
    }

    .header-menu li a {
        font-size: 13px;
    }
}

@media (max-width: 900px) {
    .header-menu {
        flex-wrap: wrap;
        justify-content: center;
        row-gap: 6px;
    }

    .header-menu-block {
        height: auto;
        padding: 8px 0;
    }
}

/* Правые кнопки в шапке */
.header-actions {
    position: absolute;
    top: 12px;
    right: 400px;          /* <-- вот “пустое место” справа */
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 5;
}

/* Кнопка "Вход" */
.header-actions .btn-login {
    background: #ffffff;
    color: #6d444b;
    text-decoration: none;
    font-weight: 700;
    font-size: 14px;
    padding: 8px 12px;
    border: 1px solid rgba(109, 68, 75, 0.35);
}

/* Квадратная кнопка-иконка */
.header-actions .btn-icon {
    width: 42px;
    height: 34px;
    background: #ffffff;
    border: 1px solid rgba(109, 68, 75, 0.35);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}


.header-menu .menu-home a {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;

    color: #fff;            /* currentColor в SVG берёт это */
    text-decoration: none;
    padding: 0;
}

/* Размер SVG */
.header-menu .menu-home-icon {
    width: 18px;
    height: 18px;
    display: block;
}

/* (Опционально) hover-эффект как на многих сайтах */
.header-menu .menu-home a:hover {
    opacity: 0.9;
}

/* Hover для всех пунктов нижнего меню */
.header-menu li a:hover {
    background-color: rgba(255, 255, 255, 0.12);
}

.header-menu li:last-child a {
    background-color: #ffffff;
    color: #6d444b;
    font-weight: 700;
}
</style>