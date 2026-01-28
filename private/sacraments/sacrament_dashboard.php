<?php
require_once '../connection.php';
session_start();

$user_id = $_SESSION['user_id'];

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../../public/login.php");
    exit();
}
$admin_name = !empty($_SESSION['givenname']) ? $_SESSION['givenname'] : $_SESSION['username'];

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
  <link href='../../stylesheets/private/parish_calendar.css' rel='stylesheet'>

  <!-- NAVBAR, MAIN CONTENT, FAVICON -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../../stylesheets/private/navbar_updated.css" />
  <link rel='stylesheet' href='../../stylesheets/private/main_content.css'>
  <link rel="icon" type="image/x-icon" href="../../stylesheets/assets/favicon.ico">
  <!--------------------->
  <title>StKPortal | Sacrament Reservation Calendar</title>
</head>
<body>

<!-------------------- NAVBAR -------------------->
<header class="custom-header">
<nav class="navbar custom-navbar">

    <!-- LEFT: Logo -->
    <div class="nav-left">
        <a href="dashboard.php">
            <img src="../santa krus logo.png"
                 alt="Parish Logo"
                 class="logo">
        </a>
    </div>

    <!-- CENTER: Navigation links -->
    <ul class="nav-links nav-center">
        <li><a href="../dashboard.php">Home</a></li>
        <li><a href="../stk/members.php">Members</a></li>

        <li class="li-dropdown">
            <a href="#">
                Publish <i class="fa-solid fa-angle-down"></i>
            </a>
            <ul class="dropdown">
                <li><a href="../stk/announcement.php">Announcement</a></li>
                <li><a href="../stk/event.php">Event</a></li>
                <li><a href="../livemass/live_mass.php">Mass Livestream</a></li>
                <li><a href="../mass/mass_schedule.php">Mass Schedule</a></li>
                <li><a href="../prayer_admin.php">Prayer Requests</a></li>
            </ul>
        </li>

        <li><a href="#" class="active">Sacrament Reservations</a></li>
        <li><a href="../sacraments/certificate_req/certificate_request.php">Certificate Requests</a></li>
        <li><a href="../donations.php">Donations</a></li>
    </ul>

    <!-- RIGHT: Notifications + User -->
    <div class="nav-right">
        <ul class="nav-links">
            <li class="nav-notification">
                <a href="../notification/admin_notif.php">
                    <span class="bell">&#128276;</span>

                    <?php if (!empty($unreadCount) && $unreadCount > 0): ?>
                        <span class="badge"><?= $unreadCount ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <li class="divider">|</li>

            <li class="li-dropdown">
                <a href="#" style="margin-right: -10px;"><?= htmlspecialchars($admin_name) ?></a>
                <ul class="dropdown">
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>

</nav>
</header>
<!----------------- NAVBAR END ----------------->

<div class="main-content">

<div style="margin:15px 0; display:flex; flex-wrap:wrap; gap:8px;">
  <a href="anoint/s_anointing.php" class="nav-btn anointing">Anointing of the Sick</a>
  <a href="baptism/s_baptism.php" class="nav-btn baptism">Baptism</a>
  <a href="first_communion/s_first_comm.php" class="nav-btn communion">First Communion</a>
  <a href="confirmation/s_confirmation.php" class="nav-btn confirmation">Confirmation</a>
  <a href="wedding/s_wedding.php" class="nav-btn wedding">Wedding</a>
  <a href="funeral/s_funeral.php" class="nav-btn funeral">Funeral Mass</a>
  <a href="celebratory/s_celebratory.php" class="nav-btn celebratory">Celebratory Mass</a>
  <a href="house/s_house_blessing.php" class="nav-btn house">House Blessing</a>
  <a href="../mass/mass_schedule.php" class="nav-btn mass">Mass Schedule</a>
</div>
<div class="layout">
  <div id="calendar"></div>
  <div id="summary">
    <h3>Schedule Summary</h3><br>
    <div id="summaryContent" class="no-events">Select a date</div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');

    const SACRAMENT_COLORS = {
      'Anointing of the Sick': '#c0392b',
      'Baptism': '#2980b9',
      'First Communion': '#27ae60',
      'Confirmation': '#8e44ad',
      'Wedding': '#e84393',
      'Funeral Mass': '#2d3436',
      'Celebratory Mass': '#16a085',
      'House Blessing': '#d35400',
      'Mass Schedule': '#34495e'
    };

    // Track selected date so highlight persists
    let selectedDateStr = null;

    function clearSelectedHighlight() {
      calendarEl.querySelectorAll('.fc-daygrid-day.selected-date')
        .forEach(el => el.classList.remove('selected-date'));
    }

    function applySelectedHighlight(dateStr) {
      clearSelectedHighlight();
      if (!dateStr) return;

      const cell = calendarEl.querySelector(`.fc-daygrid-day[data-date="${dateStr}"]`);
      if (cell) cell.classList.add('selected-date');
    }

    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      height: 'auto',

      // Show "See all" instead of "+ more"
      dayMaxEvents: true,
      moreLinkText: function () {
        return 'See all';
      },

      eventDidMount: function (info) {
        const color = SACRAMENT_COLORS[info.event.title];
        if (color) {
          info.el.style.backgroundColor = color;
          info.el.style.borderColor = color;
          info.el.style.color = '#fff';
        }
      },

      dateClick: function (info) {
        selectedDateStr = info.dateStr;
        applySelectedHighlight(selectedDateStr);
        loadSummary(info.dateStr);
      },

      datesSet: function () {
        applySelectedHighlight(selectedDateStr);
      },

      events: 'fetch_calendar_events.php'
    });

    calendar.render();

    // ---- SUMMARY LOADER ----
    function loadSummary(date) {
      fetch('fetch_day_summary.php?date=' + date)
        .then(res => res.json())
        .then(data => {
          const box = document.getElementById('summaryContent');
          box.innerHTML = '';

          if (!data.length) {
            box.innerHTML = '<div class="no-events">No schedules for this day</div>';
            return;
          }

          data.forEach(text => {
            // Split "Type: details..."
            let type = 'Schedule';
            let details = text;

            if (text.includes(':')) {
              const parts = text.split(':');
              type = parts.shift().trim();
              details = parts.join(':').trim();
            }

            // Convert time: "at 11:30:00" -> "at 11:30 AM"
            details = details.replace(
              /\bat\s+(\d{1,2}):(\d{2})(?::(\d{2}))?\b/g,
              (match, hh, mm) => 'at ' + toAmPm(hh, mm)
            );

            const div = document.createElement('div');
            div.className = 'summary-item';

            // Left accent color based on sacrament type
            const accent = SACRAMENT_COLORS[type] || '#6b7280';
            div.style.borderLeft = `6px solid ${accent}`;

            div.innerHTML = `
              <span class="summary-type">${escapeHtml(type)}</span>
              <div class="summary-text">${escapeHtml(details)}</div>
            `;

            box.appendChild(div);
          });
        })
        .catch(err => {
          console.error(err);
          document.getElementById('summaryContent').innerHTML =
            '<div class="no-events">Error loading summary</div>';
        });
    }

    // Helper: 24-hour -> AM/PM
    function toAmPm(hh, mm) {
      let h = parseInt(hh, 10);
      const suffix = h >= 12 ? 'PM' : 'AM';
      h = h % 12;
      if (h === 0) h = 12;
      return `${h}:${mm} ${suffix}`;
    }

    // Prevent HTML injection (safe display)
    function escapeHtml(str) {
      return String(str)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
    }
  });
</script>
</div>
</body>
</html>
