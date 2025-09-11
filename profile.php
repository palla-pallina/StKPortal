<?php
require_once("dbconfig.php");

$message = "";

// Handle Profile Photo Upload
if (isset($_POST['upload'])) {
    $userid = $_POST['id'];

    $file_name = $_FILES['file']['name'];
    $file_temp = $_FILES['file']['tmp_name'];
    $file_size = $_FILES['file']['size'];
    $file_type = $_FILES['file']['type'];

    $location = "upload/" . basename($file_name);

    if ($file_size < 524880) {  // roughly 512KB
        if (move_uploaded_file($file_temp, $location)) {
            try {
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $sql = "UPDATE users SET Photo = :location WHERE id = :userid";
                $stmt = $dbh->prepare($sql);
                $stmt->bindParam(':location', $location, PDO::PARAM_STR);
                $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                $stmt->execute();

                $message = "Upload successful!";
            } catch (PDOException $e) {
                $message = "DB Error: " . $e->getMessage();
            }
        } else {
            $message = "Failed to move uploaded file.";
        }
    } else {
        $message = "File is too large. Maximum size allowed is 512KB.";
    }
}

// Fetch user data for form
$userid = intval($_GET['id'] ?? 0);
$sql = "SELECT * FROM users WHERE id = :userid";
$query = $dbh->prepare($sql);
$query->bindParam(':userid', $userid, PDO::PARAM_INT);
$query->execute();
$result = $query->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Profile Upload - Admin Panel</title>
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
    <div>Admin Panel - Profile Upload</div>
  </div>

  <!-- Main Content -->
  <main>
    <div class="container">
        <h3>Profile Upload for: <?php echo htmlspecialchars($result['givenname'] . ' ' . $result['lastname']); ?></h3>
        
        <?php if (!empty($message)) : ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($userid); ?>">
            <div class="form-group">
                <label>Upload Photo (max 512KB)</label>
                <input type="file" name="file" class="form-control" required accept="image/*">
            </div>
            <button type="submit" name="upload" class="btn btn-danger">Upload</button>
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
