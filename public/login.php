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
            $_SESSION['gender'] = $user['gender'];

            // Redirect based on user type
            if ($user['user_type'] === 'Admin') {
                header("Location: ../private/dashboard.php");
            } elseif ($user['user_type'] === 'User') {
                echo "<script>
                        alert('Login Successful!');
                        window.location.href='homepage.php';
                      </script>";
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
  <link rel="stylesheet" href="../stylesheets/public/login.css" />
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
          <button type="submit" class="log-in-btn">LOG IN</button>
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