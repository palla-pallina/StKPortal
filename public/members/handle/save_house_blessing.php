<?php
require_once '../../connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Invalid request.");
}

if (!isset($_SESSION['user_id'])) {
    exit("User not logged in.");
}

/* ================= BASIC SANITIZATION ================= */
function clean($value) {
    return trim(htmlspecialchars($value ?? ''));
}

$reservation_user_id = $_SESSION['user_id'];

/* ================= SCHEDULE ================= */
$date = clean($_POST['date']);
$time = clean($_POST['time']);

/* ================= HOUSE ADDRESS ================= */
$province  = clean($_POST['province']);
$city      = clean($_POST['city']);
$street_no = clean($_POST['street_no']);
$barangay  = clean($_POST['barangay']);

/* ================= NOTES ================= */
$notes = clean($_POST['notes']);
$special_intention = ($notes === 'Special Intention') ? clean($_POST['special_intention']) : null;

/* ================= REQUESTING PERSON ================= */
$requesting_firstname  = clean($_POST['requesting_firstname']);
$requesting_middlename = clean($_POST['requesting_middlename'] ?? '');
$requesting_lastname   = clean($_POST['requesting_lastname']);
$requesting_suffix     = clean($_POST['requesting_suffix'] ?? '');
$requesting_contact    = clean($_POST['requesting_contact_no']);
$requesting_email      = clean($_POST['requesting_email']);

/* ================= INSERT INTO DATABASE ================= */
try {
    $dbh->beginTransaction();

    $sql = "INSERT INTO house_blessings (
        reservation_user_id,
        date,
        time,
        province,
        city,
        street_no,
        barangay,
        notes,
        special_intention,
        requesting_firstname,
        requesting_middlename,
        requesting_lastname,
        requesting_suffix,
        requesting_contact,
        requesting_email,
        created_at
    ) VALUES (
        :reservation_user_id,
        :date,
        :time,
        :province,
        :city,
        :street_no,
        :barangay,
        :notes,
        :special_intention,
        :requesting_firstname,
        :requesting_middlename,
        :requesting_lastname,
        :requesting_suffix,
        :requesting_contact,
        :requesting_email,
        NOW()
    )";

    $stmt = $dbh->prepare($sql);
    $stmt->execute([
        ':reservation_user_id'   => $reservation_user_id,
        ':date'                  => $date,
        ':time'                  => $time,
        ':province'              => $province,
        ':city'                  => $city,
        ':street_no'             => $street_no,
        ':barangay'              => $barangay,
        ':notes'                 => $notes,
        ':special_intention'     => $special_intention,
        ':requesting_firstname'  => $requesting_firstname,
        ':requesting_middlename' => $requesting_middlename,
        ':requesting_lastname'   => $requesting_lastname,
        ':requesting_suffix'     => $requesting_suffix,
        ':requesting_contact'    => $requesting_contact,
        ':requesting_email'      => $requesting_email
    ]);

    $reservation_id = $dbh->lastInsertId();

    /* =============================== */
    /*      ADMIN NOTIFICATION        */
    /* =============================== */
    $userStmt = $dbh->prepare("SELECT givenname, lastname FROM users WHERE user_id = :user_id");
    $userStmt->execute([':user_id' => $reservation_user_id]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    $submittedBy = $user ? $user['givenname'] . ' ' . $user['lastname'] : 'A user';
    $requesterName = trim(
        $requesting_firstname . ' ' .
        ($requesting_middlename ? $requesting_middlename . ' ' : '') .
        $requesting_lastname . ' ' .
        $requesting_suffix
    );

    $adminNotifTitle = 'House Blessing Request';
    $adminNotifMessage = $submittedBy
        . ' submitted a House Blessing request ('.$notes.') for '
        . $requesterName
        . ' scheduled on ' . $date . ' at ' . $time
        . ' located at ' . $street_no . ', ' . $barangay . ', ' . $city . ', ' . $province . '.';

    $stmtAdminNotif = $dbh->prepare("
        INSERT INTO admin_notifications (sacrament, reference_id, title, message, created_at)
        VALUES ('House Blessing', :reference_id, :title, :message, NOW())
    ");
    $stmtAdminNotif->execute([
        ':reference_id' => $reservation_id,
        ':title'        => $adminNotifTitle,
        ':message'      => $adminNotifMessage
    ]);

    /* =============================== */
    /*       USER NOTIFICATION         */
    /* =============================== */
    $userNotifTitle = 'House Blessing Request Submitted';
    $userNotifMessage = "Good day, {$user['givenname']}! "
        . "We have received your House Blessing request "
        . "scheduled on {$date} at {$time} "
        . "located at {$street_no}, {$barangay}, {$city}, {$province}. "
        . "Your request is currently under review by the parish office. "
        . "You will be notified once it has been approved. "
        . "Thank you and God bless!";

    $stmtUserNotif = $dbh->prepare("
        INSERT INTO notifications (user_id, title, message, type, created_at)
        VALUES (:user_id, :title, :message, :type, NOW())
    ");
    $stmtUserNotif->execute([
        ':user_id' => $reservation_user_id,
        ':title'   => $userNotifTitle,
        ':message' => $userNotifMessage,
        ':type'    => 'successful_request'
    ]);

    $_SESSION['toast_success'] =
    'Your house blessing request has been submitted successfully. Please wait for the approval notification.';
    $dbh->commit();

    header('Location: ../house_blessing.php?success=1');
    exit;

} catch (PDOException $e) {
    $dbh->rollBack();
    exit("Database Error: " . $e->getMessage());
}
