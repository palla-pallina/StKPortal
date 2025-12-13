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
    $id_picture = uploadFile('id_picture', $uploadDir, $relativePath, true);
    $receipt_picture = uploadFile('receipt_picture', $uploadDir, $relativePath, false);
    $authorization_letter = uploadFile('authorization_letter', $uploadDir, $relativePath, false);
    $authorized_id_picture = uploadFile('authorized_id_picture', $uploadDir, $relativePath, false);
} catch (Exception $e) {
    exit($e->getMessage());
}

/* ===============================
   FORM DATA
================================ */
$user_id = $_SESSION['user_id'];

$certificate_type = $_POST['certificate_type'];
$pickup_date = $_POST['pickup_date'] ?? null;

$request_firstname = $_POST['request_firstname'];
$request_middlename = $_POST['request_middlename'] ?? null;
$request_lastname = $_POST['request_lastname'];
$request_suffix = $_POST['request_suffix'] ?? null;

$owner_firstname = $_POST['owner_firstname'] ?? null;
$owner_middlename = $_POST['owner_middlename'] ?? null;
$owner_lastname = $_POST['owner_lastname'] ?? null;
$owner_suffix = $_POST['owner_suffix'] ?? null;

$contact = $_POST['contact'];
$email = $_POST['email'];

/* ===============================
   DATABASE TRANSACTION
================================ */
try {
    $dbh->beginTransaction();

    $sql = "INSERT INTO certificate_request (
        user_id, certificate_type, certificate_status, pickup_date,
        request_firstname, request_middlename, request_lastname, request_suffix,
        owner_firstname, owner_middlename, owner_lastname, owner_suffix,
        id_picture, receipt_picture, authorization_letter, authorized_id_picture,
        contact, email
    ) VALUES (
        :user_id, :certificate_type, 'Pending', :pickup_date,
        :request_firstname, :request_middlename, :request_lastname, :request_suffix,
        :owner_firstname, :owner_middlename, :owner_lastname, :owner_suffix,
        :id_picture, :receipt_picture, :authorization_letter, :authorized_id_picture,
        :contact, :email
    )";

    $stmt = $dbh->prepare($sql);
    $stmt->execute([
        ':user_id' => $user_id,
        ':certificate_type' => $certificate_type,
        ':pickup_date' => $pickup_date,

        ':request_firstname' => $request_firstname,
        ':request_middlename' => $request_middlename,
        ':request_lastname' => $request_lastname,
        ':request_suffix' => $request_suffix,

        ':owner_firstname' => $owner_firstname,
        ':owner_middlename' => $owner_middlename,
        ':owner_lastname' => $owner_lastname,
        ':owner_suffix' => $owner_suffix,

        ':id_picture' => $id_picture,
        ':receipt_picture' => $receipt_picture,
        ':authorization_letter' => $authorization_letter,
        ':authorized_id_picture' => $authorized_id_picture,

        ':contact' => $contact,
        ':email' => $email
    ]);

    $dbh->commit();

    echo "<script>
        alert('Certificate request submitted successfully!');
        window.location.href='../certificate_request.php';
    </script>";
    exit;

} catch (PDOException $e) {
    $dbh->rollBack();
    exit('Database Error: ' . $e->getMessage());
}
