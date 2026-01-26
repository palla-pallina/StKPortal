<?php
require_once '../../connection.php';

$date = $_GET['date'] ?? '';
$response = ['regular' => 0, 'solo' => 0];

if ($date) {
    // Regular Baptism
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM baptisms WHERE date = ? AND baptism_type = 'regular'");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $response['regular'] = (int)$result['cnt'];

    // Solo Baptism
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM baptisms WHERE date = ? AND baptism_type = 'solo'");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $response['solo'] = (int)$result['cnt'];
}

header('Content-Type: application/json');
echo json_encode($response);
