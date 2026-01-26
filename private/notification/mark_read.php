<?php
require_once "../conn.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    http_response_code(403);
    exit();
}

if (!isset($_POST['id'])) {
    http_response_code(400);
    exit();
}

$id = (int) $_POST['id'];

$stmt = $conn->prepare("UPDATE admin_notifications SET status='Read' WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

echo "ok";
