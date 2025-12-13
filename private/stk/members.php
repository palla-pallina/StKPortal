<?php
require_once 'connection.php';
$message = "";

session_start();

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../../public/login.php");
    exit();
}
$admin_name = !empty($_SESSION['givenname']) ? $_SESSION['givenname'] : $_SESSION['username'];


//$page = $_GET['page'] ?? 'dashboard';

// Handle delete if del param is set
if (isset($_GET['del'])) {
    $uid = intval($_GET['del']);
    $sql = "DELETE FROM users WHERE user_id = :user_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':user_id', $uid, PDO::PARAM_INT);
    $query->execute();

    echo "<script>alert('Deleted!');</script>";
    echo "<script>window.location.href='members.php';</script>";
    exit();
}

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
<link href='../../stylesheets/private/members.css' rel='stylesheet'>

<title>StKPortal | Members Database</title>

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
            <li><a href="announcement.php"><span class="text">Announcement</span></a></li>
            <li><a href="../event.php"><span class="text">Event</span></a></li>
            <li class="active"><a href="members.php"><span class="text">Members</span></a></li>
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

<div class="page-header">
<h1>Members</h1>
<div class="reservations-controls">
<div class="search-box">
<form method="POST" action="" class="form-inline">
  <input type="text" name="search" placeholder="Search by ID, First or Last Name"
         class="form-control"
         value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>" />
  <button type="submit" class="btn btn-default">Search</button>

  <?php if (!empty($_POST['search'])): ?>
    <a href="members.php" class="btn btn-warning">Clear</a>
  <?php endif; ?>
</form>
</div>
</form>
<a href="insert.php">
    <button class="add-btn" id="addReservationBtn">
    <i class='bx bx-plus'></i>
    </button>
</a>
</div>

<hr class="divider">
</div>

<div class="sacraments-table-container">
  <table class="sacraments-table">

    <thead>
      <tr>
        <th>#</th>
        <th>Username</th>
        <th>Name</th>
        <th>Birthday</th>
        <th>Email</th>
        <th>Phone</th>
        <th>User Type</th>
        <th class="action-column">Action</th>
      </tr>
    </thead>

    <tbody>
    <?php

    /* ================= SEARCH ================= */
    $searchSQL = "";
    $params = [];

    if (!empty($_POST['search'])) {
        $search = trim($_POST['search']);

        $searchSQL = " WHERE 
            username LIKE :search
            OR givenname LIKE :search
            OR lastname LIKE :search
            OR email LIKE :search
            OR user_type LIKE :search
        ";

        $params[':search'] = "%$search%";
    }

    try {

        $sql = "SELECT * FROM users" . $searchSQL . " ORDER BY user_id DESC";
        $query = $dbh->prepare($sql);
        $query->execute($params);
        $results = $query->fetchAll(PDO::FETCH_OBJ);

        if ($query->rowCount() > 0) {
            foreach ($results as $result) {
    ?>

        <tr>
            <td><?= htmlentities($result->user_id); ?></td>
            <td><?= htmlentities($result->username); ?></td>

            <td>
                <?= htmlentities($result->givenname); ?>
                <?= htmlentities($result->middlename); ?>
                <?= htmlentities($result->lastname); ?>
                <?= htmlentities($result->suffix); ?>
            </td>

            <td><?= htmlentities($result->birthday); ?></td>
            <td><?= htmlentities($result->email); ?></td>
            <td><?= htmlentities($result->contact); ?></td>
            <td><?= htmlentities($result->user_type); ?></td>

            <!-- ✅ ACTION COLUMN -->
            <td class="action-column">
                <div class="action-buttons">

                    <a href="update.php?user_id=<?= $result->user_id; ?>" class="btn-edit" title="Edit">
                        <i class="glyphicon glyphicon-pencil"></i>
                    </a>

                    <a href="members.php?del=<?= $result->user_id; ?>"
                       onclick="return confirm('Are you sure you want to delete this user?')"
                       class="btn-delete"
                       title="Delete">
                        <i class="glyphicon glyphicon-trash"></i>
                    </a>

                </div>
            </td>
        </tr>

    <?php
            }
        } else {
            echo '<tr><td colspan="8">No users found.</td></tr>';
        }

    } catch (PDOException $e) {
        echo "<tr><td colspan='8'>Error: " . $e->getMessage() . "</td></tr>";
    }
    ?>
    </tbody>

  </table>
</div>


    </section>
<!----------------------------------- MAIN END ------------------------------------------>

<script>
const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a'); // sidebar effect

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