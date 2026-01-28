<?php
require_once '../../connection.php';
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* ===============================
   ACCESS CONTROL
================================ */
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
    http_response_code(403);
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Invalid request');
}

$user_id = $_SESSION['user_id'];

/* ===============================
   BASIC FIELDS
================================ */
$certificate_type = $_POST['certificate_type'] ?? null;
$request_reason   = $_POST['request_reason'] ?? null;

/*
  claiming_for is determined by form structure
*/
$claiming_for = isset($_POST['certificate_owner']) ? 'other' : 'self';

/* ===============================
   REQUESTER (SUBMITTER)
================================ */
$request_firstname  = trim($_POST['request_firstname'] ?? '');
$request_middlename = trim($_POST['request_middlename'] ?? '');
$request_lastname   = trim($_POST['request_lastname'] ?? '');
$request_suffix     = trim($_POST['request_suffix'] ?? '');

/* ===============================
   OWNER (CERTIFICATE HOLDER)
================================ */
if ($claiming_for === 'self') {

    $certificate_owner = 'Self';

    $owner_firstname  = $request_firstname;
    $owner_middlename = $request_middlename;
    $owner_lastname   = $request_lastname;
    $owner_suffix     = $request_suffix;

} else {

    $certificate_owner = $_POST['certificate_owner'] ?? null;

    $owner_firstname  = trim($_POST['owner_firstname'] ?? '');
    $owner_middlename = trim($_POST['owner_middlename'] ?? '');
    $owner_lastname   = trim($_POST['owner_lastname'] ?? '');
    $owner_suffix     = trim($_POST['owner_suffix'] ?? '');
}


/* ===============================
   FILE UPLOAD CONFIG
================================ */
$uploadDir = '/opt/lampp/htdocs/stkportal_src/src/private/sacraments/uploads/';
$relativePath = 'private/sacraments/uploads/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

function uploadFile($fileKey, $uploadDir, $relativePath, $required = false) {

    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] === 4) {
        if ($required) throw new Exception("Required file missing: $fileKey");
        return null;
    }

    if ($_FILES[$fileKey]['error'] !== 0) {
        throw new Exception("Upload error ($fileKey): " . $_FILES[$fileKey]['error']);
    }

    if (!is_uploaded_file($_FILES[$fileKey]['tmp_name'])) {
        throw new Exception("Invalid uploaded file: $fileKey");
    }

    if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
        throw new Exception("Upload directory not writable");
    }

    $originalName = basename($_FILES[$fileKey]['name']);
    $fileExt = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    $allowed = ['jpg','jpeg','png','pdf'];
    if (!in_array($fileExt, $allowed)) {
        throw new Exception("Invalid file type for $fileKey");
    }

    $newFileName = uniqid($fileKey . "_") . "." . $fileExt;
    $destination = $uploadDir . $newFileName;
    $relative = $relativePath . $newFileName;

    if (!move_uploaded_file($_FILES[$fileKey]['tmp_name'], $destination)) {
        throw new Exception("move_uploaded_file failed ($fileKey)");
    }

    return $relative;
}

try {
    $id_picture             = uploadFile('id_picture', $uploadDir, true);
    $owner_id_picture       = uploadFile('owner_id_picture', $uploadDir, false);
    $authorized_id_picture  = uploadFile('authorized_id_picture', $uploadDir, false);
} catch (Exception $e) {
    exit($e->getMessage());
}


/* ===============================
   BASIC VALIDATION
================================ */
if (
    !$certificate_type ||
    !$request_reason ||
    !$request_firstname ||
    !$request_lastname ||
    !$owner_firstname ||
    !$owner_lastname
) {
    exit('Missing required fields');
}

/* ===============================
   CONTACT DETAILS
================================ */
$contact = trim($_POST['contact'] ?? '');
$email   = trim($_POST['email'] ?? null);

/* ===============================
   INSERT QUERY (ALIGNED)
================================ */
$sql = "
INSERT INTO certificate_request (
    user_id,
    certificate_type,
    request_reason,
    claiming_for,
    certificate_owner,

    request_firstname,
    request_middlename,
    request_lastname,
    request_suffix,

    owner_firstname,
    owner_middlename,
    owner_lastname,
    owner_suffix,

    id_picture,
    owner_id_picture,
    authorized_id_picture,
    contact,
    email
) VALUES (
    :user_id,
    :certificate_type,
    :request_reason,
    :claiming_for,
    :certificate_owner,

    :request_firstname,
    :request_middlename,
    :request_lastname,
    :request_suffix,

    :owner_firstname,
    :owner_middlename,
    :owner_lastname,
    :owner_suffix,

    :id_picture,
    :owner_id_picture,
    :authorized_id_picture,
    :contact,
    :email
)
";

$stmt = $dbh->prepare($sql);
$stmt->execute([
    ':user_id' => $user_id,
    ':certificate_type' => $certificate_type,
    ':request_reason' => $request_reason,
    ':claiming_for' => $claiming_for,
    ':certificate_owner' => $certificate_owner,

    ':request_firstname' => $request_firstname,
    ':request_middlename' => $request_middlename,
    ':request_lastname' => $request_lastname,
    ':request_suffix' => $request_suffix,

    ':owner_firstname' => $owner_firstname,
    ':owner_middlename' => $owner_middlename,
    ':owner_lastname' => $owner_lastname,
    ':owner_suffix' => $owner_suffix,

    ':id_picture' => $id_picture,
    ':owner_id_picture' => $owner_id_picture,
    ':authorized_id_picture' => $authorized_id_picture,
    ':contact' => $contact,
    ':email' => $email
]);

$cert_req_id = $dbh->lastInsertId();

/* ===============================
   ADMIN NOTIFICATION
================================ */

// Fetch user name
$userStmt = $dbh->prepare("
    SELECT givenname, lastname 
    FROM users 
    WHERE user_id = :user_id
");
$userStmt->execute([':user_id' => $user_id]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

$submittedBy = $user
    ? $user['givenname'] . ' ' . $user['lastname']
    : 'A user';

// Owner full name
$ownerFullname = trim(
    $owner_firstname . ' ' .
    ($owner_middlename ? $owner_middlename . ' ' : '') .
    $owner_lastname . ' ' .
    $owner_suffix
);

// Admin notification content
$notifTitle = 'Certificate Request';
$notifMessage = $submittedBy
    . ' submitted a certificate request ('
    . $certificate_type
    . ') for '
    . $ownerFullname
    . '.';

// Insert admin notification
$stmtNotif = $dbh->prepare("
    INSERT INTO admin_notifications (sacrament, reference_id, title, message)
    VALUES ('Certificate Request', :reference_id, :title, :message)
");

$stmtNotif->execute([
    ':reference_id' => $cert_req_id,
    ':title' => $notifTitle,
    ':message' => $notifMessage
]);

/* ===============================
   USER NOTIFICATION (SUCCESS)
================================ */

$userNotifTitle = 'Certificate Request Submitted';
$userNotifMessage =
    "Good day, {$user['givenname']}! "
    . "We have received your request for a {$certificate_type} certificate "
    . "for {$ownerFullname}. "
    . "Your request is currently under review. "
    . "You will be notified once it is approved for pickup. "
    . "Thank you and God bless!";

// Insert user notification
$stmtUserNotif = $dbh->prepare("
    INSERT INTO notifications (user_id, title, message, type)
    VALUES (:user_id, :title, :message, :type)
");

$stmtUserNotif->execute([
    ':user_id' => $user_id,
    ':title'   => $userNotifTitle,
    ':message' => $userNotifMessage,
    ':type'    => 'successful_request'
]);

/* ===============================
   SUCCESS
================================ */
$_SESSION['cert_success'] = "Your certificate request has been submitted successfully. Please wait for approval notification.";

header("Location: ../certificate_request.php");
exit;
