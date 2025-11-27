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
$admin_name = !empty($_SESSION['givenname']) ? $_SESSION['givenname'] : $_SESSION['username'];


if (isset($_GET['delete'])) {
  $delete_id = intval($_GET['delete']);
  $sql_delete = "DELETE FROM prayer_requests WHERE id = $delete_id";
  $conn->query($sql_delete);
}

$order = isset($_GET['order']) && $_GET['order'] === 'ASC' ? 'ASC' : 'DESC';

$hideSidebar = false;

$available = [];
function column_exists($conn, $db, $table, $column) {
  $db_e = $conn->real_escape_string($db);
  $table_e = $conn->real_escape_string($table);
  $col_e = $conn->real_escape_string($column);
  $q = "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".$db_e."' AND TABLE_NAME='".$table_e."' AND COLUMN_NAME='".$col_e."' LIMIT 1";
  $res = $conn->query($q);
  return ($res && $res->num_rows > 0);
}

$table = 'prayer_requests';
$cols = ['id','name','email','intention'];
$optional = [
  'request_text' => ['request_text','request','prayer_text','prayer'],
  'shared_public' => ['shared_public','sharePublic','visibility'],
  'anonymous' => ['anonymous','anon'],
  'created_at' => ['created_at'],
  'approval' => ['approved','is_approved','approval','approval_status','status','approved_at'],
  'date' => ['date'],
  'time' => ['time']
];

foreach ($optional as $key => $variants) {
  foreach ($variants as $v) {
    if (column_exists($conn, $db, $table, $v)) {
      $cols[] = $v;
      $available[$key] = $v;
      break;
    }
  }
}

$safe_cols = array_map(function($c) use ($conn) { return '`'.str_replace('`','``',$c).'`'; }, $cols);
$select_sql = implode(',', $safe_cols);
$result = $conn->query("SELECT $select_sql FROM `".$conn->real_escape_string($table)."` ORDER BY id $order");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
  <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="../stylesheets/private/prayer_admin.css" />
  <title>Prayer Requests</title>
</head>
<body>


<!-- SIDEBAR -->
<section id="sidebar" <?php if ($hideSidebar) echo 'class="hide"'; ?>>
<a href="#" class="label">
<img src="santa krus logo.png" alt="Logo" class="logo" />
</a>
<ul class="side-menu top">
<li><a href="dashboard.php"><span class="text">Dashboard</span></a></li>
<li><a href="stk/filemanager.php"><span class="text">File Manager</span></a></li>
<li><a href="stk/announcement.php"><span class="text">Announcement</span></a></li>
<li><a href="#"><span class="text">Event</span></a></li> <!-- N/A -->
<li><a href="stk/members.php"><span class="text">Members</span></a></li>
<li><a href="certificate/certificate.php"><span class="text">Certificates</span></a></li>
<li><a href="sacraments/s_dashboard.php"><span class="text">Reservations</span></a></li>
<li><a href="donations.php" data-section="dashboard"><span class="text">Donations</span></a></li>
<li class="active"><a href="prayer_admin.php"><span class="text">Prayer Requests</span></a></li>
</ul>
<ul class="side-menu">
<li><a href="logout.php" class="logout"><span class="text">Logout</span></a></li>
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

    <main>
      <section id="prayer-request" class="content-section active">
        <div class="main-content">
          <div class="prayer-controls">
            <h1>PRAYER REQUESTS</h1>
            <div class="search-sort-group">
              <div class="search-box">
                <input type="text" placeholder="Search...">
                <i class='bx bx-search'></i>
              </div>
              <form method="get" style="display:inline;">
                <label for="order" style="margin-right:5px;">Sort by:</label>
                <span class="sort-dropdown-wrapper">
                  <select name="order" id="order" class="sort-dropdown" onchange="this.form.submit()">
                    <option value="DESC" <?php if ($order === 'DESC') echo 'selected'; ?>>Newest</option>
                    <option value="ASC" <?php if ($order === 'ASC') echo 'selected'; ?>>Oldest</option>
                  </select>
                </span>
              </form>
            </div>
          </div>

          <hr class="divider">

          <div class="date-filter">
            <label for="filter-date"><strong>DATE</strong></label>
            <input type="date" id="filter-date" name="filter-date">
          </div>

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
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                  <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                      <td><?php echo $row['id']; ?></td>
                      <?php
                        $anonCol = isset($available['anonymous']) ? $available['anonymous'] : 'anonymous';
                        $sharedCol = isset($available['shared_public']) ? $available['shared_public'] : 'shared_public';
                        $prayerCol = isset($available['request_text']) ? $available['request_text'] : 'request_text';
                        $isAnon = false;
                        if (isset($row[$anonCol])) {
                          $isAnon = !empty($row[$anonCol]);
                        }
                        $displayName = $isAnon ? 'Anonymous' : (isset($row['name']) && $row['name'] !== null ? $row['name'] : '');
                        $displayEmail = $isAnon ? '' : (isset($row['email']) && $row['email'] !== null ? $row['email'] : '');
                        $visibility = 'Private';
                        if (isset($row[$sharedCol]) && $row[$sharedCol]) {
                          $visibility = 'Public';
                        } elseif (isset($row['visibility']) && $row['visibility']) {
                          $visibility = 'Public';
                        }
                        $prayerText = '';
                        if (isset($row[$prayerCol])) {
                          $prayerText = $row[$prayerCol];
                        } elseif (isset($row['request'])) {
                          $prayerText = $row['request'];
                        } elseif (isset($row['prayer'])) {
                          $prayerText = $row['prayer'];
                        }
                        $dateStr = '';
                        $timeStr = '';
                        if (isset($available['created_at']) && isset($row[$available['created_at']])) {
                          $created = $row[$available['created_at']];
                          if ($created) {
                            $dt = new DateTime($created);
                            $dateStr = $dt->format('Y-m-d');
                            $timeStr = $dt->format('h:i A');
                          }
                        } else {
                          if (isset($row['date'])) $dateStr = $row['date'];
                          if (isset($row['time'])) $timeStr = $row['time'];
                        }
                      ?>
                      <td><?php echo htmlspecialchars($displayName); ?></td>
                      <td><?php echo htmlspecialchars($displayEmail); ?></td>
                      <?php
                        $intentLabel = '';
                        if (isset($row['intention']) && $row['intention'] !== null && $row['intention'] !== '') {
                          $intentMap = [
                            'thanksgiving' => 'Thanksgiving',
                            'special' => 'Special Intention',
                            'soul' => 'Soul'
                          ];
                          $key = strtolower(trim($row['intention']));
                          if (array_key_exists($key, $intentMap)) {
                            $intentLabel = $intentMap[$key];
                          } else {
                            $intentLabel = '';
                          }
                        }
                      ?>
                      <td><?php echo htmlspecialchars($intentLabel); ?></td>
                      <td><?php echo nl2br(htmlspecialchars($prayerText)); ?></td>
                      <td><?php echo htmlspecialchars($visibility); ?></td>
                      <td><?php echo htmlspecialchars($dateStr); ?></td>
                      <td><?php echo htmlspecialchars($timeStr); ?></td>
                      <td class="action-column">
                        <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this request?');">
                          <button>Delete</button>
                        </a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="9" class="empty-row">No prayer requests found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <div class="pagination-container">
            <div class="pagination-bar">
              <div class="entry-info">0 – 0 of 0 Entries</div>
              <div class="pagination-nav">
                <button class="page-btn">← Previous</button>
                <button class="page-btn">Next →</button>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
  </section>

  <script>
    const menuIcon = document.querySelector('.bx-menu');
    const sidebar = document.querySelector('#sidebar');
    menuIcon.addEventListener('click', () => sidebar.classList.toggle('hide'));
    function updateDateTime() {
      const now = new Date();
      const options = { hour: '2-digit', minute: '2-digit', hour12: true };
      const formattedDate = now.toLocaleDateString();
      const formattedTime = now.toLocaleTimeString([], options);
      document.getElementById('datetime').textContent = `${formattedDate} | ${formattedTime}`;
    }
    setInterval(updateDateTime, 1000);
    updateDateTime();
  </script>
</body>
</html>
<?php
$conn->close();
?>
