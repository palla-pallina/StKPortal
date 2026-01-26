<?php
$conn = new mysqli('localhost', 'root', '', 'test_tree_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();
$user_id = $_SESSION['user_id'];

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Priest') {
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

    <!-- RIGHT: Notifications + User -->
    <div class="nav-right">
        <ul class="nav-links">
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
<div class="breadcrumb" style="margin-bottom:15px;">
    <a href="dashboard.php">Home</a><span>Parish Event Calendar</span>
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

    const calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      height: 'auto',

      eventDidMount: function (info) {
        const color = SACRAMENT_COLORS[info.event.title];
        if (color) {
          info.el.style.backgroundColor = color;
          info.el.style.borderColor = color;
          info.el.style.color = '#fff';
        }
      },

      dateClick: function (info) {
        loadSummary(info.dateStr);
      },

      events: 'fetch_calendar_events.php'
    });

    calendar.render();

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
            const div = document.createElement('div');
            div.className = 'summary-item';
            div.textContent = text;
            box.appendChild(div);
          });
        });
    }
  });
</script>
</div>
</body>
</html>
