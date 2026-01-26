<?php
require_once 'connection.php';
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "test_tree_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../../public/login.php");
    exit();
}
$admin_name = !empty($_SESSION['givenname']) ? $_SESSION['givenname'] : $_SESSION['username'];

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ut  = $_POST['user_type'] ?? '';
    $fn  = $_POST['givenname'] ?? '';
    $mn  = $_POST['middlename'] ?? '';
    $ln  = $_POST['lastname'] ?? '';
    $sf  = $_POST['suffix'] ?? '';
    $gn  = $_POST['gender'] ?? '';
    $bd  = $_POST['birthday'] ?? '';
    //$un  = $_POST['username'] ?? '';
    $em  = $_POST['email'] ?? '';
    $cn  = $_POST['contact'] ?? '';
    $pas = $_POST['password'] ?? '';
    $cpw = $_POST['confirmpassword'] ?? '';

    if ($pas !== $cpw) {
        $message = "Passwords do not match.";
    } else {
        try {
            $hashed = password_hash($pas, PASSWORD_BCRYPT);
            $sql = "INSERT INTO users 
                    (user_type, givenname, middlename, lastname, suffix, email, contact, birthday, gender, password) 
                    VALUES 
                    (:ut, :fn, :mn, :ln, :sf, :em, :cn, :bd, :gd, :pas)";
            $stmt = $dbh->prepare($sql);

            $stmt->bindParam(':ut',  $ut,  PDO::PARAM_STR);
            $stmt->bindParam(':fn',  $fn,  PDO::PARAM_STR);
            $stmt->bindParam(':mn',  $mn,  PDO::PARAM_STR);
            $stmt->bindParam(':ln',  $ln,  PDO::PARAM_STR);
            $stmt->bindParam(':sf',  $sf,  PDO::PARAM_STR);
            $stmt->bindParam(':em',  $em,  PDO::PARAM_STR);
            $stmt->bindParam(':cn',  $cn,  PDO::PARAM_STR);
            $stmt->bindParam(':bd',  $bd,  PDO::PARAM_STR);
            $stmt->bindParam(':gd',  $gd,  PDO::PARAM_STR);
            $stmt->bindParam(':pas', $hashed, PDO::PARAM_STR);

            $stmt->execute();

            if ($dbh->lastInsertId()) {
                $message = "Record saved successfully!";
            } else {
                $message = "Insert failed.";
            }
        } catch (PDOException $e) {
            $message = "DB Error: " . $e->getMessage();
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />
<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
<link href='../../stylesheets/private/sacrament_insert.css' rel='stylesheet'>
<title>Add Member</title>

</head>

<body>





<div class="main-content">


<div class="record-container">
    <h2>Add Member</h2>

    <?php if (!empty($message)) : ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" action="">

        <!-- ================= PERSONAL DETAILS ================= -->
        <div class="record-section">
            <h3>Personal Details</h3>
            <div class="row record-inline">
                <div class="col-md-2 record-row">
                    <span class="record-label">Given Name</span>
                    <input type="text" name="givenname" class="form-control" required>
                </div>

                <div class="col-md-3 record-row">
                    <span class="record-label">Middle Name</span>
                    <input type="text" name="middlename" class="form-control">
                </div>

                <div class="col-md-2 record-row">
                    <span class="record-label">Last Name</span>
                    <input type="text" name="lastname" class="form-control" required>
                </div>

                <div class="col-md-3 record-row">
                    <span class="record-label">Suffix</span>
                    <input type="text" name="suffix" class="form-control">
                </div>
            </div>


            <div class="row record-inline" style="margin-top:10px;">
                <div class="col-md-4 record-row">
                    <span class="record-label">Birthday</span>
                    <input type="date" name="birthday" class="form-control" required>
                </div>
               <div class="col-md-4 record-row">
                    <span class="record-label">Sex</span>
                    <select name="gender" class="form-control" required>
                        <option value="" disabled>Select Sex</option>
                        <option value="Female">Female</option>
                        <option value="Male">Male</option>
                    </select>
               </div>
            </div>
        </div>




        <!-- ================= ACCOUNT DETAILS ================= -->
        <div class="record-section">
            <h3>Account Details</h3>
            <div class="row record-inline">

                <!--<div class="col-md-4 record-row">
                    <span class="record-label">Username</span>
                    <input type="text" name="username" class="form-control" required>
                </div>-->

                <div class="col-md-4 record-row">
                    <span class="record-label">Email</span>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="col-md-3 record-row">
                    <span class="record-label">Contact Number</span>
                    <input type="text" name="contact" class="form-control" required>
                </div>

               <div class="col-md-4 record-row">
                    <span class="record-label">User Type</span>
                    <select name="user_type" class="form-control" required>
                        <option value="" disabled>Select User Type</option>
                        <option value="User">User</option>
                        <option value="Admin">Admin</option>
                        <option value="Priest">Priest</option>
                    </select>
               </div>

            </div>

            <div class="row record-inline" style="margin-top:10px;">
                <div class="col-md-4 record-row">
                    <span class="record-label">Password</span>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="col-md-4 record-row">
                    <span class="record-label">Confirm Password</span>
                    <input type="password" name="confirmpassword" class="form-control" required>
                </div>
            </div>
        </div>

        <!-- ================= BUTTONS ================= -->
        <div class="record-buttons" style="margin-top:20px;">
            <button type="submit" class="btn btn-primary">Add Member</button>
            <a href="members.php" class="btn btn-danger">Back</a>
        </div>

    </form>
</div>




</div>

</body>
</html>