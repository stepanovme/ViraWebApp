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
    <link rel="stylesheet" href="css/style.css?<?php echo time(); ?>">
    <link rel="stylesheet" href="css/main.css?<?php echo time(); ?>">
    <link rel="stylesheet" href="css/project.css?<?php echo time(); ?>">
    <title>Проекты</title>
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
                <div class="title"><h1>Проекты по гибке металла</h1> <button type="button">Добавить</button></div>

                <?php
                $sqlProjects = "SELECT * FROM project";
                $resultProjects = $conn -> query($sqlProjects);
                
                echo '<div class="card-list">';

                    if($resultProjects -> num_rows > 0){
                        while($row = $resultProjects -> fetch_assoc()){
                            $responsibleId = $row['projectResponsible'];
                            if($row['projectStatusId'] == 1){
                                echo '
                                    <div class="card plan">
                                        <p class="title">'.$row['projectName'].'</p>
                                        <p class="responsible">'; 
                                        
                                        $sqlResponsible = "SELECT * FROM user WHERE userId = $responsibleId";
                                        $resultResponsible = $conn -> query($sqlResponsible);

                                        if($resultResponsible -> num_rows > 0){ 
                                            $row = $resultResponsible -> fetch_assoc();
                                            echo $row['name'] .' '. $row['surname'];
                                        }

                                    echo '</p>
                                        <div class="status">Планирование</div>
                                    </div>
                                    ';
                            } elseif($row['projectStatusId'] == 2){ 
                                echo '
                                <div class="card work">
                                    <p class="title">'.$row['projectName'].'</p>
                                        <p class="responsible">'; 
                                        
                                        $sqlResponsible = "SELECT * FROM user WHERE userId = $responsibleId";
                                        $resultResponsible = $conn -> query($sqlResponsible);

                                        if($resultResponsible -> num_rows > 0){ 
                                            $row = $resultResponsible -> fetch_assoc();
                                            echo $row['name'] .' '. $row['surname'];
                                        }

                                    echo '</p>
                                    <div class="status">В работе</div>
                                </div>
                                ';
                            } elseif($row['projectStatusId'] == 3){ 
                                echo '
                                <div class="card sent">
                                    <p class="title">'.$row['projectName'].'</p>
                                        <p class="responsible">'; 
                                        
                                        $sqlResponsible = "SELECT * FROM user WHERE userId = $responsibleId";
                                        $resultResponsible = $conn -> query($sqlResponsible);

                                        if($resultResponsible -> num_rows > 0){ 
                                            $row = $resultResponsible -> fetch_assoc();
                                            echo $row['name'] .' '. $row['surname'];
                                        }

                                    echo '</p>
                                    <div class="status">Отправлено</div>
                                </div>
                                ';
                            } elseif($row['projectStatusId'] == 4){ 
                                echo '
                                <div class="card shipped">
                                    <p class="title">'.$row['projectName'].'</p>
                                        <p class="responsible">'; 
                                        
                                        $sqlResponsible = "SELECT * FROM user WHERE userId = $responsibleId";
                                        $resultResponsible = $conn -> query($sqlResponsible);

                                        if($resultResponsible -> num_rows > 0){ 
                                            $row = $resultResponsible -> fetch_assoc();
                                            echo $row['name'] .' '. $row['surname'];
                                        }

                                    echo '</p>
                                    <div class="status">Отгружен</div>
                                </div>
                                ';
                            } elseif($row['projectStatusId'] == 5){ 
                                echo '
                                <div class="card completed">
                                    <p class="title">'.$row['projectName'].'</p>
                                        <p class="responsible">'; 
                                        
                                        $sqlResponsible = "SELECT * FROM user WHERE userId = $responsibleId";
                                        $resultResponsible = $conn -> query($sqlResponsible);

                                        if($resultResponsible -> num_rows > 0){ 
                                            $row = $resultResponsible -> fetch_assoc();
                                            echo $row['name'] .' '. $row['surname'];
                                        }

                                    echo '</p>
                                    <div class="status">Завершено</div>
                                </div>
                                ';
                            } else{
                                echo '
                                <div class="card">
                                    <p class="title">'.$row['projectName'].'</p>
                                        <p class="responsible">'; 
                                        
                                        $sqlResponsible = "SELECT * FROM user WHERE userId = $responsibleId";
                                        $resultResponsible = $conn -> query($sqlResponsible);

                                        if($resultResponsible -> num_rows > 0){ 
                                            $row = $resultResponsible -> fetch_assoc();
                                            echo $row['name'] .' '. $row['surname'];
                                        }

                                    echo '</p>
                                </div>
                                ';
                            }
                        }
                    }

                echo '</div>';
                ?>

            </div>
        </div>
    </div>
</body>
</html>