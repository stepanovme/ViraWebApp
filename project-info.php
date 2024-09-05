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
                        <button type="button" class="active" onClick="window.location.href='project-info?projectId=<?php echo $projectId; ?>'">Заявка</button>
                        <button type="button" onClick="window.location.href='project-settings?projectId=<?php echo $projectId; ?>'">Настройки</button>
                        <button type="button" onClick="window.location.href='project-analyt?projectId=<?php echo $projectId; ?>'">Аналитика</button>
                    </div>
                    <button type="button">Добавить</button>
                </div>

                <?php
                $sqlTickets = "SELECT * FROM ticket WHERE projectId = $projectId";
                $resultTickets = $conn -> query($sqlTickets);
                $numTicket = 0;
                
                echo '<div class="card-list">';

                    if($resultTickets -> num_rows > 0){
                        while($row = $resultTickets -> fetch_assoc()){
                            $responsibleId = $row['responsibleId'];
                            $colorId = $row['colorCadId'];
                            $thicknessId = $row['thicknessMetalCadId'];
                            $numTicket += 1;
                            if($row['ticketStatusId'] == 1){
                                echo '
                                    <div class="card plan">
                                        <p class="title">Заявка на гибку №'.$numTicket.'</p>
                                        <p class="responsible">'; 
                                        
                                        $sqlResponsible = "SELECT * FROM user WHERE userId = $responsibleId";
                                        $resultResponsible = $conn -> query($sqlResponsible);

                                        if($resultResponsible -> num_rows > 0){ 
                                            $row = $resultResponsible -> fetch_assoc();
                                            echo $row['name'] .' '. $row['surname'];
                                        }

                                    echo '</p><p class="responsible">';
                                        
                                        $sqlColor = "SELECT * FROM colorCad WHERE colorCadId = $colorId";
                                        $resultColor = $conn -> query($sqlColor);

                                        if($resultColor -> num_rows > 0){ 
                                            $row = $resultColor -> fetch_assoc();
                                            echo $row['colorCadName'];
                                            echo ' ';
                                        }

                                        $sqlThicness = "SELECT * FROM thicknessMetalCad WHERE thicknessMetalCadId = $thicknessId";
                                        $resultThicness = $conn -> query($sqlThicness);

                                        if($resultThicness -> num_rows > 0){ 
                                            $row = $resultThicness -> fetch_assoc();
                                            echo $row['thicknessMetalCadName'] . ' мм';
                                        }

                                    

                                    echo '</p><div class="status">Новая</div>
                                    </div>
                                    ';
                            } elseif($row['ticketStatusId'] == 2){ 
                                echo '
                                <div class="card work">
                                    <p class="title">Заявка на гибку №'.$numTicket.'</p>
                                        <p class="responsible">'; 
                                        
                                        $sqlResponsible = "SELECT * FROM user WHERE userId = $responsibleId";
                                        $resultResponsible = $conn -> query($sqlResponsible);

                                        if($resultResponsible -> num_rows > 0){ 
                                            $row = $resultResponsible -> fetch_assoc();
                                            echo $row['name'] .' '. $row['surname'];
                                        }

                                        echo '</p><p class="responsible">';
                                        
                                        $sqlColor = "SELECT * FROM colorCad WHERE colorCadId = $colorId";
                                        $resultColor = $conn -> query($sqlColor);

                                        if($resultColor -> num_rows > 0){ 
                                            $row = $resultColor -> fetch_assoc();
                                            echo $row['colorCadName'];
                                            echo ' ';
                                        }

                                        $sqlThicness = "SELECT * FROM thicknessMetalCad WHERE thicknessMetalCadId = $thicknessId";
                                        $resultThicness = $conn -> query($sqlThicness);

                                        if($resultThicness -> num_rows > 0){ 
                                            $row = $resultThicness -> fetch_assoc();
                                            echo $row['thicknessMetalCadName']. ' мм';
                                        }

                                    

                                    echo '</p><div class="status">Согласование</div>
                                </div>
                                ';
                            } elseif($row['ticketStatusId'] == 3){ 
                                echo '
                                <div class="card sent">
                                    <p class="title">Заявка на гибку №'.$numTicket.'</p>
                                        <p class="responsible">'; 
                                        
                                        $sqlResponsible = "SELECT * FROM user WHERE userId = $responsibleId";
                                        $resultResponsible = $conn -> query($sqlResponsible);

                                        if($resultResponsible -> num_rows > 0){ 
                                            $row = $resultResponsible -> fetch_assoc();
                                            echo $row['name'] .' '. $row['surname'];
                                        }

                                        echo '</p><p class="responsible">';
                                        
                                        $sqlColor = "SELECT * FROM colorCad WHERE colorCadId = $colorId";
                                        $resultColor = $conn -> query($sqlColor);

                                        if($resultColor -> num_rows > 0){ 
                                            $row = $resultColor -> fetch_assoc();
                                            echo $row['colorCadName'];
                                            echo ' ';
                                        }

                                        $sqlThicness = "SELECT * FROM thicknessMetalCad WHERE thicknessMetalCadId = $thicknessId";
                                        $resultThicness = $conn -> query($sqlThicness);

                                        if($resultThicness -> num_rows > 0){ 
                                            $row = $resultThicness -> fetch_assoc();
                                            echo $row['thicknessMetalCadName']. ' мм';
                                        }

                                    

                                    echo '</p><div class="status">Отправлено</div>
                                </div>
                                ';
                            } elseif($row['ticketStatusId'] == 4){ 
                                echo '
                                <div class="card shipped">
                                    <p class="title">Заявка на гибку №'.$numTicket.'</p>
                                        <p class="responsible">'; 
                                        
                                        $sqlResponsible = "SELECT * FROM user WHERE userId = $responsibleId";
                                        $resultResponsible = $conn -> query($sqlResponsible);

                                        if($resultResponsible -> num_rows > 0){ 
                                            $row = $resultResponsible -> fetch_assoc();
                                            echo $row['name'] .' '. $row['surname'];
                                        }

                                        echo '</p><p class="responsible">';
                                        
                                        $sqlColor = "SELECT * FROM colorCad WHERE colorCadId = $colorId";
                                        $resultColor = $conn -> query($sqlColor);

                                        if($resultColor -> num_rows > 0){ 
                                            $row = $resultColor -> fetch_assoc();
                                            echo $row['colorCadName'];
                                            echo ' ';
                                        }

                                        $sqlThicness = "SELECT * FROM thicknessMetalCad WHERE thicknessMetalCadId = $thicknessId";
                                        $resultThicness = $conn -> query($sqlThicness);

                                        if($resultThicness -> num_rows > 0){ 
                                            $row = $resultThicness -> fetch_assoc();
                                            echo $row['thicknessMetalCadName']. ' мм';
                                        }

                                    

                                    echo '</p><div class="status">В производстве</div>
                                </div>
                                ';
                            } elseif($row['ticketStatusId'] == 6){ 
                                echo '
                                <div class="card completed">
                                    <p class="title">Заявка на гибку №'.$numTicket.'</p>
                                        <p class="responsible">'; 
                                        
                                        $sqlResponsible = "SELECT * FROM user WHERE userId = $responsibleId";
                                        $resultResponsible = $conn -> query($sqlResponsible);

                                        if($resultResponsible -> num_rows > 0){ 
                                            $row = $resultResponsible -> fetch_assoc();
                                            echo $row['name'] .' '. $row['surname'];
                                        }

                                        echo '</p><p class="responsible">';
                                        
                                        $sqlColor = "SELECT * FROM colorCad WHERE colorCadId = $colorId";
                                        $resultColor = $conn -> query($sqlColor);

                                        if($resultColor -> num_rows > 0){ 
                                            $row = $resultColor -> fetch_assoc();
                                            echo $row['colorCadName'];
                                            echo ' ';
                                        }

                                        $sqlThicness = "SELECT * FROM thicknessMetalCad WHERE thicknessMetalCadId = $thicknessId";
                                        $resultThicness = $conn -> query($sqlThicness);

                                        if($resultThicness -> num_rows > 0){ 
                                            $row = $resultThicness -> fetch_assoc();
                                            echo $row['thicknessMetalCadName']. ' мм';
                                        }

                                    

                                    echo '</p><div class="status">Завершено</div>
                                </div>
                                ';
                            } elseif($row['ticketStatusId'] == 5){ 
                                echo '
                                <div class="card shipped">
                                    <p class="title">Заявка на гибку №'.$numTicket.'</p>
                                        <p class="responsible">'; 
                                        
                                        $sqlResponsible = "SELECT * FROM user WHERE userId = $responsibleId";
                                        $resultResponsible = $conn -> query($sqlResponsible);

                                        if($resultResponsible -> num_rows > 0){ 
                                            $row = $resultResponsible -> fetch_assoc();
                                            echo $row['name'] .' '. $row['surname'];
                                        }

                                        echo '</p><p class="responsible">';
                                        
                                        $sqlColor = "SELECT * FROM colorCad WHERE colorCadId = $colorId";
                                        $resultColor = $conn -> query($sqlColor);

                                        if($resultColor -> num_rows > 0){ 
                                            $row = $resultColor -> fetch_assoc();
                                            echo $row['colorCadName'];
                                            echo ' ';
                                        }

                                        $sqlThicness = "SELECT * FROM thicknessMetalCad WHERE thicknessMetalCadId = $thicknessId";
                                        $resultThicness = $conn -> query($sqlThicness);

                                        if($resultThicness -> num_rows > 0){ 
                                            $row = $resultThicness -> fetch_assoc();
                                            echo $row['thicknessMetalCadName']. ' мм';
                                        }

                                    

                                    echo '</p><div class="status">Отгружен</div>
                                </div>
                                ';
                            } else{
                                echo '
                                <div class="card">
                                    <p class="title">Заявка на гибку №'.$numTicket.'</p>
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