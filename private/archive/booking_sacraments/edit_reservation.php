<?php
$conn = new mysqli('localhost', 'root', '', 'test_tree_db');
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Restrict access
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}

// Handle POST first (Edit / Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // EDIT
    if (isset($_POST['edit']) && $_POST['edit'] == 1) {
        $type = $_POST['type'];
        $id = intval($_POST['id']);
        $tables = ['wedding'=>'weddings', 'funeral'=>'funerals', 'baptism'=>'baptisms'];
        if (!array_key_exists($type, $tables)) die("Invalid type.");
        $table = $tables[$type];

        if ($type === 'wedding') {


        } elseif ($type === 'funeral') {


        } elseif ($type === 'baptism') {
            $stmt = $conn->prepare("
                UPDATE baptisms SET
                    baptism_type=?, status=?, created_at=?,
                    child_firstname=?, child_middlename=?, child_lastname=?, child_suffix=?,
                    mother_firstname=?, mother_middlename=?, mother_lastname=?,
                    father_firstname=?, father_middlename=?, father_lastname=?, father_suffix=?,
                    contact=?, date=?, time=?,
                    ninong_firstname=?, ninong_middlename=?, ninong_lastname=?, ninong_suffix=?,
                    ninang_firstname=?, ninang_middlename=?, ninang_lastname=?,
                WHERE id=?
                UPDATE as_ninong_tbl SET
                    as_ninong_firstname=?, as_ninong_middlename=?, as_ninong_lastname=?, as_ninong_suffix=?,
                WHERE id=?
                UPDATE as_ninang_tbl SET
                    as_ninang_firstname=?, as_ninang_middlename=?, as_ninang_lastname=?
                WHERE id=?
            ");
            $stmt->bind_param("sssssssssssssssssssssssssssssssi",
                $_POST['BaptType'], $_POST['Status'], $_POST['SubmitDate'],
                $_POST['ChildFN'], $_POST['ChildMN'], $_POST['ChildLN'], $_POST['ChildSuffix'],
                $_POST['MotherFN'], $_POST['MotherMN'], $_POST['MotherLN'], 
                $_POST['FatherFN'], $_POST['FatherMN'], $_POST['FatherLN'], $_POST['FatherSuffix'],
                $_POST['Contact'], $_POST['BaptDate'], $_POST['BaptTime'],
                $_POST['NinongFN'], $_POST['NinongMN'], $_POST['NinongLN'], $_POST['NinongSuffix'],
                $_POST['NinangFN'], $_POST['NinangMN'], $_POST['NinangLN'],
                $id
            );
            $stmt->execute();
            $stmt->close();
        }

        http_response_code(200);
        exit;
    }
}

// If GET, fetch record for form
$type = $_GET['type'] ?? '';
$id = intval($_GET['id'] ?? 0);
if (!$type || !$id) die("Invalid request.");

$tables = ['wedding'=>'weddings', 'funeral'=>'funerals', 'baptism'=>'baptisms'];
if (!array_key_exists($type, $tables)) die("Invalid type.");
$table = $tables[$type];

$stmt = $conn->prepare("SELECT * FROM $table WHERE id=? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$data) die("Record not found");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit <?= ucfirst($type) ?> Reservation</title>
</head>
<body>

<h1>Edit <?= ucfirst($type) ?> Reservation</h1>

<form method="POST" action="booking_sacraments.php">
    <!-- Hidden fields to make Save Changes work -->
    <input type="hidden" name="edit" value="1">
    <input type="hidden" name="type" value="<?= $type ?>">
    <input type="hidden" name="id" value="<?= $id ?>">

<?php if ($type === "wedding"): ?>
    <!-- Wedding form fields -->


<?php elseif ($type === "funeral"): ?>
    <!-- Funeral form fields -->


<?php else: ?>
    <!-- Baptism form fields -->
    <h2>Reservation Information</h2>
    <input type="text" name="SubmitDate" value="<?= $data['created_at'] ?>">
    <input type="text" name="BaptType" value="<?= $data['baptism_type'] ?>">
    <input type="text" name="BaptDate" value="<?= $data['date'] ?>">
    <input type="text" name="BaptTime" value="<?= $data['time'] ?>">
    <input type="text" name="Status" value="<?= $data['status'] ?>">

    <h2>Child Information</h2>
    <input type="text" name="ChildFN" value="<?= $data['child_firstname'] ?>">
    <input type="text" name="ChildMN" value="<?= $data['child_middlename'] ?>">
    <input type="text" name="ChildLN" value="<?= $data['child_lastname'] ?>">
    <input type="text" name="ChildSuffix" value="<?= $data['child_suffix'] ?>">

    <h2>Mother</h2>
    <input type="text" name="MotherFN" value="<?= $data['mother_firstname'] ?>">
    <input type="text" name="MotherMN" value="<?= $data['mother_middlename'] ?>">
    <input type="text" name="MotherLN" value="<?= $data['mother_lastname'] ?>">

    <h2>Father</h2>
    <input type="text" name="FatherFN" value="<?= $data['father_firstname'] ?>">
    <input type="text" name="FatherMN" value="<?= $data['father_middlename'] ?>">
    <input type="text" name="FatherLN" value="<?= $data['father_lastname'] ?>">
    <input type="text" name="FatherSuffix" value="<?= $data['father_suffix'] ?>">

    <h2>Primary Sponsors</h2>
    <h3>Pangunahing Ninong</h3>
    <input type="text" name="NinongFN" value="<?= $data['ninong_firstname'] ?>">
    <input type="text" name="NinongMN" value="<?= $data['ninong_middlename'] ?>">
    <input type="text" name="NinongLN" value="<?= $data['ninong_lastname'] ?>">
    <input type="text" name="NinongSuffix" value="<?= $data['ninong_suffix'] ?>">
    <h3>Pangunahing Ninang</h3>
    <input type="text" name="NinangFN" value="<?= $data['ninang_firstname'] ?>">
    <input type="text" name="NinangMN" value="<?= $data['ninang_middlename'] ?>">
    <input type="text" name="NinangLN" value="<?= $data['ninang_lastname'] ?>">

<?php endif; ?>

<br><br>
<button type="submit">Save Changes</button>
</form>

</body>
</html>
