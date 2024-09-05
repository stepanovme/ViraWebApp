<?php
require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $projectName = $_POST['projectName'] ?? '';
    $projectObject = $_POST['projectObject'] ?? '';
    $responsibles = explode(',', $_POST['responsibles'] ?? '');

    if (empty($projectName) || empty($projectObject)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    try {
        // Insert into project table
        $stmt = $pdo->prepare('INSERT INTO project (projectName, projectObject, projectResponsible) VALUES (?, ?, ?)');
        $stmt->execute([$projectName, $projectObject, $responsibles[0] ?? null]);
        $projectId = $pdo->lastInsertId();

        // Insert colors
        foreach ($_POST['colors'] as $colorId) {
            $stmt = $pdo->prepare('INSERT INTO projectColorCad (projectId, colorCadId) VALUES (?, ?)');
            $stmt->execute([$projectId, $colorId]);
        }

        // Insert thicknesses
        foreach ($_POST['thicknesses'] as $thicknessId) {
            $stmt = $pdo->prepare('INSERT INTO projectThicknessMetalCad (projectId, thicknessMetalCadId) VALUES (?, ?)');
            $stmt->execute([$projectId, $thicknessId]);
        }

        echo json_encode(['success' => true, 'projectId' => $projectId]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
