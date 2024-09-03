<?php

require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';

    // Подготовка и выполнение запроса
    $stmt = $pdo->prepare('SELECT * FROM user WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Отладка
    error_log("Received username: $username");
    error_log("Received password: $password");
    error_log("Fetched user: " . print_r($user, true));

    if ($user && $password === $user['password']) {
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'name' => $user['name'],
            'surname' => $user['surname'],
            'patronymic' => $user['patronymic'],
            'roleId' => $user['roleId']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
