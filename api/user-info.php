<?php

require 'db.php';

header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if (isset($_GET['userId'])) {
        $userId = $_GET['userId'];

        $stmt = $pdo->prepare(
            'SELECT * FROM user u LEFT JOIN role r ON u.roleId = r.roleId WHERE u.userId = :userId'
        );
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($user) {
            echo json_encode([
                'success' => true,
                'user' => $user
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No user found for this userId']);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'userId parameter is missing']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

?>
