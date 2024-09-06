<?php
session_start();

require 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST['login'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM user WHERE username='$login' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['userId'];
        header('Location: /');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="/assets/favicon/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css?<?php echo time(); ?>">
    <link rel="stylesheet" href="css/auth.css">
    <title>Авторизация</title>
</head>
<body>
    <div class="container">
        <img src="/assets/images/auth.jpg" alt="">
        <div class="content">
            <div class="auth">
                <form method="POST">
                    <h1>Авторизация</h1>
                    <label for="">Логин</label>
                    <input type="text" name="login" placeholder="Логин">
                    <label for="">Пароль</label>
                    <input type="password" name="password" placeholder="********">
                    <button type="submit">Войти</button>
                    <a href="">Ещё нет аккаунта?</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>