<?php
// delete_message.php
header('Content-Type: application/json');
require 'db.php';

$action   = isset($_POST['action']) ? $_POST['action'] : '';
$id       = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$username = isset($_POST['username']) ? trim($_POST['username']) : '';

if ($id <= 0 || $username === '' || ($action !== 'me' && $action !== 'everyone')) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

// --- Remove for Me ---
if ($action === 'me') {
    $stmt = $conn->prepare("INSERT IGNORE INTO deleted_messages (message_id, username) VALUES (?, ?)");
    $stmt->bind_param('is', $id, $username);
    $stmt->execute();
    echo json_encode(['status' => 'ok', 'type' => 'me']);
    exit;
}

// --- Remove for Everyone ---
if ($action === 'everyone') {
    // check ownership first
    $stmt = $conn->prepare("SELECT username FROM messages WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if (!$row = $res->fetch_assoc()) {
        echo json_encode(['status' => 'error', 'message' => 'Message not found']);
        exit;
    }
    if ($row['username'] !== $username) {
        echo json_encode(['status' => 'error', 'message' => 'You can only delete your own messages']);
        exit;
    }

    // delete the message for everyone
    $stmt = $conn->prepare("DELETE FROM messages WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    // also clean up deleted_messages table
    $stmt = $conn->prepare("DELETE FROM deleted_messages WHERE message_id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    echo json_encode(['status' => 'ok', 'type' => 'everyone']);
    exit;
}
?>
