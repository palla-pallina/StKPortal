<?php
require_once "../conn.php";
session_start();

/* Allow admins only */
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}

/* Validate ID */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_notifications.php");
    exit();
}

$notif_id = (int) $_GET['id'];

/* Delete notification */
$stmt = $conn->prepare("DELETE FROM admin_notifications WHERE id = ?");
$stmt->bind_param("i", $notif_id);
$stmt->execute();

/* Redirect back */
header("Location: admin_notif.php");
exit();
