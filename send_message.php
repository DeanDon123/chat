<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) {
  echo json_encode(['status'=>'error','message'=>'Not logged in']);
  exit;
}

$message = trim($_POST['message'] ?? '');
if ($message) {
    $stmt = $conn->prepare("INSERT INTO messages (user_id, message) VALUES (?, ?)");
    $stmt->bind_param("is", $_SESSION['user_id'], $message);
    if ($stmt->execute()) {
        echo json_encode(['status'=>'ok']);
    } else {
        echo json_encode(['status'=>'error','message'=>$stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['status'=>'error','message'=>'Empty message']);
}
?>
