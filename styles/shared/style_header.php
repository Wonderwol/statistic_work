<style>
@import url('https://fonts.googleapis.com/css2?family=PT+Sans+Narrow:wght@400;700&display=swap');

#header{
    width: 100%;
    max-width: var(--nimro-page-max);
    margin: 0 auto;

    min-height: 255px;
    height: auto;

    background-image: url('/statistics/src/img/header_bg.jpg');
    background-size: cover;
    background-position: 60% 50%;
    background-repeat: no-repeat;

    position: relative;
    box-sizing: border-box;
    padding: 0;

    display: flex;
    flex-direction: column;
    color: #fff;
}

@media (max-width: 920px){
    #header{ max-width: 100%; margin: 0; }
}

/* Лого */
#header .logo{
    position: absolute;
    left: calc(var(--nimro-page-pad, 14px) + 50px);
    top: 116px;
    transform: translateY(-50%);
    display: flex;
    align-items: center;
    z-index: 3;
    text-decoration: none;
}
#header .logo img{
    height: 118px;
    width: auto;
    object-fit: contain;
    display: block;
}

/* Правый блок (вход + глаз) */
.header-actions{
    position: absolute;
    top: 10px;
    right: var(--nimro-page-pad, 14px);
    z-index: 5;

    display: inline-flex;
    align-items: stretch;

    background: #fff;
    border: 1px solid rgba(0,0,0,.14);
    box-shadow: 0 6px 18px rgba(0,0,0,.10);
    overflow: hidden;
}
.header-actions .header-login{
    display: inline-flex;
    align-items: center;
    padding: 10px 14px;

    color: #6d444b;
    font-family: "PT Sans Narrow", "Arial Narrow", Arial, sans-serif;
    font-size: 13px;
    font-weight: 700;
    text-decoration: underline;
    text-underline-offset: 3px;
    white-space: nowrap;
}
.header-actions .header-eye{
    width: 52px;
    display: inline-flex;
    align-items: center;
    justify-content: center;

    border-left: 1px solid rgba(0,0,0,.14);
    color: #3a2a2d;
    text-decoration: none;
}
.header-actions .header-eye svg{
    width: 22px;
    height: 22px;
    display: block;
}
.header-actions a:hover{ filter: brightness(0.96); }

/* =========================================================
   КОРИЧНЕВАЯ ПОЛОСА
   ========================================================= */
.header-menu-block{
    margin-top: auto;
    width: 100%;
    box-sizing: border-box;

    padding: 0;
    height: 44px;

    display: flex;
    align-items: stretch;

    justify-content: flex-start;

    background: #6d444b;
    z-index: 4;
}

.header-menu{
    list-style: none;
    margin: 0;

    /* ВОТ ТЕ САМЫЕ ОТСТУПЫ СЛЕВА/СПРАВА */
    padding: 0 14px;

    display: flex;
    flex-wrap: nowrap;
    align-items: stretch;

    height: 100%;
    width: 100%;
    box-sizing: border-box;

    justify-content: space-between;
    gap: 0;
}

.header-menu li{
    height: 100%;
    display: flex;
    align-items: stretch;

    flex: 0 0 auto;
}

.header-menu li a{
    height: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;

    padding: 0 8px;

    color: #fff;
    text-decoration: none;

    font-family: "PT Sans Narrow", "Arial Narrow", Arial, sans-serif;
    font-weight: 700;
    font-size: 15px;
    line-height: 1;
    letter-spacing: .2px;

    border-radius: 0;
    white-space: nowrap;
}

.header-menu li a:hover{
    background: rgba(255,255,255,.12);
}

.header-menu li a.selected,
.header-menu li a[aria-current="page"]{
    background: #ffffff;
    color: #6d444b;
}

/* Домик НЕ белый по умолчанию */
.header-menu .menu-home a{
    min-width: 44px;
    padding: 0 10px;
    background: transparent;
    color: #fff;
}
.header-menu .menu-home a.selected,
.header-menu .menu-home a[aria-current="page"]{
    background: #ffffff;
    color: #6d444b;
}
.header-menu .menu-home-icon{
    width: 18px;
    height: 18px;
    display: block;
}

/* На узких экранах — переносы */
@media (max-width: 900px){
    .header-menu-block{
        height: auto;
        padding: 8px 0;
        align-items: center;
    }

    .header-menu{
        /* небольшой отступ по краям сохраняем и на мобиле */
        padding: 0 10px;

        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        column-gap: 10px;
        row-gap: 6px;
        height: auto;
    }

    .header-menu li,
    .header-menu li a{
        height: auto;
    }

    .header-menu li a{
        padding: 6px 6px;
        border-radius: 6px;
        white-space: normal;
        text-align: center;
        max-width: 260px;
        overflow-wrap: anywhere;
        hyphens: auto;
        font-size: 14px;
    }

    .header-menu .menu-home a{
        min-width: 34px;
        padding: 6px 8px;
    }
}

@media (max-width: 640px){
    #header .logo img{ height: 74px; }
    #header .logo{ top: 95px; }
    .header-menu li a{ font-size: 13px; }
}

/* Сдвиг при dock-nav */
@media (min-width: 921px){
  body.nimro-nav-docked #header{
    margin-left: calc(var(--nimro-nav-w) + 24px);
    margin-right: 24px;
  }
}
</style>
