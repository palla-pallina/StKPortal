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
    $marriageCertPath = uploadFile('marriage_certificate', $uploadDir, $relativePath, false);
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
$province = $_POST['province'];
$city = $_POST['city'];
$street_no = $_POST['street_no'];
$barangay = $_POST['barangay'];
$email = $_POST['email'];

$child_firstname = $_POST['child_firstname'];
$child_middlename = $_POST['child_middlename'];
$child_lastname = $_POST['child_lastname'];
$child_suffix = $_POST['child_suffix'] ?? '';
$child_gender = $_POST['child_gender'];

$birthdate = $_POST['birth_date'];
$birthplace = $_POST['birth_place'];

$father_firstname = $_POST['father_firstname'];
$father_middlename = $_POST['father_middlename'];
$father_lastname = $_POST['father_lastname'];
$father_suffix = $_POST['father_suffix'] ?? '';

$mother_firstname = $_POST['mother_firstname'];
$mother_middlename = $_POST['mother_middlename'];
$mother_lastname = $_POST['mother_lastname'];

$married = $_POST['married'];
$marriage_type = $_POST['marriage_type'] ?? null;

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

$baptism_type = $_POST['baptism_type'];

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

    // Insert main baptism record
    $sql1 = "INSERT INTO baptisms (
        reservation_user_id,
        child_firstname, child_middlename, child_lastname, child_suffix,
        birth_date, birth_place, child_gender,
        father_firstname, father_middlename, father_lastname, father_suffix, 
        mother_firstname, mother_middlename, mother_lastname,
        married, marriage_type,
        province, city, street_no, barangay,
        date, contact, time, email, baptism_type,
        birth_certificate, marriage_certificate
    ) VALUES (
        :reservation_user_id,
        :child_firstname, :child_middlename, :child_lastname, :child_suffix,
        :birth_date, :birth_place, :child_gender,
        :father_firstname, :father_middlename, :father_lastname, :father_suffix,
        :mother_firstname, :mother_middlename, :mother_lastname,
        :married, :marriage_type,
        :province, :city, :street_no, :barangay,
        :date, :contact, :time, :email, :baptism_type,
        :birth_certificate, :marriage_certificate
    )";

    $stmt1 = $dbh->prepare($sql1);
    $stmt1->execute([
        ':reservation_user_id' => $reservation_user_id,
        ':child_firstname' => $child_firstname,
        ':child_middlename' => $child_middlename,
        ':child_lastname' => $child_lastname,
        ':child_suffix' => $child_suffix,
        ':birth_date' => $birthdate,
        ':birth_place' => $birthplace,
        ':child_gender' => $child_gender,
        ':father_firstname' => $father_firstname,
        ':father_middlename' => $father_middlename,
        ':father_lastname' => $father_lastname,
        ':father_suffix' => $father_suffix,
        ':mother_firstname' => $mother_firstname,
        ':mother_middlename' => $mother_middlename,
        ':mother_lastname' => $mother_lastname,
        ':married' => $married,
        ':marriage_type' => $marriage_type,
        ':province' => $province,
        ':city' => $city,
        ':street_no' => $street_no,
        ':barangay' => $barangay,
        ':date' => $date,
        ':contact' => $contact,
        ':time' => $time,
        ':email' => $email,
        ':baptism_type' => $baptism_type,
        ':birth_certificate' => $birthCertPath,
        ':marriage_certificate' => $marriageCertPath
    ]);

    $baptism_id = $dbh->lastInsertId();

    /* ===============================
    INSERT GUARDIAN (IF APPLICABLE)
    ================================ */
    if (!empty($guardian_firstname) && !empty($guardian_lastname)) {

        $stmtGuardian = $dbh->prepare("
            INSERT INTO guardian_tbl (
                baptism_id,
                guardian_firstname,
                guardian_middlename,
                guardian_lastname,
                guardian_suffix,
                guardian_type,
                guardian_address
            ) VALUES (
                :baptism_id,
                :guardian_firstname,
                :guardian_middlename,
                :guardian_lastname,
                :guardian_suffix,
                :guardian_type,
                :guardian_address
            )
        ");

        $stmtGuardian->execute([
            ':baptism_id' => $baptism_id,
            ':guardian_firstname' => $guardian_firstname,
            ':guardian_middlename' => $guardian_middlename,
            ':guardian_lastname' => $guardian_lastname,
            ':guardian_suffix' => $guardian_suffix,
            ':guardian_type' => $guardian_type,
            ':guardian_address' => $guardian_address
        ]);
    }

    // Insert main ninong & ninang
    $stmt2 = $dbh->prepare("INSERT INTO ninong_tbl (baptism_id, ninong_firstname, ninong_middlename, ninong_lastname, ninong_suffix) VALUES (?, ?, ?, ?, ?)");
    $stmt2->execute([$baptism_id, $ninong_firstname, $ninong_middlename, $ninong_lastname, $ninong_suffix]);

    $stmt3 = $dbh->prepare("INSERT INTO ninang_tbl (baptism_id, ninang_firstname, ninang_middlename, ninang_lastname) VALUES (?, ?, ?, ?)");
    $stmt3->execute([$baptism_id, $ninang_firstname, $ninang_middlename, $ninang_lastname]);

    // Additional ninong
    $stmt4 = $dbh->prepare("INSERT INTO as_ninong_tbl (baptism_id, as_ninong_firstname, as_ninong_middlename, as_ninong_lastname, as_ninong_suffix) VALUES (?, ?, ?, ?, ?)");
    for ($i = 0; $i < count($as_ninong_firstname); $i++) {
        if (!empty($as_ninong_firstname[$i])) {
            $stmt4->execute([
                $baptism_id,
                $as_ninong_firstname[$i],
                $as_ninong_middlename[$i] ?? null,
                $as_ninong_lastname[$i],
                $as_ninong_suffix[$i] ?? null
            ]);
        }
    }

    // Additional ninang
    $stmt5 = $dbh->prepare("INSERT INTO as_ninang_tbl (baptism_id, as_ninang_firstname, as_ninang_middlename, as_ninang_lastname) VALUES (?, ?, ?, ?)");
    for ($i = 0; $i < count($as_ninang_firstname); $i++) {
        if (!empty($as_ninang_firstname[$i])) {
            $stmt5->execute([
                $baptism_id,
                $as_ninang_firstname[$i],
                $as_ninang_middlename[$i] ?? null,
                $as_ninang_lastname[$i]
            ]);
        }
    }

    /* ===============================
       ADMIN NOTIFICATION
    ================================= */
    // Fetch user name

    $userStmt = $dbh->prepare("SELECT givenname, lastname FROM users WHERE user_id = :user_id");
    $userStmt->execute([':user_id' => $reservation_user_id]);

    $user = $userStmt->fetch(PDO::FETCH_ASSOC);
    $submittedBy = $user ? $user['givenname'] . ' ' . $user['lastname'] : 'A user';

    // Format date & time
    $formattedDate = date('F d, Y', strtotime($date));
    $formattedTime = $time ? date('h:i A', strtotime($time)) : '';

    // Insert notification
    $notifTitle = 'Baptism Reservation';
    $notifMessage = $submittedBy
      . ' submitted a baptism reservation for '
      . $child_firstname . ' ' . $child_lastname
      . ' on ' . $formattedDate
      . ($formattedTime ? ' at ' . $formattedTime : '') . '.';

    $stmtNotif = $dbh->prepare("
        INSERT INTO admin_notifications (sacrament, reference_id, title, message)
        VALUES ('Baptism', :reference_id, :title, :message)
    ");
    $stmtNotif->execute([
        ':reference_id' => $baptism_id,
        ':title' => $notifTitle,
        ':message' => $notifMessage
    ]);


    /* ===============================
        USER NOTIFICATION (SUCCESS)
    ================================ */

    // Child full name
    $childFullname = trim(
        $child_firstname . ' ' .
        ($child_middlename ? $child_middlename . ' ' : '') .
        $child_lastname . ' ' .
        $child_suffix
    );

    // Notification content
    $userNotifTitle = 'Baptism Reservation Submitted';
    $userNotifMessage = "Good day, {$user['givenname']}! We’ve received your request for baptism for {$childFullname}, and it’s currently being reviewed. We’ll get back to you soon. Thank you and God bless!";

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
            'Your baptism request has been submitted successfully. Please wait for the approval notification.';

        echo "<script>
            window.location.href='../baptism.php';
        </script>";
        exit;

    } catch (PDOException $e) {
        $dbh->rollBack();
        exit("Database Error: " . $e->getMessage());
    } catch (Exception $e) {
        $dbh->rollBack();
        exit("Error: " . $e->getMessage());
    }
