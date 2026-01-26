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

<div class="header-banner" style="background-image: url('../../stylesheets/assets/banner/eucharist.jpg');">
    <h1>Eucharist</h1>
</div>

<div class="breadcrumb">
    <a href="../sacraments.php">Sacraments</a><span>Eucharist</span>
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


<<div class="content">

    <!-- WHAT IS HOLY EUCHARIST (WITH TOGGLE) -->
    <h2 style="display:flex; justify-content:space-between; align-items:center;">
        <span id="eucharistTitle"
              data-en="What is the Holy Eucharist?"
              data-tl="Ano ang Banal na Eukaristiya?">
              What is the Holy Eucharist?
        </span>
        
         <div class="language-switch">
    <label for="langSelect">Language:</label>
    <select id="langSelect">
        <option value="en">English</option>
        <option value="fil">Filipino</option>
    </select>
    </div>
    </h2>

    <img src="../../stylesheets/assets/banner/eucharist1.jpg" alt="Holy Eucharist" class="section-img">

    <p id="eucharistText"
       data-en="The Holy Eucharist is the sacrament where Jesus Christ is truly present—Body, Blood, Soul, and Divinity—under the appearance of bread and wine. It is the center of Catholic worship and the summit of the Christian life."
       data-tl="Ang Banal na Eukaristiya ay ang sakramento kung saan tunay na naroroon si Jesucristo—Katawan, Dugo, Kaluluwa, at Diyos—sa anyo ng tinapay at alak. Ito ang sentro ng pagsamba ng mga Katoliko at sukdulan ng buhay Kristiyano.">
        The Holy Eucharist is the sacrament where Jesus Christ is truly present—Body, Blood, Soul, and Divinity—under the appearance of bread and wine. It is the center of Catholic worship and the summit of the Christian life.
    </p>

    <div class="divider"></div>

    <h2 id="dailyMassTitle"
        data-en="Daily Communion — Why Attend Mass Daily?"
        data-tl="Araw-araw na Komunyon — Bakit Dumaralo sa Misa Araw-araw?">
        Daily Communion — Why Attend Mass Daily?
    </h2>
    <img src="../../stylesheets/assets/banner/daily-mass.jpg" alt="Daily Mass" class="section-img">

    <p id="dailyMassText"
       data-en="The Church encourages daily communion because:"
       data-tl="Hinihikayat ng Simbahan ang araw-araw na komunyon dahil:">
        The Church encourages daily communion because:
    </p>

    <ul id="dailyMassList" class="info-list"
        data-en="• The Mass is the source and summit of the Catholic faith.|• Daily Mass allows us to hear the Gospel and reflect on the homily.|• Receiving Jesus in the Eucharist strengthens us spiritually.|• Praising God each morning sets the tone for the day.|• The Mass is ongoing spiritual formation.|• It brings us closer to God daily."
        data-tl="• Ang Misa ang pinagmumulan at sukdulan ng pananampalatayang Katoliko.|• Pinapakinggan natin ang Ebanghelyo at pinagninilayan ang homiliya sa araw-araw na Misa.|• Ang pagtanggap kay Hesus sa Eukaristiya ay nagpapatibay sa atin espiritwal.|• Ang pagpupuri sa Diyos tuwing umaga ay nagtatakda ng tono para sa araw.|• Ang Misa ay patuloy na espiritwal na paghubog.|• Pinapalapit tayo nito sa Diyos araw-araw.">
        <li>The Mass is the source and summit of the Catholic faith.</li>
        <li>Daily Mass allows us to hear the Gospel and reflect on the homily.</li>
        <li>Receiving Jesus in the Eucharist strengthens us spiritually.</li>
        <li>Praising God each morning sets the tone for the day.</li>
        <li>The Mass is ongoing spiritual formation.</li>
        <li>It brings us closer to God daily.</li>
    </ul>

    <div class="divider"></div>


    <h2>What are the dates for Daily Communion?</h2>
        <div class="mass-calendar">
            <table>
                <thead>
                    <tr>
                        <th>Monday</th>
                        <th>Tuesday</th>
                        <th>Wednesday</th>
                        <th>Thursday</th>
                        <th>Friday</th>
                        <th>Saturday</th>
                        <th>Sunday</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>6:00 AM</td>
                        <td>6:00 AM</td>
                        <td>6:00 AM</td>
                        <td>6:00 AM</td>
                        <td>6:00 AM</td>
                        <td>6:00 AM</td>
                        <td>5:30 AM</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>6:00 PM</td>
                        <td>6:00 PM</td>
                        <td>6:00 PM</td>
                        <td>6:00 PM</td>
                        <td>6:00 PM</td>
                        <td>7:30 AM</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>10:30 AM</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>3:30 PM</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>5:30 PM</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </p>

    <div class="divider"></div>

 <h2 id="firstCommunionTitle"
        data-en="First Communion"
        data-tl="Unang Komunyon">
        First Communion
    </h2>
    <img src="../../stylesheets/assets/banner/first-communion.jpg" alt="First Communion" class="section-img">

    <p id="firstCommunionText1"
       data-en="First Holy Communion is a special sacrament where children receive the Eucharist for the first time. A baptized child is now welcomed to the Lord’s table and participates fully in the Mass."
       data-tl="Ang Unang Banal na Komunyon ay isang espesyal na sakramento kung saan unang tumatanggap ng Eukaristiya ang mga bata. Ang binyag na bata ay tinatanggap na sa hapag ng Panginoon at ganap na nakikilahok sa Misa.">
        First Holy Communion is a special sacrament where children receive the Eucharist for the first time.  
        A baptized child is now welcomed to the Lord’s table and participates fully in the Mass.
    </p>

    <p id="firstCommunionText2"
       data-en="The ceremony takes place during a regular Catholic Mass (60–90 minutes). Children receive Holy Communion accompanied by their parents and godparents."
       data-tl="Ang seremonya ay ginaganap sa isang regular na Misa ng Katoliko (60–90 minuto). Ang mga bata ay tumatanggap ng Banal na Komunyon kasabay ng kanilang mga magulang at ninong/ninang.">
        The ceremony takes place during a regular Catholic Mass (60–90 minutes).  
        Children receive Holy Communion accompanied by their parents and godparents.
    </p>

    <div class="divider"></div>

    <h2 id="listingTitle"
        data-en="What are the dates for listing for First Communion?"
        data-tl="Ano ang mga petsa para sa pagpaparehistro para sa Unang Komunyon?">
        What are the dates for listing for First Communion?
    </h2>
    <p id="listingText"
       data-en="Listing usually opens from January to March, depending on the parish schedule."
       data-tl="Karaniwang nagsisimula ang pagpaparehistro mula Enero hanggang Marso, depende sa iskedyul ng parokya.">
        Listing usually opens from <strong>January to March</strong>, depending on the parish schedule.
    </p>

    <div class="divider"></div>

    <h2 id="documentsTitle"
        data-en="What are the required documents?"
        data-tl="Ano ang mga kinakailangang dokumento?">
        What are the required documents?
    </h2>

    <ul id="documentsList" class="info-list"
        data-en="• Baptismal Certificate of the child|• Parents’ Marriage Certificate (if applicable)|• Certificate of Residency (if required)|• 2×2 ID picture of the child|• Completed parish registration form"
        data-tl="• Sertipiko ng Binyag ng bata|• Sertipiko ng Kasal ng Magulang (kung naaangkop)|• Sertipiko ng Paninirahan (kung kinakailangan)|• 2×2 na larawan ng bata|• Kumpletong form ng pagpaparehistro sa parokya">
        <li>• Baptismal Certificate of the child</li>
        <li>• Parents’ Marriage Certificate (if applicable)</li>
        <li>• Certificate of Residency (if required)</li>
        <li>• 2×2 ID picture of the child</li>
        <li>• Completed parish registration form</li>
    </ul>

</div>

<!-- LANGUAGE SCRIPT -->
<script>
document.addEventListener("DOMContentLoaded", () => {

    const toggle = document.getElementById("langSelect");
    if (!toggle) return;
function updateLanguage() {
        const lang = toggle.value;
    const elements = [
        "eucharistTitle","eucharistText",
        "dailyMassTitle","dailyMassText","dailyMassList",
        "firstCommunionTitle","firstCommunionText1","firstCommunionText2",
        "listingTitle","listingText",
        "documentsTitle","documentsList"
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


<a href="#" class="reserve-btn" data-href="../members/first_comm.php">Reserve First Communion</a>

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

