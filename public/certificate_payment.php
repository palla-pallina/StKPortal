<?php
require_once 'connection.php';
session_start();

// ---------------------------------------------------------
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ------------------------------
// LOAD PHPMailer
// ------------------------------
require 'vendor/PHPMailer/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/PHPMailer/src/SMTP.php';

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
            $mail->Username   = 'atsaratom@gmail.com';     // ✅ YOUR EMAIL
            $mail->Password   = 'gcrp redk guor brwo';   // ✅ APP PASSWORD
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // ------------------------------
            // EMAIL HEADERS
            // ------------------------------
            $mail->setFrom('atsaratom@gmail.com', 'StKPortal');
            $mail->addAddress('atsaratom@gmail.com');        // ✅ RECEIVER
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

$user_name = $_SESSION['givenname'] ?? null; 
$user_id = $_SESSION['user_id'] ?? null;

// Notifications
$unreadCount = 0;
if ($user_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $unreadCount = $result['cnt'] ?? 0;
    $stmt->close();
}

// Messages
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['donation_type'])) {

    // donor_email and contact are not in the form, set NULL
    $donor_email = null;
    $donor_contact_no = null;

    // Collect form data
    $donation_type = $_POST['donation_type'] ?? null;
    $donation_specified = $_POST['donation_description'] ?? null;
    $donation_cause = $_POST['donation_cause'] ?? null;
    $mode_of_payment = $_POST['payment_method'] ?? null;
    $donation_amount = $_POST['donation_amount'] ?? 0.00;
    $donation_status = 'unverified';
    $date_posted = date('Y-m-d H:i:s');

    // Handle file upload
    $proof_of_donation = null;

    if (isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] === UPLOAD_ERR_OK) {

        // ✅ Path relative to donation.php (src/public/donation.php)
        $relativePath = "/private/donation_proof/";

        // ✅ Absolute server path for moving the uploaded file
        $uploadDir = __DIR__ . "/../private/donation_proof/";

        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileTmpPath = $_FILES['screenshot']['tmp_name'];
        $fileName = time() . '_' . basename($_FILES['screenshot']['name']);

        if (move_uploaded_file($fileTmpPath, $uploadDir . $fileName)) {
            // ✅ Save relative-to-donation.php path in DB
            $proof_of_donation = $fileName;
        }
    }

    // Insert into database
    $sql = "INSERT INTO donations 
        (donor_email, donor_contact_no, donation_type, donation_specified, donation_cause, mode_of_payment, proof_of_donation, donation_amount, donation_status, date_posted)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        $error_msg = "Prepare failed: " . $conn->error;
    } else {
        $stmt->bind_param(
            "sssssssdss",
            $donor_email,
            $donor_contact_no,
            $donation_type,
            $donation_specified,
            $donation_cause,
            $mode_of_payment,
            $proof_of_donation,
            $donation_amount,
            $donation_status,
            $date_posted
        );

        if ($stmt->execute()) {
            $success_msg = "Your donation is successfully sent, thank you!";
        } else {
            $error_msg = "Error saving donation: " . $stmt->error;
        }

        $stmt->close();
    }

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
<link rel="stylesheet" href="../stylesheets/public/user_footer_navbar.css" />
<link rel="stylesheet" href="../stylesheets/public/donation.css" />
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

            <?php
            session_start();

            if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
                // USER NOT LOGGED IN → Show Login button
                echo '<li><a href="login.php" id="login-btn" style="color:white;"><i class="fa-regular fa-user"></i>   Log In</a></li>';
            } else {
                // USER LOGGED IN → Show Username + Dropdown
                ?>
            <p style="color:white; padding-left: 17px; padding-right: 17px;">|</p>
                <li class="li-dropdown">
                    <a href="#"><?= htmlspecialchars($user_name) ?></a>
                    <ul class="dropdown">
                        <li style="border-bottom: 1px solid var(--yellow);"><a href="members/status.php">Status</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </li>

                <li class="nav-notification">
                    <a href="members/notifications.php">
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

<section class="donations">
  <h3>Certificate Payment</h3>
</section>

<div class="donation-flex">
  <section class="left-image-strip">
    <img src="../stylesheets/assets/stk-ban.png" alt="Inspirational Verse" class="left-image"/>
  </section>

  
<!-- Display messages -->
<?php if ($success_msg): ?>
    <p class="success-msg"><?= htmlspecialchars(string: $success_msg) ?></p>
<?php endif; ?>
<?php if ($error_msg): ?>
    <p class="error-msg"><?= htmlspecialchars($error_msg) ?></p>
<?php endif; ?>

<section class="donation-form-section">
    <form class="donation-form" method="POST" enctype="multipart/form-data">
      
      <label for="donation-cause"><h3>Certificate Payment</h3></label>
      <select id="sacrament-reservation" name="sacrament-reservation" required>
        <option value="" disabled selected>Select</option>
        <option value="Certificate">Certificate</option>
      </select>

      <select id="donation-cause" name="donation_cause" required hidden>
        <option value="Certificate Payment" selected>Service Payment</option>
      </select>

      <select id="donation-type" name="donation_type" required>
        <option value="" disabled selected>Select</option>
        <option value="Cash">Cash</option>
       
      </select>

      <!-- Amount for Cash -->
      <div id="amount-container">
        <label for="amount">Amount</label>
        <input type="number" id="amount" name="donation_amount" value="50" min="50" max="50" placeholder="Amount" required/>
      </div>

      <!-- Description for non-Cash -->
      <div id="description-container" style="display:none;">
        <label for="description">Description</label>
        <textarea id="description" name="donation_description" placeholder="Enter description..." rows="3"></textarea>
      </div>

      <!-- Payment Options -->
      <div class="payment-method-section">
        <span>Payment Method</span>
        <div id="payment-options-container" class="payment-options"></div>
      </div>

      <!-- Screenshot Upload -->
      <div id="screenshot-container" style="display:none;">
        <label for="screenshot">Upload Screenshot (proof of transaction):</label>
        <input type="file" id="screenshot" name="screenshot" accept="image/*" />
      </div>

      <button type="submit" class="give-btn">Submit</button>

</div>
    </form>
</section>

</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('successModal');
  const closeBtn = document.getElementById('closeModalBtn');

  if (modal && closeBtn) {
    closeBtn.addEventListener('click', () => {
      modal.style.display = 'none';
      window.location.href = 'donation.php'; // refresh page safely
    });
  }
});
</script>

<?php if ($success_msg): ?>
<div id="successModal" class="modal-overlay">
  <div class="modal-box">
    <div class="modal-icon">
      <i class="fa-solid fa-circle-check"></i>
    </div>
    <h3>Thank you!</h3>
    <p>Your payment is successfully sent!</p>
    <button id="closeModalBtn">OK</button>
  </div>
</div>
<?php endif; ?>

<!-- QR Code Modal -->
<div id="qr-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
     background: rgba(0,0,0,0.7); justify-content:center; align-items:center; z-index:9999;">
  <div style="background:white; padding:20px; border-radius:10px; text-align:center; position:relative;">
    <span id="close-qr" style="position:absolute; top:10px; right:15px; cursor:pointer; font-size:20px;">&times;</span>
    <iframe id="qr-frame" src="" width="500" height="600" style="border:none;"></iframe>
  </div>
</div>

<script>

 document.addEventListener('DOMContentLoaded', () => {
  const donationType = document.getElementById('donation-type');
  const paymentContainer = document.getElementById('payment-options-container');
  const screenshotContainer = document.getElementById('screenshot-container');
  const amountContainer = document.getElementById('amount-container');
  const descriptionContainer = document.getElementById('description-container');
  const qrModal = document.getElementById('qr-modal');
  const qrFrame = document.getElementById('qr-frame');
  const closeQR = document.getElementById('close-qr');

  // Cash payment options HTML
  const cashOptionsHTML = `
    <label class="payment-option">
      <input type="radio" name="payment_method" value="Metrobank" required />
      <img src="../stylesheets/assets/metrobank.png" alt="Metrobank" class="payment-logo" />
    </label>
    <label class="payment-option">
      <input type="radio" name="payment_method" value="GCash" required />
      <img src="../stylesheets/assets/gcash.png" alt="GCash" class="payment-logo" />
    </label>
  `;

  function updateForm() {
    qrFrame.src = '';
    qrModal.style.display = 'none';

    if (donationType.value === 'Cash') {
      // Show Cash-related fields
      paymentContainer.innerHTML = cashOptionsHTML;
      paymentContainer.style.display = 'flex';
      amountContainer.style.display = 'block';
      descriptionContainer.style.display = 'none';
      screenshotContainer.style.display = 'block';

      // Add event to open QR modal when payment method selected
      const radios = paymentContainer.querySelectorAll('input[name="payment_method"]');
      radios.forEach(radio => {
        radio.addEventListener('change', () => {
          const cause = document.getElementById('donation-cause').value;
          const amount = document.getElementById('amount').value;

          if (!cause || !amount) {
            alert('Please select a cause and enter an amount first.');
            radio.checked = false;
            return;
          }

          let qrSrc = '';
          if (radio.value === 'GCash') qrSrc = 'handle/qr_gcash.html?' + new URLSearchParams({ cause, amount });
          if (radio.value === 'Metrobank') qrSrc = 'handle/qr_metrobank.html?' + new URLSearchParams({ cause, amount });

          qrFrame.src = qrSrc;
          qrModal.style.display = 'flex';
        });
      });

    } else {
      // Non-Cash: hide payment options, hide amount, show description
      paymentContainer.innerHTML = '';
      paymentContainer.style.display = 'none';
      amountContainer.style.display = 'none';
      descriptionContainer.style.display = 'block';
      screenshotContainer.style.display = 'block';
    }
  }

  donationType.addEventListener('change', updateForm);

  // Close QR modal
  closeQR.addEventListener('click', () => qrModal.style.display = 'none');
  qrModal.addEventListener('click', (e) => { if (e.target === qrModal) qrModal.style.display = 'none'; });

  // Form submission validation
  const form = document.querySelector('.donation-form');
  form.addEventListener('submit', (e) => {
    const type = donationType.value;
    const payment = document.querySelector('input[name="payment_method"]:checked');
    const screenshot = document.getElementById('screenshot').files[0];

    if (type === 'Cash' && !payment) {
      e.preventDefault();
      alert('Please select a payment method.');
      return;
    }

    if (!screenshot) {
      e.preventDefault();
      alert('Please upload a screenshot as proof of transaction.');
      return;
    }

    if (type !== 'Cash') {
      const description = document.getElementById('description').value.trim();
      if (!description) {
        e.preventDefault();
        alert('Please enter a description for your donation.');
        return;
      }
    }
  });
});
</script>

<!-- FOOTER -->
<footer class="custom-footer" style="margin-top:0px;">
  <div class="footer-left">
    <h2>Santa Krus Parish</h2>
    <p>Serving the community of Bagumbong, Caloocan with faith and love.</p>
    <p><b>Email Address</b><br>stakrusparish@gmail.com</p>
    <p><b>Phone Number</b><br>(02) 8984-3933</p>
    <ul class="social-icons">
      <li><a href="https://www.facebook.com/DioNova.StaKrusParish"><span class="fab fa-facebook-f"></span></a></li>
    </ul>
  </div>

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

  <div class="footer-contact">
    <h3>Contact Us</h3>
    <form method="POST">
      <input type="text" name="name" placeholder="Name" required>
      <input type="email" name="email" placeholder="Email Address" required>
      <input type="text" name="subject" placeholder="Subject" required>
      <textarea name="message" placeholder="Message" required></textarea>
      <button type="submit">SUBMIT</button>
    </form>
  </div>
</footer>

</body>
</html>
