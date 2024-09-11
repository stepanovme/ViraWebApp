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

if(isset($_GET['ticketId'])){
    $ticketId = $_GET['ticketId'];
}

$sqlTicketInfo = "SELECT * FROM ticket WHERE ticketId = $ticketId";
$resultTicketInfo = $conn -> query($sqlTicketInfo);

$ticketColorCadId = 0;
$ticketThicknessMetalCadId = 0; 

if($resultTicketInfo -> num_rows > 0){
    $row = $resultTicketInfo -> fetch_assoc();

    $projectId = $row['projectId'];
    $ticketArea = $row['ticketArea'];
    $ticketBrigada = $row['ticketBrigada'];
    $ticketAddressDelivery = $row['ticketAddressDelivery'];
    $ticketDatePlan = $row['ticketDatePlan'];
    $ticketColorCadId = $row['colorCadId'];
    $ticketThicknessMetalCadId = $row['thicknessMetalCadId'];
}

$sqlProjectTicketInfo = "SELECT * FROM project WHERE projectId = $projectId";
$resultProjectTicketInfo = $conn -> query($sqlProjectTicketInfo);

if($resultProjectTicketInfo -> num_rows > 0){
    $row = $resultProjectTicketInfo -> fetch_assoc();

    $object = $row['projectObject'];
}

$sqlSumQuantity = "SELECT * FROM ticket WHERE projectId = $projectId";


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
    <link rel="stylesheet" href="css/ticket.css?<?php echo time(); ?>">
    <title>Информация по заявке</title>
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
                <div class="title"><h1><a href="project-info?projectId=<?php echo $projectId; ?>"><</a> Заявка на сгибку металла №1</h1></div>

                <div class="info">
                    <div class="col">
                        <p>Объект:</p>
                        <input type="text" name="" id="" readonly value="<?php echo $object; ?>">
                        <p>Цвет:</p>
                        <select id="colorCadId" onchange="updateTicket('colorCadId', this.value)">
                            <option value="0" disabled>Цвет</option>
                            <?php 
                            $sqlColorTicket = "SELECT * FROM projectColorCad pcc LEFT JOIN colorCad cc ON pcc.colorCadId = cc.colorCadId WHERE pcc.projectId = $projectId";
                            $resultColorTicket = $conn->query($sqlColorTicket);

                            if ($resultColorTicket->num_rows > 0) {
                                while ($row = $resultColorTicket->fetch_assoc()) {
                                    // Проверяем, если уже есть значение, и выбираем его
                                    $selected = $row['colorCadId'] == $ticketColorCadId ? 'selected' : '';
                                    echo '<option value="' . $row['colorCadId'] . '" ' . $selected . '>' . $row['colorCadName'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                        <p>Толщина:</p>
                        <select id="thicknessMetalCadId" onchange="updateTicket('thicknessMetalCadId', this.value)">
                            <option value="0" disabled>Толщина</option>
                            <?php 
                            $sqlThicknessTicket = "SELECT * FROM projectThicknessMetalCad ptm LEFT JOIN thicknessMetalCad tm ON ptm.thicknessMetalCadId = tm.thicknessMetalCadId WHERE ptm.projectId = $projectId";
                            $resultThicknessTicket = $conn->query($sqlThicknessTicket);

                            if ($resultThicknessTicket->num_rows > 0) {
                                while ($row = $resultThicknessTicket->fetch_assoc()) {
                                    // Проверяем, если уже есть значение, и выбираем его
                                    $selected = $row['thicknessMetalCadId'] == $ticketThicknessMetalCadId ? 'selected' : '';
                                    echo '<option value="' . $row['thicknessMetalCadId'] . '" ' . $selected . '>' . $row['thicknessMetalCadName'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                        <p>Участок:</p>
                        <input type="text" id="ticketArea" value="<?php echo $ticketArea; ?>" onblur="updateTicket('ticketArea', this.value)">
                        <p>Бригада:</p>
                        <input type="text" id="ticketBrigada" value="<?php echo $ticketBrigada; ?>" onblur="updateTicket('ticketBrigada', this.value)">
                        <p>Адрес доставки:</p>
                        <input type="text" id="ticketAddressDelivery" value="<?php echo $ticketAddressDelivery; ?>" onblur="updateTicket('ticketAddressDelivery', this.value)">
                    </div>
                    <div class="col">
                        <p>Дата план:</p>
                        <input type="date" id="ticketDatePlan" value="<?php echo $ticketDatePlan; ?>" onchange="updateTicket('ticketDatePlan', this.value)">
                        <p>Кол-во изделий:</p>
                        <input type="text" name="" id="" readonly>
                        <p>Погонаж:</p>
                        <input type="text" name="" id="" readonly>
                    </div>
                </div>

                <div class="products">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 40px;">Поз.</th>
                                <th>Изделие</th>
                                <th style="width: 100px;">Сумма разв.</th>
                                <th style="width: 100px;">L, м</th>
                                <th style="width: 100px;">Кол-во, шт</th>
                                <th style="width: 100px;">Место</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sqlProductsList = "SELECT * FROM product WHERE ticketId = $ticketId";
                            $resultProductsList = $conn -> query($sqlProductsList);

                            $numProductList = 0;
                            if($resultProductsList -> num_rows > 0){
                                while($row = $resultProductsList -> fetch_assoc()){
                                    $numProductList += 1;
                                    $productId = $row['productId'];
                                    $productLength = $row['productLength'];
                                    $productQuantity = $row['productQuantity'];
                                    $productArea = $row['productArea'];
                                    echo '
                                        <tr>
                                            <td>'.$numProductList.'</td>
                                            <td style="display: flex; flex-direction: column; padding: 0;"><input type="text" name="" id="" value="'.$row['productName'].'">
                                                <canvas onClick="window.location.href = \'product-cad?productId='.$row['productId'].'\'"></canvas>
                                            </td>';
                                        
                                         $sqlProductSum = "SELECT SUM(`number`) as sumLength FROM `lines` WHERE productId = $productId";
                                         $resultProductSum = $conn -> query($sqlProductSum);

                                         if($resultProductSum -> num_rows > 0){
                                            $row = $resultProductSum -> fetch_assoc();
                                            echo '<td>'.$row['sumLength'].'</td>';
                                         } else{
                                            echo '<td>0</td>';
                                         }

                                    echo '
                                            <td style="cursor: pointer;" contenteditable="">'.$productLength.'</td>
                                            <td style="cursor: pointer;" contenteditable="">'.$productQuantity.'</td>
                                            <td style="cursor: pointer;" contenteditable="">'.$productArea.'</td>
                                        </tr>
                                        ';
                                }
                            }
                            ?>
                            <tr>
                                <td colspan="6">+</td>
                            </tr>
                        </tbody>
                    </table>

                    <p class="total">Итого количество: 300 м.п.</p>
                </div>

            </div>
        </div>
    </div>

    <script>
    // Функция для отправки AJAX-запроса
    function updateTicket(field, value) {
        var ticketId = "<?php echo $ticketId; ?>";
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "function/update_ticket", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                console.log(xhr.responseText);
            }
        };
        xhr.send("ticketId=" + ticketId + "&field=" + field + "&value=" + value);
    }


</script>
</body>
</html>