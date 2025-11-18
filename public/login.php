<?php
session_start();
require_once "connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form inputs
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check if email exists
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify hashed password
        if (password_verify($password, $user['password'])) {
            // Start session and store user info
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['full_name'] = $user['full_name'];

            // Redirect based on user type
            if ($user['user_type'] === 'Admin') {
                header("Location: ../private/dashboard.php");
            } else {
                header("Location: homepage.html");
            }
            exit();
        } else {
            echo "<script>alert('Incorrect password. Please try again.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('No account found with that email.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Santa Krus Parish</title>
  <link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../stylesheets/login.css" />
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
            <li><a href="baptism.php">Baptism</a></li>
            <li><a href="wedding.php">Wedding</a></li>
            <li><a href="funeral.php">Funeral</a></li>
            <li><a href="certicates.html">Certificates</a></li>
          </ul>
        </li>
        <li><a href="#">Pray</a></li>
        <li><a href="contact.html">Contact</a></li>
        <li class="hamburg-dropdown">
          <a href="#" class="active" style="font-size: 20px;">☰</a>
        </li>
      </ul>
    </nav>
  </header>
  <!-- NAVBAR END-->

  <main>
    <section class="login-section">

     <div class="logo-container">
  <img src="../stylesheets/assets/stklogo.png" alt="Parish Logo" class="login-logo">
  <h1 class="site-name">Santa Krus Parish</h1>
</div>

        <!-- Login Form -->
        <form class="login-form" method="POST" action="login.php">
          <div class="form-group">
            <input type="email" name="email" placeholder="Email Address" required>
          </div>
          <div class="form-group">
            <input type="password" name="password" placeholder="Password" required>
          </div>
          <button type="submit" class="login-btn">LOG IN</button>
        </form>

        <!-- Links -->
        <div class="form-links">
          <a href="#" class="forgot-password">Forgot Password?</a>
        </div>
        <div class="signup-text">
          Don’t have an account? <a href="register.php">Sign Up</a>
        </div>
      </div>
    </section>
  </main>

  <footer class="site-footer-simple">
    <p>&copy; 2025 Santa Krus Parish, Bagumbong Caloocan. All Rights Reserved.</p>
  </footer>
</body>
</html>
