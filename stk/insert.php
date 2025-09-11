<?php 
require_once("dbconfig.php"); // Include DB connection

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
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sign Up - Admin Panel</title>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      overflow-x: hidden;
    }
    /* Sidebar styles */
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 250px;
      height: 100vh;
      background-color: #808080;
      color: white;
      padding-top: 60px;
      overflow-y: auto;
      transition: left 0.3s ease;
      z-index: 1000;
    }
    .sidebar-logo {
      text-align: center;
      margin-bottom: 20px;
      padding: 0 20px;
    }
    .sidebar-logo img {
      max-width: 100px;
      height: auto;
      display: block;
      margin: 0 auto 8px auto;
      border-radius: 10px;
    }
    .logo-text {
      font-size: 18px;
      font-weight: bold;
      color: white;
    }
    .sidebar a {
      display: block;
      padding: 15px 20px;
      text-decoration: none;
      color: white;
      font-size: 16px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .sidebar a:hover {
      background-color: #999999;
    }
    /* Topbar styles */
    .topbar {
      position: fixed;
      top: 0;
      left: 250px;
      right: 0;
      height: 60px;
      background-color: #4b8a3f;
      color: white;
      padding: 10px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      z-index: 5;
      transition: left 0.3s ease;
    }
    .hamburger {
      font-size: 24px;
      cursor: pointer;
      background: none;
      border: none;
      color: white;
    }
    /* Main content */
    main {
      margin-left: 250px;
      margin-top: 60px;
      padding: 20px;
      transition: margin-left 0.3s ease;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
      <img src="stklogo.png" alt="Logo" />
      <div class="logo-text">My Logo Text</div>
    </div>
    <a href="index.php?page=dashboard">Dashboard</a>
    <a href="index.php?page=filemanager">File Manager</a>
    <a href="index.php?page=announcement">Announcement</a>
    <a href="index.php?page=event">Event</a>
    <a href="index.php?page=member">Member</a>
    <a href="index.php?page=certificate">Certificate</a>
    <a href="index.php?page=booking">Booking</a>
    <a href="index.php?page=donation">Donation</a>
    <a href="index.php?page=prayer">Prayer</a>
    <a href="index.php?page=logout">Log Out</a>
  </div>

  <!-- Topbar -->
  <div class="topbar">
    <button class="hamburger" onclick="toggleSidebar()">☰</button>
    <div>Admin Panel - Sign Up</div>
  </div>

  <!-- Main Content -->
  <main>
    <div class="container">
      <div class="row">
          <h3>Sign Up</h3>
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
                  <input type="text" name="middlename" class="form-control" required>
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
                  <button type="submit" class="btn btn-primary">Sign Up</button>
                  <a href="index.php" class="btn btn-danger">Back</a>
              </div>
          </div>
      </form>
    </div>
  </main>

<script>
  function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('main');
    const topbar = document.querySelector('.topbar');

    if(sidebar.style.left === '0px' || sidebar.style.left === '') {
      sidebar.style.left = '-250px';
      mainContent.style.marginLeft = '0';
      topbar.style.left = '0';
    } else {
      sidebar.style.left = '0';
      mainContent.style.marginLeft = '250px';
      topbar.style.left = '250px';
    }
  }
</script>

</body>
</html>
