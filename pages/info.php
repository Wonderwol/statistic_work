<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Открытая статистика образовательных организаций</title>
</head>
<body>
    <?php include '../header/header.php'; ?>
    <?php include '../nav/nav_left.php'; ?>
        <!-- Основной контент -->
        <div class="content-area">
            <div class="container">
		    <button style="margin-bottom: 8px;" onclick="window.history.back()">Назад</button>
                <div class="filters">
                    <p style="display: block; background-color: #eadee0; margin: 0; ">
                        <img src="../src/img/info.png" alt="Информация" style="width: 24px; height: 24px; vertical-align: middle; margin-right: 5px;">
                        Данные в разделе обновляются ежегодно 
                    </p>
                    <div class="info">
                        <p>В разделе расположены результаты сбора данных:</p>
			<ul style="margin-left: 20px;">
                        	<li><p>Федеральное статистическое наблюдение №ОО-1 «Сведения об организации, осуществляющей образовательную деятельность по образовательным программам начального общего, основного общего, среднего общего образования»;</p></li>
                        	<li><p>Федеральное статистическое наблюдение №ОО-2 «Сведения о материально-технической и информационной базе, финансово-экономической деятельности общеобразовательной организации».</p></li>
			</ul>
                        <p style="margin-top: 20px;">В разделе использованы следующие сокращения:</p>
			</ul>
                        <p>ОО – общеобразовательная организация;</p>
                        <p>НШ Д/С – начальная школа – детский сад;</p>
                        <p>НОШ – начальная общеобразовательная школа;</p>
                        <p>ООШ – основная общеобразовательная школа;</p>
                        <p>СОШ – средняя общеобразовательная школа;</p>
                        <p>СОШ с УИОП– средняя общеобразовательная школа с углубленным изучением отдельных предметов.</p>
                    </div>
                    <a href="../src/pdf/test.pdf" target="_blank" style="display: inline-block;">
                        <img src="../src/img/pdf.png" alt="Информация" style="width: 48px; height: 48px; vertical-align: middle; margin-right: 5px;">Инструкция для пользователя
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php include '../footer/footer.php'; ?>
</body>
    <?php include '../styles/style_info.php'; ?>
</html>