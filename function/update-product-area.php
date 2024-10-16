<?php
require '../conn.php';

// Получаем данные из AJAX-запроса
$data = json_decode(file_get_contents('php://input'), true);
$productId = $data['productId'];
$productArea = $data['productArea'];

// Подготовленный запрос для обновления имени продукта
$sqlUpdate = "UPDATE `product` SET `productArea` = '$productArea' WHERE `productId` = $productId";
$conn->query($sqlUpdate);

$conn->close();
?>
