<?php
declare(strict_types=1);

$docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
require_once $docRoot . '/v3/config/config.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Информация — Открытая статистика</title>

    <?php
    // Берём общий визуальный стиль витрины (карточки/контейнер/типографика)
    include $docRoot . '/v3/styles/style_index.php';

    // Хедер/футер (у тебя уже вынесены)
    include $docRoot . '/v3/styles/shared/style_header.php';
    include $docRoot . '/v3/styles/shared/style_footer.php';

    // Если левое меню используешь везде — подключи его стили здесь
    include $docRoot . '/v3/styles/shared/style_nav_left.php';
    ?>

    <link rel="icon" type="image/png" sizes="16x16" href="/v3/src/img/favicon16x16.png">

    <style>
        /* Локальные правки под страницу справки */
        .info-block p { margin: 0 0 10px 0; }
        .info-block ul { margin: 8px 0 14px 20px; }
        .info-badge {
            display: block;
            background: var(--primary-light);
            padding: 10px 12px;
            border-radius: 6px;
            margin: 0 0 14px 0;
        }
        .info-badge img {
            width: 20px;
            height: 20px;
            vertical-align: middle;
            margin-right: 8px;
        }
        .doc-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            padding: 10px 12px;
            border: 1px solid var(--medium-gray);
            border-radius: 8px;
        }
        .doc-link:hover {
            background: #fafafa;
        }
        .doc-link img {
            width: 28px;
            height: 28px;
        }
    </style>
</head>
<body>

<?php
include $docRoot . '/v3/pages/shared/header.php';
include $docRoot . '/v3/nav/nav_left.php';
?>

<div class="content-area">
    <div class="container">
        <div class="filters">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom: 10px;">
                <h1 style="margin:0;">Информация</h1>
                <button class="view-btn" onclick="window.history.back()">Назад</button>
            </div>

            <span class="info-badge">
                <img src="/v3/src/img/info.png" alt="Информация">
                Данные в разделе обновляются ежегодно.
            </span>

            <div class="info-block">
                <p><strong>Источники данных:</strong></p>
                <ul>
                    <li>Федеральное статистическое наблюдение №ОО-1 «Сведения об организации, осуществляющей образовательную деятельность по образовательным программам начального общего, основного общего, среднего общего образования»;</li>
                    <li>Федеральное статистическое наблюдение №ОО-2 «Сведения о материально-технической и информационной базе, финансово-экономической деятельности общеобразовательной организации».</li>
                </ul>

                <p style="margin-top: 10px;"><strong>Сокращения:</strong></p>
                <p>ОО — общеобразовательная организация;</p>
                <p>НШ Д/С — начальная школа — детский сад;</p>
                <p>НОШ — начальная общеобразовательная школа;</p>
                <p>ООШ — основная общеобразовательная школа;</p>
                <p>СОШ — средняя общеобразовательная школа;</p>
                <p>СОШ с УИОП — средняя общеобразовательная школа с углубленным изучением отдельных предметов.</p>
            </div>

            <div style="margin-top: 16px;">
                <a class="doc-link" href="/v3/src/pdf/test.pdf" target="_blank" rel="noopener">
                    <img src="/v3/src/img/info.png" alt="PDF">
                    <span>Инструкция для пользователя (PDF)</span>
                </a>
            </div>
        </div>
    </div>
</div>

</div><!-- /.main-wrapper (nav_left открывает main-wrapper) -->

<?php include $docRoot . '/v3/pages/shared/footer.php'; ?>
</body>
</html>