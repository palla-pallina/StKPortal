<?php
session_start();
require_once 'connection.php'; 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* ================= SECURITY CHECK ================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid request.');
}

/* ================= HELPER ================= */
function clean($value) {
    return trim(htmlspecialchars($value ?? ''));
}

/* ================= SCHEDULE ================= */
$date = clean($_POST['date']);
$time = clean($_POST['time']);

/* ================= VENUE ================= */
$venue = null;
$province = null;
$city = null;
$street_no = null;
$barangay = null;
$hospital_name = null;

if (isset($_POST['mass_venue'])) {
    $venue = clean($_POST['mass_venue']);
}

if ($venue === 'At Home') {
    $province  = clean($_POST['home_province']);
    $city      = clean($_POST['home_city']);
    $street_no = clean($_POST['home_street_no']);
    $barangay  = clean($_POST['home_barangay']);
}

if ($venue === 'At the Hospital') {
    $province      = clean($_POST['hospital_province']);
    $city          = clean($_POST['hospital_city']);
    $street_no     = clean($_POST['hospital_street_no']);
    $barangay      = clean($_POST['hospital_barangay']);
    $hospital_name = clean($_POST['hospital_name']);
}

/* ================= PERSON TO BE ANOINTED ================= */
$sick_firstname  = trim($_POST['sick_firstname'] ?? '');
$sick_middlename = trim($_POST['sick_middlename'] ?? '');
$sick_lastname   = trim($_POST['sick_lastname'] ?? '');
$sick_suffix     = trim($_POST['sick_suffix'] ?? '');

$age       = clean($_POST['age']);
$sick_condition = clean($_POST['sick_condition']);

/* ================= CONTACT PERSON ================= */
$contact_person_firstname  = trim($_POST['contact_person_firstname'] ?? '');
$contact_person_middlename = trim($_POST['contact_person_middlename'] ?? '');
$contact_person_lastname   = trim($_POST['contact_person_lastname'] ?? '');
$contact_person_suffix     = trim($_POST['contact_person_suffix'] ?? '');

$contact_relation = clean($_POST['contact_person_relation']);
$contact_number   = clean($_POST['contact_person_contact_no']);
$contact_email    = clean($_POST['contact_person_email']);

$reservation_user_id = $_SESSION['user_id'];

/* ================= INSERT ================= */
$sql = "
INSERT INTO anointing_of_sick_requests (
    reservation_user_id,
    date,
    time,
    venue,
    province,
    city,
    street_no,
    barangay,
    hospital_name,
    sick_firstname,
    sick_middlename,
    sick_lastname,
    sick_suffix,
    age,
    sick_condition,
    contact_person_firstname,
    contact_person_middlename,
    contact_person_lastname,
    contact_person_suffix,
    contact_relation,
    contact_number,
    contact_email,
    created_at
) VALUES (
    :reservation_user_id,
    :date,
    :time,
    :venue,
    :province,
    :city,
    :street_no,
    :barangay,
    :hospital_name,
    :sick_firstname,
    :sick_middlename,
    :sick_lastname,
    :sick_suffix,
    :age,
    :sick_condition,
    :contact_person_firstname,
    :contact_person_middlename,
    :contact_person_lastname,
    :contact_person_suffix,
    :contact_relation,
    :contact_number,
    :contact_email,
    NOW()
)
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':reservation_user_id' => $reservation_user_id,
    ':date'           => $date,
    ':time'           => $time,
    ':venue'          => $venue,
    ':province'       => $province,
    ':city'           => $city,
    ':street_no'      => $street_no,
    ':barangay'       => $barangay,
    ':hospital_name'  => $hospital_name,
    ':sick_firstname'   => $sick_firstname,
    ':sick_middlename'   => $sick_middlename,
    ':sick_lastname'   => $sick_lastname,
    ':sick_suffix'   => $sick_suffix,
    ':age'            => $age,
    ':sick_condition'      => $sick_condition,
    ':contact_person_firstname'   => $contact_person_firstname,
    ':contact_person_middlename'   => $contact_person_middlename,
    ':contact_person_lastname'   => $contact_person_lastname,
    ':contact_person_suffix'   => $contact_person_suffix,
    ':contact_relation'=> $contact_relation,
    ':contact_number' => $contact_number,
    ':contact_email'  => $contact_email
]);

$anointing_id = $pdo->lastInsertId();

/* ================= SUCCESS ================= */

/* ===============================
   FETCH USER INFO
================================ */

$userStmt = $pdo->prepare("
    SELECT givenname, lastname 
    FROM users 
    WHERE user_id = :user_id
");
$userStmt->execute([':user_id' => $reservation_user_id]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

$submittedBy = $user
    ? $user['givenname'] . ' ' . $user['lastname']
    : 'A user';

/* ===============================
   SICK PERSON FULL NAME
================================ */

$sickFullname = trim(
    $sick_firstname . ' ' .
    ($sick_middlename ? $sick_middlename . ' ' : '') .
    $sick_lastname . ' ' .
    $sick_suffix
);

/* ===============================
   ADMIN NOTIFICATION
================================ */

$adminNotifTitle = 'Anointing of the Sick Request';
$adminNotifMessage =
    $submittedBy
    . ' submitted an Anointing of the Sick request for '
    . $sickFullname
    . ' scheduled on '
    . $date . ' at ' . $time . '.';

// Insert admin notification
$stmtAdminNotif = $pdo->prepare("
    INSERT INTO admin_notifications (
        sacrament,
        reference_id,
        title,
        message,
        created_at
    ) VALUES (
        'Anointing of the Sick',
        :reference_id,
        :title,
        :message,
        NOW()
    )
");

$stmtAdminNotif->execute([
    ':reference_id' => $anointing_id, // anointing request ID
    ':title'        => $adminNotifTitle,
    ':message'      => $adminNotifMessage
]);

/* ===============================
   USER NOTIFICATION (SUCCESS)
================================ */

$userNotifTitle = 'Anointing of the Sick Request Submitted';
$userNotifMessage =
    "Good day, {$user['givenname']}! "
    . "We have successfully received your request for the Anointing of the Sick "
    . "for {$sickFullname}. "
    . "Your request is currently under review by the parish office. "
    . "You will be notified once it has been approved. "
    . "Thank you and God bless!";

// Insert user notification
$stmtUserNotif = $pdo->prepare("
    INSERT INTO notifications (
        user_id,
        title,
        message,
        type,
        created_at
    ) VALUES (
        :user_id,
        :title,
        :message,
        :type,
        NOW()
    )
");

$stmtUserNotif->execute([
    ':user_id' => $reservation_user_id,
    ':title'   => $userNotifTitle,
    ':message' => $userNotifMessage,
    ':type'    => 'successful_request'
]);

/* ===============================
   SUCCESS FLAG
================================ */

$_SESSION['toast_success'] =
    'Your Anointing of the Sick request has been submitted successfully. Please wait for the approval notification.';

header('Location: ../anointing.php');
exit;
