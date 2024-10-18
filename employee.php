<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
    $login = $_POST['login'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $patronymic = $_POST['patronymic'];
    $role = $_POST['role'];

    // Using prepared statements to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO user (name, surname, patronymic, username, password, roleId) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $name, $surname, $patronymic, $login, $password, $role);

    $stmt->close();
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
    <link rel="stylesheet" href="css/employee.css?<?php echo time(); ?>">
    <title>Дашборд</title>
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
            <a href="warehouse.php"><img src="/assets/icons/warehouse.svg"> Склад</a>
            <p class="title">ИНФОРМАЦИЯ</p>
            <a href="employee.php" class="active"><img src="/assets/icons/employee.svg"> Сотрудники</a>
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
                <div class="title">
                    <h1>Сотрудники</h1>
                    <button type="button" id="btnAdd">Добавить</button>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Сотрудник</th>
                            <th>Логин</th>
                            <th>Пароль</th>
                            <th>Роль</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sqlUserList = "SELECT * FROM user u LEFT JOIN role r ON u.roleId = r.roleId";
                        $resultUserList = $conn -> query($sqlUserList);

                        if($resultUserList -> num_rows > 0){
                            while($row = $resultUserList -> fetch_assoc()){
                                echo '
                                    <tr>
                                        <td>'.$row['name'].' '.$row['surname'].' '.$row['patronymic'].'</td>
                                        <td>'.$row['username'].'</td>
                                        <td>'.$row['password'].'</td>
                                        <td>'.$row['roleName'].'</td>
                                    </tr>
                                ';
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
                <p class="title">Добавить пользователя</p>
                <label>Имя</label>
                <input type="text" id="name" name="name" placeholder="Иван">
                <label>Фамилия</label>
                <input type="text" id="surname" name="surname" placeholder="Иванов">
                <label>Отчество</label>
                <input type="text" id="patronymic" name="patronymic" placeholder="Иванович">
                <label>Логин</label>
                <input type="text" id="login" name="login" placeholder="Ivanov">
                <label>Пароль</label>
                <input type="password" id="password" name="password" placeholder="*******">
                <label>Роль</label>
                <select name="role" id="role">
                    <option selected disabled>Роль</option>
                    <?php
                    $sqlRoleList = "SELECT * FROM role";
                    $resultRoleList = $conn -> query($sqlRoleList);

                    if($resultRoleList -> num_rows > 0){
                        while($row = $resultRoleList -> fetch_assoc()){
                            echo '
                                <option value="'.$row['roleId'].'">'.$row['roleName'].'</option>
                                ';
                        }
                    }
                    ?>
                </select>
                <button type="submit">Добавить</button>
                <button type="button" id="modalBtnCancel">Отменить</button>
            </form>
        </div>
    </div>

    <script>
        const btnAdd = document.getElementById('btnAdd')
        const modalBtnCancel = document.getElementById('modalBtnCancel')
        const modal = document.getElementById('modal')

        btnAdd.addEventListener('click', function(){
            modal.style.display = 'flex'
        })

        modalBtnCancel.addEventListener('click', function(){
            modal.style.display = 'none'
        })
    </script>
</body>
</html>