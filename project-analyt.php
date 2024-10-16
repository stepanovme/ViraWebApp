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

if(isset($_GET['projectId'])){
    $projectId = $_GET['projectId'];
}

$sqlProjectInfo = "SELECT * FROM project WHERE projectId = $projectId";
$resultProjectInfo = $conn -> query($sqlProjectInfo);

if($resultProjectInfo -> num_rows > 0){
    $row = $resultProjectInfo -> fetch_assoc();

    $projectName = $row['projectName'];
    $projectObject = $row['projectObject'];
    $projectStatusId = $row['projectStatusId'];
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
    <link rel="stylesheet" href="css/project-info.css?<?php echo time(); ?>">
    <title>Информация по проекту</title>
</head>
<body>
    <div class="container">
        <div class="nav">
            <div class="logo" onclick="window.location.href = '/'">
                VIRA
            </div>

            <p class="title">ГЛАВНОЕ МЕНЮ</p>
            <a href="/"><img src="/assets/icons/dashboard.svg"> Дашборд</a>
            <a href="metal.php" class="active"><img src="/assets/icons/metal.svg"> Сгибка металла</a>
            <a href="warehouse.php"><img src="/assets/icons/warehouse.svg"> Склад</a>
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
                <div class="title"><h1><a href="metal"><</a> <?php echo $projectName;?></h1> 
                    
                    <?php 
                    if($projectStatusId == 1){
                        echo '<div class="status plan">Планирование</div>';
                    }elseif($projectStatusId == 2){
                        echo '<div class="status work">В работе</div>';
                    }elseif($projectStatusId == 3){
                        echo '<div class="status sent">Отправлено</div>';
                    }elseif($projectStatusId == 4){
                        echo '<div class="status shipped">Отгружен</div>';
                    }elseif($projectStatusId == 5){
                        echo '<div class="status complete">Завершено</div>';
                    }
                    ?>
                </div>
                <div class="subtitle">
                    <div class="nav">
                        <button type="button" onClick="window.location.href='project-info?projectId=<?php echo $projectId; ?>'">Заявка</button>
                        <button type="button" onClick="window.location.href='project-settings?projectId=<?php echo $projectId; ?>'">Настройки</button>
                        <button type="button" class="active" onClick="window.location.href='project-analyt?projectId=<?php echo $projectId; ?>'">Аналитика</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>