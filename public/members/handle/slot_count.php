<?php
require_once '../../connection.php';

$date = $_GET['date'] ?? null;
$time = $_GET['time'] ?? null;
$type = $_GET['type'] ?? 'Regular';

if (!$date) {
  echo json_encode(['count' => 0, 'max' => 0]);
  exit;
}

if ($type === 'Solo') {

  // SOLO → 1 per day
  $stmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM baptisms 
    WHERE date = ? AND type = 'Solo'
  ");
  $stmt->bind_param("s", $date);
  $stmt->execute();
  $count = $stmt->get_result()->fetch_row()[0];

  echo json_encode([
    'count' => $count,
    'max'   => 1
  ]);
  exit;
}

/* REGULAR → per date + time */
$stmt = $conn->prepare("
  SELECT COUNT(*) 
  FROM baptisms
  WHERE date = ? AND time = ? AND type = 'Regular'
");
$stmt->bind_param("ss", $date, $time);
$stmt->execute();
$count = $stmt->get_result()->fetch_row()[0];

echo json_encode([
  'count' => $count,
  'max'   => 15
]);
