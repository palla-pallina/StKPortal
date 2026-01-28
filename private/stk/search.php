<?php
require_once 'connection.php';

$results = [];
$query = '';

if (isset($_POST['query'])) {
    $query = '%' . $_POST['query'] . '%';
    $sql = "SELECT * FROM users 
            WHERE givenname LIKE :search 
            OR middlename LIKE :search 
            OR lastname LIKE :search 
            OR email LIKE :search 
            OR username LIKE :search 
            OR user_type LIKE :search";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':search', $query, PDO::PARAM_STR);
} else {
    $stmt = $dbh->prepare("SELECT * FROM users");
}

$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User Search & List</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
    <link href='../../stylesheets/private/members.css' rel='stylesheet'>
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
        .img-circle {
            border-radius: 50%;
        }
    </style>
</head>
<body>

<!--------------------------------------- SIDEBAR ------------------------------------------->
<section id="sidebar" class="hide">
<a href="#" class="label">
<img src="../santa krus logo.png" alt="Logo" class="logo" />
</a>
<ul class="side-menu top">
<li><a href="../dashboard.php"><span class="text">Dashboard</span></a></li>
<li><a href="filemanager.php"><span class="text">File Manager</span></a></li>
<li><a href="announcement.php"><span class="text">Announcement</span></a></li>
<li><a href="#"><span class="text">Event</span></a></li> <!-- N/A -->
<li class="active"><a href="#"><span class="text">Members</span></a></li>
<li><a href="../sacraments/certificate_req/certificate_request.php"><span class="text">Certificates</span></a></li>
<li><a href="../booking_sacraments.php"><span class="text">Reservations</span></a></li>
<li><a href="../donations.php" data-section="dashboard"><span class="text">Donations</span></a></li>
<li><a href="../prayer_admin.php"><span class="text">Prayer Requests</span></a></li>
</ul>
<ul class="side-menu">
<li><a href="../logout.php" class="logout"><span class="text">Logout</span></a></li>
</ul>
</section>
<!------------------------------------- SIDEBAR END ----------------------------------------->
<section id="content">

<!--------------------------------------- NAVBAR -------------------------------------------->
<nav>
  <i class='bx bx-menu'></i>
  <div id="datetime"></div>
  <div class="profile">
    <span class="greeting"><?php echo htmlspecialchars($admin_name); ?></span>
    <?php var_dump($result['photo']); ?>
    <img class="profile-img" border-radius="50%" width="50" height="50" src="../profilepics/<?php echo htmlentities(basename($result['photo'])); ?>" alt="Profile Photo">
    
  </div>
</nav>
<!------------------------------------ NAVBAR END ------------------------------------------>

  <!-- Main Content -->
  <main>
    <div class="container">
        <h3>User Search & List</h3>

        <form method="POST" class="form-inline" style="margin-bottom: 20px;">
            <div class="form-group">
                <input type="text" name="query" class="form-control" placeholder="Search users..." value="<?php echo isset($_POST['query']) ? htmlentities($_POST['query']) : ''; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="index.php" class="btn btn-default">Reset</a>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Photo</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Suffix</th>
                        <th>Birthday</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>User Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($stmt->rowCount() > 0): 
                        $cnt = 1;
                        foreach ($results as $row): ?>
                            <tr>
                                <td><?php echo htmlentities($cnt); ?></td>
                                <td>
                                    <?php if (!empty($row->Photo)): ?>
                                        <img src="<?php echo htmlentities($row->Photo); ?>" class="img-circle" width="50" height="50" alt="User Photo">
                                    <?php else: ?>
                                        <img src="https://via.placeholder.com/50" class="img-circle" alt="No Photo">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlentities($row->givenname); ?></td>
                                <td><?php echo htmlentities($row->middlename); ?></td>
                                <td><?php echo htmlentities($row->lastname); ?></td>
                                <td><?php echo htmlentities($row->suffix); ?></td>
                                <td><?php echo htmlentities($row->birthday); ?></td>
                                <td><?php echo htmlentities($row->username); ?></td>
                                <td><?php echo htmlentities($row->email); ?></td>
                                <td><?php echo htmlentities($row->contact); ?></td>
                                <td><?php echo htmlentities($row->user_type); ?></td>
                                <td>
                                    <a href="profilee.php?user_id=<?php echo htmlentities($row->user_id); ?>" class="btn btn-success btn-sm" title="Upload Photo">
                                        <span class="glyphicon glyphicon-picture"></span>
                                    </a>
                                    <a href="update.php?user_id=<?php echo htmlentities($row->user_id); ?>" class="btn btn-primary btn-sm" title="Edit User">
                                        <span class="glyphicon glyphicon-pencil"></span>
                                    </a>
                                    <a href="index.php?del=<?php echo htmlentities($row->user_id); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')" title="Delete User">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </a>
                                </td>
                            </tr>
                        <?php
                        $cnt++;
                        endforeach; 
                    else: ?>
                        <tr><td colspan="12" class="text-center">No records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
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
