<?php
require '../conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticketId = $_POST['ticketId'];
    $field = $_POST['field'];
    $value = $_POST['value'];

    // Безопасность: экранируем значения для предотвращения SQL-инъекций
    $ticketId = $conn->real_escape_string($ticketId);
    $field = $conn->real_escape_string($field);
    $value = $conn->real_escape_string($value);

    // Разрешенные поля для обновления
    $allowedFields = ['ticketArea', 'ticketBrigada', 'ticketAddressDelivery', 'colorCadId', 'thicknessMetalCadId', 'ticketDatePlan'];
    
    if (in_array($field, $allowedFields)) {
        // Для поля даты добавляем проверку на правильность формата (YYYY-MM-DD)
        if ($field == 'ticketDatePlan') {
            if (!preg_match('/\d{4}-\d{2}-\d{2}/', $value)) {
                echo "Invalid date format";
                exit;
            }
        }

        // Обновляем запись в таблице ticket
        $sql = "UPDATE ticket SET $field = '$value' WHERE ticketId = $ticketId";
        if ($conn->query($sql) === TRUE) {
            echo "Record updated successfully";
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }
}
?>
