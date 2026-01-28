<?php
require_once '../../connection.php';
session_start();

// Display errors for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit('Invalid request.');
if (!isset($_SESSION['user_id'])) exit('User not logged in.');

$user_id = $_SESSION['user_id'];

function clean($value) {
    return trim(htmlspecialchars($value ?? ''));
}

/* ================= REQUIRED FIELDS ================= */
$date             = clean($_POST['date']);
$time             = clean($_POST['time']);
$celebration_type = clean($_POST['celebration_type']);
$mass_venue       = clean($_POST['mass_venue'] ?? '');

/* ================= VENUE DETAILS ================= */
$province  = $city  = $street_no  = $barangay  = null;
if ($mass_venue === 'At Home') {
    $province  = clean($_POST['province'] ?? null);
    $city      = clean($_POST['city'] ?? null);
    $street_no = clean($_POST['street_no'] ?? null);
    $barangay  = clean($_POST['barangay'] ?? null);
}

/* ================= CELEBRATION DETAILS ================= */
$thanksgiving      = null;
$special_intention = null;
$celebrant_firstname   = $celebrant_middlename = $celebrant_lastname = $celebrant_suffix = null;
$celebrant_birthdate   = null;
$wife_firstname        = $wife_middlename = $wife_lastname = null;
$husband_firstname     = $husband_middlename = $husband_lastname = $husband_suffix = null;
$wedding_date          = null;

switch ($celebration_type) {
    case 'Thanksgiving':
        $thanksgiving = clean($_POST['thanksgiving'] ?? null);
        $celebrant_name = $thanksgiving;
        break;

    case 'Birthday':
        $celebrant_firstname  = clean($_POST['celebrant_firstname'] ?? null);
        $celebrant_middlename = clean($_POST['celebrant_middlename'] ?? null);
        $celebrant_lastname   = clean($_POST['celebrant_lastname'] ?? null);
        $celebrant_suffix     = clean($_POST['celebrant_suffix'] ?? null);

        $year  = intval($_POST['year'] ?? 0);
        $month = intval($_POST['month'] ?? 0);
        $day   = intval($_POST['day'] ?? 0);

        if ($year && $month && $day) {
            $celebrant_birthdate = sprintf('%04d-%02d-%02d', $year, $month, $day);
        }

        $celebrant_name = trim(
            $celebrant_firstname . ' ' .
            ($celebrant_middlename ? $celebrant_middlename . ' ' : '') .
            $celebrant_lastname . ' ' .
            $celebrant_suffix
        );
        break;

    case 'Wedding Anniversary':
        $wife_firstname  = clean($_POST['wife_firstname'] ?? null);
        $wife_middlename = clean($_POST['wife_middlename'] ?? null);
        $wife_lastname   = clean($_POST['wife_lastname'] ?? null);

        $husband_firstname  = clean($_POST['husband_firstname'] ?? null);
        $husband_middlename = clean($_POST['husband_middlename'] ?? null);
        $husband_lastname   = clean($_POST['husband_lastname'] ?? null);
        $husband_suffix     = clean($_POST['husband_suffix'] ?? null);

        $year  = intval($_POST['year'] ?? 0);
        $month = intval($_POST['month'] ?? 0);
        $day   = intval($_POST['day'] ?? 0);

        if ($year && $month && $day) {
            $wedding_date = sprintf('%04d-%02d-%02d', $year, $month, $day);
        }

        $celebrant_name = trim(
            ($wife_firstname ? $wife_firstname . ' ' : '') .
            ($wife_middlename ? $wife_middlename . ' ' : '') .
            ($wife_lastname ? $wife_lastname . ' ' : '') .
            'and ' .
            ($husband_firstname ? $husband_firstname . ' ' : '') .
            ($husband_middlename ? $husband_middlename . ' ' : '') .
            ($husband_lastname ? $husband_lastname : '')
        );
        break;

    case 'Special Intention':
        $special_intention = clean($_POST['special_intention'] ?? null);
        $celebrant_name = $special_intention;
        break;

    default:
        $celebrant_name = 'N/A';
}

/* ================= CONTACT PERSON ================= */
$contact_firstname  = clean($_POST['contact_person_firstname'] ?? null);
$contact_middlename = clean($_POST['contact_person_middlename'] ?? null);
$contact_lastname   = clean($_POST['contact_person_lastname'] ?? null);
$contact_suffix     = clean($_POST['contact_person_suffix'] ?? null);
$contact_relation   = clean($_POST['contact_person_relation'] ?? null);
$contact_number     = clean($_POST['contact_person_contact_no'] ?? null);
$contact_email      = clean($_POST['contact_person_email'] ?? null);

/* ================= INSERT CELEBRATORY REQUEST ================= */
try {
    $sql = "INSERT INTO celebratory (
        reservation_user_id,
        date, time, celebration_type, mass_venue,
        province, city, street_no, barangay,
        thanksgiving, special_intention,
        celebrant_firstname, celebrant_middlename, celebrant_lastname, celebrant_suffix, celebrant_birthdate,
        wife_firstname, wife_middlename, wife_lastname,
        husband_firstname, husband_middlename, husband_lastname, husband_suffix,
        wedding_date,
        contact_firstname, contact_middlename, contact_lastname, contact_suffix,
        contact_relation, contact_number, contact_email
    ) VALUES (
        :reservation_user_id,
        :date, :time, :celebration_type, :mass_venue,
        :province, :city, :street_no, :barangay,
        :thanksgiving, :special_intention,
        :celebrant_firstname, :celebrant_middlename, :celebrant_lastname, :celebrant_suffix, :celebrant_birthdate,
        :wife_firstname, :wife_middlename, :wife_lastname,
        :husband_firstname, :husband_middlename, :husband_lastname, :husband_suffix,
        :wedding_date,
        :contact_firstname, :contact_middlename, :contact_lastname, :contact_suffix,
        :contact_relation, :contact_number, :contact_email
    )";

    $stmt = $dbh->prepare($sql);
    $stmt->execute([
        ':reservation_user_id'   => $user_id,
        ':date'                  => $date,
        ':time'                  => $time,
        ':celebration_type'      => $celebration_type,
        ':mass_venue'            => $mass_venue,
        ':province'              => $province,
        ':city'                  => $city,
        ':street_no'             => $street_no,
        ':barangay'              => $barangay,
        ':thanksgiving'          => $thanksgiving,
        ':special_intention'     => $special_intention,
        ':celebrant_firstname'   => $celebrant_firstname,
        ':celebrant_middlename'  => $celebrant_middlename,
        ':celebrant_lastname'    => $celebrant_lastname,
        ':celebrant_suffix'      => $celebrant_suffix,
        ':celebrant_birthdate'   => $celebrant_birthdate,
        ':wife_firstname'        => $wife_firstname,
        ':wife_middlename'       => $wife_middlename,
        ':wife_lastname'         => $wife_lastname,
        ':husband_firstname'     => $husband_firstname,
        ':husband_middlename'    => $husband_middlename,
        ':husband_lastname'      => $husband_lastname,
        ':husband_suffix'        => $husband_suffix,
        ':wedding_date'          => $wedding_date,
        ':contact_firstname'     => $contact_firstname,
        ':contact_middlename'    => $contact_middlename,
        ':contact_lastname'      => $contact_lastname,
        ':contact_suffix'        => $contact_suffix,
        ':contact_relation'      => $contact_relation,
        ':contact_number'        => $contact_number,
        ':contact_email'         => $contact_email
    ]);

    $reservation_id = $dbh->lastInsertId();

} catch (PDOException $e) {
    exit('Database Error: ' . $e->getMessage());
}

/* ================= ADMIN NOTIFICATION ================= */
$userStmt = $dbh->prepare("SELECT givenname, lastname FROM users WHERE user_id = :user_id");
$userStmt->execute([':user_id' => $user_id]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

$submittedBy = $user ? $user['givenname'] . ' ' . $user['lastname'] : 'A user';

$adminNotifTitle   = 'Celebratory Mass Request';
$adminNotifMessage = $submittedBy
    . ' submitted a Celebratory Mass request ('.$celebration_type.') for '
    . $celebrant_name
    . ' scheduled on ' . $date . ' at ' . $time
    . ' at ' . $mass_venue . '.';

$stmtAdminNotif = $dbh->prepare("
    INSERT INTO admin_notifications (sacrament, reference_id, title, message, created_at)
    VALUES ('Celebratory Mass', :reference_id, :title, :message, NOW())
");
$stmtAdminNotif->execute([
    ':reference_id' => $reservation_id,
    ':title'        => $adminNotifTitle,
    ':message'      => $adminNotifMessage
]);

/* ================= USER NOTIFICATION ================= */
$userNotifTitle   = 'Celebratory Mass Request Submitted';
$userNotifMessage = "Good day, {$user['givenname']}! "
    . "We have received your Celebratory Mass request (".$celebration_type.") "
    . "for {$celebrant_name}. "
    . "Your request is currently under review by the parish office. "
    . "You will be notified once it has been approved. "
    . "Thank you and God bless!";

$stmtUserNotif = $dbh->prepare("
    INSERT INTO notifications (user_id, title, message, type, created_at)
    VALUES (:user_id, :title, :message, :type, NOW())
");
$stmtUserNotif->execute([
    ':user_id' => $user_id,
    ':title'   => $userNotifTitle,
    ':message' => $userNotifMessage,
    ':type'    => 'successful_request'
]);

$_SESSION['toast_success'] =
    'Your celebratory mass request has been submitted successfully. Please wait for the approval notification.';
header('Location: ../celebratory.php');
exit;
