<?php

require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    // Подготовка и выполнение запроса
    $stmt = $pdo->prepare(
        'SELECT * FROM thicknessMetalCad'
    );
    $stmt->execute();
    $thickness = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Отправка результата в формате JSON
    echo json_encode([
        'success' => true,
        'projects' => $thickness
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
