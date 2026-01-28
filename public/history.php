<?php
require_once 'connection.php';

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

session_start();
$user_name = $_SESSION['givenname'] ?? null; 

$user_id = $_SESSION['user_id'];

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Santa Krus Parish</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../stylesheets/public/user_footer_navbar.css" />
  <link rel="stylesheet" href="../stylesheets/public/history.css" />
</head>
<body>

<!-------------------- NAVBAR -------------------->
<header>
    <nav class="navbar">
        <ul class="nav-links">
            <li><a href="#"><img src="../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" /></a></li>
            <li><a href="homepage.php">Home</a></li>
            <li><a href="history.php" class="active">History</a></li>
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



  <main>
    <div class="parallax">
      <div class="parallax-shield"></div>
      <h1 class="parallax-title-h1">HISTORY</h1>
      <h2 class="parallax-title-h2">Santa Krus Parish is the first parish established under the Diocese of Novaliches, founded to serve the growing spiritual and sacramental needs of the faithful in Bagumbong. Since its formation, the parish has steadily grown into a vibrant church community dedicated to worship, service, and evangelization.</h2>
    </div>

    <!-- HISTORY SECTION -->
    <section class="history-section">
      <div class="history-content history-animate">
        <p>
          <b>Santa Krus Parish</b> of the Roman Catholic Diocese of Novaliches was established on May 8, 2003.
          It is located in Bagumbong, Caloocan City. The parish traces its humble beginnings to a small
          chapel built in 1940, fondly called the <em>“Bisita”</em> by local residents. This chapel was originally
          located at Bagumbong Kanto, the present-day site of Primark Bagumbong. In 1959, the Bisita was
          transferred to its current location, where the parish church now stands.
        </p>

        <p>
          For many years, the chapel served as a spiritual center for the community. Eventually, on May 8,
          2003, it was officially elevated to parish status under the Diocese of Novaliches, led by <b>Bishop Emeritus Teodoro Bacani Jr.</b>
           This marked a new chapter in the spiritual life of the people of
          Bagumbong.
        </p>

        <p>
          The Diocese of Novaliches, to which Santa Krus Parish belongs, was created by Pope John Paul II
          on December 7, 2002, through the Apostolic Constitution <em>Animarum Utilitati</em>, and was
          canonically erected on January 16, 2003. It was formed from the Archdiocese of Manila and covers
          the northern parts of Caloocan and Quezon City. As of September 2018, the diocese is composed of
          70 parishes and one mission area, grouped into twelve vicariates.
        </p>

        <p>
          Santa Krus Parish holds the special distinction of being the first parish established under the
          newly formed Diocese of Novaliches. It was created to meet the growing spiritual and sacramental
          needs of the faithful in Bagumbong, who were previously served by nearby parishes. Since its
          establishment, Santa Krus Parish has grown into a vibrant and welcoming church community,
          offering regular Masses, sacraments, and programs that strengthen faith and unity. <b style="color: #47210a;">With the help
          of committed priests and active laypeople, the parish continues to be a center of worship,
          service, and evangelization in the area.</b>
        </p>
      </div>
    </section>

    <div class="timeline">
      <h2 class="timeline-heading-h2">Santa Krus Parish</h2>
      <h3 class="timeline-heading-h3">Priest Historical Timeline</h2>


      <div class="container">
        <div class="content-left">
          <div class="text-box">
            <h4>Rev. Fr. James Nitollama</h4>
            <p>Parish Priest</p>
            <p>2018 - Present</p>
          </div>
        </div>
        <div class="divider"></div>
        <div class="content-right">
          <img src="../stylesheets/assets/history/FrNitollama.png" alt="Parish image" />
        </div>
      </div>

      <div class="container">
        <div class="content-left">
          <div class="text-box">
            <h4>Rev. Fr. Noel Tatoy</h4>
            <p>Parish Priest</p>
            <p>2018 - Present</p>
          </div>
        </div>
        <div class="divider"></div>
        <div class="content-right">
          <img src="../stylesheets/assets/history/FrTatoy.png" alt="Parish image" />
        </div>
      </div>

      <div class="container">
        <div class="content-left">
          <div class="text-box">
            <h4>Rev. Fr. Aldrin Lopez</h4>
            <p>Former Parish Priest</p>
            <p>2013 - 2018</p>
          </div>
        </div>
        <div class="divider"></div>
        <div class="content-right">
          <img src="../stylesheets/assets/history/FrLopez.png" alt="Parish image" />
        </div>
      </div>

      <div class="container">
        <div class="content-left">
          <div class="text-box">
            <h4>Rev. Fr. Wilfredo Villanueva, SOLT</h4>
            <p>Former Parish Priest</p>
            <p>2007 - 2013</p>
          </div>
        </div>
        <div class="divider"></div>
        <div class="content-right">
          <img src="../stylesheets/assets/history/FrVillanueva.png" alt="Parish image" />
        </div>
      </div>

      <div class="container">
        <div class="content-left">
          <div class="text-box">
            <h4>Rev. Fr. Elfren Bantog</h4>
            <p>Former Parish Priest</p>
            <p>2003 - 2000</p>
          </div>
        </div>
        <div class="divider"></div>
        <div class="content-right">
          <img src="../stylesheets/assets/history/FrBantog.png" alt="Parish image" />
        </div>
      </div>
    </div>

    <section class="mission-vision-section">
      <div class="mission-vision-bg"></div>
      <div class="mission-vision-text">
        <h3>Mission & Vision</h3>
        <p>
          We, the Church of Novaliches, are a Spirit-led Christ-centered community of disciples listening
          and responding to the needs and aspirations of people, journeying and evangelizing together
          with them, and with the Blessed Virgin Mary as an exemplary model toward the newness and
          fullness of life.
        </p>
      </div>
    </section>

    <!-- Scripts -->
    <script>
      const historyBlock = document.querySelector('.history-content.history-animate');

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('show');
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.2 });

      observer.observe(historyBlock);
    </script>

    <script>
      document.addEventListener("DOMContentLoaded", () => {
        const items = document.querySelectorAll(".timeline .container");

        const observer = new IntersectionObserver(entries => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.classList.add("show");
            }
          });
        }, { threshold: 0.3 });

        items.forEach(item => observer.observe(item));
      });
    </script>

    <script>
      document.addEventListener("DOMContentLoaded", () => {
        const bg = document.querySelector(".mission-vision-bg");

        const dropObserver = new IntersectionObserver(entries => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.classList.add("show-drop");
              dropObserver.unobserve(entry.target);
            }
          });
        }, { threshold: 0.3 });

        if (bg) dropObserver.observe(bg);
      });
    </script>

    <script>
      document.addEventListener("DOMContentLoaded", () => {
        const bg = document.querySelector(".mission-vision-bg");

        const zoomObserver = new IntersectionObserver(entries => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.classList.add("show-zoom");
              zoomObserver.unobserve(entry.target);
            }
          });
        }, { threshold: 0.3 });

        if (bg) zoomObserver.observe(bg);
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

  </main>
</body>
</html>
