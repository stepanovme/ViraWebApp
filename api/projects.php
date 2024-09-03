<?php

require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    // Подготовка и выполнение запроса
    $stmt = $pdo->prepare(
        'SELECT 
            p.projectId, 
            p.projectName, 
            p.projectStatusId, 
            u.name AS responsibleName, 
            u.surname AS responsibleSurname, 
            u.patronymic AS responsiblePatronymic 
        FROM project p 
        LEFT JOIN user u ON p.projectResponsible = u.userId'
    );
    $stmt->execute();
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Отправка результата в формате JSON
    echo json_encode([
        'success' => true,
        'projects' => $projects
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
