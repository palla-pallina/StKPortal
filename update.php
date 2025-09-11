<?php
require_once 'dbconfig.php';

// Handle Update Request
if (isset($_POST['update'])) {
    $userid = intval($_GET['id']);
    $fn = $_POST['givenname'];
    $mn = $_POST['middlename'];
    $ln = $_POST['lastname'];
    $sf = $_POST['suffix'];
    $bd = $_POST['birthday'];
    $un = $_POST['username'];
    $em = $_POST['email'];
    $cn = $_POST['contact'];

    $sql = "UPDATE users 
            SET givenname = :fn, middlename = :mn, lastname = :ln, suffix = :sf, 
                birthday = :bd, username = :un, email = :em, contact = :cn 
            WHERE id = :uid";

    $query = $dbh->prepare($sql);
    $query->bindParam(':fn', $fn, PDO::PARAM_STR);
    $query->bindParam(':mn', $mn, PDO::PARAM_STR);
    $query->bindParam(':ln', $ln, PDO::PARAM_STR);
    $query->bindParam(':sf', $sf, PDO::PARAM_STR);
    $query->bindParam(':bd', $bd, PDO::PARAM_STR);
    $query->bindParam(':un', $un, PDO::PARAM_STR);
    $query->bindParam(':em', $em, PDO::PARAM_STR);
    $query->bindParam(':cn', $cn, PDO::PARAM_STR);
    $query->bindParam(':uid', $userid, PDO::PARAM_INT);

    if ($query->execute()) {
        echo "<script>alert('Record updated successfully');</script>";
        echo "<script>window.location.href='index.php?page=member';</script>";
        exit();
    } else {
        echo "<script>alert('Update failed');</script>";
    }
}

// Fetch existing data for form
$userid = intval($_GET['id']);
$sql = "SELECT * FROM users WHERE id = :uid";
$query = $dbh->prepare($sql);
$query->bindParam(':uid', $userid, PDO::PARAM_INT);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Edit User</title>
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
      z-index: 1050;
      transition: left 0.3s ease;
    }
    .hamburger {
      font-size: 24px;
      cursor: pointer;
      background: none;
      border: none;
      color: white;
      padding: 0;
      margin: 0;
    }
    /* Main content */
    main {
      margin-left: 250px;
      margin-top: 60px;
      padding: 20px;
      transition: margin-left 0.3s ease;
    }
    main.shift {
      margin-left: 0;
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
  <div class="topbar" id="topbar">
    <button class="hamburger" id="sidebarToggle">☰</button>
    <div>Edit User</div>
  </div>

  <!-- Main Content -->
  <main id="mainContent">

    <?php if ($result): ?>
    <form method="POST" action="">
        <div class="row">
            <div class="col-md-3">
                <b>Given Name</b>
                <input type="text" name="givenname" value="<?php echo htmlentities($result->givenname); ?>" class="form-control" required>
            </div>
            <div class="col-md-3">
                <b>Middle Name</b>
                <input type="text" name="middlename" value="<?php echo htmlentities($result->middlename); ?>" class="form-control" required>
            </div>
            <div class="col-md-3">
                <b>Last Name</b>
                <input type="text" name="lastname" value="<?php echo htmlentities($result->lastname); ?>" class="form-control" required>
            </div>
            <div class="col-md-3">
                <b>Suffix</b>
                <input type="text" name="suffix" value="<?php echo htmlentities($result->suffix); ?>" class="form-control">
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-6">
                <b>Birthday</b>
                <input type="date" name="birthday" value="<?php echo htmlentities($result->birthday); ?>" class="form-control" required>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-6">
                <b>Username</b>
                <input type="text" name="username" value="<?php echo htmlentities($result->username); ?>" class="form-control" required>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-6">
                <b>Email</b>
                <input type="email" name="email" value="<?php echo htmlentities($result->email); ?>" class="form-control" required>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-6">
                <b>Contact Number</b>
                <input type="text" name="contact" value="<?php echo htmlentities($result->contact); ?>" class="form-control" required>
            </div>
        </div>
        <br>

        <div class="row">
            <div class="col-md-6">
                <button type="submit" name="update" class="btn btn-primary">Update</button>
                <a href="index.php?page=member" class="btn btn-danger">Back</a>
            </div>
        </div>
    </form>
    <?php else: ?>
      <p>User not found.</p>
    <?php endif; ?>

  </main>

  <script>
    // Sidebar toggle
    document.getElementById('sidebarToggle').addEventListener('click', function() {
      const sidebar = document.querySelector('.sidebar');
      const topbar = document.getElementById('topbar');
      const mainContent = document.getElementById('mainContent');

      if (sidebar.style.left === '0px' || sidebar.style.left === '') {
        sidebar.style.left = '-250px';
        topbar.style.left = '0';
        mainContent.style.marginLeft = '0';
      } else {
        sidebar.style.left = '0';
        topbar.style.left = '250px';
        mainContent.style.marginLeft = '250px';
      }
    });
  </script>

</body>
</html>
