<?php
declare(strict_types=1);

$uri = strtok((string)($_SERVER['REQUEST_URI'] ?? ''), '?');

function hdr_selected(bool $cond): string {
    return $cond ? 'selected' : '';
}

$isHome   = ($uri === '/' || $uri === '' || strpos($uri, '/index.php') !== false);
$isStats  = (strpos($uri, '/statistics/') !== false);
?>
<div id="header">
    <!-- Логотип -->
    <a class="logo" href="http://nimro.ru" title="Новосибирский Институт Мониторинга и Развития Образования">
        <img alt="НИМРО" src="/statistics/src/img/logo_with_title_horizontal.png">
    </a>

    <!-- Правые кнопки в шапке -->
    <div class="header-actions" role="group" aria-label="Сервисы">
        <a class="header-login" href="/personal/" title="Вход в личный кабинет">Вход в личный кабинет</a>

        <a class="header-eye" href="/special/" title="Версия для слабовидящих" aria-label="Версия для слабовидящих">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path d="M12 5C6.5 5 2.1 8.3 1 12c1.1 3.7 5.5 7 11 7s9.9-3.3 11-7c-1.1-3.7-5.5-7-11-7zm0 12c-2.8 0-5-2.2-5-5s2.2-5 5-5 5 2.2 5 5-2.2 5-5 5zm0-8.2A3.2 3.2 0 1 0 12 15a3.2 3.2 0 0 0 0-6.2z"
                      fill="currentColor"/>
            </svg>
        </a>
    </div>

    <!-- Основное меню -->
    <div class="header-menu-block">
        <ul class="header-menu" id="main-menu">
            <li class="menu-home">
                <a class="<?= hdr_selected($isHome) ?>" href="/" aria-label="Главная" title="Главная">
                    <svg class="menu-home-icon" aria-hidden="true" focusable="false" role="img"
                         xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                        <path fill="currentColor"
                              d="M280.37 148.26L96 300.11V464a16 16 0 0 0 16 16l112.06-.29a16 16 0 0 0 15.92-16V368a16 16 0 0 1 16-16h64a16 16 0 0 1 16 16v95.64a16 16 0 0 0 16 16.05L464 480a16 16 0 0 0 16-16V300L295.67 148.26a12.19 12.19 0 0 0-15.3 0zM571.6 251.47L488 182.56V44.05a12 12 0 0 0-12-12h-56a12 12 0 0 0-12 12v72.61L318.47 43a48 48 0 0 0-61 0L4.34 251.47a12 12 0 0 0-1.6 16.9l25.5 31A12 12 0 0 0 45.15 301l235.22-193.74a12.19 12.19 0 0 1 15.3 0L530.9 301a12 12 0 0 0 16.9-1.6l25.5-31a12 12 0 0 0-1.7-16.93z">
                        </path>
                    </svg>
                </a>
            </li>

            <li><a href="/sveden/">Сведения об образовательной организации</a></li>
            <li><a href="/about/">Об институте</a></li>
            <li><a href="/actual/">Актуальное</a></li>
            <li><a href="/questions/">Вопросы-ответы</a></li>
            <li><a href="/news/">Новости</a></li>
            <li><a href="/map/">Карта сайта</a></li>
            <li><a href="/noko/info">НОКО</a></li>
            <li><a href="/nimro_innovations/">РИП</a></li>

            <li>
                <a class="<?= hdr_selected($isStats) ?>"
                   href="/statistics/pages/edu_orgs/chapter1/by_type.php">Статистические данные</a>
            </li>
        </ul>
    </div>
</div>
