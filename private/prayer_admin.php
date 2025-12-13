<?php
$host = 'localhost';
$db   = 'test_tree_db';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}

$admin_name = !empty($_SESSION['givenname'])
    ? $_SESSION['givenname']
    : $_SESSION['username'];

if (isset($_GET['delete'])) {
    $delete_id  = intval($_GET['delete']);
    $sql_delete = "DELETE FROM prayer_requests WHERE id = $delete_id";
    $conn->query($sql_delete);
}

if (isset($_POST['action']) && isset($_POST['id'])) {
    $id     = intval($_POST['id']);
    $action = $_POST['action'];

    if ($action === 'publish') {
        $conn->query("UPDATE prayer_requests SET status='published' WHERE id=$id");
    }

    if ($action === 'delete') {
        $conn->query("DELETE FROM prayer_requests WHERE id=$id");
    }
}

$order = (isset($_GET['order']) && $_GET['order'] === 'ASC') ? 'ASC' : 'DESC';

$hideSidebar = false;
$available   = [];

function column_exists($conn, $db, $table, $column)
{
    $db_e    = $conn->real_escape_string($db);
    $table_e = $conn->real_escape_string($table);
    $col_e   = $conn->real_escape_string($column);

    $q = "
        SELECT 1 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = '$db_e' 
          AND TABLE_NAME   = '$table_e' 
          AND COLUMN_NAME  = '$col_e' 
        LIMIT 1
    ";

    $res = $conn->query($q);

    return ($res && $res->num_rows > 0);
}

$table = 'prayer_requests';

$cols = ['id', 'name', 'email', 'intention'];

$optional = [
    'request_text'  => ['request_text', 'request', 'prayer_text', 'prayer'],
    'shared_public' => ['shared_public', 'sharePublic', 'visibility'],
    'anonymous'     => ['anonymous', 'anon'],
    'created_at'    => ['created_at'],
    'approval'      => ['approved', 'is_approved', 'approval', 'approval_status', 'status', 'approved_at'],
    'date'          => ['date'],
    'time'          => ['time']
];

foreach ($optional as $key => $variants) {
    foreach ($variants as $v) {
        if (column_exists($conn, $db, $table, $v)) {
            $cols[]           = $v;
            $available[$key] = $v;
            break;
        }
    }
}

$safe_cols = array_map(function ($c) {
    return '`' . str_replace('`', '``', $c) . '`';
}, $cols);

$select_sql = implode(',', $safe_cols);

$result = $conn->query(
    "SELECT $select_sql FROM `" . $conn->real_escape_string($table) . "` ORDER BY id $order"
);
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
    <link href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">
    <link href="../stylesheets/private/navbar.css" rel="stylesheet">
    <link rel="stylesheet" href="../stylesheets/private/prayer_admin.css" />

    <title>Prayer Requests</title>
</head>

<body>

<!-- NAVBAR -->
<section id="sidebar" <?php if ($hideSidebar) echo 'class="hide"'; ?>>
    <a href="#" class="label">
        <img src="santa krus logo.png" alt="Logo" class="logo" />
    </a>

    <ul class="side-menu top">
        <li><a href="dashboard.php"><span class="text">Dashboard</span></a></li>
        <li><a href="stk/filemanager.php"><span class="text">File Manager</span></a></li>
        <li><a href="stk/announcement.php"><span class="text">Announcement</span></a></li>
        <li><a href="event.php"><span class="text">Event</span></a></li>
        <li><a href="stk/members.php"><span class="text">Members</span></a></li>
        <li><a href="sacraments/certificate_req/certificate_request.php"><span class="text">Certificate Requests</span></a></li>
        <li><a href="sacraments/sacrament_dashboard.php"><span class="text">Reservations</span></a></li>
        <li><a href="donations.php"><span class="text">Donations</span></a></li>
        <li class="active"><a href="prayer_admin.php"><span class="text">Prayer Requests</span></a></li>
    </ul>

    <ul class="side-menu">
        <li><a href="logout.php" class="logout"><span class="text">LOGOUT</span></a></li>
    </ul>
</section>
<!-- NAVBAR END -->

<section id="content">
    <nav>
        <i class='bx bx-menu'></i>
        <div id="datetime"></div>

        <ul>
            <li class="li-dropdown">
                <a href="#">
                    <span class="greeting"><?php echo htmlspecialchars($admin_name); ?></span>
                </a>
                <ul class="dropdown">
                    <li><a href="logout.php" class="logout">Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>

    <section id="prayer-request" class="content-section active">
        <div class="main-content">

            <div class="prayer-controls">
                <h1>PRAYER REQUESTS</h1>

                <div class="search-sort-box">
                    <!--
                    <div class="search-box">
                        <input type="text" placeholder="Search...">
                        <i class="bx bx-search"></i>
                    </div>
                    -->

                    <form method="get" style="display:inline;">
                        <label for="order">Sort by:</label>
                        <span class="sort-box">
                            <select
                                name="order"
                                id="order"
                                class="sort-dropdown"
                                onchange="this.form.submit()"
                            >
                                <option value="DESC" <?php if ($order === 'DESC') echo 'selected'; ?>>Newest</option>
                                <option value="ASC" <?php if ($order === 'ASC') echo 'selected'; ?>>Oldest</option>
                            </select>
                        <i class='bx bx-filter-alt'></i>
                        </span>
                    </form>
                </div>
            </div>

            <hr class="divider">

            <div class="prayer-table-container">
                <table class="prayer-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Intention</th>
                            <th>Prayer</th>
                            <th>Visibility</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>

                                    <?php
                                    $status    = isset($row['status']) ? $row['status'] : 'private';
                                    $anonCol   = $available['anonymous']     ?? 'anonymous';
                                    $sharedCol = $available['shared_public'] ?? 'shared_public';
                                    $prayerCol = $available['request_text']  ?? 'request_text';

                                    $isAnon = isset($row[$anonCol]) && !empty($row[$anonCol]);

                                    $displayName  = $isAnon ? 'Anonymous' : ($row['name']  ?? '');
                                    $displayEmail = $isAnon ? ''          : ($row['email'] ?? '');

                                    $visibility = 'Private';
                                    if (!empty($row[$sharedCol]) || !empty($row['visibility'])) {
                                        $visibility = 'Public';
                                    }

                                    $prayerText = $row[$prayerCol]
                                        ?? $row['request']
                                        ?? $row['prayer']
                                        ?? '';

                                    $dateStr = '';
                                    $timeStr = '';

                                    if (!empty($available['created_at']) && !empty($row[$available['created_at']])) {
                                        $dt      = new DateTime($row[$available['created_at']]);
                                        $dateStr = $dt->format('Y-m-d');
                                        $timeStr = $dt->format('h:i A');
                                    } else {
                                        $dateStr = $row['date'] ?? '';
                                        $timeStr = $row['time'] ?? '';
                                    }
                                    ?>

                                    <td><?php echo htmlspecialchars($displayName); ?></td>
                                    <td><?php echo htmlspecialchars($displayEmail); ?></td>

                                    <?php
                                    $intentLabel = '';
                                    $intentMap   = [
                                        'thanksgiving' => 'Thanksgiving',
                                        'special'      => 'Special Intention',
                                        'soul'         => 'Soul'
                                    ];

                                    $key = strtolower(trim($row['intention'] ?? ''));

                                    if (array_key_exists($key, $intentMap)) {
                                        $intentLabel = $intentMap[$key];
                                    }
                                    ?>

                                    <td><?php echo htmlspecialchars($intentLabel); ?></td>
                                    <td><?php echo nl2br(htmlspecialchars($prayerText)); ?></td>
                                    <td><?php echo htmlspecialchars($visibility); ?></td>
                                    <td><?php echo htmlspecialchars($dateStr); ?></td>
                                    <td><?php echo htmlspecialchars($timeStr); ?></td>
                                    <td><?php echo ucfirst($status); ?></td>

                                    <td class="action-column">
                                        <div style="display: flex; gap: 7px;">
                                            <form method="post">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                                                <button
                                                    type="submit"
                                                    name="action"
                                                    value="publish"
                                                    class="publish-btn <?php echo $status === 'published' ? 'published' : 'not-published'; ?>"
                                                >
                                                    <?php echo $status === 'published' ? 'Published' : 'Publish'; ?>
                                                </button>
                                            </form>

                                            <form method="post">
                                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                                                <button
                                                    type="submit"
                                                    name="action"
                                                    value="delete"
                                                    class="delete-btn"
                                                >
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="empty-row">
                                    No prayer requests found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </section>
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

</body>
</html>

<?php
$conn->close();
?>
