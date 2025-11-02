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

$result = $conn->query("SELECT * FROM prayer_requests ORDER BY id $order");
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
    #sidebar .side-menu li.active {
        background: var(--grey);
    }

    #content { position: relative; width: calc(100% - 280px); left: 280px; transition: .3s ease; }
    #sidebar.hide ~ #content { width: 100%; left: 0; }
    #content nav {
      height: 56px; background-color: var(--green);
      padding: 0 15px; display: flex; align-items: center; justify-content: space-between;
      position: sticky; top: 0; z-index: 1000;
    }
    #content nav .bx { font-size: 24px; color: var(--dark); cursor: pointer; }
    #content #datetime { color: white; font-size: 14px; font-weight: 500; letter-spacing: 1px; margin-left: 16px; flex-grow: 1; }

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
<li><a href="certificate/admin.php?page=certificate"><span class="text">Certificates</span></a></li> <!-- N/A -->
<li><a href="booking_sacraments.php"><span class="text">Reservations</span></a></li>
<li><a href="donations.php" data-section="dashboard"><span class="text">Donations</span></a></li>
<li class="active"><a href="prayer_admin.php"><span class="text">Prayer Requests</span></a></li>
</ul>
<ul class="side-menu">
<li><a href="../public/homepage.html" class="logout"><span class="text">Logout</span></a></li>
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
            <h1>Prayer Requests</h1>
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
                  <th>Username</th>
                  <th>Email</th>
                  <th>Intention</th>
                  <th>Visibility</th>
                  <th>Time</th>
                  <th>Date</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($result->num_rows > 0): ?>
                  <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                      <td><?php echo $row['id']; ?></td>
                      <td><?php echo htmlspecialchars($row['name']); ?></td>
                      <td><?php echo htmlspecialchars($row['username']); ?></td>
                      <td><?php echo htmlspecialchars($row['email']); ?></td>
                      <td><?php echo htmlspecialchars($row['intention']); ?></td>
                      <td><?php echo htmlspecialchars($row['visibility']); ?></td>
                      <td><?php echo htmlspecialchars($row['time']); ?></td>
                      <td><?php echo htmlspecialchars($row['date']); ?></td>
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
