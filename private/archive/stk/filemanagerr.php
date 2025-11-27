<?php
session_start();
include 'dbconfig.php';

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
<html>
<head>
    <title>File Manager</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />
    <style>
      body {
        margin: 0;
        font-family: Arial, sans-serif;
        overflow-x: hidden;
      }
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
      main {
        margin-left: 250px;
        margin-top: 60px;
        padding: 20px;
      }
      .dropdown-menu {
        padding: 15px;
        border: 1px solid #ddd;
        background: #f9f9f9;
        box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        display: none;
        position: absolute;
        z-index: 10;
        width: 300px;
        margin-top: 5px;
      }
      .dropdown-menu form {
        margin-bottom: 15px;
      }
      table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
      }
      th, td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: left;
        vertical-align: middle;
      }
      th {
        background-color: #4b8a3f;
        color: white;
      }
      tr:hover {
        background-color: #f1f1f1;
      }
      a.file-link, a.folder-link {
        text-decoration: none;
        color: #333;
      }
      a.folder-link:hover, a.file-link:hover {
        text-decoration: underline;
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
    <a href="filemanager.php">File Manager</a>
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
    <div>File Manager</div>
</div>

<!-- Main content -->
<main>
    <?php if ($parent_id != 0): ?>
        <a href="?fid=<?php echo $current_folder_parent; ?>" class="btn btn-default" style="margin-bottom: 15px;">← Back</a>
    <?php endif; ?>

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
    <form method="GET" style="margin-bottom: 20px;">
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
</main>

<script>
function toggleSidebar() {
  const sidebar = document.getElementById('sidebar');
  if (sidebar.style.left === '0px') {
    sidebar.style.left = '-250px';
    document.querySelector('main').style.marginLeft = '0';
    document.querySelector('.topbar').style.left = '0';
  } else {
    sidebar.style.left = '0';
    document.querySelector('main').style.marginLeft = '250px';
    document.querySelector('.topbar').style.left = '250px';
  }
}

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
