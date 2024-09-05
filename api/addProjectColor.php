<?php
require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $projectId = $_POST['projectId'] ?? '';
    $colorCadId = $_POST['colorCadId'] ?? '';

    if (empty($projectId) || empty($colorCadId)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    try {
        $stmt = $pdo->prepare('INSERT INTO projectColorCad (projectId, colorCadId) VALUES (?, ?)');
        $stmt->execute([$projectId, $colorCadId]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>