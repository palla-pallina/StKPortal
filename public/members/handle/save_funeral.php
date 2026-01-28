<?php
require_once '../../connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Invalid request.");
}

if (!isset($_SESSION['user_id'])) {
    exit("User not logged in.");
}

/* ===============================
   FORM DATA
================================ */

$reservation_user_id = $_SESSION['user_id'];

/* deceased */
$deceased_firstname = $_POST['deceased_firstname'];
$deceased_middlename = $_POST['deceased_middlename'] ?? '';
$deceased_lastname = $_POST['deceased_lastname'];
$deceased_suffix = $_POST['deceased_suffix'] ?? '';

$mass_venue = $_POST['mass_venue'];
$cause_of_death = $_POST['cause_of_death'];
$funeral_date = $_POST['funeral_date'];
$funeral_time = $_POST['funeral_time'];
$province = $_POST['province'];
$city = $_POST['city'];
$street_no = $_POST['street_no'];
$barangay = $_POST['barangay'];

/* contact_person */
$contact_person_firstname = $_POST['contact_person_firstname'];
$contact_person_middlename = $_POST['contact_person_middlename'] ?? '';
$contact_person_lastname = $_POST['contact_person_lastname'];
$contact_person_suffix = $_POST['contact_person_suffix'] ?? '';
$contact_person_relation = $_POST['contact_person_relation'];
$contact_person_contact_no = $_POST['contact_person_contact_no'];
$contact_person_email = $_POST['contact_person_email'];


/* ===============================
   DATABASE TRANSACTION
================================ */
try {
    $dbh->beginTransaction();

    $sql = "INSERT INTO funerals (
        reservation_user_id,
        deceased_firstname, deceased_middlename, deceased_lastname, deceased_suffix,
        cause_of_death, mass_venue,
        funeral_date, funeral_time, 
        province, city, street_no, barangay,
        contact_person_firstname, contact_person_middlename, contact_person_lastname, contact_person_suffix,
        contact_person_relation, contact_person_contact_no, contact_person_email
    ) VALUES (
        :reservation_user_id,
        :deceased_firstname, :deceased_middlename, :deceased_lastname, :deceased_suffix,
        :cause_of_death, :mass_venue,
        :funeral_date, :funeral_time,
        :province, :city, :street_no, :barangay,
        :contact_person_firstname, :contact_person_middlename, :contact_person_lastname, :contact_person_suffix,
        :contact_person_relation, :contact_person_contact_no, :contact_person_email

    )";

    $stmt = $dbh->prepare($sql);
    $stmt->execute([
        ':reservation_user_id' => $reservation_user_id,

        ':deceased_firstname' => $deceased_firstname,
        ':deceased_middlename' => $deceased_middlename,
        ':deceased_lastname' => $deceased_lastname,
        ':deceased_suffix' => $deceased_suffix,

        ':cause_of_death' => $cause_of_death,
        ':mass_venue' => $mass_venue,
        ':funeral_date' => $funeral_date,
        ':funeral_time' => $funeral_time,

        ':province' => $province,
        ':city' => $city,
        ':street_no' => $street_no,
        ':barangay' => $barangay,

        ':contact_person_firstname' => $contact_person_firstname,
        ':contact_person_middlename' => $contact_person_middlename,
        ':contact_person_lastname' => $contact_person_lastname,
        ':contact_person_suffix' => $contact_person_suffix,

        ':contact_person_relation' => $contact_person_relation,
        ':contact_person_contact_no' => $contact_person_contact_no,
        ':contact_person_email' => $contact_person_email
    ]);

    $funeral_id = $dbh->lastInsertId();

/* ===============================
   FETCH USER INFO
================================ */

$userStmt = $dbh->prepare("
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
   DECEASED FULL NAME
================================ */

$deceasedFullname = trim(
    $deceased_firstname . ' ' .
    ($deceased_middlename ? $deceased_middlename . ' ' : '') .
    $deceased_lastname . ' ' .
    $deceased_suffix
);

/* ===============================
   ADMIN NOTIFICATION
================================ */

$adminNotifTitle = 'Funeral Mass Request';
$adminNotifMessage =
    $submittedBy
    . ' submitted a Funeral Mass request for '
    . $deceasedFullname
    . ' scheduled on '
    . $funeral_date . ' at ' . $funeral_time . '.';

// Insert admin notification
$stmtAdminNotif = $dbh->prepare("
    INSERT INTO admin_notifications (
        sacrament,
        reference_id,
        title,
        message,
        created_at
    ) VALUES (
        'Funeral',
        :reference_id,
        :title,
        :message,
        NOW()
    )
");

$stmtAdminNotif->execute([
    ':reference_id' => $funeral_id, // funeral_id
    ':title'        => $adminNotifTitle,
    ':message'      => $adminNotifMessage
]);

/* ===============================
   USER NOTIFICATION (SUCCESS)
================================ */

$userNotifTitle = 'Funeral Mass Request Submitted';
$userNotifMessage =
    "Good day, {$user['givenname']}! "
    . "We have received your Funeral Mass request for {$deceasedFullname}. "
    . "Your request is currently under review by the parish office. "
    . "You will be notified once it has been approved. "
    . "Thank you and may God grant eternal rest to the departed.";

// Insert user notification
$stmtUserNotif = $dbh->prepare("
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
    'Your Funeral Mass request has been submitted successfully. Please wait for the approval notification.';
    $dbh->commit();
    header("Location: ../funeral.php");
exit;

} catch (PDOException $e) {
    $dbh->rollBack();
    exit("Database Error: " . $e->getMessage());
}
