<?php
require_once 'dbconfig.php';

// Pagination for announcements
$limit = 3;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $limit;

// Count announcements
$countStmt = $dbh->prepare("SELECT COUNT(*) FROM announcements");
$countStmt->execute();
$total_announcements = $countStmt->fetchColumn();
$total_pages = ceil($total_announcements / $limit);

// Fetch announcements with correct column names
$stmt = $dbh->prepare("
    SELECT ann_id, title, content, date_posted
    FROM announcements
    ORDER BY date_posted DESC
    LIMIT :start, :limit
");
$stmt->bindParam(':start', $start, PDO::PARAM_INT);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events</title>

    <link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- NAVBAR -->
<header>
  <nav class="navbar">
    <img src="assets/santa krus logo.png" alt="Parish Logo" class="logo">
    <ul class="nav-links">
      <li><a href="homepage.html">Home</a></li>
      <li><a href="history.html">History</a></li>
      <li><a href="index.php" class="active">Events</a></li> 
      <li><a href="donation.html">Give</a></li>
      <li><a href="baptism.php">Sacraments</a></li>
      <li><a href="pray.html">Pray</a></li>
      <li><a href="contact.html">Contact</a></li>
      <li><a href="login.php" class="login-btn">Login</a></li>
    </ul>
  </nav>
</header>

<!-- HERO -->
<section class="masthead">
  <div class="container">
    <h1 class="site-title">SANTA KRUS PARISH</h1>
    <p class="site-subtitle">DIOCESE OF NOVALICHES</p>
  </div>
  <div class="hero-image" role="img" aria-label="Interior of church"></div>
</section>


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

        div.onclick = () => selectDate(fullDate, div);

        calDays.appendChild(div);
    }
}

/* -------------------------
   Day Click Handler
-------------------------- */
function selectDate(date, element) {
    document.querySelectorAll(".cal-day").forEach(d => d.classList.remove("selected"));
    element.classList.add("selected");

    panelTitle.innerText = `Events for ${date}`;
    panelDesc.innerText = "";

    loadEvents(date);
}

/* -------------------------
   Load Events
-------------------------- */
async function loadEvents(date) {
    const res = await fetch(`events.php?date=${date}`);
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
</script>



<!-- ======================= ANNOUNCEMENTS SECTION ======================= -->
<section class="announcements">
    <div class="container">

        <h2>Announcements</h2>

        <?php if ($announcements): ?>
            <?php foreach ($announcements as $a): ?>
                <div class="announcement">
                    <img src="announcement.jpg" alt="Announcement">

                    <div class="text">
                        <h3><?= htmlspecialchars($a['title']); ?></h3>
                        <p><?= nl2br(htmlspecialchars($a['content'])); ?></p>
                        <small>Posted on: <?= $a['date_posted']; ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No announcements available.</p>
        <?php endif; ?>


        <!-- PAGINATION -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>" class="pag-btn">Previous</a>
            <?php endif; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1 ?>" class="pag-btn">Next</a>
            <?php endif; ?>
        </div>

    </div>
</section>

</body>
</html>
