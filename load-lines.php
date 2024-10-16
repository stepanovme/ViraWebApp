<?php
header('Content-Type: application/json');

// Подключение к базе данных
require 'conn.php';

// Получаем productId из запроса
$productId = isset($_GET['productId']) ? intval($_GET['productId']) : 0;

// Инициализируем массивы для линий и текстов
$lines = [];
$texts = [];

// Получаем данные о линиях
$sql = "SELECT lineId, productId, x1, y1, x2, y2, isArc, isArrow, number FROM lines WHERE productId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $lines[] = $row;
}

// Получаем данные о текстах
$sql = "SELECT textId, productId, text, x, y FROM texts WHERE productId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $texts[] = $row;
}

// Закрываем соединение
$stmt->close();
$conn->close();

// Отправляем данные в формате JSON
echo json_encode(['lines' => $lines, 'texts' => $texts]);
?>
