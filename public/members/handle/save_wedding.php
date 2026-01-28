<?php
require_once '../../connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Invalid request.");
}

if (!isset($_SESSION['user_id'])) {
    exit("User not logged in.");
}

function calculateAge($birthdate) {
    return date_diff(date_create($birthdate), date_create('today'))->y;
}

$brideAge = calculateAge($_POST['bride_birthdate']);
$groomAge = calculateAge($_POST['groom_birthdate']);

if ($brideAge < 18 || $groomAge < 18) {
    echo "<script>
        alert('Both bride and groom must be at least 18 years old to apply.');
        window.history.back();
    </script>";
    exit();
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

/* ===============================
   UPLOAD FILES
================================ */
try {
    // BRIDE FILES
    $bBirthCertPath   = uploadFile('b_birth_certificate', $uploadDir, $relativePath, true);
    $bBaptCertPath    = uploadFile('b_baptismal_certificate', $uploadDir, $relativePath, false);
    $bConfCertPath    = uploadFile('b_confirmation_certificate', $uploadDir, $relativePath, false);
    $bMarriageLicPath = uploadFile('b_marriage_license', $uploadDir, $relativePath, false);
    $bCenomarPath     = uploadFile('b_cenomar', $uploadDir, $relativePath, false);

    // GROOM FILES
    $gBirthCertPath   = uploadFile('g_birth_certificate', $uploadDir, $relativePath, true);
    $gBaptCertPath    = uploadFile('g_baptismal_certificate', $uploadDir, $relativePath, false);
    $gConfCertPath    = uploadFile('g_confirmation_certificate', $uploadDir, $relativePath, false);
    $gMarriageLicPath = uploadFile('g_marriage_license', $uploadDir, $relativePath, false);
    $gCenomarPath     = uploadFile('g_cenomar', $uploadDir, $relativePath, false);

} catch (Exception $e) {
    exit($e->getMessage());
}

/* ===============================
   FORM DATA
================================ */

$reservation_user_id = $_SESSION['user_id'];
$date = $_POST['date'];
$time = $_POST['time'];
$wedding_type = $_POST['wedding_type'];

/* BRIDE */
$bride_firstname = $_POST['bride_firstname'];
$bride_middlename = $_POST['bride_middlename'] ?? '';
$bride_lastname = $_POST['bride_lastname'];
$bride_birthdate = $_POST['bride_birthdate'];
$bride_nationality = $_POST['bride_nationality'];
$bride_contact = $_POST['bride_contact'];
$bride_email = $_POST['bride_email'];

/* GROOM */
$groom_firstname = $_POST['groom_firstname'];
$groom_middlename = $_POST['groom_middlename'] ?? '';
$groom_lastname = $_POST['groom_lastname'];
$groom_suffix = $_POST['groom_suffix'] ?? '';
$groom_birthdate = $_POST['groom_birthdate'];
$groom_nationality = $_POST['groom_nationality'];
$groom_contact = $_POST['groom_contact'];
$groom_email = $_POST['groom_email'];

/* SPONSORS */
$ninong_firstname = $_POST['ninong_firstname'];
$ninong_middlename = $_POST['ninong_middlename'] ?? '';
$ninong_lastname = $_POST['ninong_lastname'];
$ninong_suffix = $_POST['ninong_suffix'] ?? '';

$ninang_firstname = $_POST['ninang_firstname'];
$ninang_middlename = $_POST['ninang_middlename'] ?? '';
$ninang_lastname = $_POST['ninang_lastname'];

$as_ninong_firstname = $_POST['as_ninong_firstname'] ?? [];
$as_ninong_middlename = $_POST['as_ninong_middlename'] ?? [];
$as_ninong_lastname = $_POST['as_ninong_lastname'] ?? [];
$as_ninong_suffix = $_POST['as_ninong_suffix'] ?? [];

$as_ninang_firstname = $_POST['as_ninang_firstname'] ?? [];
$as_ninang_middlename = $_POST['as_ninang_middlename'] ?? [];
$as_ninang_lastname = $_POST['as_ninang_lastname'] ?? [];

/* ===============================
   DATABASE TRANSACTION
================================ */
try {
    $dbh->beginTransaction();

    $sql1 = "INSERT INTO weddings (
        reservation_user_id, date, time, wedding_type,
        bride_firstname, bride_middlename, bride_lastname,
        bride_birthdate, bride_nationality,
        bride_contact, bride_email,
        b_birth_certificate, b_baptismal_certificate, b_confirmation_certificate,
        b_marriage_license, b_cenomar,
        groom_firstname, groom_middlename, groom_lastname, groom_suffix,
        groom_birthdate, groom_nationality,
        groom_contact, groom_email,
        g_birth_certificate, g_baptismal_certificate, g_confirmation_certificate,
        g_marriage_license, g_cenomar
    ) VALUES (
        :reservation_user_id, :date, :time, :wedding_type,
        :bride_firstname, :bride_middlename, :bride_lastname,
        :bride_birthdate, :bride_nationality,
        :bride_contact, :bride_email,
        :b_birth_certificate, :b_baptismal_certificate, :b_confirmation_certificate,
        :b_marriage_license, :b_cenomar,
        :groom_firstname, :groom_middlename, :groom_lastname, :groom_suffix,
        :groom_birthdate, :groom_nationality,
        :groom_contact, :groom_email,
        :g_birth_certificate, :g_baptismal_certificate, :g_confirmation_certificate,
        :g_marriage_license, :g_cenomar
    )";

    $stmt1 = $dbh->prepare($sql1);
    $stmt1->execute([
        ':reservation_user_id' => $reservation_user_id,
        ':date' => $date,
        ':time' => $time,
        ':wedding_type' => $wedding_type,

        ':bride_firstname' => $bride_firstname,
        ':bride_middlename' => $bride_middlename,
        ':bride_lastname' => $bride_lastname,
        ':bride_birthdate' => $bride_birthdate,
        ':bride_nationality' => $bride_nationality,
        ':bride_contact' => $bride_contact,
        ':bride_email' => $bride_email,

        ':b_birth_certificate' => $bBirthCertPath,
        ':b_baptismal_certificate' => $bBaptCertPath,
        ':b_confirmation_certificate' => $bConfCertPath,
        ':b_marriage_license' => $bMarriageLicPath,
        ':b_cenomar' => $bCenomarPath,

        ':groom_firstname' => $groom_firstname,
        ':groom_middlename' => $groom_middlename,
        ':groom_lastname' => $groom_lastname,
        ':groom_suffix' => $groom_suffix,
        ':groom_birthdate' => $groom_birthdate,
        ':groom_nationality' => $groom_nationality,
        ':groom_contact' => $groom_contact,
        ':groom_email' => $groom_email,

        ':g_birth_certificate' => $gBirthCertPath,
        ':g_baptismal_certificate' => $gBaptCertPath,
        ':g_confirmation_certificate' => $gConfCertPath,
        ':g_marriage_license' => $gMarriageLicPath,
        ':g_cenomar' => $gCenomarPath
    ]);

    $wedding_id = $dbh->lastInsertId();

    $sql2 = "INSERT INTO ninong_tbl 
    (wedding_id, ninong_firstname, ninong_middlename, ninong_lastname, ninong_suffix)
    VALUES (?, ?, ?, ?, ?)";

    $stmt2 = $dbh->prepare($sql2);
    $stmt2->execute([
        $wedding_id,
        $ninong_firstname,
        $ninong_middlename,
        $ninong_lastname,
        $ninong_suffix
    ]);

    $sql3 = "INSERT INTO ninang_tbl 
    (wedding_id, ninang_firstname, ninang_middlename, ninang_lastname)
    VALUES (?, ?, ?, ?)";

    $stmt3 = $dbh->prepare($sql3);
    $stmt3->execute([
        $wedding_id,
        $ninang_firstname,
        $ninang_middlename,
        $ninang_lastname
    ]);

    /* ===============================
    MULTIPLE ADDITIONAL NINONG
    =============================== */
    $sql4 = "INSERT INTO as_ninong_tbl
    (wedding_id, as_ninong_firstname, as_ninong_middlename, as_ninong_lastname, as_ninong_suffix)
    VALUES (?, ?, ?, ?, ?)";

    $stmt4 = $dbh->prepare($sql4);

    for ($i = 0; $i < count($as_ninong_firstname); $i++) {
        if (!empty($as_ninong_firstname[$i])) {
            $stmt4->execute([
                $wedding_id,
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
    (wedding_id, as_ninang_firstname, as_ninang_middlename, as_ninang_lastname)
    VALUES (?, ?, ?, ?)";

    $stmt5 = $dbh->prepare($sql5);

    for ($i = 0; $i < count($as_ninang_firstname); $i++) {
        if (!empty($as_ninang_firstname[$i])) {
            $stmt5->execute([
                $wedding_id,
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

    // Full names
    $brideFullname = trim($bride_firstname . ' ' . ($bride_middlename ? $bride_middlename . ' ' : '') . $bride_lastname);
    $groomFullname = trim($groom_firstname . ' ' . ($groom_middlename ? $groom_middlename . ' ' : '') . $groom_lastname . ' ' . $groom_suffix);

    // Format date & time
    $formattedDate = date('F d, Y', strtotime($date));
    $formattedTime = $time ? date('h:i A', strtotime($time)) : '';

    // Notification content
    $notifTitle = 'Wedding Reservation';
    $notifMessage = "{$submittedBy} submitted a wedding reservation for Groom: {$groomFullname} and Bride: {$brideFullname} on {$formattedDate}" .
        ($formattedTime ? " at {$formattedTime}" : '') . '.';

    // Insert admin notification
    $stmtNotif = $dbh->prepare("
        INSERT INTO admin_notifications (sacrament, reference_id, title, message)
        VALUES ('Wedding', :reference_id, :title, :message)
    ");

    $stmtNotif->execute([
        ':reference_id' => $wedding_id,
        ':title'        => $notifTitle,
        ':message'      => $notifMessage
    ]);

    /* ===============================
    USER SUCCESS NOTIFICATION
    ================================ */

    // User first name
    $userFirstName = $user ? $user['givenname'] : 'User';

    // Bride & Groom full names
    $brideFullname = trim($bride_firstname . ' ' . ($bride_middlename ? $bride_middlename . ' ' : '') . $bride_lastname);
    $groomFullname = trim($groom_firstname . ' ' . ($groom_middlename ? $groom_middlename . ' ' : '') . $groom_lastname . ' ' . $groom_suffix);

    // Notification content
    $userNotifTitle = 'Wedding Request Submitted';
    $userNotifMessage = "Good day, {$userFirstName}! " .
        "We’ve received your request for a church wedding for {$brideFullname} and {$groomFullname}, and it’s currently being reviewed. " .
        "We’ll get back to you soon. Thank you and God bless!";

    // Insert into notifications table
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
            'Your wedding request has been submitted successfully. Please wait for the approval notification.';

        echo "<script>
            window.location.href='../wedding.php';
        </script>";
    exit;

} catch (PDOException $e) {
    $dbh->rollBack();
    exit("Database Error: " . $e->getMessage());
}
