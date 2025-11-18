<?php
session_start();
include 'db.php';

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../../public/login.php");
    exit();
}
$admin_name = !empty($_SESSION['givenname']) ? $_SESSION['givenname'] : $_SESSION['username'];

// Simulate a logged-in user for testing
$_SESSION['login_id'] = 1;
$login_id = $_SESSION['login_id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];

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
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
<link href='announcement.css' rel='stylesheet'>
<title>Announcement</title>
</head>

<body>

<!-- SIDEBAR -->
<section id="sidebar" <?php if ($hideSidebar) echo 'class="hide"'; ?>>
<a href="#" class="label">
<img src="../santa krus logo.png" alt="Logo" class="logo" />
</a>
<ul class="side-menu top">
<li><a href="../dashboard.php"><span class="text">Dashboard</span></a></li>
<li><a href="filemanager.php"><span class="text">File Manager</span></a></li>
<li class="active"><a href="announcement.php"><span class="text">Announcement</span></a></li>
<li><a href="#"><span class="text">Event</span></a></li>
<li><a href="members.php"><span class="text">Members</span></a></li>
<li><a href="../certificate/certificate.php"><span class="text">Certificates</span></a></li>
<li><a href="../booking_sacraments.php"><span class="text">Reservations</span></a></li>
<li><a href="../donations.php" data-section="dashboard"><span class="text">Donations</span></a></li>
<li><a href="../prayer_admin.php"><span class="text">Prayer Requests</span></a></li>
</ul>
<ul class="side-menu">
<li><a href="../logout.php" class="logout"><span class="text">Logout</span></a></li>
</ul>
</section>
<!-- SIDEBAR END -->

<section id="content">
<nav>
<i class='bx bx-menu'></i>
<div id="datetime"></div>
<div class="profile">
<span class="greeting"><?php echo htmlspecialchars($admin_name); ?></span>
<a href="#" class="profile-link"><i class='bx bx-user'></i></a>
</div>
</nav>


<div class="main-content">




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

document.getElementById('toggleDropdownBtn').addEventListener('click', function() {
    const menu = document.getElementById('dropdownMenu');
    menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
});

window.addEventListener('click', function(e) {
    const dropdown = document.getElementById('dropdownMenu');
    const btn = document.getElementById('toggleDropdownBtn');
    if (!dropdown.contains(e.target) && e.target !== btn) {
        dropdown.style.display = 'none';
    }
});
</script>
</body>
</html>