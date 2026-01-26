<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

$user_id = $_SESSION['user_id'];
$givenname = $_SESSION['givenname'] ?? null; 

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
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Santa Krus Parish</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../stylesheets/public/sacrament_info.css" />
    <link rel="stylesheet" href="../../stylesheets/public/user_footer_navbar.css" />
    <link rel="stylesheet" href="../../stylesheets/public/login_modal.css" />
</head>
<body>

<!-- expose login state to JS (needed for authModal)-->
<script>
    const IS_LOGGED_IN = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
</script>

<div class="header-banner" style="background-image: url('../../stylesheets/assets/banner/confirmation.jpg');">
    <h1>Confirmation</h1>
</div>

<div class="breadcrumb">
    <a href="../sacraments.php">Sacraments</a><span>Confirmation</span>
</div>

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
                <a href="#" class="active">Parish <i class="fa-solid fa-angle-down"></i></a>
                <ul class="dropdown">
                    <li style="border-bottom: 1px solid var(--yellow);"><a href="../sacraments.php" class="active">Sacraments</a></li>
                    <li><a href="../services.php">Services</a></li>
                </ul>
            </li>
            
            <li><a href="../donation.php">Donate</a></li>
            <li><a href="../prayer_page.php">Prayer Request</a></li>
            <li><a href="../contact.php">Contact</a></li>

            <?php
            if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
                // USER NOT LOGGED IN → Show Login button
                echo '<li><a href="../login.php" id="login-btn" style="color:white;"><i class="fa-regular fa-user"></i>   Log In</a></li>';
            } else {
                // USER LOGGED IN → Show Username + Dropdown
                ?>
            <p style="color:white; padding-left: 17px; padding-right: 17px;">|</p>
                <li class="li-dropdown">
                    <a href="#"><?= htmlspecialchars($givenname) ?></a>
                    <ul class="dropdown">
                        <li><a href="../logout.php">Logout</a></li>
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



<div class="content">

    <!-- WHAT IS CONFIRMATION (WITH TOGGLE) -->
    <h2 style="display:flex; justify-content:space-between; align-items:center;">
        <span id="confirmationTitle"
              data-en="What is Confirmation?"
              data-tl="Ano ang Kumpil?">
              What is Confirmation?
        </span>
        <div class="language-switch">
    <label for="langSelect">Language:</label>
    <select id="langSelect">
        <option value="en">English</option>
        <option value="fil">Filipino</option>
    </select>
</div>
    </h2>

    <img src="../../stylesheets/assets/banner/confirmation-ceremony.jpg" alt="Confirmation Ceremony" class="section-img">

    <!-- NEW CONTENT ADDED WITHOUT REMOVING OLD CONTENT -->
    <p id="confirmationText"
       data-en="Confirmation is one of the seven sacraments through which Catholics pass in the process of their religious upbringing. It is a sacrament of initiation, which completes baptism through the sealing of the Holy Spirit and anoints the recipient as priest, prophet, and king. Our goal is to cultivate a relationship with Jesus Christ— inspiring to become an active witness to all we encounter during our lives."
       data-tl="Ang Kumpil ay isa sa pitong sakramento na pinagdadaanan ng mga Katoliko sa proseso ng kanilang relihiyosong pagpapalaki. Isa itong sakramento ng inisasyon na kumukumpleto sa binyag sa pamamagitan ng pag-tatak ng Banal na Espiritu at pagpinahid sa tumatanggap bilang pari, propeta, at hari. Layunin naming palaguin ang relasyon sa Panginoong Hesus—inspirasyon upang maging aktibong saksi sa lahat ng nakakasalamuha sa ating buhay.">
        Confirmation is one of the seven sacraments through which Catholics pass in the process of their religious upbringing.
        It is a sacrament of initiation, which completes baptism through the sealing of the Holy Spirit and anoints the recipient
        as priest, prophet, and king. Our goal is to cultivate a relationship with Jesus Christ— inspiring to become an active
        witness to all we encounter during our lives.
    </p>

    <!-- ORIGINAL TEXT YOU HAD -->
    <p id="confirmationOldText"
       data-en="Confirmation is the sacrament that strengthens and deepens the grace received at Baptism through the Holy Spirit."
       data-tl="Ang Kumpil ay ang sakramento na nagpapatibay at nagpapalalim ng biyayang natanggap sa Binyag sa pamamagitan ng Banal na Espiritu.">
        Confirmation is the sacrament that strengthens and deepens the grace received at Baptism through the Holy Spirit.
    </p>

    <div class="divider"></div>

    <!-- ORIGINAL SECTION KEPT -->
    <h2 id="importanceTitle" data-en="Importance of Confirmation" data-tl="Kahalagahan ng Kumpil">Importance of Confirmation</h2>
    <img src="../../stylesheets/assets/banner/holy-spirit.jpg" alt="Holy Spirit" class="section-img">
    <p id="importanceText" data-en="It empowers the faithful to live a Christian life courageously and witness to their faith." data-tl="Binibigyang lakas nito ang mga mananampalataya upang mamuhay ng Kristiyanong buhay nang may tapang at maging saksi sa kanilang pananampalataya.">
        It empowers the faithful to live a Christian life courageously and witness to their faith.
    </p>

    <div class="divider"></div>

    <!-- INSERTING NEW DATES SECTIONS -->
    <h2 id="datesTitle" data-en="What are the dates for Confirmation?" data-tl="Ano ang mga petsa para sa Kumpil?">What are the dates for Confirmation?</h2>
    <p id="datesText" data-en="Dates are announced yearly by the parish. Confirmations typically take place once a year." data-tl="Ang mga petsa ay inia-anunsiyo taun-taon ng parokya. Karaniwang ginaganap ang Kumpil isang beses kada taon.">
        Dates are announced yearly by the parish. Confirmations typically take place once a year.
    </p>

    <div class="divider"></div>

    <h2 id="listingTitle" data-en="What are the dates for listing?" data-tl="Ano ang mga petsa para sa pagpaparehistro?">What are the dates for listing?</h2>
    <p id="listingText" data-en="Listing opens 1–2 months before the scheduled Confirmation day. Visit the parish office for the current schedule." data-tl="Ang pagpaparehistro ay nagsisimula 1–2 buwan bago ang nakatakdang araw ng Kumpil. Bisitahin ang opisina ng parokya para sa kasalukuyang iskedyul.">
        Listing opens 1–2 months before the scheduled Confirmation day. Visit the parish office for the current schedule.
    </p>

    <div class="divider"></div>

    <!-- ORIGINAL REQUIREMENTS + NEW REQUIRED DOCUMENTS ADDED -->
    <h2 id="requirementsTitle" data-en="Requirements" data-tl="Mga Kailangan">Requirements</h2>
    <ul id="requirementsList" class="info-list"
        data-en="• Certificate of Baptism|• Proof of Attendance in Catechesis Program|• Baptized Catholic|• Attend preparation classes|• Have a sponsor who is a confirmed Catholic"
        data-tl="• Sertipiko ng Binyag|• Patunay ng Pagdalo sa Catechesis Program|• Binyag na Katoliko|• Dumalo sa paghahandang klase|• Magkaroon ng ninong/ninang na kumpil na Katoliko">
        <li>• Certificate of Baptism</li>
        <li>• Proof of Attendance in Catechesis Program</li>
        <li>• Baptized Catholic</li>
        <li>• Attend preparation classes</li>
        <li>• Have a sponsor who is a confirmed Catholic</li>
    </ul>

    <div class="divider"></div>

    <!-- ORIGINAL SCHEDULE KEPT -->
    <h2 id="scheduleTitle" data-en="Schedule" data-tl="Iskedyul">Schedule</h2>
    <p id="scheduleText" data-en="Confirmation ceremonies are usually held annually. Contact the parish for exact dates." data-tl="Karaniwang ginaganap ang Kumpil taun-taon. Makipag-ugnayan sa parokya para sa eksaktong petsa.">
        Confirmation ceremonies are usually held annually. Contact the parish for exact dates.
    </p>

    <div class="divider"></div>

    <!-- ORIGINAL CONTACT KEPT -->
    <h2 id="contactTitle" data-en="Contact" data-tl="Makipag-ugnayan">Contact</h2>
    <p id="contactText" data-en="For inquiries, visit the parish office or call the church hotline." data-tl="Para sa mga katanungan, bumisita sa opisina ng parokya o tumawag sa church hotline.">
        For inquiries, visit the parish office or call the church hotline.
    </p>

</div>

<!-- LANGUAGE SCRIPT -->
<script>
const toggle = document.getElementById("langSelect");

toggle.addEventListener("change", () => {
    const lang = toggle.value; // "en" or "fil"

    const elements = [
        "confirmationTitle","confirmationText","confirmationOldText",
        "importanceTitle","importanceText",
        "datesTitle","datesText",
        "listingTitle","listingText",
        "requirementsTitle","requirementsList",
        "scheduleTitle","scheduleText",
        "contactTitle","contactText"
    ];

    elements.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;

        if (el.tagName === "UL") {
            const items = (lang === "fil" ? el.dataset.tl : el.dataset.en).split("|");
            el.innerHTML = "";
            items.forEach(item => {
                const li = document.createElement("li");
                li.textContent = item;
                el.appendChild(li);
            });
        } else {
            el.textContent = (lang === "fil" ? el.dataset.tl : el.dataset.en);
        }
    });
});

</script>

<a href="#" class="reserve-btn" data-href="../members/confirmation.php">Reserve Confirmation</a>



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

<!------- LOGIN/SIGNUP MODAL HTML ----------->
<div id="authModal" class="auth-modal">
    <div class="auth-modal-content">
        <span class="auth-close">&times;</span>

        <h2>Please log in to continue</h2>
        <p>You need an account to access parish services.</p>

        <form method="POST" action="../login.php" class="auth-form">
            <input type="email" name="email" placeholder="Email" required>
            
            <div class="form-group password-field">
                <input type="password" name="password" placeholder="Password" id="password" required>
                <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i>
            </div>
            <button type="submit">LOG IN</button>
        </form>

        <div class="signup-text">
          Don’t have an account? <a href="../register.php">Sign Up</a>
        </div>
    </div>
</div>

<!------- MODAL + REDIRECT LOGIC SCRIPT ------>
<script>
// Show/Hide Password
const togglePassword = document.getElementById("togglePassword");
const passwordInput = document.getElementById("password");

togglePassword.addEventListener("click", () => {
    const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
    passwordInput.setAttribute("type", type);
    togglePassword.classList.toggle("fa-eye-slash");
});

document.querySelectorAll('.reserve-btn').forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.preventDefault();

        const target = this.dataset.href;

        if (IS_LOGGED_IN) {
            window.location.href = target;
        } else {
            openAuthModal();
        }
    });
});

function openAuthModal() {
    document.getElementById('authModal').style.display = 'flex';
    document.body.classList.add('modal-open');
}

function closeAuthModal() {
    document.getElementById('authModal').style.display = 'none';
    document.body.classList.remove('modal-open');
}

document.querySelector('.auth-close').addEventListener('click', closeAuthModal);

window.addEventListener('click', e => {
    const modal = document.getElementById('authModal');
    if (e.target === modal) closeAuthModal();
});
</script>
</body>
</html>

