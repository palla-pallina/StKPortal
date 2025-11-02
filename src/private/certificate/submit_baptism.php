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

$child_first  = val('child_first');
$child_last   = val('child_last');
$birth_date   = norm_date(val('birth_date'));
$baptism_date = norm_date(val('baptism_date'));
$birthplace   = val('birthplace');
$father_first = val('father_first');
$father_last  = val('father_last');
$mother_first = val('mother_first');
$mother_last  = val('mother_last');

$sponsors_arr = isset($_POST['sponsors']) && is_array($_POST['sponsors'])
  ? array_values(array_filter($_POST['sponsors'], fn($x)=>trim($x)!==''))
  : [];
$sponsors_json = $sponsors_arr ? json_encode($sponsors_arr, JSON_UNESCAPED_UNICODE) : null;

$notes = val('notes');
$ip    = $_SERVER['REMOTE_ADDR'] ?? '';
$ua    = $_SERVER['HTTP_USER_AGENT'] ?? '';

$stmt = $conn->prepare("
  INSERT INTO baptism_cert
    (child_first, child_last, birth_date, baptism_date, birthplace,
     father_first, father_last, mother_first, mother_last, sponsors, notes, ip_addr, user_agent)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
  "sssssssssssss",
  $child_first, $child_last, $birth_date, $baptism_date, $birthplace,
  $father_first, $father_last, $mother_first, $mother_last, $sponsors_json, $notes, $ip, $ua
);

if (!$stmt->execute()) {
  http_response_code(500);
  echo "Database error: " . $stmt->error;
  exit;
}

$id = $stmt->insert_id;
$stmt->close();

header("Location: admin.php?page=certificate&type=baptism&saved=1&id=" . urlencode($id));
exit;
