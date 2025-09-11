<?php
// index.php
$page = $_GET['page'] ?? 'dashboard';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Panel</title>
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
    <a href="file.php?page=filemanager">File Manager</a>
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
    <div>Admin Panel</div>
  </div>

  <!-- Main Content -->
  <main>
    <?php 
    // Dynamically load pages
    switch ($page) {
      case 'member':
        include 'members.php';  // This is your user listing code (the big PHP code you gave me)
        break;
      case 'dashboard':
        echo "<h2>Welcome to Dashboard</h2>";
        break;
      // Add other cases for other pages
      default:
        echo "<h2>Page not found</h2>";
        break;
    }
    ?>
  </main>

<script>
  function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    if(sidebar.style.left === '0px'){
      sidebar.style.left = '-250px';
      document.querySelector('main').style.marginLeft = '0';
      document.querySelector('.topbar').style.left = '0';
    } else {
      sidebar.style.left = '0';
      document.querySelector('main').style.marginLeft = '250px';
      document.querySelector('.topbar').style.left = '250px';
    }
  }
</script>

</body>
</html>
