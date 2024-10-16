<?php

require '../conn.php';

// Подключение к базе данных
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Получение данных из AJAX-запроса
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    die('Ошибка чтения данных JSON');
}

$ticketId = $data['ticketId'] ?? null;

if (!$ticketId) {
    die('Отсутствуют обязательные параметры');
}

// Подготовка SQL-запроса
$sql = "INSERT INTO product (ticketId) VALUES ($ticketId)";

if ($conn->query($sql)) {
    echo json_encode(['success' => true]);
}

?>
