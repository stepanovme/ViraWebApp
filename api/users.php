<?php

require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    // Подготовка и выполнение запроса
    $stmt = $pdo->prepare(
        'SELECT * FROM user u LEFT JOIN role r ON u.roleId = r.roleId'
    );
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Отправка результата в формате JSON
    echo json_encode([
        'success' => true,
        'projects' => $users
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
