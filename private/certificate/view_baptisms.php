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

require_once __DIR__ . '/connection.php';

$search = trim($_GET['q'] ?? '');
$where = '';
$params = [];
$types = '';

if ($search !== '') {
  $where = "WHERE (child_first LIKE CONCAT('%', ?, '%')
               OR child_last LIKE CONCAT('%', ?, '%')
               OR father_first LIKE CONCAT('%', ?, '%')
               OR father_last LIKE CONCAT('%', ?, '%')
               OR mother_first LIKE CONCAT('%', ?, '%')
               OR mother_last LIKE CONCAT('%', ?, '%'))";
  $params = [$search,$search,$search,$search,$search,$search];
  $types = 'ssssss';
}

$sql = "SELECT id, child_first, child_last, birth_date, baptism_date,
               father_first, father_last, mother_first, mother_last, created_at
        FROM baptism_cert
        $where
        ORDER BY id DESC LIMIT 200";

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
<title>Admin Dashboard</title>

<style>
    
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

a {
    text-decoration: none;
}

li {
    list-style: none;
}

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
    --success: #16a34a;
    --warning: #f59e0b;
    --danger: #dc2626;
}

html, body {
    height: 100%;
    overflow-x: hidden;
}

body.dark {
    --light: #0C0C1E;
    --grey: #060714;
    --dark: #FBFBFB;
}

body {
    background: var(--grey);
    font-family: var(--lato);
}

/* SIDEBAR */
#sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 280px;
    height: 100%;
    background: var(--light);
    z-index: 2000;
    transition: .3s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 0;
}

#sidebar.hide {
    width: 0 !important;
    overflow: hidden;
    pointer-events: none;
}

#sidebar .label {
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding-top: 20px;
}

#sidebar .label .logo {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

#sidebar .side-menu { width: 100%; }
#sidebar .side-menu li { height: 48px; }
#sidebar .side-menu li a {
    display: flex; align-items: center; justify-content: center;
    height: 100%; font-size: 16px; color: var(--dark); text-decoration: none;
}
#sidebar .side-menu li.active {
    background: var(--grey);
}

#content {
    position: relative;
    width: calc(100% - 280px);
    left: 280px;
    transition: .3s ease;
    min-width: 0;
}

#sidebar.hide ~ #content {
    width: 100%;
    left: 0;
}

#content nav {
    height: 56px;
    background-color: #418033;
    padding: 0 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    grid-gap: 16px;
    position: sticky;
    top: 0;
    z-index: 1000;
}

#content nav .bx {
    font-size: 24px;
    color: var(--dark);
    cursor: pointer;
}

#content nav a {
    color: var(--dark);
}

#content nav .nav-link {
    font-size: 16px;
    transition: .3s ease;
}

#content nav #datetime {
    color: white;
    font-size: 14px;
    font-weight: 500;
    letter-spacing: 1px;
    margin-left: 16px;
    flex-grow: 1;
}

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

.content-section {
    display: none;
    padding: 20px;
    min-height: calc(100vh - 56px);
}

.content-section.active {
    display: block;
}
/* SIDEBAR END */

.main-content {
    background: white;
    /*border-radius: 31px;*/
    padding: 51px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 100%;
}

.main-content h1 {
    font-family: var(--poppins);
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 16px;
}

.main-content h2 {
    font-family: var(--poppins);
    font-size: 18px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 12px;
}

@media screen and (max-width: 768px) {
    #sidebar {
        width: 200px;
    }
    #content {
        width: calc(100% - 200px);
        left: 200px;
    }
    #content nav .nav-link {
        display: none;
    }
}

@media screen and (max-width: 576px) {
    #content nav .nav-link {
        display: none;
    }
}



</style>
</head>

<body>

<!-- SIDEBAR -->
<section id="sidebar" <?php if ($hideSidebar) echo 'class="hide"'; ?>>
<a href="#" class="label">
<img src="../santa krus logo.png" alt="Logo" class="logo" />
</a>
<ul class="side-menu top">
<li class="active"><a href="dashboard.php"><span class="text">Dashboard</span></a></li>
<li><a href="#"><span class="text">File Manager</span></a></li>
<li><a href="#"><span class="text">Announcement</span></a></li>
<li><a href="#"><span class="text">Event</span></a></li>
<li><a href="#"><span class="text">Members</span></a></li>
<li><a href="#"><span class="text">Certificates</span></a></li>
<li><a href="booking_sacraments.php"><span class="text">Reservations</span></a></li>
<li><a href="donations.php" data-section="dashboard"><span class="text">Donations</span></a></li>
<li><a href="prayer_admin.php"><span class="text">Prayer Requests</span></a></li>
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
<span class="greeting"><?php echo htmlspecialchars($admin_name); ?></span>
<a href="#" class="profile-link"><i class='bx bx-user'></i></a>
</div>
</nav>


<div class="main-content">


<h3>Baptisms</h3>
<form class="form-inline" method="get" style="margin:10px 0">
  <input class="form-control" name="q" placeholder="Search names..." value="<?= htmlspecialchars($search) ?>">
  <button class="btn btn-primary">Search</button>
  <a class="btn btn-default" href="export_baptisms_csv.php<?= $search ? '?q='.urlencode($search) : '' ?>">Export CSV</a>
  <a class="btn btn-default" href="admin.php">Back to Admin</a>
</form>
<table class="table table-bordered table-striped">
<thead><tr>
  <th>ID</th><th>Child</th><th>Parents</th><th>Birth</th><th>Baptism</th><th>Created</th>
</tr></thead>
<tbody>
<?php while($r=$res->fetch_assoc()): ?>
<tr>
  <td><?= (int)$r['id'] ?></td>
  <td><?= htmlspecialchars($r['child_first'].' '.$r['child_last']) ?></td>
  <td><?= htmlspecialchars(trim(($r['father_first'].' '.$r['father_last']))) ?>
      <?= ($r['mother_first']||$r['mother_last']) ? ' &amp; '.htmlspecialchars(trim($r['mother_first'].' '.$r['mother_last'])) : '' ?>
  </td>
  <td><?= htmlspecialchars($r['birth_date'] ?: '—') ?></td>
  <td><?= htmlspecialchars($r['baptism_date'] ?: '—') ?></td>
  <td><?= htmlspecialchars($r['created_at']) ?></td>
</tr>
<?php endwhile; ?>
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
</script>
</body>
</html>

<?php $stmt->close(); $conn->close(); ?>