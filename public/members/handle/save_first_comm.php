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
    $baptCertPath = uploadFile('baptismal_certificate', $uploadDir, $relativePath, true);
    $birthCertPath = uploadFile('birth_certificate', $uploadDir, $relativePath, true);
} catch (Exception $e) {
    exit($e->getMessage());
}


/* ===============================
   FORM DATA
================================ */
$reservation_user_id = $_SESSION['user_id'];

$first_comm_type = $_POST['first_comm_type'];
$date = $_POST['date'];
$time = $_POST['time'];
$contact = $_POST['contact'];
$email = $_POST['email'];

$child_firstname = $_POST['child_firstname'];
$child_middlename = $_POST['child_middlename'];
$child_lastname = $_POST['child_lastname'];
$child_suffix = $_POST['child_suffix'] ?? '';
$child_gender = $_POST['child_gender'];
$child_nat = $_POST['child_nat'];
$birth_date = $_POST['birth_date'];
$birth_place = $_POST['birth_place'];

$father_firstname = $_POST['father_firstname'];
$father_middlename = $_POST['father_middlename'];
$father_lastname = $_POST['father_lastname'];
$father_suffix = $_POST['father_suffix'] ?? '';

$mother_firstname = $_POST['mother_firstname'];
$mother_middlename = $_POST['mother_middlename'];
$mother_lastname = $_POST['mother_lastname'];

$baptismal_certificate = $_POST['baptismal_certificate'];
$birth_certificate = $_POST['birth_certificate'];

/* ===============================
   DATABASE TRANSACTION
================================ */
try {
    $dbh->beginTransaction();

    $sql1 = "INSERT INTO first_communion (
        reservation_user_id, first_comm_type, date, time, contact, email,
        child_firstname, child_middlename, child_lastname, child_suffix, 
        child_gender, child_nat, birth_date, birth_place,
        father_firstname, father_middlename, father_lastname, father_suffix,
        mother_firstname, mother_middlename, mother_lastname,
        birth_certificate, baptismal_certificate
    ) VALUES (
        :reservation_user_id, :first_comm_type, :date, :time, :contact, :email,
        :child_firstname, :child_middlename, :child_lastname, :child_suffix, 
        :child_gender, :child_nat, :birth_date, :birth_place,
        :father_firstname, :father_middlename, :father_lastname, :father_suffix,
        :mother_firstname, :mother_middlename, :mother_lastname,
        :birth_certificate, :baptismal_certificate
    )";

    $stmt1 = $dbh->prepare($sql1);
    $stmt1->execute([
        ':reservation_user_id' => $reservation_user_id,

        ':first_comm_type' => $first_comm_type,
        ':date' => $date,
        ':time' => $time,
        ':contact' => $contact,
        ':email' => $email,

        ':child_firstname' => $child_firstname,
        ':child_middlename' => $child_middlename,
        ':child_lastname' => $child_lastname,
        ':child_suffix' => $child_suffix,
        ':child_gender' => $child_gender,
        ':child_nat' => $child_nat,
        ':birth_date' => $birth_date,
        ':birth_place' => $birth_place,
        
        ':father_firstname' => $father_firstname,
        ':father_middlename' => $father_middlename,
        ':father_lastname' => $father_lastname,
        ':father_suffix' => $father_suffix,

        ':mother_firstname' => $mother_firstname,
        ':mother_middlename' => $mother_middlename,
        ':mother_lastname' => $mother_lastname,

        ':birth_certificate' => $birthCertPath,
        ':baptismal_certificate' => $baptCertPath
    ]);

    $dbh->commit();

    echo "<script>
        alert('Reservation submitted successfully!');
        window.location.href='../first_comm.php';
    </script>";
    exit;

} catch (PDOException $e) {
    $dbh->rollBack();
    exit("Database Error: " . $e->getMessage());
}
