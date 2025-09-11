<?php
require_once 'dbconfig.php';
session_start();

$user_id = $_SESSION['login_id'] ?? null;
if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit;
}

$action = $_POST['action'] ?? '';
if ($action == 'create_folder') {
    $name = trim($_POST['name'] ?? '');
    $parent_id = intval($_POST['parent_id'] ?? 0);

    // Call create folder function
    $response = createFolder($dbh, $name, $user_id, $parent_id);

    echo json_encode($response);
    exit;
}
?>
