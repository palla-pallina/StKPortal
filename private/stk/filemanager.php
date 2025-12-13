<?php
session_start();
include 'connection.php';

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../../public/login.php");
    exit();
}
$admin_name = !empty($_SESSION['givenname']) ? $_SESSION['givenname'] : $_SESSION['username'];

// Simulate a logged-in user for testing
$_SESSION['login_id'] = 1;
$login_id = $_SESSION['login_id'];

// Handle folder creation
if (isset($_POST['create_folder'])) {
    $folder_name = trim($_POST['folder_name']);
    if (!empty($folder_name)) {
        $stmt = $dbh->prepare("INSERT INTO folders (name, parent_id, user_id) VALUES (?, ?, ?)");
        $stmt->execute([$folder_name, $_POST['fid'], $login_id]);
    }
}

// Handle file upload
if (isset($_POST['upload']) && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $file_name = basename($file['name']);
    $folder_id = $_POST['fid'];

    if ($file['error'] == 0 && $file['size'] <= 512000) {
        $upload_dir = "uploads/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $target_path = $upload_dir . time() . "_" . $file_name;
        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            $stmt = $dbh->prepare("INSERT INTO files (filename, filepath, folder_id, user_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$file_name, $target_path, $folder_id, $login_id]);
        }
    }
}

$parent_id = $_GET['fid'] ?? 0;
$search = $_GET['search'] ?? '';

// Get parent folder's parent_id for Back button
$current_folder_parent = 0;
if ($parent_id != 0) {
    $stmt = $dbh->prepare("SELECT parent_id FROM folders WHERE id = ? AND user_id = ?");
    $stmt->execute([$parent_id, $login_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $current_folder_parent = $row['parent_id'];
    }
}

// Fetch folders (with search if applicable)
if ($search !== '') {
    $stmt = $dbh->prepare("SELECT * FROM folders WHERE parent_id = ? AND user_id = ? AND name LIKE ?");
    $stmt->execute([$parent_id, $login_id, "%$search%"]);
} else {
    $stmt = $dbh->prepare("SELECT * FROM folders WHERE parent_id = ? AND user_id = ?");
    $stmt->execute([$parent_id, $login_id]);
}
$folders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch files (with search if applicable)
if ($search !== '') {
    $stmt = $dbh->prepare("SELECT * FROM files WHERE folder_id = ? AND user_id = ? AND filename LIKE ?");
    $stmt->execute([$parent_id, $login_id, "%$search%"]);
} else {
    $stmt = $dbh->prepare("SELECT * FROM files WHERE folder_id = ? AND user_id = ?");
    $stmt->execute([$parent_id, $login_id]);
}
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper function for human-readable file size
function human_filesize($bytes, $decimals = 2) {
    $size = ['B','KB','MB','GB','TB','PB','EB','ZB','YB'];
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />

<link href='../../stylesheets/private/navbar.css' rel='stylesheet'>
<link href='../../stylesheets/private/filemanager.css' rel='stylesheet'>
<title>File Manager</title>
</head>

<body>


<!-------------------------- NAVBAR -------------------------->
<section id="sidebar" <?php if ($hideSidebar) echo 'class="hide"'; ?>>
    <a href="#" class="label">
        <img src="../santa krus logo.png" alt="Logo" class="logo" />
    </a>
    <ul class="side-menu top">
            <li><a href="../dashboard.php"><span class="text">Dashboard</span></a></li>
            <li class="active"><a href="filemanager.php"><span class="text">File Manager</span></a></li>
            <li><a href="announcement.php"><span class="text">Announcement</span></a></li>
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
    <?php if ($parent_id != 0): ?>
        <a href="?fid=<?php echo $current_folder_parent; ?>" class="btn btn-default" style="margin-bottom: 15px;">← Back</a>
    <?php endif; ?>

    <div class="page-header">
        <h1>File Manager</h1>
        <hr class="divider">
    </div>
    
    <!-- Dropdown Button -->
    <div style="position: relative; margin-bottom: 30px;">
        <button id="toggleDropdownBtn" class="btn btn-primary">Add New ▼</button>

        <div id="dropdownMenu" class="dropdown-menu">
            <!-- Create Folder Form -->
            <form method="POST" style="margin-bottom: 15px;">
                <h4>Create Folder</h4>
                <input type="hidden" name="fid" value="<?php echo htmlspecialchars($parent_id); ?>">
                <input type="text" name="folder_name" class="form-control" placeholder="Folder Name" required>
                <button type="submit" name="create_folder" class="btn btn-primary" style="margin-top: 10px; width: 100%;">Create</button>
            </form>

            <!-- Upload File Form -->
            <form method="POST" enctype="multipart/form-data">
                <h4>Upload File</h4>
                <input type="hidden" name="fid" value="<?php echo htmlspecialchars($parent_id); ?>">
                <input type="file" name="file" class="form-control" required>
                <button type="submit" name="upload" class="btn btn-success" style="margin-top: 10px; width: 100%;">Upload</button>
            </form>
        </div>
    </div>

    <!-- Search Form -->
    <form method="GET">
        <input type="hidden" name="fid" value="<?php echo htmlspecialchars($parent_id); ?>">
        <input type="text" name="search" class="form-control" placeholder="Search folders or files..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit" class="btn btn-info" style="margin-top: 10px;">Search</button>
    </form>

    <!-- Table for folders and files -->
    <h3>File manager</h3>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Size</th>
                <th>Modified</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($folders as $folder):
                $stmt = $dbh->prepare("SELECT MAX(modified) as last_modified FROM files WHERE folder_id = ? AND user_id = ?");
                $stmt->execute([$folder['id'], $login_id]);
                $last_modified = $stmt->fetchColumn() ?: '—';

                $folder_size = '—'; // optional size calc
            ?>
                <tr>
                    <td><a href="?fid=<?php echo $folder['id']; ?>" class="folder-link">📁 <?php echo htmlspecialchars($folder['name']); ?></a></td>
                    <td><?php echo $folder_size; ?></td>
                    <td><?php echo $last_modified; ?></td>
                </tr>
            <?php endforeach; ?>

            <?php foreach ($files as $file):
                $filepath = $file['filepath'];
                $filesize = is_file($filepath) ? human_filesize(filesize($filepath)) : 'N/A';
                $modified = is_file($filepath) ? date("Y-m-d H:i:s", filemtime($filepath)) : 'N/A';
            ?>
                <tr>
                    <td><a href="<?php echo htmlspecialchars($filepath); ?>" class="file-link" target="_blank">📄 <?php echo htmlspecialchars($file['filename']); ?></a></td>
                    <td><?php echo $filesize; ?></td>
                    <td><?php echo $modified; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>



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