<?php

require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $name = $data['name'] ?? '';
    $surname = $data['surname'] ?? '';
    $patronymic = $data['patronymic'] ?? '';
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    $roleId = $data['roleId'] ?? '';

    if ($name && $surname && $username && $password && $roleId) {
        // Подготовка и выполнение запроса
        $stmt = $pdo->prepare('INSERT INTO user (name, surname, patronymic, username, password, roleId) VALUES (?, ?, ?, ?, ?, ?)');
        if ($stmt->execute([$name, $surname, $patronymic, $username, $password, $roleId])) {
            echo json_encode(['success' => true, 'message' => 'Пользователь добавлен']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Ошибка добавления пользователя']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Заполните все поля']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
