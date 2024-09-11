<?php
session_start();
require 'conn.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$data = json_decode(file_get_contents('php://input'), true);
$productId = intval($data['productId']);
$lines = $data['lines'];
$texts = $data['texts'];

// Удаляем старые данные
if (!$conn->query("DELETE FROM `lines` WHERE productId = $productId")) {
    die("Ошибка удаления данных из таблицы lines: " . $conn->error);
}
if (!$conn->query("DELETE FROM `texts` WHERE productId = $productId")) {
    die("Ошибка удаления данных из таблицы texts: " . $conn->error);
}

// Вставляем новые линии
$stmt = $conn->prepare("INSERT INTO `lines` (productId, x1, y1, x2, y2, isArc, isArrow, number) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    die("Ошибка подготовки запроса: " . $conn->error);
}

foreach ($lines as $line) {
    $isArc = isset($line['isArc']) ? intval($line['isArc']) : 0;
    $isArrow = isset($line['isArrow']) ? intval($line['isArrow']) : 0;
    $stmt->bind_param('iiiiiiii', $productId, $line['x1'], $line['y1'], $line['x2'], $line['y2'], $isArc, $isArrow, $line['number']);
    if (!$stmt->execute()) {
        die("Ошибка выполнения запроса на вставку линий: " . $stmt->error);
    }
}
$stmt->close();

// Вставляем новый текст
$stmt = $conn->prepare("INSERT INTO `texts` (productId, text, x, y) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    die("Ошибка подготовки запроса: " . $conn->error);
}

foreach ($texts as $text) {
    $stmt->bind_param('issi', $productId, $text['text'], $text['x'], $text['y']);
    if (!$stmt->execute()) {
        die("Ошибка выполнения запроса на вставку текста: " . $stmt->error);
    }
}
$stmt->close();

$conn->close();
?>
