<?php
// cancel_request.php
session_start();
header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['error' => 'Unauthorized']);
  exit;
}

$userId = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['error' => 'Method not allowed']);
  exit;
}

$serviceType = $_POST['service_type'] ?? '';
$requestId   = isset($_POST['request_id']) ? (int)$_POST['request_id'] : 0;

$cancelReason = trim($_POST['cancel_reason'] ?? '');

if ($cancelReason === '') {
  http_response_code(400);
  echo json_encode(['error' => 'Cancellation reason is required']);
  exit;
}

if (!$serviceType || !$requestId) {
  http_response_code(400);
  echo json_encode(['error' => 'Missing parameters']);
  exit;
}

// DB
$dsn = "mysql:host=localhost;dbname=test_tree_db;charset=utf8mb4";
$dbUser = "root";
$dbPass = "";

try {
  $pdo = new PDO($dsn, $dbUser, $dbPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);

    // Fetch requesting user's name
    $stmt = $pdo->prepare("
    SELECT givenname, lastname
    FROM users
    WHERE user_id = :uid
    LIMIT 1
    ");
    $stmt->execute([':uid' => $userId]);
    $userRow = $stmt->fetch();

    $userFullName = $userRow
    ? trim($userRow['givenname'] . ' ' . $userRow['lastname'])
  : "User #{$userId}";

  // Map service -> table + PK + user column + date/time columns (optional, for message)
  $map = [
    'Baptism' => [
      'table' => 'baptisms', 'pk' => 'id', 'user_col' => 'reservation_user_id',
      'date_col' => 'date', 'time_col' => 'time'
    ],
    'First Communion' => [
      'table' => 'first_communion', 'pk' => 'id', 'user_col' => 'reservation_user_id',
      'date_col' => 'date', 'time_col' => 'time'
    ],
    'Confirmation' => [
      'table' => 'confirmations', 'pk' => 'id', 'user_col' => 'reservation_user_id',
      'date_col' => 'date', 'time_col' => 'time'
    ],
    'Wedding' => [
      'table' => 'weddings', 'pk' => 'id', 'user_col' => 'reservation_user_id',
      'date_col' => 'date', 'time_col' => 'time'
    ],
    'Funeral' => [
      'table' => 'funerals', 'pk' => 'id', 'user_col' => 'reservation_user_id',
      'date_col' => 'funeral_date', 'time_col' => 'funeral_time'
    ],
    'Anointing of the Sick' => [
      'table' => 'anointing_of_sick_requests', 'pk' => 'id', 'user_col' => 'reservation_user_id',
      'date_col' => 'date', 'time_col' => 'time'
    ],
    'House Blessing' => [
      'table' => 'house_blessings', 'pk' => 'id', 'user_col' => 'reservation_user_id',
      'date_col' => 'date', 'time_col' => 'time'
    ],
    'Celebratory' => [
      'table' => 'celebratory', 'pk' => 'id', 'user_col' => 'reservation_user_id',
      'date_col' => 'date', 'time_col' => 'time'
    ],
    'Certificate Request' => [
      'table' => 'certificate_request', 'pk' => 'cert_req_id', 'user_col' => 'user_id',
      'date_col' => null, 'time_col' => null
    ],
  ];

  if (!isset($map[$serviceType])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid service_type']);
    exit;
  }

  $t = $map[$serviceType]['table'];
  $pk = $map[$serviceType]['pk'];
  $uc = $map[$serviceType]['user_col'];
  $dc = $map[$serviceType]['date_col'];
  $tc = $map[$serviceType]['time_col'];

  // Fetch the record and verify ownership + current status
  $sqlGet = "SELECT * FROM {$t} WHERE {$pk} = :id AND {$uc} = :uid LIMIT 1";
  $stmt = $pdo->prepare($sqlGet);
  $stmt->execute([':id' => $requestId, ':uid' => $userId]);
  $row = $stmt->fetch();

  if (!$row) {
    http_response_code(404);
    echo json_encode(['error' => 'Record not found']);
    exit;
  }

    $subjectFullName = 'this record';

    switch ($serviceType) {
    case 'Baptism':
        $subjectFullName = trim(
        ($row['child_firstname'] ?? '') . ' ' .
        ($row['child_middlename'] ?? '') . ' ' .
        ($row['child_lastname'] ?? '') . ' ' .
        ($row['child_suffix'] ?? '')
        );
        break;

    case 'First Communion':
        $subjectFullName = trim(
        ($row['child_firstname'] ?? '') . ' ' .
        ($row['child_middlename'] ?? '') . ' ' .
        ($row['child_lastname'] ?? '') . ' ' .
        ($row['child_suffix'] ?? '')
        );
        break;

    case 'Confirmation':
        $subjectFullName = trim(
        ($row['confirm_firstname'] ?? '') . ' ' .
        ($row['confirm_middlename'] ?? '') . ' ' .
        ($row['confirm_lastname'] ?? '') . ' ' .
        ($row['confirm_suffix'] ?? '')
        );
        break;

    case 'Wedding':
        $subjectFullName = trim(
        ($row['bride_firstname'] ?? '') . ' ' . ($row['bride_lastname'] ?? '') .
        ' & ' .
        ($row['groom_firstname'] ?? '') . ' ' . ($row['groom_lastname'] ?? '')
        );
        break;

    case 'Funeral':
        $subjectFullName = trim(
        ($row['deceased_firstname'] ?? '') . ' ' .
        ($row['deceased_middlename'] ?? '') . ' ' .
        ($row['deceased_lastname'] ?? '') . ' ' .
        ($row['deceased_suffix'] ?? '')
        );
        break;

    case 'Anointing of the Sick':
        $subjectFullName = trim(
        ($row['sick_firstname'] ?? '') . ' ' .
        ($row['sick_middlename'] ?? '') . ' ' .
        ($row['sick_lastname'] ?? '')
        );
        break;

    case 'Certificate Request':
        $subjectFullName = trim(
        ($row['owner_firstname'] ?? '') . ' ' .
        ($row['owner_middlename'] ?? '') . ' ' .
        ($row['owner_lastname'] ?? '') . ' ' .
        ($row['owner_suffix'] ?? '')
        );
        break;
    }

  // Determine status column name for certificate_request
  $statusCol = ($serviceType === 'Certificate Request') ? 'certificate_status' : 'status';
  $currentStatus = $row[$statusCol] ?? '';

  // Allow cancel request only from Pending/Accepted
  $allowedStatuses = ['Pending', 'Accepted'];
  if (!in_array($currentStatus, $allowedStatuses, true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Cancellation not allowed for this status.']);
    exit;
  }

  // Prevent duplicates
  if ($currentStatus === 'Cancel Requested') {
    echo json_encode(['ok' => true, 'message' => 'Already requested.']);
    exit;
  }

  $pdo->beginTransaction();

  // Update status -> Cancel Requested
  $sqlUpd = "
    UPDATE {$t}
    SET {$statusCol} = 'Cancel Requested',
        decline_reason = :reason
    WHERE {$pk} = :id AND {$uc} = :uid
  ";

  $stmt = $pdo->prepare($sqlUpd);
  $stmt->execute([
    ':reason' => $cancelReason,
    ':id' => $requestId,
    ':uid' => $userId
  ]);


  // Build notification message
  $dateVal = ($dc && isset($row[$dc])) ? $row[$dc] : null;
  $timeVal = ($tc && isset($row[$tc])) ? $row[$tc] : null;

    $title = "Cancellation Requested: {$serviceType} (Reference #{$requestId})";

    $message = sprintf(
      "%s requested the cancellation for %s's %s (Reference #%d).<br>Reason: %s",
      $userFullName,
      $subjectFullName,
      $serviceType,
      $requestId,
      $cancelReason
    );


  if ($dateVal) $message .= " Date: {$dateVal}.";
  if ($timeVal) $message .= " Time: {$timeVal}.";

  $sqlNotif = "INSERT INTO admin_notifications (sacrament, reference_id, title, message)
               VALUES (:sacrament, :ref, :title, :msg)";
  $stmt = $pdo->prepare($sqlNotif);
  $stmt->execute([
    ':sacrament' => $serviceType,
    ':ref' => $requestId,
    ':title' => $title,
    ':msg' => $message
  ]);



  $pdo->commit();

  echo json_encode(['ok' => true]);
} catch (Throwable $e) {
  if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
  http_response_code(500);
  echo json_encode(['error' => 'Server error', 'details' => $e->getMessage()]);
}
