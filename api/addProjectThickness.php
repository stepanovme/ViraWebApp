<?php
require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $projectId = $_POST['projectId'] ?? '';
    $thicknessMetalCadId = $_POST['thicknessMetalCadId'] ?? '';

    if (empty($projectId) || empty($thicknessMetalCadId)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    try {
        $stmt = $pdo->prepare('INSERT INTO projectThicknessMetalCad (projectId, thicknessMetalCadId) VALUES (?, ?)');
        $stmt->execute([$projectId, $thicknessMetalCadId]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>