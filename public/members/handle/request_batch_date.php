<?php
require_once '../../connection.php'; // adjust if your folder depth differs
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'User') {
  http_response_code(401);
  echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
  exit();
}

$user_id = (int)$_SESSION['user_id'];

// Build full name safely (based on what you already store in session)
$givenname  = trim($_SESSION['givenname'] ?? '');
$middlename = trim($_SESSION['middlename'] ?? '');
$lastname   = trim($_SESSION['lastname'] ?? '');
$suffix     = trim($_SESSION['suffix'] ?? '');

$fullnameParts = array_filter([$givenname, $middlename, $lastname, $suffix], fn($v) => $v !== '');
$user_fullname = implode(' ', $fullnameParts);
if ($user_fullname === '') $user_fullname = 'A user';

// Inputs
$sacrament = trim($_POST['sacrament'] ?? '');
$preferred_date = trim($_POST['preferred_date'] ?? '');
$preferred_time = trim($_POST['preferred_time'] ?? '');
$batch_amount = (int)($_POST['batch_amount'] ?? 0);

// Validate
$allowedSacraments = ['Baptism', 'First Communion', 'Confirmation', 'Wedding'];
if (!in_array($sacrament, $allowedSacraments, true)) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Invalid sacrament.']);
  exit();
}

if (!$preferred_date || !$preferred_time) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Preferred date and time are required.']);
  exit();
}

if ($batch_amount < 1 || $batch_amount > 4) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Batch amount must be between 1 and 4.']);
  exit();
}

// Format message exactly as requested
$title = 'Batch Date Request';
$message = sprintf(
  '%s is requesting to open a date for solo %s for %d people, at %s %s',
  $user_fullname,
  $sacrament,
  $batch_amount,
  $preferred_date,
  $preferred_time
);

// Insert admin notification (reference_id -> user_id)
$stmt = $conn->prepare("
  INSERT INTO admin_notifications (sacrament, reference_id, title, message, status)
  VALUES (?, ?, ?, ?, 'Unread')
");
if (!$stmt) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Database error (prepare failed).']);
  exit();
}

$stmt->bind_param("siss", $sacrament, $user_id, $title, $message);

if (!$stmt->execute()) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Database error (execute failed).']);
  exit();
}

$stmt->close();

echo json_encode(['success' => true]);
