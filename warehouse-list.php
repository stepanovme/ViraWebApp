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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $address = $_POST['address'];

    if($name !== ''){
        $warhouseAdd = "INSERT INTO warehouseList (warehouseName, warehouseAddress) VALUES ('$name','$address')";
        $resultWarehouse = $conn->query($warhouseAdd);
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
                <h1>Состояние склада</h1>
                <div class="subtitle">
                    <div class="nav">
                        <button type="button" onclick="window.location.href = 'warehouse'">Состояние склада</button>
                        <button type="button">Склад обрези</button>
                        <button type="button">Склад изделий</button>
                        <button type="button" class="active">Список складов</button>
                    </div>
                    <form>
                        <button type="button" id="btnModalAdd">Добавить</button>
                    </form>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Название</th>
                            <th>Адрес</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sqlWarhouseList = "SELECT * FROM warehouseList";
                        $resultWarehouseList = $conn -> query($sqlWarhouseList);

                        if($resultWarehouseList -> num_rows > 0){
                            while($row = $resultWarehouseList -> fetch_assoc()){
                                echo '<tr>';
                                echo '<td>'.$row['warehouseName'].'</td>';
                                echo '<td>'.$row['warehouseAddress'].'</td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal" id="modal">
        <div class="modal-content">
            <form method="POST">
                <p class="title">Добавить склад</p>
                <label>Название</label>
                <input type="text" id="name" name="name" placeholder="Зелёный бор" required>
                <label>Адрес</label>
                <input type="text" id="address" name="address" placeholder="Ул. Стрелковая, д.4">
                <button type="submit">Добавить</button>
                <button type="button" id="modalBtnCancel">Отменить</button>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('modal')
        const modalBtnAdd = document.getElementById('btnModalAdd')
        const modalBtnCancel = document.getElementById('modalBtnCancel')

        modalBtnAdd.addEventListener('click', function(){
            modal.style.display = 'flex'
        })

        modalBtnCancel.addEventListener('click', function(){
            modal.style.display = 'none'
        })
    </script>
</body>
</html>