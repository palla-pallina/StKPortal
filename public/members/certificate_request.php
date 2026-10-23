<?php
require_once '../connection.php';
session_start();

// ---------------------------------------------------------
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ------------------------------
// LOAD PHPMailer
// ------------------------------
require '../vendor/PHPMailer/PHPMailer/src/Exception.php';
require '../vendor/PHPMailer/PHPMailer/src/PHPMailer.php';
require '../vendor/PHPMailer/PHPMailer/src/SMTP.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ------------------------------
    // COLLECT & SANITIZE INPUT
    // ------------------------------
    $name    = trim($_POST["name"]);
    $email   = trim($_POST["email"]);
    $subject = trim($_POST["subject"]);
    $msg     = trim($_POST["message"]);

    if ($name && $email && $subject && $msg) {

        try {
            $mail = new PHPMailer(true);

            // ------------------------------
            // SMTP SETTINGS (GMAIL EXAMPLE)
            // ------------------------------
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = ' ';     // EMAIL
            $mail->Password   = ' ';   // PASSWORD
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // ------------------------------
            // EMAIL HEADERS
            // ------------------------------
            $mail->setFrom(' ', 'StKPortal');
            $mail->addAddress(' ');        // ✅ RECEIVER
            $mail->addReplyTo($email, $name);

            // ------------------------------
            // EMAIL CONTENT
            // ------------------------------
            $mail->isHTML(true);
            $mail->Subject = "Inquiry: " . $subject;

            $mail->Body = "
                <p><strong>Name:</strong> {$name}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Subject:</strong> {$subject}</p>
                <p><strong>Message:</strong><br>{$msg}</p>
            ";

            $mail->AltBody = "
                Name: $name
                Email: $email
                Subject: $subject
                Message: $msg
            ";

            // ------------------------------
            // SEND EMAIL
            // ------------------------------
            $mail->send();
            $message = "✅ Your message has been sent successfully!";

        } catch (Exception $e) {
            $message = "❌ Mail Error: {$mail->ErrorInfo}";
        }

    } else {
        $message = "❌ All fields are required.";
    }
}
// ---------------------------------------------------------

/*--------- USER ACCESS RESTRICT START
- restrict access to registered users only */
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
    echo "<script>
            window.history.back();
          </script>";
    exit();
}

$user_name = $_SESSION['givenname'] ?? null; 
// retrieves logged in user's username
$user_id = $_SESSION['user_id']; 
// retrieves logged in user ID
/*--------- USER ACCESS RESTRICT END */

/*--------- NOTIFICATION HANDLING START
- for notification icon at navbar
- retrieves all notifications belonging to logged in user */
$stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);

$unreadCount = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $unreadCount = $result['cnt'] ?? 0;
    $stmt->close();
}
/*--------- NOTIFICATION HANDLING END */

/*--------- FOR AUTO FILL
- retrieves user information */
$stmt = $dbh->prepare("
    SELECT givenname, middlename, lastname, suffix, email, contact
    FROM users WHERE user_id = :id
");
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Store in session for JS access
foreach ($user as $key => $value) {
    $_SESSION[$key] = $value;
}
/*--------- FOR AUTO FILL END */
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Santa Krus Parish</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../../stylesheets/public/user_footer_navbar.css" />
  <link rel="stylesheet" href="../../stylesheets/public/TOS_modal.css" />
  <link rel="stylesheet" href="../../stylesheets/public/certicates.css" /> 
</head>
<body>

<!-------------------- NAVBAR -------------------->
<header>
    <nav class="navbar">
        <ul class="nav-links">
            <li><a href="#"><img src="../../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" /></a></li>
            <li><a href="../homepage.php">Home</a></li>
            <li><a href="../history.php">History</a></li>
            <li><a href="../events.php">News & Events</a></li>
            <!--<li><a href="sacraments.php">Sacraments</a></li>-->

            <li class="li-dropdown">
                <a href="#">Parish <i class="fa-solid fa-angle-down"></i></a>
                <ul class="dropdown">
                    <li style="border-bottom: 1px solid var(--yellow);"><a href="../sacraments.php">Sacraments</a></li>
                    <li><a href="../services.php">Services</a></li>
                </ul>
            </li>
            
            <li><a href="../donation.php">Donate</a></li>
            <li><a href="../prayer_page.php">Prayer Request</a></li>
            <li><a href="../contact.php">Contact</a></li>

            <?php
            session_start();

            if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
                // USER NOT LOGGED IN → Show Login button
                echo '<li><a href="../login.php" id="login-btn" style="color:white;"><i class="fa-regular fa-user"></i>   Log In</a></li>';
            } else {
                // USER LOGGED IN → Show Username + Dropdown
                ?>
            <p style="color:white; padding-left: 17px; padding-right: 17px;">|</p>
                <li class="li-dropdown">
                    <a href="#"><?= htmlspecialchars($user_name) ?></a>
                    <ul class="dropdown">
                        <li style="border-bottom: 1px solid var(--yellow);"><a href="../members/status.php">Status</a></li>
                        <li><a href="../logout.php" a>Logout</a></li>
                    </ul>
                </li>

                <li class="nav-notification">
                    <a href="notifications.php">
                        <span class="bell">&#128276;</span>
                        <?php if (!empty($unreadCount) && $unreadCount > 0): ?>
                            <span class="badge"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php
            }
            ?>
        </ul>
    </nav>
</header>
<!----------------- NAVBAR END----------------->

<div class="header-banner" style="background-image: url('../../stylesheets/assets/banner.png');">
    <h1>Certificate Request</h1>
</div>

<main>
  <?php if (!empty($_SESSION['cert_success'])): ?>
    <div class="success-alert">
      <i class="fa-solid fa-circle-check"></i>
      <?= htmlspecialchars($_SESSION['cert_success']) ?>
    </div>
    <?php unset($_SESSION['cert_success']); ?>
  <?php endif; ?>

  <div class="reservation-form">

    <!-- INFO SECTION -->
    <div class="info-section">
      <h4>Important Information</h4>
      <ul>
        <li>
          Certificate requests can be submitted online in advance for faster processing.
        </li>
        <li>
          Requests are reviewed by the parish office. If approved, you will receive a
          notification with the scheduled pick-up date.
        </li>
        <li>
          Certificates are issued as hard copies only and must be claimed in person
          at Santa Krus Parish.
        </li>
      </ul>
    </div>

    <form action="handle/save_certificate_request.php" method="POST" enctype="multipart/form-data">

      <!-- CERTIFICATE TYPE -->
      <div class="form-grid grid-1">

        <div class="form-group">
          <h4 class="form-section-title">Certificate Type <span style="color: #ff0707;font-size: 18px;font-family: courier;">*</span></h4>
          <select name="certificate_type" required>
            <option value="" disabled selected>- Select Certificate Type-</option>
            <option value="Baptism">Baptism</option>
            <option value="First Communion">First Communion</option>
            <option value="Confirmation">Confirmation</option>
            <option value="Wedding">Wedding</option>
          </select>
        </div>
        <div class="form-group">
          <h4>What is the purpose of your certificate request? <span style="color: #ff0707;font-size: 18px;font-family: courier;">*</span></h4>
          <select name="request_reason" required>
            <option value="" disabled selected>- Select -</option>
            <option value="Personal record">Personal record</option>
            <option value="Legal requirement">Legal requirement</option>
            <option value="Church requirement">Church requirement</option>
            <option value="School requirement">School requirement</option>
            <option value="Replacement copy">Replacement copy</option>
            <option value="Verification purposes">Verification purposes</option>
          </select>
        </div>
      </div>

      <!-- CLAIMING FOR WHO? -->
      <div class="form-group centered-section">

      <div class="form-group">
        <h4 class="form-section-title" style="margin-bottom:-17px;">Are you requesting for your own certificate or for someone else? </h4>

        <div class="form-claim">
          <button type="button" class="claim-btn" name="claiming_for" value="self"
                  onclick="toggleClaim('self', this); autofill('self')">
            For self
          </button>

          <button type="button" class="claim-btn" name="claiming_for" value="other"
                  onclick="toggleClaim('other', this); autofill('other')">
            For someone else
          </button>
        </div>
      </div>
      </div>

      <!-- CLAIM FOR SELF -->
      <div id="claim-self-section" style="display:none">
      
      <hr class="divider" style="margin-top: 22px;">
      <h4 class="form-section-title">Certificate Owner's Information
        <span class="tooltip-icon">ⓘ
          <span class="tooltip-text" id="dateTooltip">
            Information of the person who owns the certificate
            <!--Name of the person submmiting the form-->
          </span>
        </span>
      </h4>

      <div class="form-grid grid-4">
        <div class="form-group">
          <label>Given Name <span class="required">*</span></label>
          <input type="text" id="self_first" name="request_firstname" placeholder="First Name" required>
        </div>

        <div class="form-group">
          <label>Middle Name</label>
          <input type="text" id="self_middle" name="request_middlename" placeholder="Middle Name" style="margin-top: 7px;">
        </div>

        <div class="form-group">
          <label>Last Name <span class="required">*</span></label>
          <input type="text" id="self_last" name="request_lastname" placeholder="Last Name" required>
        </div>

        <div class="form-group">
          <label>Suffix</label>
          <input type="text" id="self_suffix" name="request_suffix" placeholder="Suffix" style="margin-top: 7px;">
        </div>
      </div>

        <!-- CONTACT -->
        <h4 class="form-section-title">Contact Information</h4>

        <div class="form-grid grid-2">
          <div class="form-group">
            <label>Contact Number <span class="required">*</span></label>
            <input type="tel" name="contact" id="self_contact" placeholder="09xxxxxxxxx" required >
          </div>

          <div class="form-group">
            <label>Email Address <span class="required">*</span></label>
            <input type="email" name="email" id="self_email" placeholder="Email Address" required>
          </div>
        </div>

      <div class="form-group">
        <label>ID of the Certificate Owner<span class="required">*</span></label>
        <input type="file" name="id_picture" accept="image/*,.pdf" required>
      </div>

      <div class="form-group">
        <div class="info-section-id">
          <h4 class="toggle-header" onclick="toggleIDs()">
            Accepted Identification Documents
            <span id="toggle-icon">▴</span>
          </h4>

          <div id="accepted-ids" class="collapsible open">
            <table class="id-table">
              <tbody>
                <tr>
                  <td>
                    <ul>
                      <li>Birth Certificate</li>
                      <li>National ID</li>
                      <li>Passport</li>
                      <li>PhilHealth ID (card or PVC)</li>
                      <li>Unified Multi-Purpose ID (UMID)</li>
                      <li>Driver's License</li>
                      <li>Voter's ID</li>
                    </ul>
                  </td>
                  <td>
                    <ul>
                      <li>Senior Citizen ID</li>
                      <li>PWD ID</li>
                      <li>School ID</li>
                      <li>Barangay ID</li>
                      <li>City ID</li>
                      <li>Company ID</li>
                    </ul>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      </div>

      <!-- CLAIM FOR OTHER SECTION -->
      <div id="claim-other-section" style="display:none">

        <div class="form-group-prereq">
          <h4>Whose certificate are you requesting for?</h4>
          <select name="certificate_owner" required style="margin-bottom:10px;">
            <option value="" disabled selected>- Select -</option>
            <option value="Child">Child</option>
            <option value="Parent">Parent</option>
          </select>
        </div>

        <hr class="divider">

        <h4 class="form-section-title">Certificate Owner’s Information
        <span class="tooltip-icon">ⓘ
          <span class="tooltip-text" id="dateTooltip">
            Information of the person who owns the certificate
          </span>
        </span>
        </h4>

        <div class="form-grid grid-4">
          <div class="form-group">
            <label>Given Name<span class="required"> *</span></label>
            <input type="text" name="owner_firstname" placeholder="First Name">
          </div>

          <div class="form-group">
            <label>Middle Name</label>
            <input type="text" name="owner_middlename" placeholder="Middle Name" style="margin-top: 7px;">
          </div>

          <div class="form-group">
            <label>Last Name<span class="required"> *</span></label>
            <input type="text" name="owner_lastname" placeholder="Last Name">
          </div>

          <div class="form-group">
            <label>Suffix</label>
            <input type="text" name="owner_suffix" placeholder="Suffix" style="margin-top: 7px;">
          </div>
        </div>

        <div class="form-group">
          <label>ID of the Certificate Owner<span class="required"> *</span></label>
          <input type="file" name="owner_id_picture" accept="image/*,.pdf" required>
        </div>

        <div class="info-section-id">
          <h4 class="toggle-header" onclick="toggleIDs()">
            Accepted Identification Documents
            <span id="toggle-icon">▴</span>
          </h4>

          <div id="accepted-ids" class="collapsible open">
            <table class="id-table">
              <tbody>
                <tr>
                  <td>
                    <ul>
                      <li>Birth Certificate</li>
                      <li>National ID</li>
                      <li>Passport</li>
                      <li>PhilHealth ID (card or PVC)</li>
                      <li>Unified Multi-Purpose ID (UMID)</li>
                      <li>Driver's License</li>
                      <li>Voter's ID</li>
                    </ul>
                  </td>
                  <td>
                    <ul>
                      <li>Senior Citizen ID</li>
                      <li>PWD ID</li>
                      <li>School ID</li>
                      <li>Barangay ID</li>
                      <li>City ID</li>
                      <li>Company ID</li>
                    </ul>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <hr class="divider-dotted" style="border-top: 4px dotted #e5e0db; border-bottom: hidden;">

        <h4 class="form-section-title">Authorized Person's Information
          <span class="tooltip-icon">ⓘ
            <span class="tooltip-text" id="dateTooltip">
              Information of the person who is submitting the request
            </span>
          </span>
        </h4>

        <div class="form-grid grid-4">
          <div class="form-group">
            <label>Given Name <span class="required">*</span></label>
            <input type="text" id="auth_first" name="request_firstname" placeholder="First Name" required>
          </div>

          <div class="form-group">
            <label>Middle Name</label>
            <input type="text" id="auth_middle" name="request_middlename" placeholder="Middle Name" style="margin-top: 7px;">
          </div>

          <div class="form-group">
            <label>Last Name <span class="required">*</span></label>
            <input type="text" id="auth_last" name="request_lastname" placeholder="Last Name" required>
          </div>

          <div class="form-group">
            <label>Suffix</label>
            <input type="text" id="auth_suffix" name="request_suffix" placeholder="Suffix" style="margin-top: 7px;">
          </div>
        </div>

        <!-- CONTACT -->
        <h4 class="form-section-title">Contact Information</h4>

        <div class="form-grid grid-2">
          <div class="form-group">
            <label>Contact Number <span class="required">*</span></label>
            <input type="tel" name="contact" id="auth_contact" placeholder="09xxxxxxxxx" required >
          </div>

          <div class="form-group">
            <label>Email Address <span class="required">*</span></label>
            <input type="email" name="email" id="auth_email" placeholder="Email Address" required>
          </div>
        </div>
        
        <div class="form-group">
          <label>ID of Authorized Person<span class="required">*</span>
          </label>
          <input type="file" name="authorized_id_picture" accept="image/*,.pdf">
        </div>
      </div>

      <!-- TERMS MODAL -->
      <div class="modal-overlay" id="termsModal">
        <div class="modal">
          <div class="modal-header">
            <h3>Terms & Conditions</h3>
            <button type="button" class="close-modal" id="closeTerms">&times;</button>
          </div>

          <div class="modal-body">
            <p>
              By requesting a certificate from Santa Krus Parish, you agree to comply
              with parish policies and guidelines.
            </p>
            <br>
            <p>
              All personal information submitted will be used solely for verification,
              documentation, and parish-related services. Your data will not be shared
              with third parties without consent.
            </p>
            <br>
            <p>
              Submitting false or misleading information may result in request denial.
            </p>
          </div>

          <div class="modal-footer">
            <button type="button" class="modal-btn" id="acceptTerms">
              I Understand
            </button>
          </div>
        </div>
      </div>

      <!-- TERMS -->
      <br><div class="terms-row">
        <input type="checkbox" id="terms" required>
        <label for="terms">
          I agree to the
          <a href="javascript:void(0)" id="openTerms">Terms & Conditions</a>
          <span class="required">*</span>
        </label>
      </div>

      <!-- SUBMIT -->
      <div class="text-center" id="reserve-btn">
        <button type="submit" name="submit">SUBMIT REQUEST</button>
      </div>

    </form>
  </div>

</main>

<!-- FOOTER -->
<footer class="custom-footer">

  <div class="footer-left">
    <h2>Santa Krus Parish</h2>
    <p>Serving the community of Bagumbong, Caloocan with faith and love.</p>
    <p><b>Email Address</b><br>stakrusparish@gmail.com</p>
    <p><b>Contact Number</b><br>(02) 8984-3933</p>

    <ul class="social-icons">
      <li><a href="https://www.facebook.com/DioNova.StaKrusParish"><span class="fab fa-facebook-f"></span></a></li>
    </ul>
  </div>
  <!-- footer-left was not closed before -->

  <!-- LOCATION SECTION (Center) -->
  <div class="footer-location">
    <h3>Location</h3>
    <p>205 Deparo Rd, Caloocan City, 1420 Metro Manila</p>

    <div class="map-container">
      <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3858.6890401821715!2d121.00261717591935!3d14.737954572658468!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b7bb4e6b1b65%3A0x424ba9f332da9e86!2s205%20Deparo%20Rd%2C%20Caloocan%2C%201420%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1731985610001"
        width="100%"
        height="250"
        style="border:0;"
        allowfullscreen=""
        loading="lazy">
      </iframe>
    </div>
  </div>

  <!-- CONTACT SECTION -->
  <div class="footer-contact">
    <?php if (!empty($message)) : ?>
        <div class="alert alert-info text-center">
            <?= htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <h3>Contact Us</h3>
    <form  method="POST">
      <input type="text" name="name" placeholder="Name" required>
      <input type="email" name="email" placeholder="Email Address" required>
      <input type="text" name="subject" placeholder="Subject" required>
      <textarea name="message" placeholder="Message" required></textarea>
      <button type="submit">SUBMIT</button>
    </form>
  </div>

</footer>

<script>
function autofill(section) {
  const data = {
    first:   "<?= $_SESSION['givenname'] ?? '' ?>",
    middle:  "<?= $_SESSION['middlename'] ?? '' ?>",
    last:    "<?= $_SESSION['lastname'] ?? '' ?>",
    suffix:  "<?= $_SESSION['suffix'] ?? '' ?>",
    email:   "<?= $_SESSION['email'] ?? '' ?>",
    contact: "<?= $_SESSION['contact'] ?? '' ?>"
  };

  if (section === 'self') {
    document.getElementById('self_first').value  = data.first;
    document.getElementById('self_middle').value = data.middle;
    document.getElementById('self_last').value   = data.last;
    document.getElementById('self_suffix').value = data.suffix;
    document.getElementById('self_email').value = data.email;
    document.getElementById('self_contact').value = data.contact;
  }

  if (section === 'other') {
    document.getElementById('auth_first').value  = data.first;
    document.getElementById('auth_middle').value = data.middle;
    document.getElementById('auth_last').value   = data.last;
    document.getElementById('auth_suffix').value = data.suffix;
    document.getElementById('auth_email').value = data.email;
    document.getElementById('auth_contact').value = data.contact;
  }
}
</script>


<script>
function toggleClaim(type, clickedBtn) {
  const selfSection  = document.getElementById('claim-self-section');
  const otherSection = document.getElementById('claim-other-section');

  // Clear the section being hidden
  if (type === 'self') {
    clearSection(otherSection);
  } else {
    clearSection(selfSection);
  }

  // Toggle visibility
  selfSection.style.display  = (type === 'self')  ? 'block' : 'none';
  otherSection.style.display = (type === 'other') ? 'block' : 'none';

  // Enable / disable required fields
  selfSection.querySelectorAll('[required]').forEach(el => {
    el.disabled = (type !== 'self');
  });

  otherSection.querySelectorAll('[required]').forEach(el => {
    el.disabled = (type !== 'other');
  });

  // Button active state
  document.querySelectorAll('.claim-btn').forEach(btn => {
    btn.classList.remove('active');
  });
  clickedBtn.classList.add('active');
}
</script>



<script>
  function toggleIDs() {
    const section = document.getElementById("accepted-ids");
    const icon = document.getElementById("toggle-icon");

    section.classList.toggle("open");

    if (section.classList.contains("open")) {
      icon.textContent = "▴";
    } else {
      icon.textContent = "▾";
    }
  }
</script>

<script>
function clearSection(section) {
  section.querySelectorAll('input').forEach(input => {
    if (input.type === 'file') {
      input.value = '';
    } else {
      input.value = '';
    }
  });
}
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

</body>
</html>