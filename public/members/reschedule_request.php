<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['error'=>'Unauthorized']); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['error'=>'Method not allowed']); exit; }

$userId = (int)$_SESSION['user_id'];

$serviceType = $_POST['service_type'] ?? '';
$requestId   = (int)($_POST['request_id'] ?? 0);
$changeType  = $_POST['change_type'] ?? '';
$reqDate     = $_POST['requested_date'] ?? '';
$reqTime     = $_POST['requested_time'] ?? '';
$reason      = trim($_POST['reason'] ?? '');

$allowedChange = ['Service','Rehearsal','Canon Interview','Pre-Cana'];
if (!$serviceType || !$requestId || !$reqDate || !$reqTime || !$reason || !in_array($changeType, $allowedChange, true)) {
  http_response_code(400); echo json_encode(['error'=>'Missing/invalid parameters']); exit;
}

$dsn="mysql:host=localhost;dbname=test_tree_db;charset=utf8mb4"; $dbUser="root"; $dbPass="";

try {
  $pdo = new PDO($dsn,$dbUser,$dbPass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);

  $map = [
    'Baptism' => ['table'=>'baptisms','pk'=>'id','uc'=>'reservation_user_id','status'=>'status'],
    'First Communion' => ['table'=>'first_communion','pk'=>'id','uc'=>'reservation_user_id','status'=>'status'],
    'Confirmation' => ['table'=>'confirmations','pk'=>'id','uc'=>'reservation_user_id','status'=>'status'],
    'Wedding' => ['table'=>'weddings','pk'=>'id','uc'=>'reservation_user_id','status'=>'status'],
  ];

  if (!isset($map[$serviceType])) { http_response_code(400); echo json_encode(['error'=>'Invalid service_type']); exit; }
  if ($serviceType !== 'Wedding' && ($changeType === 'Canon Interview' || $changeType === 'Pre-Cana')) {
    http_response_code(400); echo json_encode(['error'=>'Change type not allowed for this service']); exit;
  }

  // user name
  $stmt = $pdo->prepare("SELECT givenname, lastname FROM users WHERE user_id=:uid LIMIT 1");
  $stmt->execute([':uid'=>$userId]);
  $u = $stmt->fetch();
  $userFullName = $u ? trim(($u['givenname'] ?? '').' '.($u['lastname'] ?? '')) : "User #$userId";

  $t  = $map[$serviceType]['table'];
  $pk = $map[$serviceType]['pk'];
  $uc = $map[$serviceType]['uc'];
  $sc = $map[$serviceType]['status'];

  // fetch record (ownership + status)
  $stmt = $pdo->prepare("SELECT * FROM {$t} WHERE {$pk}=:id AND {$uc}=:uid LIMIT 1");
  $stmt->execute([':id'=>$requestId, ':uid'=>$userId]);
  $row = $stmt->fetch();
  if (!$row) { http_response_code(404); echo json_encode(['error'=>'Record not found']); exit; }
  if (($row[$sc] ?? '') !== 'Accepted') { http_response_code(400); echo json_encode(['error'=>'Reschedule allowed only for Accepted reservations']); exit; }

  // subject name
  $subjectFullName = 'this record';
  if ($serviceType === 'Baptism' || $serviceType === 'First Communion') {
    $subjectFullName = trim(($row['child_firstname']??'').' '.($row['child_middlename']??'').' '.($row['child_lastname']??'').' '.($row['child_suffix']??''));
  } elseif ($serviceType === 'Confirmation') {
    $subjectFullName = trim(($row['confirm_firstname']??'').' '.($row['confirm_middlename']??'').' '.($row['confirm_lastname']??'').' '.($row['confirm_suffix']??''));
  } elseif ($serviceType === 'Wedding') {
    $subjectFullName = trim(($row['bride_firstname']??'').' '.($row['bride_lastname']??'').' & '.($row['groom_firstname']??'').' '.($row['groom_lastname']??''));
  }

  // current date/time based on changeType
  $currentDate = null; $currentTime = null;
  if ($changeType === 'Service') { $currentDate = $row['date'] ?? null; $currentTime = $row['time'] ?? null; }
  if ($changeType === 'Rehearsal') { $currentDate = $row['rehearsal_date'] ?? null; $currentTime = $row['rehearsal_time'] ?? null; }
  if ($changeType === 'Canon Interview') { $currentDate = $row['canon_interview_date'] ?? null; $currentTime = $row['canon_interview_time'] ?? null; }
  if ($changeType === 'Pre-Cana') { $currentDate = $row['precana_seminar_date'] ?? null; $currentTime = $row['precana_seminar_time'] ?? null; }

  $pdo->beginTransaction();

  // store request
  $stmt = $pdo->prepare("
    INSERT INTO reschedule_requests
      (sacrament, reference_id, user_id, change_type, date_current, time_current, requested_date, requested_time, reason)
    VALUES
      (:s, :ref, :uid, :ct, :cd, :ctm, :rd, :rt, :rsn)
  ");
  $stmt->execute([
    ':s'=>$serviceType, ':ref'=>$requestId, ':uid'=>$userId, ':ct'=>$changeType,
    ':cd'=>$currentDate, ':ctm'=>$currentTime, ':rd'=>$reqDate, ':rt'=>$reqTime, ':rsn'=>$reason
  ]);

  // set status (optional but recommended)
  $stmt = $pdo->prepare("UPDATE {$t} SET {$sc}='Reschedule Requested' WHERE {$pk}=:id AND {$uc}=:uid");
  $stmt->execute([':id'=>$requestId, ':uid'=>$userId]);

  // notify admin
  $title = "Reschedule Requested: {$serviceType} (Reference #{$requestId})";
  $message = "{$userFullName} requested a reschedule for {$subjectFullName}'s {$serviceType} (Reference #{$requestId}).\n".
             "Type: {$changeType}\n".
             "Requested: {$reqDate} {$reqTime}\n".
             "Reason: {$reason}";

  $stmt = $pdo->prepare("INSERT INTO admin_notifications (sacrament, reference_id, title, message) VALUES (:s, :ref, :t, :m)");
  $stmt->execute([':s'=>$serviceType, ':ref'=>$requestId, ':t'=>$title, ':m'=>$message]);

  $pdo->commit();
  echo json_encode(['ok'=>true]);

} catch (Throwable $e) {
  if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
  http_response_code(500);
  echo json_encode(['error'=>'Server error','details'=>$e->getMessage()]);
}
