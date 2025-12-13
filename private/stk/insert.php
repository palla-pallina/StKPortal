<?php
require_once 'connection.php';
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

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fn  = $_POST['givenname'] ?? '';
    $mn  = $_POST['middlename'] ?? '';
    $ln  = $_POST['lastname'] ?? '';
    $sf  = $_POST['suffix'] ?? '';
    $bd  = $_POST['birthday'] ?? '';
    $un  = $_POST['username'] ?? '';
    $em  = $_POST['email'] ?? '';
    $cn  = $_POST['contact'] ?? '';
    $pas = $_POST['password'] ?? '';
    $cpw = $_POST['confirmpassword'] ?? '';

    if ($pas !== $cpw) {
        $message = "Passwords do not match.";
    } else {
        try {
            $hashed = password_hash($pas, PASSWORD_BCRYPT);
            $sql = "INSERT INTO users 
                    (givenname, middlename, lastname, suffix, email, username, contact, birthday, password) 
                    VALUES 
                    (:fn, :mn, :ln, :sf, :em, :un, :cn, :bd, :pas)";
            $stmt = $dbh->prepare($sql);

            $stmt->bindParam(':fn',  $fn,  PDO::PARAM_STR);
            $stmt->bindParam(':mn',  $mn,  PDO::PARAM_STR);
            $stmt->bindParam(':ln',  $ln,  PDO::PARAM_STR);
            $stmt->bindParam(':sf',  $sf,  PDO::PARAM_STR);
            $stmt->bindParam(':em',  $em,  PDO::PARAM_STR);
            $stmt->bindParam(':un',  $un,  PDO::PARAM_STR);
            $stmt->bindParam(':cn',  $cn,  PDO::PARAM_STR);
            $stmt->bindParam(':bd',  $bd,  PDO::PARAM_STR);
            $stmt->bindParam(':pas', $hashed, PDO::PARAM_STR);

            $stmt->execute();

            if ($dbh->lastInsertId()) {
                $message = "Record saved successfully!";
            } else {
                $message = "Insert failed.";
            }
        } catch (PDOException $e) {
            $message = "DB Error: " . $e->getMessage();
        }
    }
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
<title>Add Member</title>

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


    <div class="container">
      <div class="row">
          <h3>Add Member</h3>
          <?php if (!empty($message)) : ?>
              <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
          <?php endif; ?>
      </div>

      <form method="POST" action="">
          <div class="row">
              <div class="col-md-3">
                  <b>Given Name</b>
                  <input type="text" name="givenname" class="form-control" required>
              </div>
              <div class="col-md-3">
                  <b>Middle Name</b>
                  <input type="text" name="middlename" class="form-control">
              </div>
              <div class="col-md-3">
                  <b>Last Name</b>
                  <input type="text" name="lastname" class="form-control" required>
              </div>
              <div class="col-md-3">
                  <b>Suffix</b>
                  <input type="text" name="suffix" class="form-control">
              </div>
          </div>
          <br>
          <div class="row">
              <div class="col-md-6">
                  <b>Birthday</b>
                  <input type="date" name="birthday" class="form-control" required>
              </div>
          </div>
          <br>
          <div class="row">
              <div class="col-md-6">
                  <b>Username</b>
                  <input type="text" name="username" class="form-control" required>
              </div>
          </div>
          <br>
          <div class="row">
              <div class="col-md-6">
                  <b>Email</b>
                  <input type="email" name="email" class="form-control" required>
              </div>
          </div>
          <br>
          <div class="row">
              <div class="col-md-6">
                  <b>Contact Number</b>
                  <input type="text" name="contact" class="form-control" required>
              </div>
          </div>
          <br>
          <div class="row">
              <div class="col-md-6">
                  <b>Password</b>
                  <input type="password" name="password" class="form-control" required>
              </div>
          </div>
          <br>
          <div class="row">
              <div class="col-md-6">
                  <b>Confirm Password</b>
                  <input type="password" name="confirmpassword" class="form-control" required>
              </div>
          </div>
          <br>
          <div class="row">
              <div class="col-md-6">
                  <button type="submit" class="btn btn-primary">Add Member</button>
                  <a href="members.php" class="btn btn-danger">Back</a>
              </div>
          </div>
      </form>



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