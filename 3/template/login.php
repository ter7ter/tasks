<!DOCTYPE html>
<head>
    <title>Авторизация</title>
    <link href="style.css" rel="stylesheet" />
</head>
<body>
<form method="post" action="login.php">
    <?= ($errorAuth ? '<p style=color:red;text-align:center;">Неверный логин или пароль!</p>' : null); ?>
    Логин:<br><input type=text name='login'><br/>
    Пароль<br/><input type=password name='password'/></br/>
    <input type ="submit" value="Войти">
</form>
</body>
</html>
