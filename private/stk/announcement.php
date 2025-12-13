<?php
// Database connection
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

$conn->close();
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
<link rel="stylesheet" href="../../stylesheets/private/announcement.css" />
<title>Add Announcement</title>
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
            <li class="active"><a href="announcement.php"><span class="text">Announcement</span></a></li>
            <li><a href="../event.php"><span class="text">Event</span></a></li>
            <li><a href="members.php"><span class="text">Members</span></a></li>
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


<nav>
<i class='bx bx-menu' id="menu-bar"></i>
<div id="datetime"></div>
<ul>
    <li class="li-dropdown">
      <a href="#"><span class="greeting"><?php echo htmlspecialchars($admin_name); ?></span></a>
      <ul class="dropdown">
          <li><a href="../logout.php" class="logout">Logout</a></li>
      </ul>
  </li>
</ul>

<!-- Notif -->
<?php if (isset($_GET['posted']) && $_GET['posted'] == 1): ?>
<div id="notif-success" 
     style="
        background:#418033;
        color:white;
        padding:12px 18px;
        border-radius:6px;
        margin:15px;
        font-size:15px;
        animation: fadeOut 1s ease 3s forwards;
     ">
    ✅ Announcement Posted Successfully!
</div>

<style>
@keyframes fadeOut {
    to { opacity:0; transform:translateY(-10px); }
}
</style>

<script>
// Auto-hide after animation ends
setTimeout(()=> {
    const notif = document.getElementById('notif-success');
    if(notif){ notif.remove(); }
}, 4000);
</script>
<?php endif; ?>

</nav>


<div class="main-content">
<div class="page-header">
<h1>Post Announcement</h1>
<a href="all_announcements.php" class="btn">View All Posts</a>
<hr class="divider">
</div>



<form action="save_announcement.php" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <input type="text" name="title" placeholder="Title" required>
    </div>
    <div class="form-group">
        <textarea name="content" rows="5" placeholder="Content" required></textarea>
    </div>

    <div class="form-group">
        <label>Upload Image:</label>
        <input type="file" name="image" accept="image/*" id="image-input">
    </div>

    <!-- Updated preview box -->
    <div id="image-preview">
        <button id="remove-image-btn" type="button">Remove</button>
    </div>

    <button type="submit" class="btn">Save Announcement</button>
</form>
</div>
</section>


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


<script>
/* IMAGE PREVIEW + REMOVE BUTTON + ORIENTATION */
const imageInput = document.getElementById('image-input');
const previewContainer = document.getElementById('image-preview');
const removeBtn = document.getElementById('remove-image-btn');

imageInput.addEventListener('change', function () {
    previewContainer.classList.remove("portrait");
    previewContainer.innerHTML = "";
    previewContainer.appendChild(removeBtn);

    const file = this.files[0];
    if (!file) return;

    const img = document.createElement('img');
    img.src = URL.createObjectURL(file);

    img.onload = function () {
        if (img.naturalHeight > img.naturalWidth) {
            previewContainer.classList.add("portrait");
        }
        previewContainer.appendChild(img);
        removeBtn.style.display = "block";
    };
});

// Remove image
removeBtn.addEventListener("click", function () {
    imageInput.value = "";
    previewContainer.innerHTML = "";
    previewContainer.appendChild(removeBtn);
    removeBtn.style.display = "none";
});
</script>

</body>
</html>
