<?php
/* =========================================================
   SESSION & DATABASE CONNECTION
========================================================= */
session_start();
require_once "../../public/connection.php";
date_default_timezone_set('Asia/Manila');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
/* =========================================================
   ADD MASS SCHEDULE
========================================================= */
if (isset($_POST['add_schedule'])) {
    $day      = $_POST['day'];
    $time_in  = $_POST['time_in'];
    $time_out = $_POST['time_out'];

    $mass_time = $time_in . ":00";

    $stmt = $conn->prepare(
        "INSERT INTO mass_schedule (day, mass_time, time_in, time_out)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("ssss", $day, $mass_time, $time_in, $time_out);
    $stmt->execute();
}

/* =========================================================
   UPDATE MASS SCHEDULE (EDIT)
========================================================= */
if (isset($_POST['update_schedule'])) {
    $id       = $_POST['edit_id'];
    $day      = $_POST['edit_day'];
    $time_in  = $_POST['edit_time_in'];
    $time_out = $_POST['edit_time_out'];

    $mass_time = $time_in . ":00";

    $stmt = $conn->prepare(
        "UPDATE mass_schedule
         SET day=?, mass_time=?, time_in=?, time_out=?
         WHERE id=?"
    );
    $stmt->bind_param("ssssi", $day, $mass_time, $time_in, $time_out, $id);
    $stmt->execute();
}

/* =========================================================
   DELETE MASS
========================================================= */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM mass_schedule WHERE id=$id");
}

/* =========================================================
   DAYS
========================================================= */
$days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />
<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
<link href='../../stylesheets/private/navbar.css' rel='stylesheet'>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link href="mass_schedule.css" rel="stylesheet">

<!-- NAVBAR, MAIN CONTENT, FAVICON -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../../stylesheets/private/navbar_updated.css" />
<link rel='stylesheet' href='../../stylesheets/private/main_content.css'>
<link rel="icon" type="image/x-icon" href="../../stylesheets/assets/favicon.ico">
<!--------------------->
<title>StKPortal | Edit Mass Schedule</title>

<style>
.edit-modal{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.5);
    z-index:9999;
}
.edit-modal-content{
    background:#fff;
    width:350px;
    margin:10% auto;
    padding:20px;
    border-radius:8px;
}
</style>
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
        <li><a href="../stk/members.php">Members</a></li>

        <li class="li-dropdown">
            <a href="#" class="active">
                Publish <i class="fa-solid fa-angle-down"></i>
            </a>
            <ul class="dropdown">
                <li><a href="../stk/announcement.php">Announcement</a></li>
                <li><a href="../stk/event.php">Event</a></li>
                <li><a href="../livemass/live_mass.php">Mass Livestream</a></li>
                <li><a href="#" class="active">Mass Schedule</a></li>
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
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>

</nav>
</header>
<!----------------- NAVBAR END ----------------->

<!-- ================= CONTENT ================= -->
<section id="content">

<main class="container-fluid mass-container">
<h1>Weekly Mass Schedule</h1>

<div class="row">

<!-- ================= MASS CALENDAR ================= -->
<div class="col-md-8">
<div class="mass-card">
<div class="mass-calendar">

<?php foreach ($days as $day): ?>
<div class="day-column">
<div class="day-header"><?= $day ?></div>

<?php
$res = mysqli_query($conn,
    "SELECT * FROM mass_schedule WHERE day='$day' ORDER BY mass_time"
);

if (mysqli_num_rows($res) == 0) {
    echo "<p class='no-mass'>No schedule</p>";
}

while ($row = mysqli_fetch_assoc($res)):
?>

<div class="mass-item"
     data-id="<?= $row['id'] ?>"
     data-day="<?= $day ?>"
     data-time-in="<?= date('H:i', strtotime($row['time_in'])) ?>"
     data-time-out="<?= date('H:i', strtotime($row['time_out'])) ?>">

    <div class="mass-time-row">
        <span class="mass-time">
            <?= date("g:i A", strtotime($row['time_in'])) ?>
            -
            <?= date("g:i A", strtotime($row['time_out'])) ?>
        </span>
        <a href="#" class="edit-btn"><i class='bx bx-edit'></i></a>
    </div>

    <div class="priest-row">
        <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this schedule?')">
            <i class='bx bx-trash'></i>
        </a>
    </div>
</div>

<?php endwhile; ?>
</div>
<?php endforeach; ?>

</div>
</div>
</div>

<!-- ================= ADD MASS ================= -->
<div class="col-md-4">
<div class="mass-card">
<h3>Add Mass Schedule</h3>

<form method="POST">
<select name="day" class="form-control" required>
<option value="">Select Day</option>
<?php foreach ($days as $d) echo "<option>$d</option>"; ?>
</select>

<label>Time In</label>
<input type="time" name="time_in" class="form-control" required>

<label>Time Out</label>
<input type="time" name="time_out" class="form-control" required>

<button name="add_schedule" class="btn btn-success btn-block mt-2" style="margin-top:15px;">
Add Schedule
</button>
</form>
</div>
</div>

</div>
</main>
</section>

<!-- ================= EDIT MODAL ================= -->
<div id="editModal" class="edit-modal">
<div class="edit-modal-content">
<h4>Edit Mass</h4>

<form method="POST">
<input type="hidden" name="edit_id" id="edit_id">

<label>Day</label>
<select name="edit_day" id="edit_day" class="form-control">
<?php foreach ($days as $d) echo "<option>$d</option>"; ?>
</select>

<label>Time In</label>
<input type="time" name="edit_time_in" id="edit_time_in" class="form-control">

<label>Time Out</label>
<input type="time" name="edit_time_out" id="edit_time_out" class="form-control">


<br>
<button name="update_schedule" class="btn btn-primary">Update</button>
<button type="button" class="btn btn-default" onclick="closeEdit()">Cancel</button>
</form>
</div>
</div>

<script>
// Sidebar
document.querySelector('.bx-menu').onclick=()=>{
document.getElementById('sidebar').classList.toggle('hide');
};

// Date time
setInterval(()=>{
const n=new Date();
datetime.innerText=n.toLocaleDateString()+" | "+n.toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
},1000);

// Highlight
function liveMassHighlight(){
const n=new Date();
const day=n.toLocaleDateString('en-US',{weekday:'long'});
const time=n.getHours().toString().padStart(2,'0')+':'+n.getMinutes().toString().padStart(2,'0');

document.querySelectorAll('.mass-item').forEach(i=>{
if(i.dataset.day===day && time>=i.dataset.timeIn && time<=i.dataset.timeOut){
i.classList.add('highlight-mass');
}else i.classList.remove('highlight-mass');
});
}
setInterval(liveMassHighlight,1000);
liveMassHighlight();
</script>

<script>
const modal = document.getElementById('editModal');

const editId     = document.getElementById('edit_id');
const editDay    = document.getElementById('edit_day');
const editTimeIn = document.getElementById('edit_time_in');
const editTimeOut= document.getElementById('edit_time_out');

document.querySelectorAll('.edit-btn').forEach(btn => {
  btn.addEventListener('click', (e) => {
    e.preventDefault();

    const item = btn.closest('.mass-item');
    if (!item) return;

    editId.value      = item.getAttribute('data-id') || '';
    editDay.value     = item.getAttribute('data-day') || '';
    editTimeIn.value  = item.getAttribute('data-time-in') || '';
    editTimeOut.value = item.getAttribute('data-time-out') || '';

    modal.style.display = 'block';
  });
});

function closeEdit() {
  modal.style.display = 'none';
}

// close when clicking outside modal content
modal.addEventListener('click', (e) => {
  if (e.target === modal) closeEdit();
});
</script>

</body>
</html>

<?php $conn->close(); ?>
