<?php

require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    // Подготовка и выполнение запроса
    $stmt = $pdo->prepare(
        'SELECT * FROM colorCad'
    );
    $stmt->execute();
    $colors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Отправка результата в формате JSON
    echo json_encode([
        'success' => true,
        'projects' => $colors
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
