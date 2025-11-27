<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "test_tree_db";
    
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'test_tree_db');

$conn = new mysqli($host, $user, $pass, $dbname);
    
// Check connection
if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

try {
  $conn = new PDO("mysql:host=localhost;dbname=test_tree_db;charset=utf8", "root", "");
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} 
catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Collect and sanitize input
    $givenname   = trim($_POST['givenname']);
    $middlename  = trim($_POST['middlename']);
    $lastname    = trim($_POST['lastname']);
    $suffix    = trim($_POST['suffix']);
    $gender      = $_POST['gender'];
    $birthday    = $_POST['birthday'];
    $username    = trim($_POST['username']);
    $email       = trim($_POST['email']);
    $contact     = trim($_POST['contact']);
    $password    = $_POST['password'];
    $confirmPass = $_POST['confirm_password'];

    // Validate passwords match
    if ($password !== $confirmPass) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit;
    }

    // Check if username or email exists
    $checkQuery = "SELECT user_id FROM users WHERE username = :username OR email = :email";
    $stmt = $conn->prepare($checkQuery);

    if (!$stmt) {
        echo "<pre>";
        print_r($conn->errorInfo());
        echo "</pre>";
        exit;
    }

    $stmt->execute([
        ':username' => $username,
        ':email' => $email
    ]);


    if ($stmt->rowCount() > 0) {
        echo "<script>alert('Username or Email already exists!'); window.history.back();</script>";
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user
    $insertQuery = "INSERT INTO users 
    (user_type, username, password, givenname, middlename, lastname, suffix, gender, email, contact, birthday)
    VALUES 
    (:user_type, :username, :password, :givenname, :middlename, :lastname, :suffix, :gender, :email, :contact, :birthday)";

    $stmt = $conn->prepare($insertQuery);
    $success = $stmt->execute([
        ':user_type' => 'User',
        ':username' => $username,
        ':password' => $hashedPassword,
        ':givenname' => $givenname,
        ':middlename' => $middlename,
        ':lastname' => $lastname,
        ':suffix' => $suffix,
        ':gender' => $gender,
        ':email' => $email,
        ':contact' => $contact,
        ':birthday' => $birthday
    ]);

    if ($success) {
        echo "<script>alert('Registration successful! Redirecting to login...'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Error: Unable to register. Please try again later.'); window.history.back();</script>";
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Santa Krus Parish</title>
  <link rel="stylesheet" href="../stylesheets/public/register.css">
</head>
<body>

  <!-- NAVBAR -->
  <header>
    <nav class="navbar">
      <img src="../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" />
      <ul class="nav-links">
        <li><a href="homepage.php">Home</a></li>
        <li><a href="history.php">History</a></li>
        <li><a href="events.php">Events</a></li> 
        <li><a href="donation.php">Donate</a></li>
        <li><a href="baptism.php">Sacraments</a></li>
        <li><a href="prayer-page.php">Prayer Request</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a href="login.php" class="login-btn">Login</a></li>
      </ul>
    </nav>
  </header>
  <!-- NAVBAR END-->

  <!-- Register Page Content -->
  <div class="container">
    <!-- Left Section -->
    <div class="left-section">

      <img src="../stylesheets/assets/stklogo.png" alt="Santa Krus Parish Logo">


      <h1 class="site-name">Santa<br>Krus<br>Parish</h1>
    </div>

    <!-- Right Section -->
    <div class="right-section">
      <h2>Create an account</h2>
      
        <form class="register-form" method="POST" action="register.php">
          <div class="name-fields">
            <input type="text" name="givenname" placeholder="Given Name" required>
            <input type="text" name="middlename" placeholder="Middle Name">
            <input type="text" name="lastname" placeholder="Last Name" required>
            <input type="text" name="suffix" placeholder="Suffix">
          </div>

          
          <div class="pii-fields">
          <input type="date" name="birthday" placeholder="Birthday" required>
          <select name="gender" required>
            <option value="" disabled selected>Sex</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
          </div>

          <input type="text" name="username" placeholder="Username" required>
          <input type="email" name="email" placeholder="Email Address" required>
          <input type="text" name="contact" placeholder="Contact Number" required>
          <input type="password" name="password" placeholder="Password" required>
          <input type="password" name="confirm_password" placeholder="Confirm Password" required>
          <button type="submit" class="signup-btn">SIGN UP</button>
        </form>

      <p class="login-link">Already have an account? <a href="login.php">Log In</a></p>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <p>© 2025 Santa Krus Parish, Bagumbong Caloocan. All Rights Reserved.</p>
  </footer>
</body>
</html>