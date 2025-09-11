<?php
$page = $_GET['page'] ?? 'dashboard';

require_once 'dbconfig.php';

// Handle delete if on member page and del param set
if ($page === 'member' && isset($_GET['del'])) {
    $uid = intval($_GET['del']);
    $sql = "DELETE FROM users WHERE id=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id', $uid, PDO::PARAM_STR);
    $query->execute();

    echo "<script>alert('Deleted!');</script>";
    echo "<script>window.location.href='index.php?page=member';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Panel</title>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
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
    <a href="filemanager.php?page=filemanager">File Manager</a>
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
    <div>Admin Panel</div>
  </div>

  <!-- Main Content -->
  <main id="mainContent">
    <?php 
    switch ($page) {
      case 'member':
        // Insert your Members page content here (cleaned up slightly)
        ?>
        <h3>Members</h3>
        <a href="insert.php" class="btn btn-primary">Add Batch</a>
        <br><br>

        <form method="POST" action="search.php" class="form-inline">
          <input type="text" name="query" placeholder="Search by ID, First or Last Name" class="form-control" />
          <button type="submit" class="btn btn-default">Search</button>
        </form>

        <br>

        <div class="table-responsive">
            <table id="mytable" class="table table-bordered table-striped">
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
                      <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $sql = "SELECT * FROM users";
                        $query = $dbh->prepare($sql);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);
                        $cnt = 1;

                        if ($query->rowCount() > 0) {
                            foreach ($results as $result) {
                                ?>
                                <tr>
                                    <td><?php echo htmlentities($cnt); ?></td>
                                    <td><img src="<?php echo htmlentities($result->Photo); ?>" class="img-circle" width="50" height="50"></td>
                                    <td><?php echo htmlentities($result->givenname); ?></td>
                                    <td><?php echo htmlentities($result->middlename); ?></td>
                                    <td><?php echo htmlentities($result->lastname); ?></td>
                                    <td><?php echo htmlentities($result->suffix); ?></td>
                                    <td><?php echo htmlentities($result->birthday); ?></td>
                                    <td><?php echo htmlentities($result->username); ?></td>
                                    <td><?php echo htmlentities($result->email); ?></td>
                                    <td><?php echo htmlentities($result->contact); ?></td>
                                    <td><?php echo htmlentities($result->user_type); ?></td>
                                    <td>
                                        <a href="profile.php?id=<?php echo htmlentities($result->id);?>" class="btn btn-success btn-sm"><span class="glyphicon glyphicon-picture"></span></a>
                                        <a href="update.php?id=<?php echo htmlentities($result->id);?>" class="btn btn-primary btn-sm"><span class="glyphicon glyphicon-pencil"></span></a>
                                        <a href="index.php?page=member&del=<?php echo htmlentities($result->id);?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')"><span class="glyphicon glyphicon-trash"></span></a>
                                    </td>
                                </tr>
                                <?php
                                $cnt++;
                            }
                        } else {
                            echo '<tr><td colspan="12">No records found.</td></tr>';
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='12'>Error: " . $e->getMessage() . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
        break;

      case 'dashboard':
        echo "<h2>Welcome to Dashboard</h2>";
        break;

      default:
        echo "<h2>Page not found</h2>";
        break;
    }
    ?>
  </main>

<script>
  // Sidebar toggle
  document.getElementById('sidebarToggle').addEventListener('click', function() {
    const sidebar = document.querySelector('.sidebar');
    const topbar = document.getElementById('topbar');
    const mainContent = document.getElementById('mainContent');

    if (sidebar.style.left === '0px') {
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
