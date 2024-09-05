<?php

require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    // Подготовка и выполнение запроса
    $stmt = $pdo->prepare(
        'SELECT * FROM user WHERE roleId = 3'
    );
    $stmt->execute();
    $responsible = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Отправка результата в формате JSON
    echo json_encode([
        'success' => true,
        'projects' => $responsible
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
