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
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
<title>Announcement</title>

<style>
    
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

a {
    text-decoration: none;
}

li {
    list-style: none;
}

:root {
    --poppins: 'Poppins', sans-serif;
    --lato: 'Lato', sans-serif;

    --light: #F9F9F9;
    --blue: #3C91E6;
    --grey: #eee;
    --dark-grey: #AAAAAA;
    --dark: #342E37;
    --red: #DB504A;
    --green: #418033;
    --success: #16a34a;
    --warning: #f59e0b;
    --danger: #dc2626;
}

html, body {
    height: 100%;
    overflow-x: hidden;
}

body.dark {
    --light: #0C0C1E;
    --grey: #060714;
    --dark: #FBFBFB;
}

body {
    background: var(--grey);
    font-family: var(--lato);
}

/* SIDEBAR */
#sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 280px;
    height: 100%;
    background: var(--light);
    z-index: 2000;
    transition: .3s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 0;
}

#sidebar.hide {
    width: 0 !important;
    overflow: hidden;
    pointer-events: none;
}

#sidebar .label {
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding-top: 20px;
}

#sidebar .label .logo {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

#sidebar .side-menu { width: 100%; }
#sidebar .side-menu li { height: 48px; }
#sidebar .side-menu li a {
    display: flex; align-items: center; justify-content: center;
    height: 100%; font-size: 16px; color: var(--dark); text-decoration: none;
}
#sidebar .side-menu li.active {
    background: var(--grey);
}

#content {
    position: relative;
    width: calc(100% - 280px);
    left: 280px;
    transition: .3s ease;
    min-width: 0;
}

#sidebar.hide ~ #content {
    width: 100%;
    left: 0;
}

#content nav {
    height: 56px;
    background-color: #418033;
    padding: 0 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    grid-gap: 16px;
    position: sticky;
    top: 0;
    z-index: 1000;
}

#content nav .bx {
    font-size: 24px;
    color: var(--dark);
    cursor: pointer;
}

#content nav a {
    color: var(--dark);
}

#content nav .nav-link {
    font-size: 16px;
    transition: .3s ease;
}

#content nav #datetime {
    color: white;
    font-size: 14px;
    font-weight: 500;
    letter-spacing: 1px;
    margin-left: 16px;
    flex-grow: 1;
}

#content nav .profile {
    display: flex;
    align-items: center;
    gap: 10px;
}

#content nav .profile .greeting {
    font-size: 14px;
    color: var(--light);
    font-weight: 500;
}

#content nav .profile-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #ffffff;
    font-size: 24px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

#content nav .profile-link:hover {
    background-color: #9fecd3;
    transform: scale(1.05);
}

.content-section {
    display: none;
    padding: 20px;
    min-height: calc(100vh - 56px);
}

.content-section.active {
    display: block;
}
/* SIDEBAR END */

.main-content {
    background: white;
    /*border-radius: 31px;*/
    padding: 51px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 100%;
}

.main-content h1 {
    font-family: var(--poppins);
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 16px;
}

.main-content h2 {
    font-family: var(--poppins);
    font-size: 18px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 12px;
}

@media screen and (max-width: 768px) {
    #sidebar {
        width: 200px;
    }
    #content {
        width: calc(100% - 200px);
        left: 200px;
    }
    #content nav .nav-link {
        display: none;
    }
}

@media screen and (max-width: 576px) {
    #content nav .nav-link {
        display: none;
    }
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

<!-- SIDEBAR -->
<section id="sidebar" <?php if ($hideSidebar) echo 'class="hide"'; ?>>
<a href="#" class="label">
<img src="../santa krus logo.png" alt="Logo" class="logo" />
</a>
<ul class="side-menu top">
<li><a href="../dashboard.php"><span class="text">Dashboard</span></a></li>
<li><a href="filemanager.php"><span class="text">File Manager</span></a></li>
<li class="active"><a href="#"><span class="text">Announcement</span></a></li>
<li><a href="#"><span class="text">Event</span></a></li> <!-- N/A -->
<li><a href="members.php"><span class="text">Members</span></a></li>
<li><a href="certificate/admin.php?page=certificate"><span class="text">Certificates</span></a></li> <!-- N/A -->
<li><a href="../booking_sacraments.php"><span class="text">Reservations</span></a></li>
<li><a href="../donations.php" data-section="dashboard"><span class="text">Donations</span></a></li>
<li><a href="../prayer_admin.php"><span class="text">Prayer Requests</span></a></li>
</ul>
<ul class="side-menu">
<li><a href="../../public/homepage.html" class="logout"><span class="text">Logout</span></a></li>
</ul>
</section>
<!-- SIDEBAR END -->

<section id="content">
<nav>
<i class='bx bx-menu'></i>
<div id="datetime"></div>
<div class="profile">
<span class="greeting">Admin</span>
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
</script>
</body>
</html>