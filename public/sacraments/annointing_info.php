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

<div class="header-banner" style="background-image: url('../../stylesheets/assets/banner/aots.jpg');">
    <h1>Anointing of the Sick</h1>
</div>

<div class="breadcrumb">
    <a href="../sacraments.php">Sacraments</a><span>Annointing of the Sick</span>
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
        <span id="anointingTitle"
              data-en="What is Anointing of the Sick?"
              data-tl="Ano ang Banal na Pagkakapanimdim sa May Sakit?">
              What is Anointing of the Sick?
        </span>
       <div class="language-switch">
    <label for="langSelect">Language:</label>
    <select id="langSelect">
        <option value="en">English</option>
        <option value="fil">Filipino</option>
    </select>
    </div>
    </h2> 

    <!-- NEW FULL DESCRIPTION INSERTED -->
    <p id="anointingText"
       data-en="The Sacrament of the Anointing of the Sick is the Church’s sacrament for those who are sick and aging, and is a sacred moment when we pray for God’s grace to strengthen someone who is suffering from any form of illness. An anointing Mass is generally offered during the seasons of Lent and Easter. Arrangements can be made for a priest to come to your home if you are homebound or too ill to come to daily Mass."
       data-tl="Ang Sakramento ng Banal na Pagkakapanimdim sa May Sakit ay sakramento ng Simbahan para sa mga may sakit at matanda, at isang banal na sandali kung saan nananalangin tayo para sa biyaya ng Diyos upang palakasin ang isang taong dumaranas ng anumang uri ng karamdaman. Karaniwang isinasagawa ang misa ng pagpapanimdim sa panahon ng Kuwaresma at Pasko ng Pagkabuhay. Maaaring humiling na pumunta ang pari sa inyong tahanan kung hindi makalabas o masyadong may sakit para dumalo sa araw-araw na misa.">
        The Sacrament of the Anointing of the Sick is the Church’s sacrament for those who are sick and aging,
        and is a sacred moment when we pray for God’s grace to strengthen someone who is suffering from any form
        of illness. An anointing Mass is generally offered during the seasons of Lent and Easter. Arrangements
        can be made for a priest to come to your home if you are homebound or too ill to come to daily Mass.
    </p>

    <!-- ORIGINAL TEXT KEPT -->
    <p id="anointingText2"
       data-en="This sacrament brings spiritual and sometimes physical healing to those who are seriously ill or elderly."
       data-tl="Ang sakramentong ito ay nagbibigay ng espiritwal at minsan pisikal na kagalingan sa mga seryosong may sakit o matatanda.">
        This sacrament brings spiritual and sometimes physical healing to those who are seriously ill or elderly.
    </p>

    <div class="divider"></div>

    <!-- IMPORTANCE SECTION WITH NEW INSERTED CONTENT -->
    <h2 id="importanceTitle"
        data-en="Importance"
        data-tl="Kahalagahan">
        Importance
    </h2>
    <img src="../../stylesheets/assets/banner/holy-oil.jpg" alt="Holy Oil" class="section-img">

    <p id="importanceText"
       data-en="The healing ministry of the Church follows that of Jesus and is concerned with the healing of the whole person—the body, mind, and spirit. Sometimes a person is cured of a bodily illness. Sometimes there is psychological or spiritual healing. What is certain is that the healing taking place strengthens a sick person and helps them to bear illness."
       data-tl="Ang ministeryo ng pagpapagaling ng Simbahan ay sumusunod sa halimbawa ni Hesus at nakatuon sa pagpapagaling ng buong tao—katawan, isipan, at espiritu. Minsan ay gumagaling ang isang tao sa karamdaman ng katawan. Minsan naman ay mayroong pagpapagaling sa isip o espiritu. Ang tiyak ay ang pagpapagaling ay nagpapalakas sa may sakit at tumutulong sa kanila na tiisin ang karamdaman.">
        The healing ministry of the Church follows that of Jesus and is concerned with the healing of the whole person—
        the body, mind, and spirit. Sometimes a person is cured of a bodily illness. Sometimes there is psychological
        or spiritual healing. What is certain is that the healing taking place strengthens a sick person and helps
        them to bear illness.
    </p>

    <!-- ORIGINAL IMPORTANCE TEXT KEPT -->
    <p id="importanceText2"
       data-en="Anointing of the Sick offers strength, peace, and courage in the face of illness or old age."
       data-tl="Ang Banal na Pagkakapanimdim sa May Sakit ay nagbibigay ng lakas, kapayapaan, at tapang sa harap ng karamdaman o pagtanda.">
        Anointing of the Sick offers strength, peace, and courage in the face of illness or old age.
    </p>

    <div class="divider"></div>

    <!-- NEW SECTION INSERTED -->
    <h2 id="whenTitle"
        data-en="When Should a Person Be Anointed?"
        data-tl="Kailan Dapat Panimdiman ang Isang Tao?">
        When Should a Person Be Anointed?
    </h2>
    <p id="whenText"
       data-en="The Rite of Anointing indicates there is no need to wait until a person is at the point of death. A careful judgment about the serious nature of the illness is sufficient. The Sacrament may be repeated if the sick person recovers after the anointing but becomes ill again, or if the illness worsens."
       data-tl="Ang Ritwal ng Pagkapanimdim ay nagpapahiwatig na hindi na kailangang maghintay hanggang ang isang tao ay nasa punto ng kamatayan. Ang maingat na pagpapasya tungkol sa seryosong kalagayan ng karamdaman ay sapat na. Maaaring ulitin ang Sakramento kung gumaling ang may sakit pagkatapos ng panimdim ngunit nagkasakit muli, o kung lumala ang karamdaman.">
        The Rite of Anointing indicates there is no need to wait until a person is at the point of death.
        A careful judgment about the serious nature of the illness is sufficient. The Sacrament may be repeated
        if the sick person recovers after the anointing but becomes ill again, or if the illness worsens.
    </p>

    <p id="whenText2"
       data-en="A person should be anointed before surgery when a dangerous illness is the reason for the intervention. Parishioners who are seriously ill in any way should request the Sacrament."
       data-tl="Dapat panimdiman ang isang tao bago ang operasyon kung ang mapanganib na karamdaman ang dahilan ng interbensyon. Dapat humiling ang mga parokyano na seryosong may sakit sa anumang paraan ng Sakramento.">
        A person should be anointed before surgery when a dangerous illness is the reason for the intervention.
        Parishioners who are seriously ill in any way should request the Sacrament.
    </p>

    <div class="divider"></div>

    <!-- ORIGINAL REQUIREMENTS KEPT -->
    <h2 id="requirementsTitle"
        data-en="Requirements"
        data-tl="Mga Kinakailangan">
        Requirements
    </h2>
    <ul id="requirementsList" class="info-list"
        data-en="• Baptized Catholic|• Serious illness or age-related infirmity|• Confession recommended before the sacrament"
        data-tl="• Binyag na Katoliko|• Seryosong karamdaman o kahinaan dahil sa pagtanda|• Inirerekomenda ang Kumpisal bago ang sakramento">
        <li>• Baptized Catholic</li>
        <li>• Serious illness or age-related infirmity</li>
        <li>• Confession recommended before the sacrament</li>
    </ul>

    <div class="divider"></div>

    <!-- ORIGINAL SCHEDULE -->
    <h2 id="scheduleTitle"
        data-en="Schedule"
        data-tl="Iskedyul">
        Schedule
    </h2>
    <p id="scheduleText"
       data-en="Available upon request. Contact the parish office to arrange a priest."
       data-tl="Makukuha kapag hiniling. Makipag-ugnayan sa opisina ng parokya upang ayusin ang pagdating ng pari.">
        Available upon request. Contact the parish office to arrange a priest.
    </p>

    <div class="divider"></div>

    <!-- ORIGINAL CONTACT -->
    <h2 id="contactTitle"
        data-en="Contact"
        data-tl="Makipag-ugnayan">
        Contact
    </h2>
    <p id="contactText"
       data-en="For inquiries, visit the parish office or call the church hotline."
       data-tl="Para sa mga katanungan, bisitahin ang opisina ng parokya o tawagan ang hotline ng simbahan.">
        For inquiries, visit the parish office or call the church hotline.
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
        "anointingTitle","anointingText","anointingText2",
        "importanceTitle","importanceText","importanceText2",
        "whenTitle","whenText","whenText2",
        "requirementsTitle","requirementsList",
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

<a href="#" class="reserve-btn" data-href="../members/anointing.php">Request Anointing of Sick</a>

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

