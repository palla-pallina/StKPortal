<?php
$conn = new mysqli('localhost', 'root', '', 'test_tree_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
require_once "pdo_connection.php";
$rows = $pdo->query("SELECT * FROM certificate_request ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
session_start();


// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../../../public/login.php");
    exit();
}
$admin_name = !empty($_SESSION['givenname']) ? $_SESSION['givenname'] : $_SESSION['username'];
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
<link href='../../../stylesheets/private/navbar.css' rel='stylesheet'>
<link href='../../../stylesheets/private/certificate_request.css' rel='stylesheet'>
<title>StKPortal | Certificate Requests</title>
<style>
</style>
</head>

<body>

<!-------------------------- NAVBAR -------------------------->
<section id="sidebar" <?php if ($hideSidebar) echo 'class="hide"'; ?>>
    <a href="#" class="label">
        <img src="../../santa krus logo.png" alt="Logo" class="logo" />
    </a>
    <ul class="side-menu top">
            <li><a href="../../dashboard.php"><span class="text">Dashboard</span></a></li>
            <li><a href="../../stk/filemanager.php"><span class="text">File Manager</span></a></li>
            <li><a href="../../stk/announcement.php"><span class="text">Announcement</span></a></li>
            <li><a href="../../event.php"><span class="text">Event</span></a></li>
            <li><a href="../../stk/members.php"><span class="text">Members</span></a></li>
            <li class="active"><a href="../../sacraments/certificate_req/certificate_request.php"><span class="text">Certificate Requests</span></a></li>
            <li><a href="../sacrament_dashboard.php"><span class="text">Reservations</span></a></li>
            <li><a href="../../donations.php" data-section="dashboard"><span class="text">Donations</span></a></li>
            <li><a href="../../prayer_admin.php"><span class="text">Prayer Requests</span></a></li>
        </ul>
        <ul class="side-menu">
            <li><a href="../../logout.php" class="logout"><span class="text">LOGOUT</span></a></li>
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
          <li><a href="../../logout.php" class="logout">Logout</a></li>
      </ul>
  </li>
</ul>
</nav>
<!------------------------- SIDEBAR END ------------------------>


<!--------------------------------------- MAIN --------------------------------------------->
<div class="main-content">

<div class="reservations-header">
<h1>Certificate Requests</h1>
<div class="reservations-controls">
</div>
</div>


<div class="sacraments-table-container">
<table class="sacraments-table">

  <thead>
    <tr>
      <th>#</th>
      <th>Type</th>
      <th>Status</th>
      <th>Pickup Date</th>
      <th>Requester Name</th>
      <th>Owner Name</th>
      <th>Contact</th>
      <th>Email</th>
      <th>Files</th>
      <th>Set Pickup Date</th>
    </tr>
  </thead>
  
  <tbody>
    <?php foreach($rows as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['cert_req_id']) ?></td>
        <td><?= htmlspecialchars($r['certificate_type']) ?></td>
        <td><span class="pill <?= $r['certificate_status'] ?>"><?= $r['certificate_status'] ?></span></td>
        <td><?= htmlspecialchars($r['pickup_date'] ?? '-') ?></td>

        <td>
          <?= htmlspecialchars(trim(($r['request_firstname'] ?? '').' '.($r['request_middlename'] ?? '').' '.($r['request_lastname'] ?? '').' '.($r['request_suffix'] ?? ''))) ?>
        </td>
        <td>
          <?= htmlspecialchars(trim(($r['owner_firstname'] ?? '').' '.($r['owner_middlename'] ?? '').' '.($r['owner_lastname'] ?? '').' '.($r['owner_suffix'] ?? ''))) ?>
        </td>

        <td><?= htmlspecialchars($r['contact']) ?></td>
        <td><?= htmlspecialchars($r['email']) ?></td>

<td>
        <?php 
        $uploadBase = "../uploads/"; // relative to this PHP file
        if($r['id_picture']): ?>
            <a href="<?= $uploadBase . htmlspecialchars(basename($r['id_picture'])) ?>" target="_blank"><span class="glyphicon glyphicon-file"></span>Requester ID</a><br>
        <?php endif; ?>
        <?php if($r['receipt_picture']): ?>
            <a href="<?= $uploadBase . htmlspecialchars(basename($r['receipt_picture'])) ?>" target="_blank"><span class="glyphicon glyphicon-file"></span>Receipt</a><br>
        <?php endif; ?>
        <?php if($r['authorization_letter']): ?>
            <a href="<?= $uploadBase . htmlspecialchars(basename($r['authorization_letter'])) ?>" target="_blank"><span class="glyphicon glyphicon-file"></span>Auth Letter</a><br>
        <?php endif; ?>
        <?php if($r['authorized_id_picture']): ?>
            <a href="<?= $uploadBase . htmlspecialchars(basename($r['authorized_id_picture'])) ?>" target="_blank"><span class="glyphicon glyphicon-file"></span>Authorized ID</a>
        <?php endif; ?>
        </td>


        <td>
          <?php if($r['certificate_status'] === "Pending"): ?>
  <form action="admin_action.php" method="POST">
    <input type="hidden" name="id" value="<?= $r['cert_req_id'] ?>">

    <?php $today = date('Y-m-d'); ?>
    <input type="date" name="pickup_date" min="<?= $today ?>" required>

    <button class="accept" name="action" value="accept">Accept</button>
    <button class="decline" name="action" value="decline">Decline</button>
  </form>
<?php endif; ?>
            
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
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
