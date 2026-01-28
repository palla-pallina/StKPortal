<?php
session_start();
require_once "connection.php";

$alertMessage = '';
$alertType = 'error'; // error | success

function verifyRecaptcha(string $recaptchaResponse, string $secretKey): bool {
    if (empty($recaptchaResponse)) return false;

    $url = "https://www.google.com/recaptcha/api/siteverify";
    $postData = http_build_query([
        "secret" => $secretKey,
        "response" => $recaptchaResponse,
    ]);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_TIMEOUT => 10,
    ]);

    $result = curl_exec($ch);
    curl_close($ch);

    if (!$result) return false;

    $data = json_decode($result, true);
    return isset($data["success"]) && $data["success"] === true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ✅ reCAPTCHA first
    $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
    $recaptchaSecret = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe"; // test secret
    if (!verifyRecaptcha($recaptchaResponse, $recaptchaSecret)) {
        $alertMessage = "Please complete the CAPTCHA.";
        $alertType = "error";
    }

    // Get form inputs
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Check if email exists
    $query = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // block unverified users
        if (isset($user['email_verified']) && (int)$user['email_verified'] !== 1) {
            $alertMessage = "Your account is not yet verified. Please check your email for the verification link.";
            $alertType = "error";
        }

        if (empty($alertMessage) && password_verify($password, $user['password'])) {
            session_regenerate_id(true);

            $_SESSION['user_id']   = $user['user_id'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['givenname'] = $user['givenname'];
            $_SESSION['lastname']  = $user['lastname'];
            $_SESSION['gender']    = $user['gender'];

            if ($user['user_type'] === 'Admin') {
                header("Location: ../private/dashboard.php");
            } elseif ($user['user_type'] === 'Priest') {
                header("Location: ../private/priest/dashboard.php");
            } else {
                header("Location: homepage.php");
            }
            exit();
        } elseif (empty($alertMessage)) {
            $alertMessage = "Incorrect password. Please try again.";
        }
    } else {
        $alertMessage = "No account found with that email.";
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
    <span id="formAlertMsg">Registration successful, please verify your account.</span>
    <button type="button" class="close-alert" id="closeFormAlert">&times;</button>
</div>
<?php endif; ?>

<?php if (!empty($alertMessage)): ?>
<div class="form-alert <?= $alertType ?>" id="formAlert">
    <span><?= htmlspecialchars($alertMessage) ?></span>
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
          
          <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI" style="margin-left: 22px;"></div>

          <button type="submit" class="log-in-btn">LOG IN</button>
        </form>

        <!-- Links -->
        <div class="form-links">
          <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
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

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

</body>
</html>