<?php
require_once 'dbconfig.php';
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
    header("Location: ../../public/login.php");
    exit();
}
$admin_name = !empty($_SESSION['givenname']) ? $_SESSION['givenname'] : $_SESSION['username'];

// Handle deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $dbh->prepare("SELECT image_path FROM announcements WHERE ann_id = :id");
    $stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
    $stmt->execute();
    $ann = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($ann) {
        if (!empty($ann['image_path']) && file_exists($ann['image_path'])) {
            unlink($ann['image_path']);
        }
        $del = $dbh->prepare("DELETE FROM announcements WHERE ann_id = :id");
        $del->bindParam(':id', $delete_id, PDO::PARAM_INT);
        $del->execute();
        header("Location: all_announcements.php");
        exit;
    }
}

// Filter handling
$orderBy = 'date_posted DESC';
if (isset($_GET['filter'])) {
    if ($_GET['filter'] === 'title_asc') $orderBy = 'title ASC';
    if ($_GET['filter'] === 'title_desc') $orderBy = 'title DESC';
    if ($_GET['filter'] === 'date_asc') $orderBy = 'date_posted ASC';
    if ($_GET['filter'] === 'date_desc') $orderBy = 'date_posted DESC';
}

// Fetch all announcements
$stmt = $dbh->prepare("SELECT * FROM announcements ORDER BY $orderBy");
$stmt->execute();
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
<link href='../../stylesheets/private/navbar.css' rel='stylesheet'>
<link rel="stylesheet" href="../../stylesheets/private/all_ann.css" />

</head>
<body>

<!-------------------------- NAVBAR -------------------------->
<section id="sidebar" <?php if ($hideSidebar) echo 'class="hide"'; ?>>
    <a href="#" class="label">
        <img src="../santa krus logo.png" alt="Logo" class="logo" />
    </a>
    <ul class="side-menu top">
            <li><a href="../dashboard.php"><span class="text">Dashboard</span></a></li>
            <li><a href="filemanager.php"><span class="text">File Manager</span></a></li>
            <li class="active"><a href="announcement.php"><span class="text">Announcement</span></a></li>
            <li><a href="../event.php"><span class="text">Event</span></a></li>
            <li><a href="members.php"><span class="text">Members</span></a></li>
            <li><a href="../sacraments/certificate_req/certificate_request.php"><span class="text">Certificate Requests</span></a></li>
            <li><a href="../sacraments/sacrament_dashboard.php"><span class="text">Reservations</span></a></li>
            <li><a href="../donations.php" data-section="dashboard"><span class="text">Donations</span></a></li>
            <li><a href="../prayer_admin.php"><span class="text">Prayer Requests</span></a></li>
        </ul>
        <ul class="side-menu">
            <li><a href="../logout.php" class="logout"><span class="text">LOGOUT</span></a></li>
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
          <li><a href="../logout.php" class="logout">Logout</a></li>
      </ul>
  </li>
</ul>
</nav>
<!------------------------- SIDEBAR END ------------------------>


<div class="main-content">
  <div class="breadcrumb">
      <a href="announcement.php">Post Announcement</a><span>View Announcements</span>
  </div>

  <div class="page-header">
  <h1>All Announcements</h1>
  <hr class="divider">
  </div>

  <!-- Filter Dropdown -->
  <form method="GET" class="filter">
    <select name="filter" onchange="this.form.submit()">
      <option value="date_desc" <?= (isset($_GET['filter']) && $_GET['filter']=='date_desc')?'selected':'' ?>>Date Desc</option>
      <option value="date_asc" <?= (isset($_GET['filter']) && $_GET['filter']=='date_asc')?'selected':'' ?>>Date Asc</option>
      <option value="title_asc" <?= (isset($_GET['filter']) && $_GET['filter']=='title_asc')?'selected':'' ?>>Title A-Z</option>
      <option value="title_desc" <?= (isset($_GET['filter']) && $_GET['filter']=='title_desc')?'selected':'' ?>>Title Z-A</option>
    </select>
  </form>

  <?php foreach($announcements as $ann): ?>
  <div class="announcement-card">
    <?php if($ann['image_path']): ?>
      <img src="<?= $ann['image_path'] ?>" alt="">
    <?php else: ?>
      <div style="width:300px; height:180px; background:#ccc; display:flex; align-items:center; justify-content:center; border-radius:8px;">No Image</div>
    <?php endif; ?>
    <div class="ann-text">
      <h4><?= htmlspecialchars($ann['title']) ?></h4>
      <p><?= nl2br(htmlspecialchars(substr($ann['content'],0,500))) ?>...</p>
      <a href="edit_announcement.php?id=<?= $ann['ann_id'] ?>" class="btn">Edit</a>
      <a href="?delete_id=<?= $ann['ann_id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this announcement?')">Delete</a>
    </div>
  </div>
  <?php endforeach; ?>
</div>
</section>

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
