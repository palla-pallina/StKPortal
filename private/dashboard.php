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
$admin_name = !empty($_SESSION['givenname']) ? $_SESSION['givenname'] : $_SESSION['username'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="../stylesheets/private/dashboard.css" />
<title>Dashboard</title>

</head>

<body>

<!-- SIDEBAR -->
<section id="sidebar" <?php if ($hideSidebar) echo 'class="hide"'; ?>>
<a href="#" class="label">
<img src="santa krus logo.png" alt="Logo" class="logo" />
</a>
<ul class="side-menu top">
<li class="active"><a href="dashboard.php"><span class="text">Dashboard</span></a></li>
<li><a href="stk/filemanager.php"><span class="text">File Manager</span></a></li>
<li><a href="stk/announcement.php"><span class="text">Announcement</span></a></li>
<li><a href="#"><span class="text">Event</span></a></li> <!-- N/A -->
<li><a href="stk/members.php"><span class="text">Members</span></a></li>
<li><a href="certificate/certificate.php"><span class="text">Certificates</span></a></li>
<li><a href="sacraments/s_dashboard.php"><span class="text">Reservations</span></a></li>
<li><a href="donations.php" data-section="dashboard"><span class="text">Donations</span></a></li>
<li><a href="prayer_admin.php"><span class="text">Prayer Requests</span></a></li>
</ul>
<ul class="side-menu">
<li><a href="logout.php" class="logout"><span class="text">Logout</span></a></li>
</ul>
</section>
<!-- SIDEBAR END -->

<section id="content">

<nav>
<i class='bx bx-menu'></i>
<div id="datetime"></div>
<div class="profile">
<span class="greeting"><?php echo htmlspecialchars($admin_name); ?></span>
<a href="#" class="profile-link"><i class='bx bx-user'></i></a>
</div>
</nav>
<div class="main-content">

<!-- CONTENT -->
       <!-- File Manager -->

    <span>Welcome, <strong><?php echo htmlspecialchars($admin_name); ?></strong>!</span>

    <br><br>
    <a href="stk/filemanager.php">
      <div class="file-manager">
        <div class="label">
          <span>📁 FILE MANAGER</span>
        </div>
        <div class="progress-bar">
          <div class="progress"></div>
        </div>
      </div>
    </a>

      <!-- Buttons Grid -->
      <div class="dashboard-grid">
        <a href="stk/announcement.php"><div class="dashboard-btn">📢<br>ANNOUNCEMENT</div></a>
        <a href="events.php"><div class="dashboard-btn">📅<br>EVENT</div></a>
        <a href="stk/members.php"><div class="dashboard-btn">👥<br>MEMBERS</div></a>
        <a href="certificate/certificate.php"><div class="dashboard-btn">📄<br>CERTIFICATES</div></a>
        <a href="sacraments/s_dashboard.php"><div class="dashboard-btn">🗃️<br>RESERVATIONS</div></a>
        <a href="donations.php"><div class="dashboard-btn">❤️<br>DONATIONS<br></div></a>
        <a href="prayer_admin.php"><div class="dashboard-btn">🙏<br>PRAYER REQUESTS</div></a>
        <a href="logout.php"><div class="dashboard-btn">🚪<br>LOG OUT</div></a>
      </div>
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
</body>
</html>