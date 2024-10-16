<?php
require '../conn.php';

// Получаем данные из AJAX-запроса
$data = json_decode(file_get_contents('php://input'), true);
$productId = $data['productId'];

// Удаление продукта из таблицы
$sqlDelete = "DELETE FROM `product` WHERE `productId` = $productId";
$conn->query($sqlDelete);

$conn->close();
?>
