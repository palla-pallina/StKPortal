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

<div class="header-banner" style="background-image: url('../../stylesheets/assets/banner/marriage.jpg');">
    <h1>Marriage</h1>
</div>

<div class="breadcrumb">
    <a href="../sacraments.php">Sacraments</a><span>Marriage</span>
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

<div class="content">

    <!-- TITLE SECTION WITH TRANSLATION TOGGLE -->
    <h2 style="display:flex; justify-content:space-between; align-items:center;">
        <span id="marriageTitle"
              data-en="What is Marriage?"
              data-tl="Ano ang Kasal?">
              What is Marriage?
        </span>
        <div class="language-switch">
    <label for="langSelect">Language:</label>
    <select id="langSelect">
        <option value="en">English</option>
        <option value="fil">Filipino</option>
    </select>
    </div>
    </h2> 

    <img src="../../stylesheets/assets/banner/marriage-ceremony.jpg" class="section-img">

    <p id="marriageText"
       data-en="Marriage is the sacrament that unites a man and a woman in a lifelong covenant of love and faithfulness before God and the Church."
       data-tl="Ang Kasal ay sakramento na nagbubuklod sa lalaki at babae sa isang panghabambuhay na tipan ng pag-ibig at katapatan sa harap ng Diyos at ng Simbahan.">
       Marriage is the sacrament that unites a man and a woman in a lifelong covenant of love and faithfulness before God and the Church.
    </p>

    <div class="divider"></div>

    <h2 id="matrimonyTitle"
        data-en="Matrimony"
        data-tl="Pag-aasawa">
        Matrimony
    </h2>
    <p id="matrimonyText"
       data-en="Marriage is an intimate sharing of life and love. There are four words that describe Matrimony:"
       data-tl="Ang kasal ay isang malapit na pagbabahagi ng buhay at pag-ibig. May apat na salita na naglalarawan sa Pag-aasawa:">
       Marriage is an intimate sharing of life and love. There are four words that describe Matrimony:
    </p>

    <h3 id="sacramentTitle" data-en="1. Sacrament" data-tl="1. Sakramento">1. Sacrament</h3>
    <p id="sacramentText" data-en="You do not receive the sacrament of matrimony — you become it. You and your spouse become a living symbol of God’s love."
       data-tl="Hindi mo natatanggap ang sakramento ng pag-aasawa — nagiging sakramento ka nito. Ikaw at ang iyong asawa ay nagiging buhay na simbolo ng pag-ibig ng Diyos.">
       You do not receive the sacrament of matrimony — you become it. You and your spouse become a living symbol of God’s love.
    </p>

    <h3 id="covenantTitle" data-en="2. Covenant" data-tl="2. Tipan">2. Covenant</h3>
    <p id="covenantText" data-en="A covenant is permanent and faithful, in good times and bad."
       data-tl="Ang tipan ay permanente at tapat, sa mabuti at masamang panahon.">
       A covenant is permanent and faithful, in good times and bad.
    </p>

    <h3 id="commitmentTitle" data-en="3. Commitment" data-tl="3. Pangako">3. Commitment</h3>
    <p id="commitmentText" data-en="You freely choose each other on your wedding day and every day that follows."
       data-tl="Malaya ninyong pinipili ang isa't isa sa araw ng inyong kasal at sa bawat araw na sumusunod.">
       You freely choose each other on your wedding day and every day that follows.
    </p>

    <h3 id="vocationTitle" data-en="4. Vocation" data-tl="4. Bokasyon">4. Vocation</h3>
    <p id="vocationText" data-en="God calls a couple to a life of mutual self-giving, showing Christ’s love through their union."
       data-tl="Tinatawagan ng Diyos ang mag-asawa sa isang buhay ng pagtutulungan at pagbibigay sa isa't isa, ipinapakita ang pag-ibig ni Kristo sa pamamagitan ng kanilang pagsasama.">
       God calls a couple to a life of mutual self-giving, showing Christ’s love through their union.
    </p>

    <div class="divider"></div>

    <h2 id="importanceTitle" data-en="Importance of Marriage" data-tl="Kahalagahan ng Kasal">Importance of Marriage</h2>
    <img src="../../stylesheets/assets/banner/wedding-ring.jpg" class="section-img">
    <p id="importanceText" data-en="Marriage reflects Christ’s love for the Church and provides a strong foundation for family life."
       data-tl="Ang Kasal ay sumasalamin sa pag-ibig ni Kristo sa Simbahan at nagbibigay ng matibay na pundasyon para sa buhay-pamilya.">
       Marriage reflects Christ’s love for the Church and provides a strong foundation for family life.
    </p>

    <div class="divider"></div>

    <h2 id="requirementsTitle" data-en="Requirements" data-tl="Mga Kinakailangan">Requirements</h2><br>

    <h3 id="documentsTitle" data-en="Documents" data-tl="Mga Dokumento">Documents</h3>
    <ul id="documentsList" class="info-list"
        data-en="• PSA Birth Certificate|• Baptismal Certificate|• Confirmation Certificate|• CENOMAR (Certificate of No Marriage)|• Government-issued Marriage License"
        data-tl="• PSA Birth Certificate|• Sertipiko ng Binyag|• Sertipiko ng Kumpil|• CENOMAR (Sertipiko ng Walang Kasal)|• Lisensya ng Kasal mula sa Gobyerno">
        <li>• PSA Birth Certificate</li>
        <li>• Baptismal Certificate</li>
        <li>• Confirmation Certificate</li>
        <li>• CENOMAR (Certificate of No Marriage)</li>
        <li>• Government-issued Marriage License</li>
    </ul><br>

    <h3 id="interviewTitle" data-en="Interviews & Seminars" data-tl="Panayam at Seminar">Interviews & Seminars</h3>
    <ul id="interviewList" class="info-list"
        data-en="• Canonical Interview – 1–2 months before the wedding|• Pre-Cana Seminar – Required marriage preparation course"
        data-tl="• Panayam ayon sa Canon – 1–2 buwan bago ang kasal|• Pre-Cana Seminar – Kailangan sa paghahanda ng kasal">
        <li>• Canonical Interview – 1–2 months before the wedding.</li>
        <li>• Pre-Cana Seminar – Required marriage preparation course.</li>
    </ul><br>

    <h3 id="churchReqTitle" data-en="Church Requirements" data-tl="Kailangan ng Simbahan">Church Requirements</h3>
    <ul id="churchReqList" class="info-list"
        data-en="• Wedding banns posted for 3 Sundays|• Confession recommended before the wedding"
        data-tl="• I-post ang wedding banns sa loob ng 3 Linggo|• Inirerekomenda ang Kumpisal bago ang kasal">
        <li>• Wedding banns posted for 3 Sundays</li>
        <li>• Confession recommended before the wedding</li>
    </ul><br>

    <div class="divider"></div>

    <h2 id="processTitle" data-en="What is the Process?" data-tl="Ano ang Proseso?">What is the Process?</h2>
    <ul id="processList" class="info-list"
        data-en="1. Submit documents 3–6 months before the wedding|2. Attend the Pre-Cana seminar|3. Secure marriage license from the LCR|4. Reserve wedding date at the parish|5. Prepare additional parish-required documents|6. Ceremony includes vows, ring exchange, and signing of the contract|7. Priest submits the marriage contract to LCR within 15 days"
        data-tl="1. Isumite ang mga dokumento 3–6 buwan bago ang kasal|2. Dumalo sa Pre-Cana seminar|3. Kunin ang lisensya ng kasal mula sa LCR|4. Ipareserba ang petsa ng kasal sa parokya|5. Ihanda ang karagdagang dokumento na kailangan ng parokya|6. Kasama sa seremonya ang panata, pagpapalitan ng singsing, at pagpirma ng kontrata|7. Isusumite ng pari ang kontrata ng kasal sa LCR sa loob ng 15 araw">
        <li>1. Submit documents 3–6 months before the wedding.</li>
        <li>2. Attend the Pre-Cana seminar.</li>
        <li>3. Secure marriage license from the LCR.</li>
        <li>4. Reserve wedding date at the parish.</li>
        <li>5. Prepare additional parish-required documents.</li>
        <li>6. Ceremony includes vows, ring exchange, and signing of the contract.</li>
        <li>7. Priest submits the marriage contract to LCR within 15 days.</li>
    </ul>

    <div class="divider"></div>

    <h2 id="scheduleTitle" data-en="Schedule" data-tl="Iskedyul">Schedule</h2>
    <p id="scheduleText" data-en="Weddings must be scheduled through the parish office. Please inquire early as dates fill quickly."
       data-tl="Ang mga kasal ay dapat i-iskedyul sa opisina ng parokya. Mangyaring magtanong nang maaga dahil mabilis mapuno ang mga petsa.">
       Weddings must be scheduled through the parish office. Please inquire early as dates fill quickly.
    </p>

    <div class="divider"></div>

    <h2 id="contactTitle" data-en="Contact" data-tl="Makipag-ugnayan">Contact</h2>
    <p id="contactText" data-en="Visit the parish office or call the church hotline for more details."
       data-tl="Bisitahin ang opisina ng parokya o tawagan ang hotline ng simbahan para sa karagdagang detalye.">
       Visit the parish office or call the church hotline for more details.
    </p>

</div>

<!-- LANGUAGE SCRIPT -->
<script>
document.addEventListener("DOMContentLoaded", () => {

    const toggle = document.getElementById("langSelect");
    if (!toggle) return;
function updateLanguage() {
        const lang = toggle.value;
    const elements = [
        "marriageTitle","marriageText",
        "matrimonyTitle","matrimonyText",
        "sacramentTitle","sacramentText",
        "covenantTitle","covenantText",
        "commitmentTitle","commitmentText",
        "vocationTitle","vocationText",
        "importanceTitle","importanceText",
        "requirementsTitle","documentsTitle","documentsList",
        "interviewTitle","interviewList",
        "churchReqTitle","churchReqList",
        "processTitle","processList",
        "scheduleTitle","scheduleText",
        "contactTitle","contactText"
    ];

     elements.forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            if (!el.dataset.en || !el.dataset.tl) return;

            if (el.tagName === "UL") {
                const items = (lang === "fil" ? el.dataset.tl : el.dataset.en).split("|");
                el.innerHTML = "";
                items.forEach(item => {
                    const li = document.createElement("li");
                    li.textContent = item.trim();
                    el.appendChild(li);
                });
            } else {
                el.textContent = (lang === "fil")
                    ? el.dataset.tl
                    : el.dataset.en;
            }
        });
    }

    toggle.addEventListener("change", updateLanguage);

    // ✅ Run once on page load
    updateLanguage();
});
</script>


<a href="#" class="reserve-btn" data-href="../members/wedding.php">Reserve Wedding</a>

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

</body>
</html>

