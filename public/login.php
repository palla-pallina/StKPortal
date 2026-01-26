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

    session_regenerate_id(true); // ✅ recommended for security

    $_SESSION['user_id']   = $user['user_id'];
    $_SESSION['user_type'] = $user['user_type'];
    $_SESSION['givenname'] = $user['givenname'];
    $_SESSION['lastname'] = $user['lastname']; 
    $_SESSION['gender']    = $user['gender'];

    if ($user['user_type'] === 'Admin') {
        header("Location: ../private/dashboard.php");
    } else if ($user['user_type'] === 'Priest') {
        header("Location: ../private/priest/dashboard.php");
    } else {
        header("Location: homepage.php");
    }
    exit();
}
 else {
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../stylesheets/public/login.css" />
    <link rel="stylesheet" href="../stylesheets/public/user_footer_navbar.css" />
</head>
<body>

<!-------------------- NAVBAR -------------------->
<header>
    <nav class="navbar">
        <ul class="nav-links">
            <li><a href="#"><img src="../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" /></a></li>
            <li><a href="homepage.php">Home</a></li>
            <li><a href="history.php">History</a></li>
            <li><a href="events.php">News & Events</a></li>
                        <!--<li><a href="sacraments.php">Sacraments</a></li>-->

            <li class="li-dropdown">
                <a href="#">Parish <i class="fa-solid fa-angle-down"></i></a>
                <ul class="dropdown">
                    <li style="border-bottom: 1px solid var(--yellow);"><a href="sacraments.php">Sacraments</a></li>
                    <li><a href="services.php">Services</a></li>
                </ul>
            </li>
            
            <li><a href="donation.php">Donate</a></li>
            <li><a href="prayer_page.php">Prayer Request</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </nav>
</header>
<!----------------- NAVBAR END----------------->
  <main>
    <section class="login-section">
     <div class="logo-container">
<?php if (isset($_GET['registered']) && $_GET['registered'] == 1): ?>
<div class="form-alert success" id="formAlert">
    <span id="formAlertMsg">Registration successful! You can now log in.</span>
    <button type="button" class="close-alert" id="closeFormAlert">&times;</button>
</div>
<?php endif; ?>
  <img src="../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="login-logo">
  <h1 class="site-name">Santa Krus Parish</h1>
</div>
        <!-- Login Form -->
        <form class="login-form" method="POST" action="login.php">
          <div class="form-group">
            <input type="email" name="email" placeholder="Email Address" required>
          </div>
          <div class="form-group password-field">
              <input type="password" name="password" placeholder="Password" id="password" required>
              <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i>
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

<script>
// Close registration success alert
const closeFormAlert = document.getElementById("closeFormAlert");
if (closeFormAlert) {
    closeFormAlert.addEventListener("click", () => {
        document.getElementById("formAlert").style.display = "none";
    });
}

// Show/Hide Password
const togglePassword = document.getElementById("togglePassword");
const passwordInput = document.getElementById("password");

togglePassword.addEventListener("click", () => {
    const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
    passwordInput.setAttribute("type", type);
    togglePassword.classList.toggle("fa-eye-slash");
});
</script>


<script>
const closeFormAlert = document.getElementById("closeFormAlert");
if (closeFormAlert) {
    closeFormAlert.addEventListener("click", () => {
        document.getElementById("formAlert").style.display = "none";
    });
}
</script>

</body>
</html>