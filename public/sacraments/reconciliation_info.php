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
</head>
<body>

<div class="header-banner" style="background-image: url('../../stylesheets/assets/banner/Reconciliation.jpg');">
    <h1>Reconciliation</h1>
</div>

<div class="breadcrumb">
    <a href="../sacraments.php">Sacraments</a><span>Reconciliation</span>
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


<!-- INFORMATION SECTION -->
<div class="content">

    <!-- WHAT IS RECONCILIATION (WITH TOGGLE) -->
    <h2 style="display:flex; justify-content:space-between; align-items:center;">
        <span id="reconciliationTitle"
              data-en="What is Reconciliation?"
              data-tl="Ano ang Kumpisal?">
              What is Reconciliation?
        </span>
      <div class="language-switch">
    <label for="langSelect">Language:</label>
    <select id="langSelect">
        <option value="en">English</option>
        <option value="fil">Filipino</option>
    </select>
    </div>
    </h2>

    <img src="../../stylesheets/assets/banner/reconciliation-ceremony.jpg" alt="Reconciliation Ceremony" class="section-img">

    <p id="reconciliationText"
       data-en="Reconciliation, also known as Confession or Penance, is a sacrament in which a baptized Catholic confesses their sins to a priest and receives absolution. Through this sacrament, the faithful restore their relationship with God, receive healing, and return to a state of grace."
       data-tl="Ang Kumpisal, na kilala rin bilang Pagbabalik-loob o Penance, ay isang sakramento kung saan ang binyag na Katoliko ay inaamin ang kanilang mga kasalanan sa pari at tumatanggap ng kapatawaran. Sa pamamagitan ng sakramentong ito, naibabalik ng mananampalataya ang kanilang relasyon sa Diyos, nakatatanggap ng kagalingan, at bumabalik sa estado ng biyaya.">
        Reconciliation, also known as Confession or Penance, is a sacrament in which a baptized Catholic 
        confesses their sins to a priest and receives absolution. Through this sacrament, the faithful 
        restore their relationship with God, receive healing, and return to a state of grace.
    </p>

    <div class="divider"></div>

    <h2 id="importanceTitle"
        data-en="Why is Reconciliation Important?"
        data-tl="Bakit Mahalaga ang Kumpisal?">
        Why is Reconciliation Important?
    </h2>

    <img src="../../stylesheets/assets/banner/priest-confession.jpg" alt="Priest Confession" class="section-img">

    <p id="importanceText"
       data-en="Confession helps us grow spiritually, strengthens our conscience, and cleanses our soul from sin. It brings comfort, peace, and a renewed sense of purpose, helping us walk closer with God. It also teaches humility, discipline, and responsibility for our actions."
       data-tl="Ang Kumpisal ay tumutulong sa ating lumago espiritwal, pinapalakas ang ating budhi, at nililinis ang ating kaluluwa mula sa kasalanan. Nagdudulot ito ng kaginhawahan, kapayapaan, at bagong layunin, na tumutulong sa atin na lumapit sa Diyos. Itinuturo rin nito ang kababaang-loob, disiplina, at responsibilidad sa ating mga gawain.">
        Confession helps us grow spiritually, strengthens our conscience, and cleanses our soul from sin.  
        It brings comfort, peace, and a renewed sense of purpose, helping us walk closer with God.  
        It also teaches humility, discipline, and responsibility for our actions.
    </p>

    <div class="divider"></div>

    <h2 id="processTitle"
        data-en="How to Go to Confession (Process)"
        data-tl="Paano Pumunta sa Kumpisal (Proseso)">
        How to Go to Confession (Process)
    </h2>

    <p id="processText"
       data-en="The Sacrament of Reconciliation follows these steps:"
       data-tl="Ang Sakramento ng Kumpisal ay sumusunod sa mga hakbang na ito:">
        The Sacrament of Reconciliation follows these steps:
    </p>

    <ul id="processList" class="info-list"
        data-en="• 1. Examination of Conscience – Quietly reflect on your sins.|• 2. Confession – Tell your sins to the priest sincerely.|• 3. Penance – Receive a prayer or action to complete.|• 4. Act of Contrition – Express your sorrow to God.|• 5. Absolution – The priest forgives your sins in God's name."
        data-tl="• 1. Pagsusuri ng Budhi – Tahimik na pagninilay sa iyong mga kasalanan.|• 2. Pag-amin – Sabihin nang tapat ang iyong mga kasalanan sa pari.|• 3. Penance – Tumanggap ng panalangin o gawain na dapat gawin.|• 4. Akto ng Pagsisisi – Ipakita ang iyong pagsisisi sa Diyos.|• 5. Kapatawaran – Pinapatawad ng pari ang iyong mga kasalanan sa pangalan ng Diyos.">
        <li><strong>1. Examination of Conscience</strong> – Quietly reflect on your sins.</li>
        <li><strong>2. Confession</strong> – Tell your sins to the priest sincerely.</li>
        <li><strong>3. Penance</strong> – Receive a prayer or action to complete.</li>
        <li><strong>4. Act of Contrition</strong> – Express your sorrow to God.</li>
        <li><strong>5. Absolution</strong> – The priest forgives your sins in God's name.</li>
    </ul>

    <div class="divider"></div>

    <h2 id="whoTitle"
        data-en="Who Can Receive Reconciliation?"
        data-tl="Sino ang Maaaring Tumanggap ng Kumpisal?">
        Who Can Receive Reconciliation?
    </h2>

    <ul id="whoList" class="info-list"
        data-en="• All baptized Catholics|• Children who have received First Communion|• Adults preparing for Baptism (during RCIA)"
        data-tl="• Lahat ng binyag na Katoliko|• Mga bata na nakatanggap ng Unang Komunyon|• Mga matatanda na naghahanda para sa Binyag (sa panahon ng RCIA)">
        <li>All baptized Catholics</li>
        <li>Children who have received First Communion</li>
        <li>Adults preparing for Baptism (during RCIA)</li>
    </ul>

    <div class="divider"></div>

    <h2 id="requirementsTitle"
        data-en="Requirements"
        data-tl="Mga Kinakailangan">
        Requirements
    </h2>

    <ul id="requirementsList" class="info-list"
        data-en="• No documents needed|• A sincere heart and willingness to change|• Honest confession|• Completion of the assigned penance"
        data-tl="• Walang dokumentong kailangan|• Tapat na puso at kagustuhang magbago|• Tapat na pag-amin|• Pagtupad sa ipinagawang penance">
        <li>No documents needed</li>
        <li>A sincere heart and willingness to change</li>
        <li>Honest confession</li>
        <li>Completion of the assigned penance</li>
    </ul>

    <div class="divider"></div>

    <h2 id="scheduleTitle"
        data-en="Schedule"
        data-tl="Iskedyul">
        Schedule
    </h2>

    <p id="scheduleText"
       data-en="Confession schedules vary for each parish. Typically available:"
       data-tl="Nag-iiba ang iskedyul ng Kumpisal sa bawat parokya. Karaniwang makukuha sa:">
        Confession schedules vary for each parish.  
        Typically available:
    </p>

    <ul id="scheduleList" class="info-list"
        data-en="• Before or after Mass|• During parish Recollection / Lent / Advent|• By appointment at the parish office"
        data-tl="• Bago o pagkatapos ng Misa|• Sa panahon ng parokyal na Recollection / Kuwaresma / Adbiyento|• Sa pamamagitan ng appointment sa opisina ng parokya">
        <li>Before or after Mass</li>
        <li>During parish Recollection / Lent / Advent</li>
        <li>By appointment at the parish office</li>
    </ul>

    <div class="divider"></div>

    <h2 id="contactTitle"
        data-en="Contact"
        data-tl="Makipag-ugnayan">
        Contact
    </h2>

    <p id="contactText"
       data-en="For questions or to schedule a Reconciliation, please visit the parish office or call the official church hotline."
       data-tl="Para sa mga katanungan o upang mag-iskedyul ng Kumpisal, mangyaring bisitahin ang opisina ng parokya o tawagan ang opisyal na hotline ng simbahan.">
        For questions or to schedule a Reconciliation, please visit the parish office or call the official church hotline.
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
        "reconciliationTitle","reconciliationText",
        "importanceTitle","importanceText",
        "processTitle","processText","processList",
        "whoTitle","whoList",
        "requirementsTitle","requirementsList",
        "scheduleTitle","scheduleText","scheduleList",
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

