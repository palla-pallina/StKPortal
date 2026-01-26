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
   FILE UPLOAD CONFIG
================================ */
$uploadDir = '/opt/lampp/htdocs/stkportal_src/src/private/sacraments/uploads/';
$relativePath = 'private/sacraments/uploads/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

function uploadFile($fileKey, $uploadDir, $relativePath, $required = true) {

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
    $birthCertPath = uploadFile('birth_certificate', $uploadDir, $relativePath, true);
    $baptCertPath = uploadFile('baptismal_certificate', $uploadDir, $relativePath, true);
} catch (Exception $e) {
    exit($e->getMessage());
}

/* ===============================
   FORM DATA
================================ */
$reservation_user_id = $_SESSION['user_id'];

$date = $_POST['date'];
$contact = $_POST['contact'];
$time = $_POST['time'];
$email = $_POST['email'];

$confirm_firstname = $_POST['confirm_firstname'];
$confirm_middlename = $_POST['confirm_middlename'];
$confirm_lastname = $_POST['confirm_lastname'];
$confirm_suffix = $_POST['confirm_suffix'] ?? '';
$confirm_gender = $_POST['confirm_gender'];

$birthdate = $_POST['birth_date'];
$birthplace = $_POST['birth_place'];

$father_firstname = $_POST['father_firstname'];
$father_middlename = $_POST['father_middlename'];
$father_lastname = $_POST['father_lastname'];
$father_suffix = $_POST['father_suffix'] ?? '';

$mother_firstname = $_POST['mother_firstname'];
$mother_middlename = $_POST['mother_middlename'];
$mother_lastname = $_POST['mother_lastname'];

$ninong_firstname = $_POST['ninong_firstname'];
$ninong_middlename = $_POST['ninong_middlename'];
$ninong_lastname = $_POST['ninong_lastname'];
$ninong_suffix = $_POST['ninong_suffix'] ?? '';

$ninang_firstname = $_POST['ninang_firstname'];
$ninang_middlename = $_POST['ninang_middlename'];
$ninang_lastname = $_POST['ninang_lastname'];

$as_ninong_firstname = $_POST['as_ninong_firstname'] ?? [];
$as_ninong_middlename = $_POST['as_ninong_middlename'] ?? [];
$as_ninong_lastname = $_POST['as_ninong_lastname'] ?? [];
$as_ninong_suffix = $_POST['as_ninong_suffix'] ?? [];

$as_ninang_firstname = $_POST['as_ninang_firstname'] ?? [];
$as_ninang_middlename = $_POST['as_ninang_middlename'] ?? [];
$as_ninang_lastname = $_POST['as_ninang_lastname'] ?? [];

$confirmation_type = $_POST['confirmation_type'];

/* ===============================
   GUARDIAN DATA (OPTIONAL)
================================ */
$guardian_firstname = $_POST['guardian_firstname'] ?? null;
$guardian_middlename = $_POST['guardian_middlename'] ?? null;
$guardian_lastname = $_POST['guardian_lastname'] ?? null;
$guardian_suffix = $_POST['guardian_suffix'] ?? null;
$guardian_type = $_POST['guardian_type'] ?? null;
$guardian_type_other = $_POST['guardian_type_other'] ?? null;

// Guardian address (reuse same address inputs)
$guardian_province = $_POST['province'] ?? null;
$guardian_city = $_POST['city'] ?? null;
$guardian_street = $_POST['street_no'] ?? null;
$guardian_barangay = $_POST['barangay'] ?? null;

// Normalize "Other"
if ($guardian_type === 'Other' && !empty($guardian_type_other)) {
    $guardian_type = $guardian_type_other;
}

// Build address string
$guardian_address = null;
if ($guardian_province || $guardian_city || $guardian_street || $guardian_barangay) {
    $guardian_address = trim(
        ($guardian_street ? $guardian_street . ', ' : '') .
        ($guardian_barangay ? $guardian_barangay . ', ' : '') .
        ($guardian_city ? $guardian_city . ', ' : '') .
        $guardian_province
    );
}

/* ===============================
   DATABASE TRANSACTION
================================ */
try {
    $dbh->beginTransaction();

    $sql1 = "INSERT INTO confirmations (
        reservation_user_id,
        confirm_firstname, confirm_middlename, confirm_lastname, confirm_suffix,
        birth_date, birth_place, confirm_gender,
        father_firstname, father_middlename, father_lastname, father_suffix, 
        mother_firstname, mother_middlename, mother_lastname,
        date, contact, time, email, confirmation_type,
        birth_certificate, baptismal_certificate
    ) VALUES (
        :reservation_user_id,
        :confirm_firstname, :confirm_middlename, :confirm_lastname, :confirm_suffix,
        :birth_date, :birth_place, :confirm_gender,
        :father_firstname, :father_middlename, :father_lastname, :father_suffix,
        :mother_firstname, :mother_middlename, :mother_lastname,
        :date, :contact, :time, :email, :confirmation_type,
        :birth_certificate, :baptismal_certificate
    )";

    $stmt1 = $dbh->prepare($sql1);
    $stmt1->execute([
        ':reservation_user_id' => $reservation_user_id,
        ':confirm_firstname' => $confirm_firstname,
        ':confirm_middlename' => $confirm_middlename,
        ':confirm_lastname' => $confirm_lastname,
        ':confirm_suffix' => $confirm_suffix,
        ':birth_date' => $birthdate,
        ':birth_place' => $birthplace,
        ':confirm_gender' => $confirm_gender,

        ':father_firstname' => $father_firstname,
        ':father_middlename' => $father_middlename,
        ':father_lastname' => $father_lastname,
        ':father_suffix' => $father_suffix,

        ':mother_firstname' => $mother_firstname,
        ':mother_middlename' => $mother_middlename,
        ':mother_lastname' => $mother_lastname,

        ':date' => $date,
        ':contact' => $contact,
        ':time' => $time,
        ':email' => $email,
        ':confirmation_type' => $confirmation_type,

        ':birth_certificate' => $birthCertPath,
        ':baptismal_certificate' => $baptCertPath
    ]);

    $confirmation_id = $dbh->lastInsertId();

    /* ===============================
    INSERT GUARDIAN (IF APPLICABLE)
    ================================ */
    if (!empty($guardian_firstname) && !empty($guardian_lastname)) {

        $stmtGuardian = $dbh->prepare("
            INSERT INTO guardian_tbl (
                confirmation_id,
                guardian_firstname,
                guardian_middlename,
                guardian_lastname,
                guardian_suffix,
                guardian_type,
                guardian_address
            ) VALUES (
                :confirmation_id,
                :guardian_firstname,
                :guardian_middlename,
                :guardian_lastname,
                :guardian_suffix,
                :guardian_type,
                :guardian_address
            )
        ");

        $stmtGuardian->execute([
            ':confirmation_id' => $confirmation_id,
            ':guardian_firstname' => $guardian_firstname,
            ':guardian_middlename' => $guardian_middlename,
            ':guardian_lastname' => $guardian_lastname,
            ':guardian_suffix' => $guardian_suffix,
            ':guardian_type' => $guardian_type,
            ':guardian_address' => $guardian_address
        ]);
    }

    $sql2 = "INSERT INTO ninong_tbl 
    (confirmation_id, ninong_firstname, ninong_middlename, ninong_lastname, ninong_suffix)
    VALUES (?, ?, ?, ?, ?)";

    $stmt2 = $dbh->prepare($sql2);
    $stmt2->execute([
        $confirmation_id,
        $ninong_firstname,
        $ninong_middlename,
        $ninong_lastname,
        $ninong_suffix
    ]);

    $sql3 = "INSERT INTO ninang_tbl 
    (confirmation_id, ninang_firstname, ninang_middlename, ninang_lastname)
    VALUES (?, ?, ?, ?)";

    $stmt3 = $dbh->prepare($sql3);
    $stmt3->execute([
        $confirmation_id,
        $ninang_firstname,
        $ninang_middlename,
        $ninang_lastname
    ]);

    /* ===============================
    MULTIPLE ADDITIONAL NINONG
    =============================== */
    $sql4 = "INSERT INTO as_ninong_tbl
    (confirmation_id, as_ninong_firstname, as_ninong_middlename, as_ninong_lastname, as_ninong_suffix)
    VALUES (?, ?, ?, ?, ?)";

    $stmt4 = $dbh->prepare($sql4);

    for ($i = 0; $i < count($as_ninong_firstname); $i++) {
        if (!empty($as_ninong_firstname[$i])) {
            $stmt4->execute([
                $confirmation_id,
                $as_ninong_firstname[$i],
                $as_ninong_middlename[$i] ?? null,
                $as_ninong_lastname[$i],
                $as_ninong_suffix[$i] ?? null
            ]);
        }
    }

    /* ===============================
    MULTIPLE ADDITIONAL NINANG
    =============================== */
    $sql5 = "INSERT INTO as_ninang_tbl
    (confirmation_id, as_ninang_firstname, as_ninang_middlename, as_ninang_lastname)
    VALUES (?, ?, ?, ?)";

    $stmt5 = $dbh->prepare($sql5);

    for ($i = 0; $i < count($as_ninang_firstname); $i++) {
        if (!empty($as_ninang_firstname[$i])) {
            $stmt5->execute([
                $confirmation_id,
                $as_ninang_firstname[$i],
                $as_ninang_middlename[$i] ?? null,
                $as_ninang_lastname[$i]
            ]);
        }
    }

    /* ===============================
    ADMIN NOTIFICATION
    ================================ */

    // Fetch user name
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

    // Confirmand full name
    $confirmFullname = trim(
        $confirm_firstname . ' ' .
        ($confirm_middlename ? $confirm_middlename . ' ' : '') .
        $confirm_lastname . ' ' .
        $confirm_suffix
    );

    // Format date & time
    $formattedDate = date('F d, Y', strtotime($date));
    $formattedTime = $time ? date('h:i A', strtotime($time)) : '';

    // Notification content
    $notifTitle = 'Confirmation Reservation';
    $notifMessage =
        $submittedBy .
        ' submitted a confirmation reservation for ' .
        $confirmFullname .
        ' on ' . $formattedDate .
        ($formattedTime ? ' at ' . $formattedTime : '') . '.';

    // Insert admin notification
    $stmtNotif = $dbh->prepare("
        INSERT INTO admin_notifications (sacrament, reference_id, title, message)
        VALUES ('Confirmation', :reference_id, :title, :message)
    ");

    $stmtNotif->execute([
        ':reference_id' => $confirmation_id,
        ':title'        => $notifTitle,
        ':message'      => $notifMessage
    ]);


    /* ===============================
    USER NOTIFICATION (SUCCESS)
    ================================ */

    // Confirm full name
    $confirmFullname = trim(
        $confirm_firstname . ' ' .
        ($confirm_middlename ? $confirm_middlename . ' ' : '') .
        $confirm_lastname . ' ' .
        $confirm_suffix
    );

    // Notification content
    $userNotifTitle = 'Confirmation Reservation Submitted';
    $userNotifMessage = "Good day, {$user['givenname']}! We’ve received your request for confirmation for {$confirmFullname}, and it’s currently being reviewed. We’ll get back to you soon. Thank you and God bless!";

    // Insert notification
    $stmtUserNotif = $dbh->prepare("
        INSERT INTO notifications (user_id, title, message, type)
        VALUES (:user_id, :title, :message, :type)
    ");

    $stmtUserNotif->execute([
        ':user_id' => $reservation_user_id,
        ':title'   => $userNotifTitle,
        ':message' => $userNotifMessage,
        ':type'    => 'successful_request'
    ]);


    $dbh->commit();

    $_SESSION['toast_success'] =
        'Your confirmation request has been submitted successfully. Please wait for the approval notification.';

    echo "<script>
        window.location.href='../confirmation.php';
    </script>";
    exit;
    exit;

} catch (PDOException $e) {
    $dbh->rollBack();
    exit("Database Error: " . $e->getMessage());
}
