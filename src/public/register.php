<?php
require_once "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $givenname   = trim($_POST['givenname']);
    $middlename  = trim($_POST['middlename']);
    $lastname    = trim($_POST['lastname']);
    $suffix      = trim($_POST['suffix']);
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

    // Check if username or email already exists
    $checkQuery = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Username or Email already exists!'); window.history.back();</script>";
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Combine full name
    $full_name = trim("$givenname $middlename $lastname $suffix");

    // Insert new user
    $insertQuery = "INSERT INTO users (user_type, username, password, givenname, middlename, lastname, suffix, full_name, email, contact, birthday)
                    VALUES ('User', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssssssssss", 
        $username, 
        $hashedPassword, 
        $givenname, 
        $middlename, 
        $lastname, 
        $suffix, 
        $full_name, 
        $email, 
        $contact, 
        $birthday
    );

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! Redirecting to login...'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Error: Unable to register. Please try again later.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Santa Krus Parish</title>
  <link rel="stylesheet" href="../stylesheets/register.css">
</head>
<body>

  <!-- NAVBAR -->
  <header>
    <nav class="navbar">
      <img src="../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" />
      <ul class="nav-links">
        <li><a href="homepage.html">Home</a></li>
        <li><a href="history.html">History</a></li>
        <li><a href="events.html">Events</a></li> <!-- N/A -->
        <li><a href="donation.html">Give</a></li>
        <li class="services-dropdown">
          <a href="#" class="active">Sacraments</a>
          <ul class="dropdown">
            <li><a href="baptism.html">Baptism</a></li>
            <li><a href="wedding_service.html">Wedding</a></li>
            <li><a href="funeral.html">Funeral</a></li>
            <li><a href="certicates.html">Certificates</a></li>
          </ul>
        </li>
        <li><a href="#">Pray</a></li>
        <li><a href="contact.html">Contact</a></li>
        <li class="hamburg-dropdown">
          <a href="#" class="active" style="font-size: 20px;">☰</a>
          <ul class="dropdown">
            <li><a href="login.php">Log In</a></li>
          </ul>
        </li>
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
          <input type="date" name="birthday" placeholder="Birthday" required>
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
