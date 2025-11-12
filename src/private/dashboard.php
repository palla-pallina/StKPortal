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
    header("Location: login.php");
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
<title>Dashboard</title>

<style>
    
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

a {
    text-decoration: none;
}

li {
    list-style: none;
}

:root {
    --poppins: 'Poppins', sans-serif;
    --lato: 'Lato', sans-serif;

    --light: #F9F9F9;
    --blue: #3C91E6;
    --grey: #eee;
    --dark-grey: #AAAAAA;
    --dark: #342E37;
    --red: #DB504A;
    --green: #418033;
    --success: #16a34a;
    --warning: #f59e0b;
    --danger: #dc2626;
}

html, body {
    height: 100%;
    overflow-x: hidden;
}

body.dark {
    --light: #0C0C1E;
    --grey: #060714;
    --dark: #FBFBFB;
}

body {
    background: var(--grey);
    font-family: var(--lato);
}

/* SIDEBAR */
#sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 280px;
    height: 100%;
    background: var(--light);
    z-index: 2000;
    transition: .3s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 0;
}

#sidebar.hide {
    width: 0 !important;
    overflow: hidden;
    pointer-events: none;
}

#sidebar .label {
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding-top: 20px;
}

#sidebar .label .logo {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

#sidebar .side-menu { width: 100%; }
#sidebar .side-menu li { height: 48px; }
#sidebar .side-menu li a {
    display: flex; align-items: center; justify-content: center;
    height: 100%; font-size: 16px; color: var(--dark); text-decoration: none;
}
#sidebar .side-menu li.active {
    background: var(--grey);
}

#content {
    position: relative;
    width: calc(100% - 280px);
    left: 280px;
    transition: .3s ease;
    min-width: 0;
}

#sidebar.hide ~ #content {
    width: 100%;
    left: 0;
}

#content nav {
    height: 56px;
    background-color: #418033;
    padding: 0 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    grid-gap: 16px;
    position: sticky;
    top: 0;
    z-index: 1000;
}

#content nav .bx {
    font-size: 24px;
    color: var(--dark);
    cursor: pointer;
}

#content nav a {
    color: var(--dark);
}

#content nav .nav-link {
    font-size: 16px;
    transition: .3s ease;
}

#content nav #datetime {
    color: white;
    font-size: 14px;
    font-weight: 500;
    letter-spacing: 1px;
    margin-left: 16px;
    flex-grow: 1;
}

#content nav .profile {
    display: flex;
    align-items: center;
    gap: 10px;
}

#content nav .profile .greeting {
    font-size: 14px;
    color: var(--light);
    font-weight: 500;
}

#content nav .profile-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #ffffff;
    font-size: 24px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

#content nav .profile-link:hover {
    background-color: #9fecd3;
    transform: scale(1.05);
}

.content-section {
    display: none;
    padding: 20px;
    min-height: calc(100vh - 56px);
}

.content-section.active {
    display: block;
}
/* SIDEBAR END */

.main-content {
    background: white;
    /*border-radius: 31px;*/
    padding: 51px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 100%;
}

.main-content h1 {
    font-family: var(--poppins);
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 16px;
}

.main-content h2 {
    font-family: var(--poppins);
    font-size: 18px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 12px;
}

@media screen and (max-width: 768px) {
    #sidebar {
        width: 200px;
    }
    #content {
        width: calc(100% - 200px);
        left: 200px;
    }
    #content nav .nav-link {
        display: none;
    }
}

@media screen and (max-width: 576px) {
    #content nav .nav-link {
        display: none;
    }
}

    .file-manager {
      background-color: #2e7d32;
      color: white;
      border-radius: 10px;
      padding: 31px;
      margin-bottom: 30px;
      position: relative;
    }

    .file-manager .label {
      font-size: 1.2em;
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .file-manager .progress-bar {
      background-color: #ccc;
      height: 10px;
      border-radius: 20px;
      overflow: hidden;
      margin-top: 10px;
    }


    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 20px;
    }

    .dashboard-btn {
      background-color: #388e3c;
      color: white;
      padding: 31px;
      border-radius: 10px;
      text-align: center;
      cursor: pointer;
      transition: background 0.2s;
      position: relative;
    }

    .dashboard-btn:hover {
      background-color: #2e7d32;
    }

    .dashboard-btn::after {
      content: "";
      position: absolute;
      bottom: 0;
      left: 0;
      height: 20px;
      width: 100%;
      background: #66bb6a;
      border-bottom-left-radius: 10px;
      border-bottom-right-radius: 10px;
    }

    .dashboard-btn i {
      display: block;
      font-size: 2em;
      margin-bottom: 10px;
    }


</style>
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
<li><a href="certificate/admin.php?page=certificate"><span class="text">Certificates</span></a></li>
<li><a href="booking_sacraments.php"><span class="text">Reservations</span></a></li>
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
        <a href="certificate/admin.php?page=certificate"><div class="dashboard-btn">📄<br>CERTIFICATES</div></a>
        <a href="booking_sacraments.php"><div class="dashboard-btn">🗃️<br>BOOKINGS</div></a>
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
