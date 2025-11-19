<?php
header('Content-Type: application/json; charset=utf-8');
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'test_tree_db';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

$sql = "SELECT id, name, intention, request_text, anonymous, created_at FROM `prayer_requests` WHERE shared_public = 1 ORDER BY created_at DESC";
$res = $conn->query($sql);
$out = [];
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $out[] = $row;
    }
}

echo json_encode($out);
$conn->close();
exit;
?>
