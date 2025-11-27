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
<link href='../../stylesheets/private/members.css' rel='stylesheet'>
<title>StKPortal | Members Database</title>

</head>

<body>

<!--------------------------------------- SIDEBAR ------------------------------------------->
<section id="sidebar" <?php if ($hideSidebar) echo 'class="hide"'; ?>>
    <a href="#" class="label">
        <img src="../santa krus logo.png" alt="Logo" class="logo" />
    </a>
    <ul class="side-menu top">
        <li><a href="../dashboard.php"><span class="text">Dashboard</span></a></li>
        <li><a href="filemanager.php"><span class="text">File Manager</span></a></li>
        <li><a href="announcement.php"><span class="text">Announcement</span></a></li>
        <li><a href="#"><span class="text">Event</span></a></li> <!-- N/A -->
        <li class="active"><a href="#"><span class="text">Members</span></a></li>
        <li><a href="../certificate/certificate.php"><span class="text">Certificates</span></a></li>
        <li><a href="../sacraments/s_dashboard.php"><span class="text">Reservations</span></a></li>
        <li><a href="../donations.php" data-section="dashboard"><span class="text">Donations</span></a></li>
        <li><a href="../prayer_admin.php"><span class="text">Prayer Requests</span></a></li>
    </ul>
    <ul class="side-menu">
        <li><a href="../logout.php" class="logout"><span class="text">Logout</span></a></li>
    </ul>
</section>
<!------------------------------------- SIDEBAR END ----------------------------------------->

<section id="content">

<!--------------------------------------- NAVBAR -------------------------------------------->
<nav>
  <i class='bx bx-menu'></i>
  <div id="datetime"></div>
  <div class="profile">
    <span class="greeting""><?php echo htmlspecialchars($admin_name); ?></span>
  </div>
</nav>
<!------------------------------------ NAVBAR END ------------------------------------------>

<!--------------------------------------- MAIN --------------------------------------------->
<div class="main-content">

<h1>Members</h1>
<hr class="divider" />
<a href="insert.php" class="btn btn-primary">Add Member</a>
<br><br>

<form method="POST" action="search.php" class="form-inline">
  <input type="text" name="query" placeholder="Search by ID, First or Last Name" class="form-control" />
  <button type="submit" class="btn btn-default">Search</button>
</form><br>

<div class="table-responsive">
  <table id="mytable" class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>#</th>
        <th>Username</th>
        <th>Name</th>
        <th>Birthday</th>
        <th>Email</th>
        <th>Phone Number</th>
        <th>User Type</th>
        <th>Edit</th>
      </tr>
    </thead>
    <tbody>
      <?php
      try {
          $sql = "SELECT * FROM users";
          $query = $dbh->prepare($sql);
          $query->execute();
          $results = $query->fetchAll(PDO::FETCH_OBJ);
          $cnt = 1;
          
          if ($query->rowCount() > 0) {
              foreach ($results as $result) {
                  ?>

                  <tr>
                    <td><?php echo htmlentities($result->user_id); ?></td>
                    <td><?php echo htmlentities($result->username); ?></td>
                    <td><?php echo htmlentities($result->givenname); ?> <?php echo htmlentities($result->middlename); ?> <?php echo htmlentities($result->lastname); ?> <?php echo htmlentities($result->suffix); ?>
                    <td><?php echo htmlentities($result->birthday); ?></td>
                    <td><?php echo htmlentities($result->email); ?></td>
                    <td><?php echo htmlentities($result->contact); ?></td>
                    <td><?php echo htmlentities($result->user_type); ?></td>
                    <td>

                        <a href="update.php?user_id=<?php echo htmlentities($result->user_id);?>" class="btn btn-primary btn-sm">
                            <span class="glyphicon glyphicon-pencil"></span>
                        </a>

                        <a href="members.php?del=<?php echo htmlentities($result->user_id);?>" 
                        class="btn btn-danger btn-sm" 
                        onclick="return confirm('Are you sure?')">
                        <span class="glyphicon glyphicon-trash"></span>
                        </a>

                    </td>
                  </tr>
                  <?php
                  $cnt++;
              }
          } else {
              echo '<tr><td colspan="12">No records found.</td></tr>';
          }
      } catch (PDOException $e) {
          echo "<tr><td colspan='12'>Error: " . $e->getMessage() . "</td></tr>";
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