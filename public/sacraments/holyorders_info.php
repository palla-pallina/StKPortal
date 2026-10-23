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
    <link rel="stylesheet" href="../../stylesheets/public/user_footer_navbar.css" />
    <link rel="stylesheet" href="../../stylesheets/public/sacrament_info.css" />
</head>
<body>

<div class="header-banner" style="background-image: url('../../stylesheets/assets/banner/holyorders.png');">
    <h1>Holy Orders</h1>
</div>

<div class="breadcrumb">
    <a href="../sacraments.php">Sacraments</a><span>Holy Orders</span>
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


<!-- INFORMATION SECTION -->
<div class="content">

    <!-- TITLE SECTION WITH TRANSLATION TOGGLE -->
    <h2 style="display:flex; justify-content:space-between; align-items:center;">
        <span id="holyOrdersTitle"
              data-en="What is Holy Orders?"
              data-tl="Ano ang Banal na Orden?">
              What is Holy Orders?
        </span>
        <div class="language-switch">
    <label for="langSelect">Language:</label>
    <select id="langSelect">
        <option value="en">English</option>
        <option value="fil">Filipino</option>
    </select>
    </div>
    </h2> 

    <img src="../../stylesheets/assets/banner/holyorders-ceremony..jpg" alt="Holy Orders Ceremony" class="section-img">

    <p id="holyOrdersText1"
       data-en="The Sacrament of Holy Orders is the sacrament of service in which a baptized man is ordained as a Bishop, Priest, or Deacon. Through this sacrament, the mission of Jesus continues in the Church. Ordained clergy are entrusted to teach, sanctify, and guide the Christian community, acting as visible signs of Christ’s presence."
       data-tl="Ang Sakramento ng Banal na Orden ay sakramento ng paglilingkod kung saan ang isang binyagang lalaki ay pinapari bilang Obispo, Pari, o Diakono. Sa pamamagitan ng sakramentong ito, nagpapatuloy ang misyon ni Hesus sa Simbahan. Ang mga ordinadong klero ay pinagkakatiwalaang magturo, magpabanal, at gumabay sa komunidad ng Kristiyano, bilang nakikitang tanda ng presensya ni Kristo.">
       The Sacrament of Holy Orders is the sacrament of service in which a baptized man is ordained as a Bishop, Priest, or Deacon. Through this sacrament, the mission of Jesus continues in the Church. Ordained clergy are entrusted to teach, sanctify, and guide the Christian community, acting as visible signs of Christ’s presence.
    </p>

    <p id="holyOrdersText2"
       data-en="From the time of the Apostles, the Church has passed down this sacred ministry. The Apostles received the Holy Spirit at Pentecost and were sent to lead the Church, proclaim the Gospel, and administer the Sacraments. Today's Bishops are direct successors of the Apostles through an unbroken line called Apostolic Succession."
       data-tl="Mula pa noong panahon ng mga Apostol, ipinasa ng Simbahan ang sagradong ministeryong ito. Natanggap ng mga Apostol ang Banal na Espiritu sa Pentecostes at ipinadala upang pamunuan ang Simbahan, ipahayag ang Ebanghelyo, at ipatupad ang mga Sakramento. Ang mga Obispo ngayon ay direktang kahalili ng mga Apostol sa pamamagitan ng tuloy-tuloy na linya na tinatawag na Apostolic Succession.">
       From the time of the Apostles, the Church has passed down this sacred ministry. The Apostles received the Holy Spirit at Pentecost and were sent to lead the Church, proclaim the Gospel, and administer the Sacraments. Today's Bishops are direct successors of the Apostles through an unbroken line called Apostolic Succession.
    </p>

    <div class="divider"></div>

    <h2 id="levelsTitle" data-en="Levels of Ordination" data-tl="Mga Antas ng Orden">Levels of Ordination</h2>
    <img src="../../stylesheets/assets/banner/ordination.png" alt="Ordination" class="section-img">

    <h3 id="deaconTitle" data-en="Deacon" data-tl="Diakono">Deacon</h3>
    <p id="deaconText" data-en="Deacons assist priests and bishops in the Mass, proclaim the Gospel, preach, baptize, witness marriages, and serve the poor. Permanent Deacons may be married before ordination."
       data-tl="Ang mga Diakono ay tumutulong sa mga pari at obispo sa Misa, ipinapahayag ang Ebanghelyo, nangangaral, nagbabinyag, saksi sa kasal, at naglilingkod sa mga mahihirap. Ang mga permanenteng Diakono ay maaaring may asawa bago ang ordinasyon.">
       Deacons assist priests and bishops in the Mass, proclaim the Gospel, preach, baptize, witness marriages, and serve the poor. Permanent Deacons may be married before ordination.
    </p>

    <h3 id="priestTitle" data-en="Priest" data-tl="Pari">Priest</h3>
    <p id="priestText" data-en="Priests celebrate Mass, hear confessions, anoint the sick, and provide spiritual guidance. They shepherd parish communities and continue the mission of Christ by teaching and serving."
       data-tl="Ang mga Pari ay nagdiriwang ng Misa, nakikinig ng Kumpisal, nagpapa-ointment sa may sakit, at nagbibigay ng gabay espiritwal. Pinangangasiwaan nila ang mga komunidad ng parokya at ipinagpapatuloy ang misyon ni Kristo sa pamamagitan ng pagtuturo at paglilingkod.">
       Priests celebrate Mass, hear confessions, anoint the sick, and provide spiritual guidance. They shepherd parish communities and continue the mission of Christ by teaching and serving.
    </p>

    <h3 id="bishopTitle" data-en="Bishop" data-tl="Obispo">Bishop</h3>
    <p id="bishopText" data-en="Bishops receive the fullness of the Sacrament of Holy Orders. They oversee dioceses, ordain priests and deacons, teach the faith, and ensure unity in the Church alongside the Pope."
       data-tl="Natatanggap ng mga Obispo ang kabuuan ng Sakramento ng Banal na Orden. Pinangangasiwaan nila ang mga diyosesis, pinapari ang mga pari at diakono, nagtuturo ng pananampalataya, at tinitiyak ang pagkakaisa sa Simbahan kasama ang Papa.">
       Bishops receive the fullness of the Sacrament of Holy Orders. They oversee dioceses, ordain priests and deacons, teach the faith, and ensure unity in the Church alongside the Pope.
    </p>

    <div class="divider"></div>

    <h2 id="requirementsTitle" data-en="Requirements" data-tl="Mga Kinakailangan">Requirements</h2>
    <ul id="requirementsList" class="info-list"
        data-en="Baptized Catholic male|2x2 Photo|Must complete elementary school, high school, or college.|Years of seminary formation and spiritual discernment|Strong prayer life and commitment to Church mission|Psychological, pastoral, and academic preparation|For priests and bishops: a promise of lifelong celibacy"
        data-tl="Binyagang Katolikong lalaki|2x2 Larawan|Dapat nakatapos ng elementarya, mataas na paaralan, o kolehiyo.|Taon ng formasyon sa seminaryo at espiritwal na pagkilala|Malakas na buhay-pananalangin at pagtatalaga sa misyon ng Simbahan|Paghahanda sa sikolohikal, pastoral, at akademiko|Para sa mga pari at obispo: pangakong panghabambuhay na walang asawa">
        <li>Baptized Catholic male</li>
        <li>2x2 Photo</li>
        <li>Must complete elementary school, high school, or college.</li>
        <li>Years of seminary formation and spiritual discernment</li>
        <li>Strong prayer life and commitment to Church mission</li>
        <li>Psychological, pastoral, and academic preparation</li>
        <li>For priests and bishops: a promise of lifelong celibacy</li>
    </ul>

    <div class="divider"></div>

    <h2 id="formationTitle" data-en="The Formation Process" data-tl="Proseso ng Formasyon">The Formation Process</h2>
    <p id="formationText" data-en="Vocational discernment includes prayer, spiritual direction, parish involvement, and formal seminary training. Before ordination, the candidate makes a promise of obedience and receives the laying on of hands from a bishop, invoking the Holy Spirit."
       data-tl="Kasama sa pagbibigay ng bokasyon ang panalangin, espiritwal na paggabay, pakikilahok sa parokya, at pormal na pagsasanay sa seminaryo. Bago ang ordinasyon, ang kandidato ay nangangako ng pagsunod at tumatanggap ng pagpapatong ng kamay mula sa obispo, nananawagan sa Banal na Espiritu.">
       Vocational discernment includes prayer, spiritual direction, parish involvement, and formal seminary training. Before ordination, the candidate makes a promise of obedience and receives the laying on of hands from a bishop, invoking the Holy Spirit.
    </p>

    <div class="divider"></div>

    <h2 id="scheduleTitle" data-en="Schedule" data-tl="Iskedyul">Schedule</h2>
    <p id="scheduleText" data-en="Ordinations are celebrated during special Masses organized by the diocese. Those interested should inquire through the parish or diocesan vocations office."
       data-tl="Ang mga ordinasyon ay ipinagdiriwang sa mga espesyal na Misa na inorganisa ng diyosesis. Ang mga interesado ay dapat magtanong sa parokya o opisina ng bokasyon ng diyosesis.">
       Ordinations are celebrated during special Masses organized by the diocese. Those interested should inquire through the parish or diocesan vocations office.
    </p>

    <div class="divider"></div>

    <h2 id="contactTitle" data-en="Contact" data-tl="Makipag-ugnayan">Contact</h2>
    <p id="contactText" data-en="If you feel called to a vocation or want guidance, please visit the parish office for more information or speak with a priest."
       data-tl="Kung nararamdaman mong tinawag sa isang bokasyon o nais ng gabay, bisitahin ang opisina ng parokya para sa karagdagang impormasyon o kausapin ang pari.">
       If you feel called to a vocation or want guidance, please visit the parish office for more information or speak with a priest.
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
        "holyOrdersTitle","holyOrdersText1","holyOrdersText2",
        "levelsTitle","deaconTitle","deaconText",
        "priestTitle","priestText","bishopTitle","bishopText",
        "requirementsTitle","requirementsList",
        "formationTitle","formationText",
        "scheduleTitle","scheduleText","contactTitle","contactText"
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

