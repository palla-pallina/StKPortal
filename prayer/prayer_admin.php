<?php
$host = 'localhost';
$db   = 'test_tree_db';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

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
  // approval/status column variants - used to gate display on user-side
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
  <title>Prayer Requests</title>
  <style>
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
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: var(--lato);
      background: var(--grey);
      overflow-x: hidden;
    }
    #sidebar {
      position: fixed; top: 0; left: 0;
      width: 280px; height: 100%;
      background: var(--light);
      display: flex; flex-direction: column; justify-content: space-between;
      z-index: 2000; transition: .3s ease;
    }
    #sidebar.hide { width: 0 !important; overflow: hidden; pointer-events: none; }
    #sidebar .label { height: 150px; display: flex; align-items: center; justify-content: center; padding-top: 20px; }
    #sidebar .label .logo { width: 100%; height: 100%; object-fit: contain; }
    #sidebar .side-menu { width: 100%; }
    #sidebar .side-menu li { height: 48px; }
    #sidebar .side-menu li a {
      display: flex; align-items: center; justify-content: center;
      height: 100%; font-size: 16px; color: var(--dark); text-decoration: none;
    }
    #sidebar .side-menu li.active a { font-weight: 600; color: var(--green); }
    #content { position: relative; width: calc(100% - 280px); left: 280px; transition: .3s ease; }
    #sidebar.hide ~ #content { width: 100%; left: 0; }
    #content nav {
      height: 56px; background-color: var(--green);
      padding: 0 15px; display: flex; align-items: center; justify-content: space-between;
      position: sticky; top: 0; z-index: 1000;
    }
    #content nav .bx { font-size: 24px; color: var(--dark); cursor: pointer; }
    #content #datetime { color: white; font-size: 14px; font-weight: 500; letter-spacing: 1px; margin-left: 16px; flex-grow: 1; }
    #content nav .profile { display: flex; align-items: center; gap: 10px; }
    #content nav .profile .greeting { font-size: 14px; color: var(--light); font-weight: 500; }
    .content-section { padding: 20px; min-height: calc(100vh - 56px); }
    .main-content { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }
    .prayer-controls { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .prayer-controls h1 { font-family: var(--poppins); font-size: 28px; font-weight: 700; color: var(--dark); margin: 0; }
    .search-sort-group { display: flex; align-items: center; gap: 10px; }
    .search-box { position: relative; }
    .search-box input {
      padding: 8px 30px 8px 10px;
      border-radius: 6px;
      border: 1px solid var(--dark-grey);
    }
    .search-box i {
      position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
      color: var(--dark-grey);
    }
    .sort-button {
      background: var(--green); color: white; border: none; padding: 8px 12px;
      border-radius: 6px; cursor: pointer; font-size: 16px;
    }
    .sort-button:hover { background: #2f5e22; }
    .divider { border: none; border-top: 2px solid black; margin: 12px 0 20px 0; width: 100%; }
    .date-filter { margin-bottom: 16px; font-family: var(--poppins); }
    .date-filter input {
      margin-left: 10px;
      padding: 6px;
      border-radius: 6px;
      border: 1px solid var(--dark-grey);
    }
    .prayer-table-container { overflow-x: auto; }
    .prayer-table {
      width: 100%; border-collapse: collapse; margin-top: 12px; border: 1px solid var(--grey);
    }
    .prayer-table th, .prayer-table td {
      padding: 10px 8px;
      border: 1px solid var(--grey);
      text-align: left;
      font-size: 15px;
      white-space: normal;
      max-width: 300px;
      overflow-wrap: break-word;
      transition: background 0.2s;
    }
    .prayer-table tr:hover { background: #f3f8e6; }
    .prayer-table th { background-color: #f1d3a8; color: black; position: sticky; top: 0; z-index: 1; }
    .prayer-table td.empty-row {
      color: var(--dark-grey);
      text-align: center;
      font-style: italic;
    }
    .action-column button {
      background: var(--red);
      border: none;
      color: white;
      padding: 6px 10px;
      border-radius: 6px;
      cursor: pointer;
    }
    .action-column button:hover { background: #a8322e; }
    .pagination-container { margin-top: 16px; }
    .pagination-bar {
      display: flex; justify-content: space-between; align-items: center;
      font-size: 14px; color: var(--dark-grey);
    }
    .pagination-nav button {
      background: linear-gradient(180deg, #ffffff, #ebebeb);
      color: #333;
      border: 1px solid #ccc;
      padding: 6px 12px;
      border-radius: 6px;
      cursor: pointer;
      margin-left: 5px;
      font-weight: 500;
      transition: all 0.2s ease-in-out;
    }
    .pagination-nav button:hover {
      background: linear-gradient(180deg, #f0f0f0, #dcdcdc);
      border-color: #999;
    }
    .sort-dropdown {
      padding: 8px 32px 8px 12px;
      border-radius: 6px;
      border: 1px solid var(--green);
      background: white;
      font-size: 16px;
      color: var(--dark);
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      position: relative;
      cursor: pointer;
    }
    .sort-dropdown:focus {
      border: 2px solid var(--blue);
      outline: none;
    }
    .sort-dropdown-wrapper {
      position: relative;
      display: inline-block;
    }
    .sort-dropdown-wrapper::after {
      content: '\25BC';
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      pointer-events: none;
      color: var(--green);
      font-size: 14px;
    }
  </style>
</head>
<body>
  <section id="sidebar" <?php if ($hideSidebar) echo 'class="hide"'; ?>>
    <a href="#" class="label">
      <img src="santa krus logo.png" alt="Logo" class="logo" />
    </a>
    <ul class="side-menu top">
      <li><a href="admin.php"><span class="text">Dashboard</span></a></li>
      <li><a href="#"><span class="text">File Manager</span></a></li>
      <li><a href="#"><span class="text">Announcement</span></a></li>
      <li><a href="#"><span class="text">Event</span></a></li>
      <li><a href="#"><span class="text">Members</span></a></li>
      <li><a href="#"><span class="text">Certificates</span></a></li>
      <li><a href="#"><span class="text">Bookings</span></a></li>
      <li><a href="#"><span class="text">Donations</span></a></li>
      <li><a href="#"><span class="text">Prayer Request</span></a></li>
    </ul>
    <ul class="side-menu">
      <li><a href="#" class="logout"><span class="text">Logout</span></a></li>
    </ul>
  </section>

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
