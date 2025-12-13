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
    <h1>Welcome, <strong><?php echo htmlspecialchars($admin_name); ?></strong>!</h1>
    <hr class="divider">
    <br><br>
    <a href="sacraments/sacrament_dashboard.php">
      <div class="sacrament-reservations">
        <div class="label">
          <span>🗃️ RESERVATIONS</span>
        </div>
      </div>
    </a>

      <!-- Buttons Grid -->
      <div class="dashboard-grid">
        <a href="stk/filemanager.php"><div class="dashboard-btn">📂️<br>FILE MANAGER</div></a>
        <a href="stk/announcement.php"><div class="dashboard-btn">📢<br>ANNOUNCEMENT</div></a>
        <a href="event.php"><div class="dashboard-btn">📅<br>EVENT</div></a>
        <a href="stk/members.php"><div class="dashboard-btn">👥<br>MEMBERS</div></a>
        <a href="sacraments/certificate_req/certificate_request.php"><div class="dashboard-btn">📄<br>CERTIFICATES</div></a>
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