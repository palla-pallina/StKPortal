<?php
// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "test_tree_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();

// Restrict access to priests only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Priest') {
    header("Location: ../../public/login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$admin_name = $_SESSION['givenname'] ?? null; 
$hideSidebar = false;

/*--------- NOTIFICATION HANDLING START
- for notification icon at navbar
- retrieves all notifications belonging to logged in user */
$stmt = $conn->prepare("SELECT * FROM admin_notifications ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$unreadCount = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM admin_notifications WHERE status = 'Unread'");
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $unreadCount = $result['cnt'] ?? 0;
    $stmt->close();
}
/*--------- NOTIFICATION HANDLING END */

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />
<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="../../stylesheets/private/dashboard.css" />
<link rel="stylesheet" href="../../stylesheets/private/dashboard_navbar.css" />
<link rel="icon" type="image/x-icon" href="../../stylesheets/assets/favicon.ico">
<title>StKPortal | Dashboard</title>

</head>

<body>

<section id="content">

<!-------------------------- SIDEBAR -------------------------->
<nav>
<a href="dashboard.php"><img src="../santa krus logo.png" alt="Logo" class="nav-logo" /></a>
<div id="datetime"></div>
<div class="spacer"></div>
<ul>

    <li class="li-dropdown">
      <a href="#"><span class="greeting"><?php echo htmlspecialchars($admin_name); ?></span></a>
      <ul class="dropdown">
          <li><a href="logout.php" class="../logout">Logout</a></li>
      </ul>
  </li>
</ul>
</nav>
<!------------------------- SIDEBAR END ------------------------>

<section class="hero-banner">
    <div class="slides-container">
        <img src="../../stylesheets/assets/ad_dash/churchStk.png" alt="Banner" class="banner-image">
        <img src="../../stylesheets/assets/ad_dash/churchStk2.png" alt="Banner 1" class="banner-image">
        <img src="../../stylesheets/assets/ad_dash/churchStk3.png" alt="Banner 2" class="banner-image">
        <img src="../../stylesheets/assets/ad_dash/churchStk4.png" alt="Banner 3" class="banner-image">
        <img src="../../stylesheets/assets/ad_dash/churchStk5.png" alt="Banner 4" class="banner-image">
        <img src="../../stylesheets/assets/ad_dash/churchStk6.png" alt="Banner 2" class="banner-image">
    </div>
    <div class="overlay"></div>
    <div class="hero-content">
        <h1 id="greeting">
        Welcome, <strong><?php echo htmlspecialchars($admin_name); ?></strong>!
        </h1>
        <p>Glad to see you again!</p>
    </div>

    <div class="verse-content">
    <p class="verse-text" id="verseText"></p>
    <span class="verse-ref" id="verseRef"></span>
</div>
</section>


<div class="main-content">

<div class="dashboard-grid">

    <a href="priest_prayer.php">
    <div class="col-4">
        <div class="action-card">
        <i class="bi bi-envelope"></i>
        <h6>PRAYER REQUESTS</h6>
        <p>Read online submitted prayer requests.</p>
        </div>
    </div>
    </a>

    <a href="parish_calendar.php">
    <div class="col-4">
        <div class="action-card">
        <i class="bi bi-inboxes-fill"></i>
        <h6>PARISH CALENDAR</h6>
        <p>View the parish event calendar.</p>
        </div>
    </div>
    </a>

    <a href="logout.php">
    <div class="col-4 ms-auto" id="logout">
        <div class="action-card logout">
        <i class="bi bi-box-arrow-right"></i>
        <h6>LOG OUT</h6>
        <p>Exit StKPortal.</p>
        </div>
    </div>
</div>

<script>
function updateDateTime() {
    const now = new Date();

    const optionsDate = { year: 'numeric', month: 'short', day: 'numeric' };
    const date = now.toLocaleDateString(undefined, optionsDate);

    const optionsTime = { hour: '2-digit', minute: '2-digit' };
    const time = now.toLocaleTimeString(undefined, optionsTime);

    document.getElementById('datetime').textContent = `${date} | ${time}`;
}
setInterval(updateDateTime, 1000);
updateDateTime();
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const hour = new Date().getHours();
    let greeting = "Welcome";

    if (hour >= 5 && hour < 12) {
        greeting = "Good morning, Rev Fr.";
    } else if (hour >= 12 && hour < 18) {
        greeting = "Good afternoon, Rev Fr.";
    } else {
        greeting = "Good evening, Rev Fr.";
    }

    const greetingEl = document.getElementById("greeting");
    greetingEl.innerHTML = `${greeting} <strong><?php echo htmlspecialchars($admin_name); ?></strong>!`;
});

// Slideshow functionality
let currentSlide = 0;
const slides = document.querySelectorAll('.banner-image');
const totalSlides = slides.length;
let startX = 0;
let endX = 0;

function showSlide(index) {
    slides.forEach((slide, i) => {
        slide.classList.remove('active');
    });
    slides[index].classList.add('active');
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    showSlide(currentSlide);
}

function prevSlide() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    showSlide(currentSlide);
}

const heroBanner = document.querySelector('.hero-banner');

heroBanner.addEventListener('touchstart', (e) => {
    startX = e.touches[0].clientX;
});

heroBanner.addEventListener('touchend', (e) => {
    endX = e.changedTouches[0].clientX;
    handleSwipe();
});

function handleSwipe() {
    const diffX = startX - endX;
    const threshold = 50;

    if (Math.abs(diffX) > threshold) {
        if (diffX > 0) {
        
            nextSlide();
        } else {
            
            prevSlide();
        }
    }
}

setInterval(nextSlide, 4000);
showSlide(currentSlide);
</script>

<script>
const verses = [
    {
        text: "When you pass through the waters, I will be with you; and through the rivers, they shall not overwhelm you. When you walk through fire you shall not be burned, and the flame shall not consume you.",
        ref: "— Isaiah 43:2"
    },
    {
        text: "And after you have suffered a little while, the God of all grace, who has called you to his eternal glory in Christ, will himself restore, confirm, strengthen, and establish you.",
        ref: "— 1 Peter 5:10"
    },
    {
        text: "My flesh and my heart may fail, but God is the strength of my heart and my portion forever.",
        ref: "— Psalm 73:26"
    },
    {
        text: "Come to me, all who labor and are heavy laden, and I will give you rest.",
        ref: "— Matthew 11:28"
    }
];

const verseText = document.getElementById("verseText");
const verseRef  = document.getElementById("verseRef");

let verseIndex = 0;
let charIndex = 0;

const typingSpeed = 55;
const pauseAfterTyping = 13000;
const pauseBeforeNext = 6000;

function typeVerse() {
    const current = verses[verseIndex];

    if (charIndex < current.text.length) {
        verseText.textContent += current.text.charAt(charIndex);
        verseText.classList.add("type-cursor");
        charIndex++;
        setTimeout(typeVerse, typingSpeed);
    } else {
        verseRef.textContent = current.ref;

        setTimeout(() => {
            verseText.textContent = "";
            verseRef.textContent = "";
            charIndex = 0;
            verseIndex = (verseIndex + 1) % verses.length;

            setTimeout(typeVerse, pauseBeforeNext);
        }, pauseAfterTyping);
    }
}

typeVerse();
</script>


</body>
</html>