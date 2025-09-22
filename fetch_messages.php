<?php
session_start();
include 'db.php';

$after_id = isset($_GET['after_id']) ? intval($_GET['after_id']) : 0;

$sql = "SELECT m.id, u.username, m.message, m.created_at
        FROM messages m
        JOIN users u ON m.user_id = u.id
        WHERE m.id > ?
        ORDER BY m.id ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $after_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode(['status'=>'ok','messages'=>$messages]);
$stmt->close();
?>
