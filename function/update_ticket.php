<?php

require '../conn.php';

// Подключение к базе данных
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Получение данных из AJAX-запроса
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    die(json_encode(['success' => false, 'error' => 'Ошибка чтения данных JSON']));
}

$ticketId = $data['ticketId'] ?? null;
$productName = $data['productName'] ?? null;
$productLength = $data['productLength'] ?? null;
$productQuantity = $data['productQuantity'] ?? null;

if (!$ticketId || !$productName || !$productLength || !$productQuantity) {
    die(json_encode(['success' => false, 'error' => 'Отсутствуют обязательные параметры']));
}

// Подготовка SQL-запроса с параметрами
$stmt = $conn->prepare("INSERT INTO product (ticketId, productName, productLength, productQuantity) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isdi", $ticketId, $productName, $productLength, $productQuantity);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
