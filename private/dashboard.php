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

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$admin_name = $_SESSION['givenname'] ?? null; 

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />
<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
<link href='../stylesheets/private/navbar.css' rel='stylesheet'>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="../stylesheets/private/dashboard.css" />
<title>Dashboard</title>

</head>

<body>


<!-------------------------- NAVBAR -------------------------->
<section id="sidebar" <?php if ($hideSidebar) echo 'class="hide"'; ?>>
    <a href="#" class="label">
        <img src="santa krus logo.png" alt="Logo" class="logo" />
    </a>
    <ul class="side-menu top">
            <li class="active"><a href="dashboard.php"><span class="text">Dashboard</span></a></li>
            <li><a href="stk/filemanager.php"><span class="text">File Manager</span></a></li>
            <li><a href="stk/announcement.php"><span class="text">Announcement</span></a></li>
            <li><a href="event.php"><span class="text">Event</span></a></li>
            <li><a href="stk/members.php"><span class="text">Members</span></a></li>
            <li><a href="sacraments/certificate_req/certificate_request.php"><span class="text">Certificate Requests</span></a></li>
            <li><a href="sacraments/sacrament_dashboard.php"><span class="text">Reservations</span></a></li>
            <li><a href="donations.php" data-section="dashboard"><span class="text">Donations</span></a></li>
            <li><a href="prayer_admin.php"><span class="text">Prayer Requests</span></a></li>
        </ul>
        <ul class="side-menu">
            <li><a href="logout.php" class="logout"><span class="text">LOGOUT</span></a></li>
        </ul>
</section>
<!------------------------- NAVABAR END ------------------------>

<section id="content">


<!-------------------------- SIDEBAR -------------------------->
<nav>
<i class='bx bx-menu'></i>
<div id="datetime"></div>
<ul>
    <li class="li-dropdown">
      <a href="#"><span class="greeting"><?php echo htmlspecialchars($admin_name); ?></span></a>
      <ul class="dropdown">
          <li><a href="logout.php" class="logout">Logout</a></li>
      </ul>
  </li>
</ul>
</nav>
<!------------------------- SIDEBAR END ------------------------>


<div class="main-content">
    <h1 id="greeting">
    Welcome, <strong><?php echo htmlspecialchars($admin_name); ?></strong>!
    </h1>

    <hr class="divider">

<div class="dashboard-grid">

    <a href="stk/announcement.php">
    <div class="col-4">
        <div class="action-card">
        <i class="bi bi-megaphone"></i>
        <h6>ANNOUNCEMENT</h6>
        <p>Publish church announcements.</p>
        </div>
    </div>
    </a>

    <a href="sacraments/certificate_req/certificate_request.php">
    <div class="col-4">
        <div class="action-card">
        <i class="bi bi-file-earmark-text"></i>
        <h6>CERTIFICATE REQUESTS</h6>
        <p>Verify certificate requests.</p>
        </div>
    </div>
    </a>

    <a href="donations.php">
    <div class="col-4">
        <div class="action-card">
        <i class="bi bi-heart-fill"></i>
        <h6>DONATIONS</h6>
        <p>Manage church donations.</p>
        </div>
    </div>
    </a>

    <a href="event.php">
    <div class="col-4">
        <div class="action-card">
        <i class="bi bi-calendar-event"></i>
        <h6>EVENT</h6>
        <p>Update church calendar.</p>
        </div>
    </div>
    </a>

    <a href="mass_livestream.php">
    <div class="col-4">
        <div class="action-card">
        <i class="bi bi-broadcast"></i>
        <h6>MASS LIVESTREAM</h6>
        <p>Update the website’s mass livestream display.</p>
        </div>
    </div>
    </a>

    <a href="stk/members.php">
    <div class="col-4">
        <div class="action-card">
        <i class="bi bi-people-fill"></i>
        <h6>MEMBERS</h6>
        <p>Manage registered StKPortal members.</p>
        </div>
    </div>
    </a>

    <a href="prayer_admin.php">
    <div class="col-4">
        <div class="action-card">
        <i class="bi bi-envelope"></i>
        <h6>PRAYER REQUESTS</h6>
        <p>Verify and publish prayer requests.</p>
        </div>
    </div>
    </a>

    <a href="priest_sched.php">
    <div class="col-4">
        <div class="action-card">
        <i class="bi bi-calendar-check"></i>
        <h6>PRIEST SCHEDULE</h6>
        <p>Manage priest’s schedule.</p>
        </div>
    </div>
    </a>

    <a href="sacraments/sacrament_dashboard.php">
    <div class="col-4">
        <div class="action-card">
        <i class="bi bi-inboxes-fill"></i>
        <h6>SACRAMENT RESERVATIONS</h6>
        <p>Manage sacrament reservations.</p>
        </div>
    </div>
    </a>

    <!-- Logout -->
    <a href="logout.php">
    <div class="col-4 ms-auto" id="logout">
        <div class="action-card logout">
        <i class="bi bi-box-arrow-right"></i>
        <h6>LOG OUT</h6>
        <p>Exit StKPortal.</p>
        </div>
    </div>
    </a>
</div>
</div>

<script>
const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');

const menuBar = document.querySelector('#content nav .bx.bx-menu');
const sidebar = document.getElementById('sidebar');

menuBar.addEventListener('click', function () {
    sidebar.classList.toggle('hide');
});

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
        greeting = "Good morning";
    } else if (hour >= 12 && hour < 18) {
        greeting = "Good afternoon";
    } else {
        greeting = "Good evening";
    }

    const greetingEl = document.getElementById("greeting");
    greetingEl.innerHTML = `${greeting}, <strong><?php echo htmlspecialchars($admin_name); ?></strong>!`;
});
</script>

</body>
</html>