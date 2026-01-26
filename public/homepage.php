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

$user_id = $_SESSION['user_id'];
$givenname = $_SESSION['givenname'] ?? null; 


/* ===============================
   DATE & TIME SETUP
================================ */

/* Days of the week (used for table headers) */
$days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

/* Set Philippine timezone */
date_default_timezone_set('Asia/Manila');

/* Get current day and current time (HH:MM) */
$todayDay      = date('l');
$currentTimeHM = date('H:i');

/* ===============================
   MASS SCHEDULE DATA (PHP SIDE)
================================ */

/* Store mass schedules grouped by day */
$schedules = [];
$maxRows = 0;

/* Fetch all mass schedules from database */
$stmt = $conn->prepare("SELECT day, mass_time, priest_name FROM mass_schedule ORDER BY mass_time ASC");
$stmt->execute();
$result = $stmt->get_result();

/* Loop through each mass schedule record */
while($row = $result->fetch_assoc()) {
    $day     = $row['day'];
    $time    = $row['mass_time'];
    $priest  = $row['priest_name'];

    /* Group mass schedules per day */
    $schedules[$day][] = [
        'time'    => $time,
        'priest' => $priest
    ];

    /* Track maximum number of rows needed for table */
    $count = count($schedules[$day]);
    if($count > $maxRows) $maxRows = $count;
}

/* Re-calculate max rows for safety */
$maxRows = 0;
foreach ($schedules as $dayTimes) {
    $count = count($dayTimes);
    if ($count > $maxRows) {
        $maxRows = $count;
    }
}

// Get live stream URL
$live_stream_embed = '<iframe src="https://www.facebook.com/plugins/video.php?height=314&href=https%3A%2F%2Fwww.facebook.com%2FDioNova.StaKrusParish%2Fvideos%2F2356318521492438%2F&show_text=false&width=560&t=0" width="560" height="314" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowfullscreen="true" allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share" allowFullScreen="true"></iframe>'; // default
try {
    $stmt = $dbh->query("SELECT embed FROM live_streams ORDER BY created_at DESC LIMIT 1");
    $db_embed = $stmt->fetchColumn();
    if ($db_embed) {
        $live_stream_embed = $db_embed;
    }
} catch (Exception $e) {
    // keep default
}

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

// Pagination setup
$perPage = 3;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Get total announcements
$totalStmt = $dbh->query("SELECT COUNT(*) FROM announcements");
$totalAnnouncements = $totalStmt->fetchColumn();
$totalPages = ceil($totalAnnouncements / $perPage);

// Fetch current page announcements
$announcements = [];
try {
    $stmt = $dbh->prepare("SELECT * FROM announcements ORDER BY date_posted DESC LIMIT :offset, :perpage");
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':perpage', $perPage, PDO::PARAM_INT);
    $stmt->execute();
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $db_error = $e->getMessage();
}

?>

<?php
/* =========================================
   ONGOING (ACCEPTED) RESERVATIONS – HOMEPAGE
========================================= */

$ongoingReservations = [];

if (isset($_SESSION['user_id'])) {

    $sql = "
        SELECT 
            'Baptism' AS sacrament_type,
            CONCAT(child_firstname, ' ', child_lastname) AS recipient,
            date,
            time
        FROM baptisms
        WHERE reservation_user_id = :user_id
          AND status = 'Accepted'

        UNION ALL

        SELECT 
            'First Communion' AS sacrament_type,
            CONCAT(child_firstname, ' ', child_lastname) AS recipient,
            date,
            time
        FROM first_communion
        WHERE reservation_user_id = :user_id
          AND status = 'Accepted'

        UNION ALL

        SELECT 
            'Confirmation' AS sacrament_type,
            CONCAT(confirm_firstname, ' ', confirm_lastname) AS recipient,
            date,
            time
        FROM confirmations
        WHERE reservation_user_id = :user_id
          AND status = 'Accepted'

        UNION ALL

        SELECT 
            'Wedding' AS sacrament_type,
            CONCAT(groom_firstname, ' & ', bride_firstname) AS recipient,
            date,
            time
        FROM weddings
        WHERE reservation_user_id = :user_id
          AND status = 'Accepted'

        ORDER BY date ASC, time ASC
    ";

    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $ongoingReservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="../stylesheets/public/homepage.css" />
    <link rel="stylesheet" href="../stylesheets/public/ann_modal.css" />
    <link rel="stylesheet" href="../stylesheets/public/login_modal.css" />
</head>
<body>
    
<!-- expose login state to JS (needed for authModal)-->
<script>
    const IS_LOGGED_IN = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
</script>

<!-------------------- NAVBAR -------------------->
<header>
    <nav class="navbar">
        <ul class="nav-links">
            <li><a href="#"><img src="../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" /></a></li>
            <li><a href="homepage.php" class="active">Home</a></li>
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
            if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
                // USER NOT LOGGED IN → Show Login button
                echo '<li><a href="login.php" id="login-btn" style="color:white;"><i class="fa-regular fa-user"></i>   Log In</a></li>';
            } else {
                // USER LOGGED IN → Show Username + Dropdown
                ?>
            <p style="color:white; padding-left: 17px; padding-right: 17px;">|</p>
                <li class="li-dropdown">
                    <a href="#"><?= htmlspecialchars($givenname) ?></a>
                    <ul class="dropdown">
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

<!----------- ONGOING RESERVATION NOTIF ---------->
<?php if (!empty($ongoingReservations)): ?>
<section class="toast-container">
    <?php foreach ($ongoingReservations as $res): ?>
        <div class="toast">
            <div class="toast-header">
                🔔 Reminder
                <span class="toast-close" onclick="this.parentElement.parentElement.remove()">×</span>
            </div>

            <div class="toast-body">
                <h4><?= htmlspecialchars($res['recipient']); ?>'s <?= htmlspecialchars($res['sacrament_type']); ?></h4>
                <p>
                    <?= date("F j, Y", strtotime($res['date'])); ?> at
                    <?= date("h:i A", strtotime($res['time'])); ?>
                </p>
            </div>
        </div>
    <?php endforeach; ?>
</section>
<?php endif; ?>
<!--------- ONGOING RESERVATION NOTIF END --------->

<!----------------- SLIDESHOW ----------------->
<section class="slideshow">
    <section class="hero">
        <h1>Santa Krus Parish</h1>
        <h2>DIOCESE OF NOVALICHES</h2>
    </section>

    <div class="slides">
        <img src="assets/sim1.jpg" alt="Slide 1" class="active">
        <img src="assets/sim2.jpg" alt="Slide 2">
        <img src="assets/sim3.jpg" alt="Slide 3">
        <img src="assets/sim4.jpg" alt="Slide 4">
    </div>
</section>

<script>
const slides = document.querySelectorAll(".slides img");
let currentSlide = 0;

function showSlide(index) {
    slides.forEach((slide, i) => {
        slide.classList.remove("active");
        if (i === index) slide.classList.add("active");
    });
}

setInterval(() => {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
}, 6000);
</script>

<!---------------- MASS SCHED ----------------->
<section class="mass-sched">
    <div class="mass-layout">

        <!-- LEFT: MASS SCHEDULE -->
        <div class="mass-calendar">
            <h3 class="mass-sched-title">Mass Schedule</h3>
            <table>
                <thead>
                    <tr>
                        <?php foreach($days as $day): ?>
                            <th><?= $day ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody id="massTableBody">
                    <!-- AJAX will populate this -->
                </tbody>
            </table>
        </div>

        <!-- RIGHT: PARISH SERVICES -->
        <div class="parish-services">

            <h3>Parish Services</h3>

            <div class="ps-table-wrapper">
                <table class="ps-table">
                    <thead>
                        <tr>
                            <th>Sacraments</th>
                            <th>Services</th>
                        </tr>
                    </thead>

                    <tbody class="active">
                        <tr>
                            <td><a href="#" class="service-btn" data-href="members/anointing.php">Anointing of the Sick</a></td>
                            <td><a href="#" class="service-btn" data-href="members/certificate_request.php">Certificate Request</a></td>
                        </tr>
                        <tr>
                            <td><a href="#" class="service-btn" data-href="members/baptism.php">Baptism</a></td>
                            <td><a href="#" class="service-btn" data-href="members/celebratory.php">Celebratory Mass</a></td>
                        </tr>
                        <tr>
                            <td><a href="#" class="service-btn" data-href="members/first_comm.php">First Communion</a></td>
                            <td><a href="#" class="service-btn" data-href="members/funeral.php">Funeral Mass</a></td>
                        </tr>
                        <tr>
                            <td><a href="#" class="service-btn" data-href="members/confirmation.php">Confirmation</a></td>
                            <td><a href="#" class="service-btn" data-href="members/house_blessing.php">House Blessing</a></td>
                        </tr>
                        <tr>
                            <td><a href="#" class="service-btn" data-href="members/wedding.php">Wedding</a></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="office-hours">
                <b>Office Hours</b><br>
                9:00 AM – 6:00 PM (Tuesday to Sunday)
            </div>
        </div>
    </div>
</section>
<!-------------- MASS SCHED END --------------->

<!--------------- LIVE MASS ------------------->
<section class="livemass-header">
    <h2>Live Mass</h2>
    <!--<h4>December 7, 2025 • 7:30 AM</h4>-->
</section>
<section class="livemass-section">
    <div class="livemass-video">
<?= $live_stream_embed ?>
    </div>
</section>
<!------------- LIVE MASS END ----------------->

<!--------------- SACRAMENTS ------------------>
<section class="services-header">
    <h2>Sacraments</h2>
</section>
<section class="services-section">
    
    <div class="services-grid-1">
        <a href="sacraments/baptism_info.php">
            <div class="card baptism" style="background-image: url('assets/Baptism.jpg');">
                <div class="card-overlay">
                    <h3>Baptism</h3>
                    <p>Christian initiation sacrament</p>
                </div>
            </div>
        </a>

        <a href="sacraments/eucharist_info.php">
        <div class="card eucharist" style="background-image: url('assets/euharistia.jpg');">
            <div class="card-overlay">
                <h3>Eucharist</h3>
                <p>Receiving the body and blood of Christ</p>
            </div>
        </div>
        </a>


        <a href="sacraments/confirmation_info.php">
        <div class="card confirmation" style="background-image: url('assets/Confirmation.jpg');">
            <div class="card-overlay">
                <h3>Confirmation</h3>
                <p>Strengthening faith through the Holy Spirit</p>
            </div>
        </div>
        </a>


        <a href="sacraments/reconciliation_info.php">
        <div class="card reconciliation" style="background-image: url('assets/Reconciliatio.jpg');">
            <div class="card-overlay">
                <h3>Reconciliation</h3>
                <p>Confession and forgiveness of sins</p>
            </div>
        </div>
        </a>


    </div>

    <!-- row2 -------->
    <div class="services-grid-2">

        <a href="sacraments/annointing_info.php">
        <div class="card anointing" style="background-image: url('assets/Anointing of the Sick.jpg');">
            <div class="card-overlay">
                <h3>Anointing of the Sick</h3>
                <p>Healing and comfort for the ill</p>
            </div>
        </div>
        </a>

        <a href="sacraments/wedding_info.php">
            <div class="card marriage" style="background-image: url('assets/Marriage.jpg');">
                <div class="card-overlay">
                    <h3>Marriage</h3>
                    <p>Union of husband and wife in God</p>
                </div>
            </div>
        </a>

        <a href="sacraments/holyorders_info.php">
            <div class="card holyorders" style="background-image: url('assets/holy-orders.jpg');">
                <div class="card-overlay">
                    <h3>Holy Orders</h3>
                    <p>Ordination into the priesthood</p>
                </div>
            </div>
        </a>

    </div>
    
</section>
<!-------------- SACRAMENTS END --------------->

<!--------------- ANNOUNCEMENTS --------------->
<section class="announcements-header">
    <h2>News & Announcements</h2>
</section>

<section class="New">
    
    <div class="announcements-container">
        <?php
        $latestStmt = $dbh->query("SELECT * FROM announcements ORDER BY date_posted DESC LIMIT 4");
        $latestAnnouncements = $latestStmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <?php if (!empty($latestAnnouncements)) : ?>
            <?php foreach ($latestAnnouncements as $announcement) :

                $dbPath   = $announcement['image_path'];
                $realPath = "../private/stk/" . $dbPath;
                $webPath  = "../private/stk/" . $dbPath;

                $img = (!empty($dbPath) && file_exists($realPath))
                    ? $webPath
                    : "../stylesheets/assets/placeholder.jpg";
            ?>
                <div class="announcement-card">
                    <div class="card-image">
                        <img src="<?= htmlspecialchars($img); ?>" alt="Announcement Image">
                    </div>

                    <div class="card-content">
                        <h3><?= htmlspecialchars($announcement['title']); ?></h3>
                        <span class="date"><?= date("F j, Y", strtotime($announcement['date_posted'])); ?></span>
                        <p><?= htmlspecialchars(substr($announcement['content'], 0, 100)) . '...'; ?></p>
                        <a href="javascript:void(0)" 
                        class="read-more"
                        data-title="<?= htmlspecialchars($announcement['title']); ?>"
                        data-date="<?= date("F j, Y", strtotime($announcement['date_posted'])); ?>"
                        data-content="<?= htmlspecialchars($announcement['content']); ?>"
                        data-image="<?= htmlspecialchars($img); ?>"
                        >
                        Read More
                        </a>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>No announcements found.</p>
        <?php endif; ?>
    </div>

    <div class="see-more-container">
        <a href="announcements.php" class="see-more">view all...</a>
    </div>
</section>
<!------------ ANNOUNCEMENTS END -------------->

<!-- ANNOUNCEMENT MODAL -->
<div id="announcementModal" class="modal">
    <div class="modal-content">
        <span class="modal-close">&times;</span>

        <img id="modalImage" src="" alt="Announcement Image">

        <h2 id="modalTitle"></h2>
        <span class="date" id="modalDate"></span>
        <p id="modalContent"></p>
    </div>
</div>

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

        <form method="POST" action="login.php" class="auth-form">
            <input type="email" name="email" placeholder="Email" required>
            
            <div class="form-group password-field">
                <input type="password" name="password" placeholder="Password" id="password" required>
                <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i>
            </div>
            <button type="submit">LOG IN</button>
        </form>

        <div class="signup-text">
          Don’t have an account? <a href="register.php">Sign Up</a>
        </div>
    </div>
</div>

<!------- ANNOUNCEMENT MODAL SCRIPT ---------->
<script>
document.querySelectorAll('.read-more').forEach(button => {
    button.addEventListener('click', function () {

        document.getElementById('modalTitle').textContent = this.dataset.title;
        document.getElementById('modalDate').textContent = this.dataset.date;
        document.getElementById('modalContent').textContent = this.dataset.content;
        document.getElementById('modalImage').src = this.dataset.image;

        document.getElementById('announcementModal').style.display = 'flex';
        document.body.classList.add('modal-open');
    });
});

/* CLOSE MODAL */
document.querySelector('.modal-close').addEventListener('click', () => {
    document.getElementById('announcementModal').style.display = 'none';
    document.body.classList.remove('modal-open');
});

/* CLOSE WHEN CLICKING OUTSIDE CONTENT */
window.addEventListener('click', function (e) {
    const modal = document.getElementById('announcementModal');
    if (e.target === modal) {
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
    }
});
</script>
    
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

document.querySelectorAll('.service-btn').forEach(btn => {
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


<script>
let massData = null;

function formatTime(time) {
    const [h, m] = time.split(':');
    let hour = parseInt(h);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    hour = hour % 12 || 12;
    return hour + ':' + m + ' ' + ampm;
}

/* ===============================
   FETCH MASS SCHEDULE (USER VIEW)
================================ */
function fetchMassSchedule() {
    fetch('fetch_mass_schedule.php')
        .then(res => res.json())
        .then(data => {
            massData = data;

            const { days, schedules, maxRows } = data;
            const tbody = document.getElementById('massTableBody');
            tbody.innerHTML = '';

            for (let i = 0; i < maxRows; i++) {
                const tr = document.createElement('tr');

                days.forEach(day => {
                    const td = document.createElement('td');
                    const entry = schedules[day][i] || null;

                    if (entry) {
                        // STORE RANGE FOR LOGIC
                        td.dataset.day = day;
                        td.dataset.timeIn = entry.time_in;
                        td.dataset.timeOut = entry.time_out;
                        td.dataset.priest = entry.priest;

                        // ✅ DISPLAY ONLY TIME IN
                        td.innerHTML = formatTime(entry.time_in);
                    }

                    tr.appendChild(td);
                });

                tbody.appendChild(tr);
            }

            updateMassHighlights();
        });
}

/* ===============================
   LIVE HIGHLIGHT (RANGE-BASED)
================================ */
function updateMassHighlights() {
    const now = new Date();
    const currentDay = now.toLocaleDateString('en-US', { weekday: 'long' });
    const currentTime =
        now.getHours().toString().padStart(2,'0') + ':' +
        now.getMinutes().toString().padStart(2,'0');

    document.querySelectorAll('#massTableBody td').forEach(td => {
        const day = td.dataset.day;
        const timeIn = td.dataset.timeIn;
        const timeOut = td.dataset.timeOut;
        const priest = td.dataset.priest;

        if (!timeIn || !timeOut) return;

        if (
            day === currentDay &&
            currentTime >= timeIn &&
            currentTime <= timeOut
        ) {
            td.classList.add('highlight-mass');

            // Show priest only if active
            if (!td.innerHTML.includes(priest)) {
                td.innerHTML = formatTime(timeIn) + `<br><small>${priest}</small>`;
            }
        } else {
            td.classList.remove('highlight-mass');

            // Revert back to ONLY time in
            td.innerHTML = formatTime(timeIn);
        }
    });
}

/* Initial load */
fetchMassSchedule();

/* Update highlight every 2 seconds */
setInterval(updateMassHighlights, 2000);

/* Refresh schedule every 10 seconds */
setInterval(fetchMassSchedule, 10000);
</script>

</body>
</html>

