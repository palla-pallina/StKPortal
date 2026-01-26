<?php
require_once 'dbconfig.php';
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

$user_id = $_SESSION['user_id'];

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../../public/login.php");
    exit();
}
$admin_name = !empty($_SESSION['givenname']) ? $_SESSION['givenname'] : $_SESSION['username'];

/*--------- NOTIFICATION HANDLING START
- for notification icon at navbar
- retrieves all notifications belonging to logged in user */
$stmt = $conn->prepare("SELECT * FROM admin_notifications ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$unreadCount = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM admin_notifications WHERE status = 'Unread'");
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $unreadCount = $result['cnt'] ?? 0;
    $stmt->close();
}
/*--------- NOTIFICATION HANDLING END */

// Handle delete if del param is set
if (isset($_GET['del'])) {
    $uid = intval($_GET['del']);
    $sql = "DELETE FROM users WHERE user_id = :user_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':user_id', $uid, PDO::PARAM_INT);
    $query->execute();

    echo "<script>alert('Deleted!');</script>";
    echo "<script>window.location.href='members.php';</script>";
    exit();
}

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

<link rel='stylesheet' href='../../stylesheets/private/main_content.css'>
<link href='../../stylesheets/private/members.css' rel='stylesheet'>

<!-- NAVBAR, MAIN CONTENT, FAVICON -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../../stylesheets/private/navbar_updated.css" />

<link rel="icon" type="image/x-icon" href="../../stylesheets/assets/favicon.ico">
<!--------------------->
<title>StKPortal | Manage Members</title>
</head>

<body>

<!-------------------- NAVBAR -------------------->
<header class="custom-header">
<nav class="navbar custom-navbar">

    <!-- LEFT: Logo -->
    <div class="nav-left">
        <a href="dashboard.php">
            <img src="../santa krus logo.png"
                 alt="Parish Logo"
                 class="logo">
        </a>
    </div>

    <!-- CENTER: Navigation links -->
    <ul class="nav-links nav-center">
        <li><a href="../dashboard.php">Home</a></li>
        <li><a href="members.php" class="active">Members</a></li>

        <li class="li-dropdown">
            <a href="#">
                Publish <i class="fa-solid fa-angle-down"></i>
            </a>
            <ul class="dropdown">
                <li><a href="announcement.php">Announcement</a></li>
                <li><a href="event.php">Event</a></li>
                <li><a href="../livemass/live_mass.php">Mass Livestream</a></li>
                <li><a href="../mass/mass_schedule.php">Mass Schedule</a></li>
                <li><a href="../prayer_admin.php">Prayer Requests</a></li>
            </ul>
        </li>

        <li><a href="../sacraments/sacrament_dashboard.php">Sacrament Reservations</a></li>
        <li><a href="../sacraments/certificate_req/certificate_request.php">Certificate Requests</a></li>
        <li><a href="../donations.php">Donations</a></li>
    </ul>

    <!-- RIGHT: Notifications + User -->
    <div class="nav-right">
        <ul class="nav-links">
            <li class="nav-notification">
                <a href="../notification/admin_notif.php">
                    <span class="bell">&#128276;</span>

                    <?php if (!empty($unreadCount) && $unreadCount > 0): ?>
                        <span class="badge"><?= $unreadCount ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <li class="divider">|</li>

            <li class="li-dropdown">
                <a href="#" style="margin-right: -10px;"><?= htmlspecialchars($admin_name) ?></a>
                <ul class="dropdown">
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>

</nav>
</header>
<!----------------- NAVBAR END ----------------->
<section id="content">
<div class="main-content">

<div class="page-header">
<h1>Members</h1>
<div class="reservations-controls">
<form method="GET" action="members.php" class="form-inline" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">

    <!-- User Type Filter -->
    <select name="user_type" class="form-control" onchange="this.form.submit()">
        <option value="">All Types</option>
        <option value="User"   <?= ($_GET['user_type'] ?? '') === 'User' ? 'selected' : '' ?>>User</option>
        <option value="Priest" <?= ($_GET['user_type'] ?? '') === 'Priest' ? 'selected' : '' ?>>Priest</option>
        <option value="Admin"  <?= ($_GET['user_type'] ?? '') === 'Admin' ? 'selected' : '' ?>>Admin</option>
    </select>

    <!-- Created At Sort -->
    <select name="sort" class="form-control" onchange="this.form.submit()">
        <option value="newest" <?= ($_GET['sort'] ?? 'newest') === 'newest' ? 'selected' : '' ?>>
            Newest First
        </option>
        <option value="oldest" <?= ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>
            Oldest First
        </option>
    </select>

</form>

    
<div class="search-box">
    <form method="POST" action="" class="form-inline">

        <div class="search-group">
        <input type="text" name="search" placeholder="Search by ID, First or Last Name"
                class="form-control"
                value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>" />
        <button type="submit" class="search-btn" aria-label="Search">
        <span class="glyphicon glyphicon-search"></span>
        </div>

        <?php if (!empty($_POST['search'])): ?>
            <a href="members.php" class="btn btn-warning">Clear</a>
        <?php endif; ?>

    </form>
</div>

<a href="insert.php">
    <button class="add-btn" id="addReservationBtn">
    <i class='bx bx-plus'></i>
    </button>
</a>
</div>

<hr class="divider">
</div>

<div class="sacraments-table-container">
  <table class="sacraments-table">

    <thead>
      <tr>
        <th>#</th>
        <!--<th>Username</th>-->
        <th>Name</th>
        <th>Birthday</th>
        <th>Email</th>
        <th>Phone</th>
        <th>User Type</th>
        <th class="action-column">Action</th>
        <th>Created At</th>
      </tr>
    </thead>

    <tbody>
    <?php
    // ================= PAGINATION =================
    $limit = 10; // rows per page
    $page  = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    $page  = max($page, 1);
    $offset = ($page - 1) * $limit;

    $where = [];
    $params = [];

    // ================= COUNT TOTAL =================
    $countSql = "SELECT COUNT(*) FROM users";
    if ($where) {
        $countSql .= " WHERE " . implode(" AND ", $where);
    }

    $countQuery = $dbh->prepare($countSql);
    $countQuery->execute($params);
    $totalRows = $countQuery->fetchColumn();

    $totalPages = ceil($totalRows / $limit);

    /* ================= SEARCH ================= */
    if (!empty($_GET['search'])) {
        $where[] = "(
            givenname LIKE :search OR
            middlename LIKE :search OR
            lastname LIKE :search OR
            suffix LIKE :search OR
            email LIKE :search OR
            contact LIKE :search
        )";
        $params[':search'] = '%' . trim($_GET['search']) . '%';
    }

    /* ================= USER TYPE FILTER ================= */
    if (!empty($_GET['user_type'])) {
        $where[] = "user_type = :user_type";
        $params[':user_type'] = $_GET['user_type'];
    }

    /* ================= SORT ================= */
    $sortOrder = "created_at DESC";
    if (!empty($_GET['sort']) && $_GET['sort'] === 'oldest') {
        $sortOrder = "created_at ASC";
    }

    /* ================= BUILD SQL ================= */
    $sql = "SELECT * FROM users";

    if ($where) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " ORDER BY $sortOrder LIMIT :limit OFFSET :offset";

    $query = $dbh->prepare($sql);

    foreach ($params as $key => $val) {
        $query->bindValue($key, $val);
    }

    $query->bindValue(':limit',  $limit,  PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, PDO::PARAM_INT);

    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);



    try {
        if ($query->rowCount() > 0) {
            foreach ($results as $result) {
    ?>

        <tr>
            <td><?= htmlentities($result->user_id); ?></td>

            <td>
                <?= htmlentities($result->givenname); ?>
                <?= htmlentities($result->middlename); ?>
                <?= htmlentities($result->lastname); ?>
                <?= htmlentities($result->suffix); ?>
            </td>

            <td>
                <?php
                    $date = new DateTime($result->birth_date);
                    echo htmlentities($date->format('Y/m/d'));
                ?>
            </td>
            <td><?= htmlentities($result->email); ?></td>
            <td><?= htmlentities($result->contact); ?></td>
            <td><?= htmlentities($result->user_type); ?></td>

            <!-- ✅ ACTION COLUMN -->
            <td class="action-column">
                <div class="action-buttons">

                    <a href="update.php?user_id=<?= $result->user_id; ?>" class="btn-edit" title="Edit">
                        <i class="glyphicon glyphicon-pencil"></i>
                    </a>

                    <a href="members.php?del=<?= $result->user_id; ?>"
                       onclick="return confirm('Are you sure you want to delete this user?')"
                       class="btn-delete"
                       title="Delete">
                        <i class="glyphicon glyphicon-trash"></i>
                    </a>

                </div>
            </td>

            <td>
                <?php
                    $date = new DateTime($result->created_at);
                    echo htmlentities($date->format('Y/m/d')) . '<br>' . htmlentities($date->format('h:i A'));
                ?>
            </td>
        </tr>

    <?php
            }
        } else {
            echo '<tr><td colspan="8">No users found.</td></tr>';
        }

    } catch (PDOException $e) {
        echo "<tr><td colspan='8'>Error: " . $e->getMessage() . "</td></tr>";
    }
    ?>
    </tbody>

  </table>
<?php if ($totalPages > 1): ?>
<div class="pagination-container">
    <nav aria-label="Page navigation">
        <ul class="pagination">

            <!-- Previous -->
            <li class="<?= $page <= 1 ? 'disabled' : '' ?>">
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">
                    &laquo;
                </a>
            </li>

            <!-- Pages -->
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="<?= $i === $page ? 'active' : '' ?>">
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>

            <!-- Next -->
            <li class="<?= $page >= $totalPages ? 'disabled' : '' ?>">
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">
                    &raquo;
                </a>
            </li>

        </ul>
    </nav>
</div>
<?php endif; ?>

</div>
</section>


</body>
</html>