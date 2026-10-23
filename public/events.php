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

// Fetch all events by date
$events = $dbh->query("SELECT * FROM events ORDER BY date ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Santa Krus Parish</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../stylesheets/public/user_footer_navbar.css" />
    <link rel="stylesheet" href="../stylesheets/public/events.css" />
    <link rel="stylesheet" href="../stylesheets/public/ann_modal.css" />
</head>
<body>

<!-------------------- NAVBAR -------------------->
<header>
    <nav class="navbar">
        <ul class="nav-links">
            <li><a href="#"><img src="../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" /></a></li>
            <li><a href="homepage.php">Home</a></li>
            <li><a href="history.php">History</a></li>
            <li><a href="events.php" class="active">News & Events</a></li>
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

<div class="hero-image" role="img" aria-label="Interior of church">
  <img src="../stylesheets/assets/bannernight.jpg" alt="Church Exterior" />
</div>

    <script>
    const eventDates = <?= json_encode(array_column($events, 'date')); ?>;
    </script>

    <!-- ======================= INTERACTIVE CALENDAR ======================= -->
    <div class="events-wrapper container">
        <div class="calendar-card">
        <div class="cal-header">
            <button class="cal-btn" onclick="prevMonth()">&#8249;</button>
            <h2 id="cal-title"></h2>
            <button class="cal-btn" onclick="nextMonth()">&#8250;</button>
        </div>
        <div class="cal-grid">

            <!-- WEEKDAY LABELS -->
            <div class="cal-weekday">Su</div>
            <div class="cal-weekday">Mo</div>
            <div class="cal-weekday">Tu</div>
            <div class="cal-weekday">We</div>
            <div class="cal-weekday">Th</div>
            <div class="cal-weekday">Fr</div>
            <div class="cal-weekday">Sa</div>

            <!-- DAYS GRID (JS will fill this) -->
            <div id="cal-days"></div>

        </div>
    </div>
        <div class="event-details-card">
            <h3 id="event-panel-title">Select a date</h3>
            <p id="event-panel-desc">Click any highlighted day to see details here.</p>

            <div id="event-list"></div>
        </div>
    </div>

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

  
<script>
let currentDate = new Date();

// DOM elements
const calTitle = document.getElementById("cal-title");
const calDays = document.getElementById("cal-days");
const panelTitle = document.getElementById("event-panel-title");
const panelDesc = document.getElementById("event-panel-desc");
const eventList = document.getElementById("event-list");
const eventText = document.getElementById("event-text");

/* -------------------------
   Render Real Calendar
-------------------------- */
function renderCalendar() {
    calDays.innerHTML = "";

    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();

    // Month Name + Year
    calTitle.innerText = currentDate.toLocaleString("default", {
        month: "long",
        year: "numeric"
    });

    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const firstWeekDay = firstDay.getDay();

    // Empty placeholders
    for (let i = 0; i < firstWeekDay; i++) {
        const blank = document.createElement("div");
        blank.classList.add("cal-day", "empty");
        calDays.appendChild(blank);
    }

    // Actual days
    for (let day = 1; day <= lastDay.getDate(); day++) {
        const div = document.createElement("div");
        div.classList.add("cal-day");
        div.innerText = day;

        const fullDate = `${year}-${String(month+1).padStart(2,"0")}-${String(day).padStart(2,"0")}`;
        div.dataset.date = fullDate;

        // ✅ HIGHLIGHT IF THIS DATE HAS AN EVENT
        if (eventDates.includes(fullDate)) {
            div.classList.add("has-event");
        }
        
        div.onclick = () => selectDate(fullDate, div);

        calDays.appendChild(div);
    }
}

/* -------------------------
   Day Click Handler
-------------------------- */
function selectDate(date, element) {
    // Remove previous selection
    document.querySelectorAll(".cal-day").forEach(d => d.classList.remove("selected"));
    element.classList.add("selected");

    // Format date as text
    const [year, month, day] = date.split("-");
    const formattedDate = new Date(year, month - 1, day).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric"
    });

    panelTitle.innerText = `Events for ${formattedDate}`;
    panelDesc.innerText = "";

    loadEvents(date);
}


/* -------------------------
   Load Events
-------------------------- */
async function loadEvents(date) {
    const res = await fetch(`event-handle.php?date=${date}`);
    const data = await res.json();

    eventList.innerHTML = "";

    if (data.length === 0) {
        eventList.innerHTML = "<p style='color:#777;'>No events for this date.</p>";
        return;
    }

    data.forEach(ev => {
        const item = document.createElement("div");
        item.className = "event-item";
        item.innerHTML = `
            <span>${ev.text}</span>
        `
        eventList.appendChild(item);
    });
}
/* -------------------------
   Month Navigation
-------------------------- */
function prevMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
}

function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
}

// Initialize Calendar
renderCalendar();

// Automatically select today's date
const today = new Date();
const todayStr = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,"0")}-${String(today.getDate()).padStart(2,"0")}`;
const todayElem = document.querySelector(`.cal-day[data-date='${todayStr}']`);
if (todayElem) {
    selectDate(todayStr, todayElem);
}

</script>

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


</body>
</html>
