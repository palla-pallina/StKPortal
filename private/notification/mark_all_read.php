<?php
require_once '../conn.php';
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Admin') {
    http_response_code(403);
    exit;
}

$conn->query("UPDATE admin_notifications SET status='Read' WHERE status='Unread'");
