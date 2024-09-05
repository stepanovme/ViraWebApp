<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$user_id = $_SESSION['user_id'];

require 'conn.php';

$sql = "SELECT * FROM user WHERE userId = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $surname = $row['surname'];
    $roleId = $row['roleId'];
}

$sql = "SELECT * FROM role WHERE roleId = $roleId";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $roleName = $row['roleName'];
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
    <link rel="stylesheet" href="css/main.css?<?php echo time(); ?>">
    <title>Дашборд</title>
</head>
<body>
    <div class="container">
        <div class="nav">
            <div class="logo" onclick="window.location.href = '/'">
                VIRA
            </div>

            <p class="title">ГЛАВНОЕ МЕНЮ</p>
            <a href="/" class="active"><img src="/assets/icons/dashboard.svg"> Дашборд</a>
            <a href="metal.php"><img src="/assets/icons/metal.svg"> Сгибка металла</a>
            <p class="title">ИНФОРМАЦИЯ</p>
            <a href="employee.php"><img src="/assets/icons/employee.svg"> Сотрудники</a>
        </div>
        <div class="content">
            <header>
                <div class="profile">
                    <img src="/assets/icons/avatar.jpg">
                    <div class="info">
                        <p class="name"><?php echo $name .' '. $surname; ?></p>
                        <p class="role"><?php echo $roleName; ?></p>
                    </div>
                </div>
            </header>

            <div class="page">
                <h1>Дашборд</h1>
                <p>Привет, <?php echo $name .' '. $surname; ?>!</p>
            </div>
        </div>
    </div>
</body>
</html>