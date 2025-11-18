<?php
require_once 'dbconfig.php';
session_start();

$user_id = $_SESSION['login_id'] ?? null;
if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}


?>
