<?php
require '../conn.php';

$ticketId = $_GET['ticketId']; // Получаем ticketId из запроса

// SQL-запрос для получения суммы PogMetr
$sql = "SELECT ROUND(SUM((productLength * productQuantity) / 1000), 2) AS PogMetrSum FROM product WHERE ticketId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $ticketId); // Защита от SQL-инъекций
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Возвращаем результат в формате JSON
echo json_encode(['PogMetrSum' => $row['PogMetrSum']]);

$conn->close();
?>
