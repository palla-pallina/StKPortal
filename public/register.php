<?php
session_start();
require_once "connection.php";

// PHPMailer (Composer)
require 'vendor/autoload.php';

require 'vendor/PHPMailer/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/PHPMailer/src/SMTP.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$alertMessage = "";

function verifyRecaptcha(string $recaptchaResponse, string $secretKey): bool {
    if (empty($recaptchaResponse)) return false;

    $url = "https://www.google.com/recaptcha/api/siteverify";
    $postData = http_build_query([
        "secret" => $secretKey,
        "response" => $recaptchaResponse,
        // Optional: "remoteip" => $_SERVER['REMOTE_ADDR'] ?? null
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

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Collect inputs
    $givenname   = trim($_POST['givenname'] ?? '');
    $middlename  = trim($_POST['middlename'] ?? '');
    $lastname    = trim($_POST['lastname'] ?? '');
    $suffix      = trim($_POST['suffix'] ?? '');
    $gender      = $_POST['gender'] ?? '';
    $month       = $_POST['month'] ?? '';
    $day         = $_POST['day'] ?? '';
    $year        = $_POST['year'] ?? '';
    $email       = trim($_POST['email'] ?? '');
    $contact     = trim($_POST['contact'] ?? '');
    $password    = $_POST['password'] ?? '';
    $confirmPass = $_POST['confirm_password'] ?? '';

    // Build birthday safely
    $birthday = '';
    if ($year && $month && $day) {
        $birthday = date('Y-m-d', strtotime("$year-$month-$day"));
    }

    // --- Contact format validation ---
    if (!preg_match('/^(09\d{9}|\+639\d{9})$/', $contact)) {
        $alertMessage = "Invalid contact number. Use 09XXXXXXXXX or +639XXXXXXXXX format.";
    }

    // --- Age validation ---
    if (empty($alertMessage)) {
        $birthDate = new DateTime($birthday);
        $today = new DateTime();
        $age = $today->diff($birthDate)->y;
        if ($age < 18) {
            $alertMessage = "You must be at least 18 years old to register.";
        }
    }

    // --- Password match ---
    if (empty($alertMessage) && $password !== $confirmPass) {
        $alertMessage = "Passwords do not match.";
    }

    // --- reCAPTCHA server-side verification ---
    if (empty($alertMessage)) {
        $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
        $recaptchaSecret = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe"; 
        
        if (!verifyRecaptcha($recaptchaResponse, $recaptchaSecret)) {
            $alertMessage = "Please complete the CAPTCHA correctly.";
        }
    }

    // --- Email exists validation ---
    if (empty($alertMessage)) {
        $checkQuery = "SELECT user_id FROM users WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($checkQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $alertMessage = "Email already exists.";
        }
        $stmt->close();
    }

    // --- Insert user + generate token + email ---
    if (empty($alertMessage)) {

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Create token + hash (store only hash)
        $token = bin2hex(random_bytes(32));
        $tokenHash = password_hash($token, PASSWORD_DEFAULT);
        $expires = date("Y-m-d H:i:s", time() + 3600); // 1 hour

        // Insert user as NOT verified, store token hash + expiry
        $insertQuery = "INSERT INTO users
            (user_type, password, givenname, middlename, lastname, suffix, gender, email, contact, birthday, email_verified, verify_token_hash, verify_token_expires)
            VALUES
            ('User', ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?)";

        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param(
            "sssssssssss",
            $hashedPassword,
            $givenname,
            $middlename,
            $lastname,
            $suffix,
            $gender,
            $email,
            $contact,
            $birthday,
            $tokenHash,
            $expires
        );

        if (!$stmt->execute()) {
            $alertMessage = "Error: Unable to register. Please try again later.";
            $stmt->close();
        } else {
            $stmt->close();

            // Build verify link (use your real domain in production)
            $verifyLink = "http://localhost/stkportal_src/src/public/handle/verify.php"
                        . "?email=" . urlencode($email)
                        . "&token=" . urlencode($token);

            // Send email
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'stakrusp@gmail.com'; 
                $mail->Password   = 'pegx lmst lzje vuie';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('stakrusp@gmail.com', 'Santa Krus Parish');
                $mail->addAddress($email, $givenname);
                $mail->isHTML(true);
                $mail->Subject = "Verify your email address";
                $mail->Body = "
                    <p>Hi {$givenname}!</p>
                    <p>Please verify your email by clicking the link below (valid for 1 hour):</p>
                    <p><a href='{$verifyLink}'>Verify Email</a></p>
                ";
                $mail->AltBody = "Verify your email: {$verifyLink}";

                $mail->send();

                header("Location: login.php?registered=1&verify=sent");
                exit;

            } catch (Exception $e) {
                // Account created but email failed
                header("Location: login.php?registered=1&verify=failed");
                exit;
            }
        }
    }
}
?>


    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Santa Krus Parish</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="../stylesheets/public/register.css" />
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
    <div class="container">

      <!-- Left Branding -->
      <aside class="left-section">  
        <img src="../stylesheets/assets/santa krus logo.png" alt="Santa Krus Parish Logo">
        <h1 class="site-name">Santa<br>Krus<br>Parish</h1>
      </aside>

      <!-- Right Form -->
      <main class="right-section">
        <h2>Create an Account</h2>
        <p class="subtitle">Join the Santa Krus Parish community</p>
        <hr class="divider">

        <form class="register-form" method="POST" action="register.php">

          <!-- PERSONAL INFORMATION -->
          <section class="form-section">
            <h3>Personal Information</h3>

          <!-- Alert Message -->
          <?php if (!empty($alertMessage)): ?>
          <div class="form-alert" id="formAlert">
            <span id="formAlertMsg"><?= htmlspecialchars($alertMessage) ?></span>
            <button type="button" class="close-alert" id="closeFormAlert">&times;</button>
          </div>
          <?php endif; ?>

            <div class="grid-4">
              <div class="form-row">
                <label>Given Name <span class="required">*</span></label>
                <input type="text" name="givenname" required placeholder="Given Name">
              </div>

              <div class="form-row">
                <label>Middle Name</label>
                <input type="text" name="middlename" style="margin-top: 7px;" placeholder="Middle Name">
              </div>

              <div class="form-row">
                <label>Last Name <span class="required">*</span></label>
                <input type="text" name="lastname" required placeholder="Last Name">
              </div>

              <div class="form-row">
                <label>Suffix</label>
                <input type="text" name="suffix" style="margin-top: 7px;" placeholder="Suffix">
              </div>
            </div>

            <div class="grid-4" style="margin-top: 10px;">
              <div class="form-row">
                <label>Birthday <span class="required">*</span></label>
                <select name="month" required>
                  <option value="" disabled selected>Month</option>
                  <option value="1">January</option> <option value="2">February</option> <option value="3">March</option> <option value="4">April</option> <option value="5">May</option> <option value="6">June</option> <option value="7">July</option> <option value="8">August</option> <option value="9">September</option> <option value="10">October</option> <option value="11">November</option> <option value="12">December</option> 
                </select>
              </div>

              <div class="form-row" style="margin-top: 1.5rem;">
                <select name="day" required style="margin-top: 6px;">
                  <option value="" disabled selected>Day</option>
                  <option value="1">1</option> <option value="2">2</option> <option value="3">3</option> <option value="4">4</option> <option value="5">5</option> <option value="6">6</option> <option value="7">7</option> <option value="8">8</option> <option value="9">9</option> <option value="10">10</option> <option value="11">11</option> <option value="12">12</option> <option value="13">13</option> <option value="14">14</option> <option value="15">15</option> <option value="16">16</option> <option value="17">17</option> <option value="18">18</option> <option value="19">19</option><option value="20">20</option> <option value="21">21</option> <option value="22">22</option> <option value="23">23</option> <option value="24">24</option> <option value="25">25</option> <option value="26">26</option> <option value="27">27</option> <option value="28">28</option> <option value="29">29</option> <option value="30">30</option> <option value="31">31</option>
                </select>
              </div>

              <div class="form-row" style="margin-top: 1.5rem;">
                <select name="year" required style="margin-top: 6px;">
                  <option value="" disabled selected>Year</option>
                  <?php
                    $currentYear = date("Y");
                    for ($year = $currentYear; $year >= 1935; $year--) {
                      echo "<option value=\"$year\">$year</option>";
                    }
                  ?>
                </select>
              </div>


              <div class="form-row">
                <label>Sex <span class="required">*</span></label>
                <select name="gender" required>
                  <option value="" disabled selected>Select</option>
                  <option>Male</option>
                  <option>Female</option>
                </select>
              </div>
            </div>
          </section>

          <!-- ACCOUNT DETAILS -->
          <section class="form-section">
            <h3>Account Details</h3>

            <div class="grid-2">
              <div class="form-row">
                <label>Email Address <span class="required">*</span></label>
                <input type="email" name="email" required placeholder="Email Address">
              </div>

              <div class="form-row">
                <label>Contact Number <span class="required">*</span></label>
                <input type="tel" name="contact" required  placeholder="09xxxxxxxxx">
              </div>
            </div>
          </section>

          <!-- SECURITY -->
          <section class="form-section">
            <div class="grid-2">
              <div class="form-row password-field">
                <label>Password <span class="required">*</span></label>
                <div class="password-wrapper">
                  <input type="password" name="password" id="password" required placeholder="Password">
                  <i class="fa-solid fa-eye toggle-password" data-target="password"></i>
                </div>
                <small class="error-text"></small>
              </div>

              <div class="form-row password-field">
                <label>Confirm Password <span class="required">*</span></label>
                <div class="password-wrapper">
                  <input type="password" name="confirm_password" id="confirm_password" required placeholder="Confirm Password">
                  <i class="fa-solid fa-eye toggle-password" data-target="confirm_password"></i>
                </div>
                <small class="error-text"></small>
              </div>
            </div>
          </section>

          <!-- TERMS -->
          <!-- Terms Modal -->
          <div class="modal-overlay" id="termsModal">
            <div class="modal">
              <div class="modal-header">
                <h3>Terms & Conditions</h3>
                <button class="close-modal" id="closeTerms">&times;</button>
              </div>

              <div class="modal-body">
                <p>
                  By creating an account, you agree to comply with the policies and
                  guidelines of Santa Krus Parish.
                </p><br>

                <p>
                  Your information will be used solely for parish-related services,
                  communication, and record-keeping. We do not share your data with
                  third parties without consent.
                </p><br>

                <p>
                  You are responsible for maintaining the confidentiality of your
                  account credentials.
                </p>
              </div>

              <div class="modal-footer">
                <button type="button" class="modal-btn" id="acceptTerms">
                  I Understand
                </button>
              </div>
            </div>
          </div>

          <div class="terms-row">
            <input type="checkbox" id="terms" required>
            <label for="terms">
              I agree to the
              <a href="javascript:void(0)" id="openTerms">Terms & Conditions</a>
              <span class="required">*</span>
            </label>
          </div>
          
          <div class="g-recaptcha" data-sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI" style="margin: 6px 0"></div>

          <button type="submit" class="signup-btn">CREATE ACCOUNT</button>
        </form>

        <p class="login-link">
          Already have an account? <a href="login.php">Log In</a>
        </p>

      </main>
    </div>

    <script>
  document.addEventListener("DOMContentLoaded", () => {

    const form = document.querySelector(".register-form");
    const contactInput = document.querySelector('input[name="contact"]');
    const year = document.querySelector('select[name="year"]');
    const month = document.querySelector('select[name="month"]');
    const day = document.querySelector('select[name="day"]');

    const formAlert = document.getElementById("formAlert");
    const formAlertMsg = document.getElementById("formAlertMsg");
    const closeFormAlert = document.getElementById("closeFormAlert");

    // Close alert button
    closeFormAlert.addEventListener("click", () => {
      formAlert.style.display = "none";
    });

    form.addEventListener("submit", (e) => {
      // --- Contact Validation ---
      const contact = contactInput.value.trim();
      const phRegex = /^(09\d{9}|\+639\d{9})$/;
      if (!phRegex.test(contact)) {
        e.preventDefault();
        formAlertMsg.textContent = "Invalid contact number. Use 09XXXXXXXXX format.";
        formAlert.style.display = "flex";
        contactInput.focus();
        return;
      }

      // --- Age Validation ---
      if (year.value && month.value && day.value) {
        const birthDate = new Date(`${year.value}-${month.value}-${day.value}`);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age--;
        if (age < 18) {
          e.preventDefault();
          formAlertMsg.textContent = "You must be at least 18 years old to register.";
          formAlert.style.display = "flex";
          return;
        }
      }
    });

  });

    </script>

    <script>
    document.addEventListener("DOMContentLoaded", () => {

      const openTerms = document.getElementById("openTerms");
      const closeTerms = document.getElementById("closeTerms");
      const acceptTerms = document.getElementById("acceptTerms");
      const modal = document.getElementById("termsModal");
      const checkbox = document.getElementById("terms");

      // Open modal
      openTerms.addEventListener("click", () => {
        modal.style.display = "flex";
      });

      // Close modal (X button)
      closeTerms.addEventListener("click", () => {
        modal.style.display = "none"; 
      });

      // Accept terms
      acceptTerms.addEventListener("click", () => {
        checkbox.checked = true;
        modal.style.display = "none";
      });

      // Close when clicking outside modal
      modal.addEventListener("click", (e) => {
        if (e.target === modal) {
          modal.style.display = "none";
        }
      });

    });
    </script>
    
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    </body>
    </html>