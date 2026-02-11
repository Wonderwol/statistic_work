<style>
:root{
  --nav-accent: #6d444b;             /* твой бордовый */
  --nav-accent-rgb: 109, 68, 75;

  --sidebar-w: 320px;
  --header-h: 255px;                 /* у тебя #header { height: 255px } */
  --content-max: 1200px;             /* подгони под реальный max-width .container */
  --sidebar-gap: 16px;

  --nav-text: rgba(44, 62, 80, .92);
  --nav-muted: rgba(44, 62, 80, .72);
  --nav-border: rgba(0,0,0,.08);
  --nav-soft: rgba(0,0,0,.05);
}

/* Меню в левом "пустом поле" рядом с центрированным контентом */
.left-navigation{
  position: fixed;
  top: var(--header-h);
  left: clamp(
    0px,
    calc(50% - (var(--content-max) / 2) - var(--sidebar-w) - var(--sidebar-gap)),
    9999px
  );

  width: var(--sidebar-w);
  height: calc(100vh - var(--header-h));
  overflow: auto;
  overscroll-behavior: contain;
  scrollbar-gutter: stable;

  background: #fff;
  border: 1px solid var(--nav-border);
  border-radius: 14px;
  box-shadow: 0 10px 26px rgba(0,0,0,.08);
  z-index: 20;
}

/* Внутренний отступ */
.left-navigation .nav-panel{
  padding: 12px 12px 16px 12px;
}

/* Заголовок "Навигация" */
.left-navigation .nav-panel > a{
  display:block;
  text-decoration:none;
}

.left-navigation .nav-panel h2{
  margin: 6px 8px 10px 8px;
  font-size: 14px;
  font-weight: 800;
  color: var(--nav-text);
  letter-spacing: .2px;
  line-height: 1.2;
}

/* Секции */
.left-navigation .nav-section{
  margin: 10px 6px;
  padding: 10px 10px;
  border: 1px solid rgba(0,0,0,.06);
  border-radius: 12px;
  background: linear-gradient(180deg, rgba(0,0,0,.015), rgba(0,0,0,0));
}

.left-navigation .nav-section-title{
  margin: 0 0 8px 0;
  font-weight: 800;
  font-size: 12px;
  color: var(--nav-muted);
  text-transform: uppercase;
  letter-spacing: .35px;
}

.left-navigation .nav-section-title a{
  color: inherit;
  text-decoration: none;
  display:block;
  padding: 6px 8px;
  border-radius: 10px;
}

.left-navigation .nav-section-title a:hover{
  background: rgba(0,0,0,.04);
  color: var(--nav-text);
}

/* Список пунктов */
.left-navigation .nav-menu{
  list-style: none;
  margin: 0;
  padding: 0;
}

.left-navigation .nav-menu li{
  margin: 4px 0;
}

/* Пункты меню — заливка на всю ширину */
.left-navigation .nav-menu a{
  position: relative;
  display:flex;
  align-items:flex-start;
  gap: 10px;
  width: 100%;
  box-sizing: border-box;

  padding: 10px 10px 10px 12px;
  border-radius: 10px;

  color: var(--nav-text);
  text-decoration: none;
  font-size: 13px;
  line-height: 1.25;

  border: 1px solid transparent;
  transition: background-color .15s ease, border-color .15s ease, transform .12s ease;
}

.left-navigation .nav-menu a:hover{
  background: rgba(var(--nav-accent-rgb), 0.07);
  border-color: rgba(var(--nav-accent-rgb), 0.16);
}

.left-navigation .nav-menu a:active{
  transform: translateY(1px);
}

/* Активный пункт — акцентная полоска слева */
.left-navigation .nav-menu a.active{
  background: rgba(var(--nav-accent-rgb), 0.12);
  border-color: rgba(var(--nav-accent-rgb), 0.25);
  font-weight: 800;
}

.left-navigation .nav-menu a.active::before{
  content:"";
  position:absolute;
  left: 6px;
  top: 8px;
  bottom: 8px;
  width: 3px;
  border-radius: 3px;
  background: var(--nav-accent);
}

/* ===== Скроллбар: тонкий, ненавязчивый, проявляется при наведении ===== */
.left-navigation{
  scrollbar-width: thin; /* Firefox */
  scrollbar-color: rgba(var(--nav-accent-rgb), .35) transparent;
}

.left-navigation::-webkit-scrollbar{
  width: 10px;
}

.left-navigation::-webkit-scrollbar-track{
  background: transparent;
}

.left-navigation::-webkit-scrollbar-thumb{
  background: rgba(var(--nav-accent-rgb), .22);
  border-radius: 10px;
  border: 3px solid transparent;
  background-clip: content-box;
}

.left-navigation:hover::-webkit-scrollbar-thumb{
  background: rgba(var(--nav-accent-rgb), .40);
  border: 2px solid transparent;
  background-clip: content-box;
}

/* На узких экранах скрываем меню */
@media (max-width: 920px){
  .left-navigation{ display:none; }
}
</style>

