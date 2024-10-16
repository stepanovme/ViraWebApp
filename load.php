<?php
session_start();
require 'conn.php';

// Убедитесь, что пользователь авторизован
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Получите идентификатор продукта из GET-параметра
$productId = intval($_GET['productId']);

if ($productId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid product ID']);
    exit;
}

// Извлечение линий
$linesQuery = "SELECT * FROM `lines` WHERE productId = $productId";
$linesResult = $conn->query($linesQuery);
if ($linesResult === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to retrieve lines']);
    exit;
}

$lines = [];
while ($line = $linesResult->fetch_assoc()) {
    $lines[] = $line;
}

// Извлечение текстов
$textsQuery = "SELECT * FROM `texts` WHERE productId = $productId";
$textsResult = $conn->query($textsQuery);
if ($textsResult === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to retrieve texts']);
    exit;
}

$texts = [];
while ($text = $textsResult->fetch_assoc()) {
    $texts[] = $text;
}

// Формирование ответа
$response = [
    'productId' => $productId,
    'lines' => $lines,
    'texts' => $texts
];

echo json_encode($response);

$conn->close();
?>
