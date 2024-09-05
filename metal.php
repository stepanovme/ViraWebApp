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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $projectName = $_POST['project_name'];
    $responsible = $_POST['responsible'];
    $projectObject = $_POST['object'];
    $colors = $_POST['colors'];
    $thicknesses = $_POST['thickness'];

    $sqlInsertProject = "INSERT INTO project (projectName, projectObject, projectResponsible) VALUES ('$projectName', '$projectObject', '$responsible')";
    if ($conn->query($sqlInsertProject) === TRUE) {
        $projectId = $conn->insert_id;

        foreach ($colors as $colorCadId) {
            $sqlInsertProjectColor = "INSERT INTO projectColorCad (projectId, colorCadId) VALUES ('$projectId', '$colorCadId')";
            $conn->query($sqlInsertProjectColor);
        }

        foreach ($thicknesses as $thicknessMetalCadId) {
            $sqlInsertProjectThickness = "INSERT INTO projectThicknessMetalCad (projectId, thicknessMetalCadId) VALUES ('$projectId', '$thicknessMetalCadId')";
            $conn->query($sqlInsertProjectThickness);
        }

        header("Location: metal");
        exit();
    } else {
        echo "Error: " . $conn->error;
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="shortcut icon" href="/assets/favicon/favicon.png" type="image/x-icon">
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
                <div class="title"><h1>Проекты по гибке металла</h1> <button type="button" id="btnAdd">Добавить</button></div>

                <?php
                $sqlProjects = "SELECT * FROM project";
                $resultProjects = $conn -> query($sqlProjects);
                
                echo '<div class="card-list">';

                    if($resultProjects -> num_rows > 0){
                        while($row = $resultProjects -> fetch_assoc()){
                            $responsibleId = $row['projectResponsible'];
                            if($row['projectStatusId'] == 1){
                                echo '
                                    <div class="card plan" onClick="window.location.href = \'project-info?projectId='.$row['projectId'].'\'">
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
                                <div class="card work" onClick="window.location.href = \'project-info?projectId='.$row['projectId'].'\'">
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
                                <div class="card sent" onClick="window.location.href = \'project-info?projectId='.$row['projectId'].'\'">
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
                                <div class="card shipped" onClick="window.location.href = \'project-info?projectId='.$row['projectId'].'\'">
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
                                <div class="card completed" onClick="window.location.href = \'project-info?projectId='.$row['projectId'].'\'">
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
                                <div class="card" onClick="window.location.href = \'project-info?projectId='.$row['projectId'].'\'" >
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

    <div class="modal" id="modal">
        <div class="modal-content">
            <form method="POST">
                <p class="title">Создание проекта</p>
                <label>Название проекта</label>
                <input type="text" placeholder="Название" name="project_name">
                <label>Объект</label>
                <input type="text" placeholder="Объект" name="object">
                <label>Цвет</label>
                <select name="colors[]" id="colors" class="select2" multiple>
                    <?php
                    $sqlModalColors = "SELECT * FROM colorCad";
                    $resultModalColors = $conn -> query($sqlModalColors);

                    if($resultModalColors -> num_rows > 0){
                        while($row = $resultModalColors -> fetch_assoc()){
                            echo '<option value='.$row['colorCadId'].'>'.$row['colorCadName'].'</option>';
                        }
                    }
                    ?>
                </select>
                <label>Толщина</label>
                <select name="thickness[]" id="thickness" class="select2" multiple>
                    <?php
                    $sqlModalThickness = "SELECT * FROM thicknessMetalCad";
                    $resultModalThickness = $conn -> query($sqlModalThickness);

                    if($resultModalThickness -> num_rows > 0){
                        while($row = $resultModalThickness -> fetch_assoc()){
                            echo '<option value='.$row['thicknessMetalCadId'].'>'.$row['thicknessMetalCadName'].'</option>';
                        }
                    }
                    ?>
                </select>
                <label for="">Ответственный</label>
                <select name="responsible" id="responsible">
                    <?php
                    $sqlModalResponsible = "SELECT * FROM user WHERE roleId = 3";
                    $resultModalResponsible = $conn -> query($sqlModalResponsible);

                    if($resultModalResponsible -> num_rows > 0){
                        echo '<option selected>Ответственный</option>';

                        while($row = $resultModalResponsible -> fetch_assoc()){
                            echo '<option value='.$row['userId'].'>'.$row['name'].' '.$row['surname'].'</option>';
                        }
                    }
                    ?>
                </select>
                <button type="submit" id="btnModalAdd">Добавить</button>
                <button type="button" id="btnModalCancel">Отменить</button>
            </form>
        </div>
    </div>

    <script src="js/jquery.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Initialize Select2
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select options"
            });
        });
    </script>
    <script src="js/metal.js"></script>

</body>
</html>