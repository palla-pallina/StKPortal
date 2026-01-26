<?php
require_once '../connection.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
    http_response_code(403);
    exit('Unauthorized');
}

$user_id = (int)$_SESSION['user_id'];
$action  = $_POST['action'] ?? '';
$id      = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if (!$id) {
    http_response_code(400);
    exit('Invalid ID');
}

if ($action === 'read') {
    $stmt = $conn->prepare("
        UPDATE notifications 
        SET is_read = 1 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $stmt->close();

    echo 'ok';
    exit();
}

if ($action === 'delete') {
    $stmt = $conn->prepare("
        DELETE FROM notifications 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $stmt->close();

    echo 'ok';
    exit();
}

http_response_code(400);
echo 'Invalid action';
