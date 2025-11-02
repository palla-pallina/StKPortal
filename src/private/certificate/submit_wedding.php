<?php
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo "Method not allowed";
  exit;
}

function val($k) { return isset($_POST[$k]) ? trim($_POST[$k]) : null; }
function norm_date($s) {
  if (!$s) return null;
  $t = strtotime($s);
  return $t ? date('Y-m-d', $t) : null;
}

$groom_first  = val('groom_first');
$groom_last   = val('groom_last');
$bride_first  = val('bride_first');
$bride_last   = val('bride_last');
$wedding_date = norm_date(val('wedding_date'));
$wedding_place= val('wedding_place');

$witnesses_arr = isset($_POST['witnesses']) && is_array($_POST['witnesses'])
  ? array_values(array_filter($_POST['witnesses'], fn($x)=>trim($x)!==''))
  : [];
$witnesses_json = $witnesses_arr ? json_encode($witnesses_arr, JSON_UNESCAPED_UNICODE) : null;

$notes = val('notes');
$ip    = $_SERVER['REMOTE_ADDR'] ?? '';
$ua    = $_SERVER['HTTP_USER_AGENT'] ?? '';

$stmt = $conn->prepare("
  INSERT INTO wedding_cert
    (groom_first, groom_last, bride_first, bride_last, wedding_date, wedding_place,
     witnesses, notes, ip_addr, user_agent)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
  "ssssssssss",
  $groom_first, $groom_last, $bride_first, $bride_last, $wedding_date, $wedding_place,
  $witnesses_json, $notes, $ip, $ua
);

if (!$stmt->execute()) {
  http_response_code(500);
  echo "Database error: " . $stmt->error;
  exit;
}

$id = $stmt->insert_id;
$stmt->close();

header("Location: admin.php?page=certificate&type=wedding&saved=1&id=" . urlencode($id));
exit;
