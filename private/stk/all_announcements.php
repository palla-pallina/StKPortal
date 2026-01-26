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

// Pagination setup
$limit = 5; // Announcements per page
$page  = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Count total announcements
$stmt = $dbh->prepare("SELECT COUNT(*) AS total FROM announcements");
$stmt->execute();
$totalAnnouncements = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
$totalPages = ceil($totalAnnouncements / $limit);


// Fetch all announcements
$stmt = $dbh->prepare("SELECT * FROM announcements ORDER BY $orderBy LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch events for current page
$stmt = $dbh->prepare("SELECT * FROM events ORDER BY date ASC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

<link rel="stylesheet" href="../../stylesheets/private/all_ann.css" />

<!-- NAVBAR, MAIN CONTENT, FAVICON -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../../stylesheets/private/navbar_updated.css" />
<link rel='stylesheet' href='../../stylesheets/private/main_content.css'>
<link rel="icon" type="image/x-icon" href="../../stylesheets/assets/favicon.ico">
<title>StKPortal | View Announcements</title>
<!--------------------->
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
        <li><a href="members.php">Members</a></li>

        <li class="li-dropdown">
            <a href="#" class="active">
                Publish <i class="fa-solid fa-angle-down"></i>
            </a>
            <ul class="dropdown">
                <li><a href="announcement.php" class="active">Announcement</a></li>
                <li><a href="event.php">Event</a></li>
                <li><a href="../livemass/live_mass.php">Mass Livestream</a></li>
                <li><a href="../mass/mass_schedule.php">Mass Schedule</a></li>
                <li><a href="../prayer_admin.php">Prayer Requests</a></li>
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

<div class="page-header">
    <div class="page-header-left">
        <h1>All Announcements</h1>
    </div>
    <div class="page-header-right">
        <Label>Sort by:</Label>
        <div class="filter-wrapper">
        <form method="GET" class="filter">
            <select name="filter" onchange="this.form.submit()">
            <option value="date_desc" <?= (isset($_GET['filter']) && $_GET['filter']=='date_desc')?'selected':'' ?>>Date Desc</option>
            <option value="date_asc" <?= (isset($_GET['filter']) && $_GET['filter']=='date_asc')?'selected':'' ?>>Date Asc</option>
            <option value="title_asc" <?= (isset($_GET['filter']) && $_GET['filter']=='title_asc')?'selected':'' ?>>Title A-Z</option>
            <option value="title_desc" <?= (isset($_GET['filter']) && $_GET['filter']=='title_desc')?'selected':'' ?>>Title Z-A</option>
            </select>
        </form>
        <a href="announcement.php" class="btn" style="margin-top: -18px;">Create Announcement</a>
        </div>
    </div>
</div>
<hr class="divider"> 

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
      <a href="edit_announcement.php?id=<?= $ann['ann_id'] ?>" class="btn"><i class="glyphicon glyphicon-pencil"></i></a>
      <a href="?delete_id=<?= $ann['ann_id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this announcement?')"><i class="glyphicon glyphicon-trash"></i></a>
    </div>
  </div>
  <?php endforeach; ?>

<div class="pagination-container">
  <ul class="pagination">
    <!-- Previous -->
    <li class="<?= $page <= 1 ? 'disabled' : '' ?>">
      <a href="?page=<?= $page-1 ?>&filter=<?= $_GET['filter'] ?? 'date_desc' ?>">&laquo; Prev</a>
    </li>

    <!-- Page numbers -->
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <li class="<?= $i == $page ? 'active' : '' ?>">
        <a href="?page=<?= $i ?>&filter=<?= $_GET['filter'] ?? 'date_desc' ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>

    <!-- Next -->
    <li class="<?= $page >= $totalPages ? 'disabled' : '' ?>">
      <a href="?page=<?= $page+1 ?>&filter=<?= $_GET['filter'] ?? 'date_desc' ?>">Next &raquo;</a>
    </li>
  </ul>
</div>

</div>
</section>


</body>
</html>
