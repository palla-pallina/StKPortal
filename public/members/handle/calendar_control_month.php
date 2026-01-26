<?php
require_once '../../connection.php';

$year    = intval($_GET['year']);
$month   = intval($_GET['month']); // 1–12
$service = $_GET['service'] ?? '';

if (!$service) {
  echo json_encode([]);
  exit;
}

$stmt = $conn->prepare("
  SELECT date, time, max_slots
  FROM calendar_control
  WHERE service_type = ?
    AND is_enabled = 1
    AND YEAR(date) = ?
    AND MONTH(date) = ?
");
$stmt->bind_param("sii", $service, $year, $month);
$stmt->execute();
$res = $stmt->get_result();

$data = [];
while ($row = $res->fetch_assoc()) {
  $data[$row['date']][] = $row; // allow MULTIPLE times per date
}

echo json_encode($data);
