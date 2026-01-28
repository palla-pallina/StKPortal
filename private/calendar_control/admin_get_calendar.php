<?php
require_once '../connection.php';

$filter = $_GET['filter'] ?? 'All';
$sql = "SELECT * FROM calendar_control";
$params = [];

if($filter !== 'All'){
    $sql .= " WHERE service_type=?";
    $params[] = $filter;
}

$stmt = $conn->prepare($sql);
if($params) $stmt->bind_param("s",$params[0]);
$stmt->execute();
$result = $stmt->get_result();

$schedules = [];
while($row = $result->fetch_assoc()){
    $schedules[$row['date']][] = $row;
}

header('Content-Type: application/json');
echo json_encode($schedules);
