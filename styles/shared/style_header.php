<style>
/* =========================================================
   HEADER (глобальная шапка сайта НИМРО)
   ========================================================= */
@import url('https://fonts.googleapis.com/css2?family=PT+Sans+Narrow:wght@400;700&display=swap');

#header{
    width: 100%;
    max-width: var(--nimro-page-max);
    margin: 0 auto;

    border-radius: 0;
    overflow: visible;

    min-height: 255px;
    height: auto;

    background-image: url('/v3/src/img/header_bg.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;

    position: relative;
    box-sizing: border-box;
    padding: 0;

    display: flex;
    flex-direction: column;
    color: #fff;
}

/* На планшете/мобиле делаем шапку на всю ширину */
@media (max-width: 920px){
    #header{
        max-width: 100%;
        margin: 0;
    }
}

/* Логотип (как был по месту, но чуть левее через переменную) */
#header .logo{
    position: absolute;
    left: var(--nimro-page-pad, 14px);
    top: 116px;               /* стабильно при зуме/ресайзе */
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    justify-content: flex-start;
    z-index: 3;
    text-decoration: none;
}

#header .logo img{
    height: 118px;
    width: auto;
    object-fit: contain;
    display: block;
}

/* Правые кнопки в шапке */
.header-actions{
    position: absolute;
    top: 12px;
    right: var(--nimro-page-pad, 14px);
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 5;
}

/* Нижняя полоса меню — ВАЖНО: в потоке, без absolute, без скролла */
.header-menu-block{
    margin-top: auto; /* прижимаем вниз */
    width: 100%;
    box-sizing: border-box;

    padding: 8px var(--nimro-page-pad, 14px);

    background: rgba(109, 68, 75, 0.92);
    z-index: 4;

    min-height: 40px;
    height: auto;

    display: flex;
    align-items: center;
    justify-content: center;

    overflow: visible; /* скроллбар не нужен */
}

/* Меню: перенос строк, чтобы ничего не вылезало */
.header-menu{
    list-style: none;
    margin: 0;
    padding: 0;

    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;

    column-gap: 14px;
    row-gap: 6px;

    width: 100%;
    box-sizing: border-box;
}

/* Пункты меню: разрешаем перенос */
.header-menu li a{
    text-decoration: none;
    color: #fff;
    font-weight: 600;
    font-size: 13px;
    line-height: 1.15;
    padding: 6px 6px;
    font-family: "PT Sans Narrow", "Arial Narrow", Arial, sans-serif;

    white-space: normal;
    text-align: center;
    max-width: 240px;
    overflow-wrap: anywhere;
    hyphens: auto;

    display: inline-block;
    border-radius: 6px;
}

/* Активный пункт */
.header-menu li a.selected,
.header-menu li a[aria-current="page"]{
    text-decoration: underline;
    text-underline-offset: 4px;
}

/* Домик */
.header-menu .menu-home a{
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;

    color: #fff;
    text-decoration: none;
    padding: 0;
}
.header-menu .menu-home-icon{
    width: 18px;
    height: 18px;
    display: block;
}
.header-menu .menu-home a:hover{
    opacity: 0.9;
}

/* Hover для всех пунктов */
.header-menu li a:hover{
    background-color: rgba(255, 255, 255, 0.12);
}

/* Последний пункт — белая кнопка */
.header-menu li:last-child a{
    background-color: #ffffff;
    color: #6d444b;
    font-weight: 700;
}

/* Адаптив */
@media (max-width: 1100px){
    .header-menu{ column-gap: 12px; row-gap: 6px; }
    .header-menu li a{ font-size: 13px; }
}

@media (max-width: 900px){
    .header-menu{ column-gap: 10px; row-gap: 6px; }
    .header-menu li a{ font-size: 12px; padding: 6px 5px; max-width: 200px; }
}

@media (max-width: 640px){
    #header .logo img{ height: 74px; }
    #header .logo{ top: 95px; }
    .header-menu li a{ font-size: 11px; padding: 5px 5px; max-width: 180px; }
}

/* Сдвиг при dock-nav */
@media (min-width: 921px){
  body.nimro-nav-docked #header{
    margin-left: calc(var(--nimro-nav-w) + 24px);
    margin-right: 24px;
  }
}
</style>
