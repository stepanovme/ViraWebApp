<?php
require '../conn.php';

// Получаем данные из AJAX-запроса
$data = json_decode(file_get_contents('php://input'), true);
$productId = $data['productId'];
$productQuantity = $data['productQuantity'];

// Подготовленный запрос для обновления имени продукта
$sqlUpdate = "UPDATE `product` SET `productQuantity` = '$productQuantity' WHERE `productId` = $productId";
$conn->query($sqlUpdate);

$conn->close();
?>
