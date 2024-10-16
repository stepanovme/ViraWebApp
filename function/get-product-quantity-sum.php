<?php
require '../conn.php';

$ticketId = $_GET['ticketId'];

// SQL-запрос для получения суммы productQuantity для заданного ticketId
$sql = "SELECT SUM(productQuantity) AS sumQuantity FROM product WHERE ticketId = $ticketId";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response = ['success' => true, 'sumQuantity' => $row['sumQuantity']];
} else {
    $response = ['success' => false];
}

echo json_encode($response);

$conn->close();
?>
