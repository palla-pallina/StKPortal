<?php
include 'db.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $posted_by = 1; // Hardcoded for now, you can replace with logged-in user ID

    $imagePath = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $imagePath = $targetDir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
    }

    $sql = "INSERT INTO announcements (title, image_path, content, posted_by) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $title, $imagePath, $content, $posted_by);
    $stmt->execute();
    $stmt->close();
}

// Fetch announcements
$result = $conn->query("SELECT * FROM announcements ORDER BY date_posted DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Announcement</title>
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
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
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


  /* Announcement form */

  form {
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      margin-bottom: 30px;
  }

  form label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      color: #333;
  }

  form input[type="text"],
  form input[type="file"],
  form textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
  }

  form button {
      background: #4b8a3f;
      color: white;
      padding: 10px 15px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
  }

  form button:hover {
      background: #3a6c33;
  }


  /* Announcement cards */

  .announcement-card {
      background: white;
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  }

  .announcement-card h4 {
      margin-bottom: 10px;
      color: #4b8a3f;
  }

  .announcement-card img {
      max-width: 100%;
      border-radius: 6px;
      margin-bottom: 10px;
  }

  .announcement-card p {
      margin-bottom: 8px;
  }

  .announcement-card small {
      display: block;
      color: #777;
      font-size: 12px;
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
    <a href="announcement.php?page=announcement">Announcement</a>
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
    <h2>Post New Announcement</h2>
    <form action="" method="POST" enctype="multipart/form-data">
      <label for="title">Title:</label>
      <input type="text" name="title" required>

      <label for="image">Upload Image (optional):</label>
      <input type="file" name="image">

      <label for="content">Content:</label>
      <textarea name="content" rows="5" required></textarea>

      <button type="submit">Post Announcement</button>
    </form>

    <h2>Past Announcements</h2>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="announcement-card">
        <h4><?= htmlspecialchars($row['title']) ?></h4>
        <?php if (!empty($row['image_path'])): ?>
          <img src="<?= $row['image_path'] ?>" alt="Announcement Image">
        <?php endif; ?>
        <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
        <small>Posted on <?= $row['date_posted'] ?></small>
      </div>
    <?php endwhile; ?>
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
