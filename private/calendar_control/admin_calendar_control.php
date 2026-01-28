<?php
require_once __DIR__ . '/../../snippets/connection.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
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

$current_embed = null;
$current_src = '';
$result = $conn->query("SELECT embed FROM live_streams ORDER BY created_at DESC LIMIT 1");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $current_embed = $row['embed'];
    if (preg_match('/src="([^"]+)"/i', $current_embed, $matches)) {
        $current_src = $matches[1];
    } else {
        $current_src = $current_embed;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />
	<link href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">

	<link href="../../stylesheets/private/navbar.css" rel="stylesheet">
	<link rel="stylesheet" href="admin_calendar_control.css" />

	<!-- NAVBAR, MAIN CONTENT, FAVICON -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link rel="stylesheet" href="../../stylesheets/private/navbar_updated.css" />
	<link rel='stylesheet' href='../../stylesheets/private/main_content.css'>
	<link rel="icon" type="image/x-icon" href="../../stylesheets/assets/favicon.ico">
	<!--------------------->
	<title>StKPortal | Calendar Control</title>
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
            <a href="#" class="active">
                Publish <i class="fa-solid fa-angle-down"></i>
            </a>
            <ul class="dropdown">
                <li><a href="../stk/announcement.php">Announcement</a></li>
                <li><a href="../stk/event.php">Event</a></li>
                <li><a href="../livemass/live_mass.php">Mass Livestream</a></li>
                <li><a href="../mass/mass_schedule.php">Mass Schedule</a></li>
                <li><a href="../prayer_admin.php">Prayer Requests</a></li>
                <li><a href="#" class="active">Sacrament Schedule</a></li>
            </ul>
        </li>

        <li><a href="../sacraments/sacrament_dashboard.php">Sacrament Reservations</a></li>
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

<section id="content">
	<div class="main-content">

<h1>Sacrament Calendar Control</h1>


<label>Filter by Sacrament: 
  <select id="filterSacrament">
    <option value="All">All</option>
    <option value="Baptism">Baptism</option>
    <option value="First Communion">First Communion</option>
    <option value="Confirmation">Confirmation</option>
    <option value="Wedding">Wedding</option>
  </select>
</label>

<div class="calendar-header">
  <button id="prevMonth">❮</button>
  <span id="calendarMonth"></span>
  <button id="nextMonth">❯</button>
</div>

<div class="calendar-grid" id="calendarGrid"></div>

<!-- Schedule Modal -->
<div class="modal" id="scheduleModal">
  <div class="modal-content modal-split">

    <div class="modal-split-header">
      <div>
        <h3 style="margin:0;">Schedule Manager</h3>
        <p class="modal-subtitle">Add / edit schedules and review existing ones for the selected date.</p>
      </div>
      <button type="button" class="modal-x" onclick="closeScheduleModal()" aria-label="Close">×</button>
    </div>

    <div class="modal-split-grid">
      <!-- LEFT: Add/Edit Form -->
      <div class="modal-pane">
        <h4 class="pane-title">Add / Edit Schedule</h4>

        <form id="scheduleForm">
          <input type="hidden" name="id" id="schedule_id">

          <label>Date</label>
          <input type="date" name="date" id="schedule_date" required>

          <label>Time</label>
          <input type="time" name="time" id="schedule_time">

          <label>Sacrament</label>
          <select name="service_type" id="schedule_service_type" required>
            <option value="Baptism">Baptism</option>
            <option value="First Communion">First Communion</option>
            <option value="Confirmation">Confirmation</option>
            <option value="Wedding">Wedding</option>
          </select>

          <label>Type</label>
          <select name="type" id="schedule_type">
            <option value="Regular">Regular</option>
            <option value="Solo">Solo</option>
          </select>

          <label>Max Slots</label>
          <input type="number" name="max_slots" id="schedule_max_slots" value="15">

          <label>Status</label>
          <select name="is_enabled" id="schedule_is_enabled">
            <option value="1">Enabled</option>
            <option value="0">Disabled</option>
          </select>

          <label>Note</label>
          <textarea name="note" id="schedule_note" rows="4"></textarea>

          <div class="modal-actions">
            <button type="submit">Save</button>
            <button type="button" onclick="closeScheduleModal()">Cancel</button>
          </div>
        </form>
      </div>

      <!-- RIGHT: Existing Schedules -->
      <div class="modal-pane">
        <div class="pane-head">
          <h4 class="pane-title" style="margin:0;">Existing Schedules</h4>
        </div>

        <div id="existingSchedules" class="existing-schedules"></div>
      </div>
    </div>

  </div>
</div>


<script src="admin_calendar.js"></script>
</body>
</html>
