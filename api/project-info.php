<?php

require 'db.php';

header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if (isset($_GET['projectId'])) {
        $projectId = $_GET['projectId'];

        $stmt = $pdo->prepare(
            'SELECT 
                t.ticketId,
                t.projectId,
                t.responsibleId,
                t.colorCadId,
                c.colorCadName,
                t.thicknessMetalCadId,
                tm.thicknessMetalCadName,
                u.name AS responsibleName,
                u.surname AS responsibleSurname,
                t.*
            FROM ticket t
            LEFT JOIN colorCad c ON t.colorCadId = c.colorCadId
            LEFT JOIN thicknessMetalCad tm ON t.thicknessMetalCadId = tm.thicknessMetalCadId
            LEFT JOIN user u ON t.responsibleId = u.userId
            WHERE t.projectId = :projectId'
        );
        $stmt->bindParam(':projectId', $projectId, PDO::PARAM_INT);
        $stmt->execute();
        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($tickets) {
            echo json_encode([
                'success' => true,
                'tickets' => $tickets
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No tickets found for this projectId']);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'projectId parameter is missing']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

?>
