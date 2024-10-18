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
    <link rel="stylesheet" href="css/warehouse.css?<?php echo time(); ?>">
    <title>Склад</title>
</head>
<body>
    <div class="container">
        <div class="nav">
            <div class="logo" onclick="window.location.href = '/'">
                VIRA
            </div>

            <p class="title">ГЛАВНОЕ МЕНЮ</p>
            <a href="/"><img src="/assets/icons/dashboard.svg"> Дашборд</a>
            <a href="metal.php"><img src="/assets/icons/metal.svg"> Сгибка металла</a>
            <a href="warehouse.php" class="active"><img src="/assets/icons/warehouse.svg"> Склад</a>
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
                <h1>Склад</h1>
                <div class="subtitle">
                    <div class="nav">
                        <button type="button" class="active">Состояние склада</button>
                        <button type="button">Склад обрези</button>
                        <button type="button">Склад изделий</button>
                        <button type="button" onclick="window.location.href = 'warehouse-list'">Список складов</button>
                    </div>

                </div>
                <div class="search">
                    <input type="text" name="" id="" placeholder="Поиск">
                    <select name="" id="">
                        <option value="" selected diasbled>По наличию</option>
                        <option value="">В наличие</option>
                        <option value="">Нет в наличие</option>
                    </select>
                    <select name="" id="">
                        <option value="" selected diasbled>По толщине</option>
                        <option value="">0,4</option>
                        <option value="">0,5</option>
                        <option value="">0,7</option>
                    </select>
                    <select name="" id="">
                        <option value="" selected diasbled>По цвету</option>
                        <option value="">RAL 7024</option>
                        <option value="">RAL 9003</option>
                        <option value="">RAL 5005</option>
                        <option value="">RAL 7004</option>
                    </select>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Артикул</th>
                            <th>Название</th>
                            <th>Наличие</th>
                            <th>Цена</th>
                            <th>Сумма</th>
                            <th>Место</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>ПЛОСЛИСТ</td>
                            <td>Плоский лист</td>
                            <td>100</td>
                            <td>815</td>
                            <td>81 500</td>
                            <td>Зелёный бор</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>