<?php
$conn = new mysqli('localhost', 'root', '', 'test_tree_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();

// ✅ Restrict to Admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../../../public/login.php");
    exit();
}
$admin_name = !empty($_SESSION['givenname']) ? $_SESSION['givenname'] : $_SESSION['username'];

/* ===============================
   SACRAMENT / TYPE SELECTION
   =============================== */
$type = $_GET['type'] ?? 'baptism'; // default type
$selected = $_GET['month'] ?? date('Y-m');   // YYYY-MM
$selectedDay = $_GET['day'] ?? null; // YYYY-MM-DD

$year  = date('Y', strtotime($selected));
$month = date('m', strtotime($selected));
$startDate = "$year-$month-01";
$endDate   = date("Y-m-t", strtotime($startDate));
$currentMonthName = date("F Y", strtotime($startDate));
$prev = date("Y-m", strtotime("-1 month", strtotime($startDate)));
$next = date("Y-m", strtotime("+1 month", strtotime($startDate)));

/* ===============================
   TYPE CONFIG
   =============================== */
$tables = [
    'baptism' => ['table'=>'baptisms','date'=>'date','time'=>'time','fields'=>['child_firstname','child_middlename','child_lastname','child_suffix']],
    'first_communion' => ['table'=>'first_communion','date'=>'date','time'=>'time','fields'=>['child_firstname','child_middlename','child_lastname','child_suffix']],
    'confirmation' => ['table'=>'confirmations','date'=>'date','time'=>'time','fields'=>['confirm_firstname','confirm_middlename','confirm_lastname','confirm_suffix']],
    'wedding' => ['table'=>'weddings','date'=>'date','time'=>'time','fields'=>['groom_firstname','groom_middlename','groom_lastname','groom_suffix','bride_firstname','bride_middlename','bride_lastname']],
    'funeral' => ['table'=>'funerals','date'=>'funeral_date','time'=>'funeral_time','fields'=>['deceased_firstname','deceased_middlename','deceased_lastname','deceased_suffix']],
];

if(!isset($tables[$type])) $type = 'baptism';

$table = $tables[$type]['table'];
$dateCol = $tables[$type]['date'];
$timeCol = $tables[$type]['time'];
$fields = $tables[$type]['fields'];

/* ===============================
   FETCH MONTHLY RESULTS
   =============================== */
if ($selectedDay) {
    $placeholders = implode(",", array_fill(0, count($fields), "?"));
    $cols = implode(",", array_merge(['id',$timeCol], $fields));
    $stmt = $conn->prepare("SELECT $cols FROM $table WHERE $dateCol = ? ORDER BY $timeCol");
    $stmt->bind_param("s",$selectedDay);
} else {
    $cols = implode(",", array_merge(['id',$dateCol,$timeCol], $fields));
    $stmt = $conn->prepare("SELECT $cols FROM $table WHERE $dateCol BETWEEN ? AND ? ORDER BY $dateCol, $timeCol");
    $stmt->bind_param("ss",$startDate,$endDate);
}

$stmt->execute();
$monthlyResults = $stmt->get_result();
$stmt->close();

/* ===============================
   MONTHLY TOTAL
   =============================== */
$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM $table WHERE $dateCol BETWEEN ? AND ?");
$stmt->bind_param("ss",$startDate,$endDate);
$stmt->execute();
$totalMonthly = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

/* ===============================
   DAILY TOTALS
   =============================== */
$stmt = $conn->prepare("SELECT DATE($dateCol) as day, COUNT(*) AS total FROM $table WHERE $dateCol BETWEEN ? AND ? GROUP BY DATE($dateCol)");
$stmt->bind_param("ss",$startDate,$endDate);
$stmt->execute();
$result = $stmt->get_result();
$dailyCounts = [];
while($row = $result->fetch_assoc()) $dailyCounts[$row['day']] = $row['total'];
$stmt->close();

/* ===============================
   CALENDAR METRICS
   =============================== */
$firstDay = date('w', strtotime($startDate));
$totalDays = date('t', strtotime($startDate));
$currentDay = 1;
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
<link href='../../stylesheets/private/navbar.css' rel='stylesheet'>
<link href='../../stylesheets/private/s_calendar.css' rel='stylesheet'>
<title>StKPortal | Reservations Dashboard</title>
</head>

<body>


<!-------------------------- NAVBAR -------------------------->
<section id="sidebar" <?php if ($hideSidebar) echo 'class="hide"'; ?>>
    <a href="#" class="label">
        <img src="../santa krus logo.png" alt="Logo" class="logo" />
    </a>
    <ul class="side-menu top">
            <li><a href="../dashboard.php"><span class="text">Dashboard</span></a></li>
            <li><a href="../stk/filemanager.php"><span class="text">File Manager</span></a></li>
            <li><a href="../stk/announcement.php"><span class="text">Announcement</span></a></li>
            <li><a href="../event.php"><span class="text">Event</span></a></li>
            <li><a href="../stk/members.php"><span class="text">Members</span></a></li>
            <li><a href="../sacraments/certificate_req/certificate_request.php"><span class="text">Certificate Requests</span></a></li>
            <li class="active"><a href="sacrament_dashboard.php"><span class="text">Reservations</span></a></li>
            <li><a href="../donations.php" data-section="dashboard"><span class="text">Donations</span></a></li>
            <li><a href="../prayer_admin.php"><span class="text">Prayer Requests</span></a></li>
        </ul>
        <ul class="side-menu">
            <li><a href="../logout.php" class="logout"><span class="text">LOGOUT</span></a></li>
        </ul>
</section>
<!------------------------- NAVABAR END ------------------------>

<section id="content">


<!-------------------------- SIDEBAR -------------------------->
<nav>
<i class='bx bx-menu'></i>
<div id="datetime"></div>
<ul>
    <li class="li-dropdown">
      <a href="#"><span class="greeting"><?php echo htmlspecialchars($admin_name); ?></span></a>
      <ul class="dropdown">
          <li><a href="../logout.php" class="logout">Logout</a></li>
      </ul>
  </li>
</ul>
</nav>
<!------------------------- SIDEBAR END ------------------------>



<div class="main-content">

<div class="reservations-header">
  <h1>Reservations Dashboard</h1>
  <select id="eventType" onchange="changeType(this.value)" style="height:39px;font-size:18px;">
    <option value="baptism" <?= $type=='baptism'?'selected':'' ?>>Baptism</option>
    <option value="first_communion" <?= $type=='first_communion'?'selected':'' ?>>First Communion</option>
    <option value="confirmation" <?= $type=='confirmation'?'selected':'' ?>>Confirmation</option>
    <option value="wedding" <?= $type=='wedding'?'selected':'' ?>>Wedding</option>
    <option value="funeral" <?= $type=='funeral'?'selected':'' ?>>Funeral</option>
  </select>
</div>

<hr class="divider">

<div class="directory">
  <div class="dashboard-grid">
    <a href="baptism/s_baptism.php"><div class="dashboard-btn">BAPTISM</div></a>
    <a href="first_communion/s_first_comm.php"><div class="dashboard-btn">FIRST COMMUNION</div></a>
    <a href="confirmation/s_confirmation.php"><div class="dashboard-btn">CONFIRMATION</div></a>
    <a href="wedding/s_wedding.php"><div class="dashboard-btn">WEDDING</div></a>
    <a href="funeral/s_funeral.php"><div class="dashboard-btn">FUNERAL MASS</div></a>
  </div>
</div>

<div class="calendar-layout">

  <!-- LEFT: CALENDAR -->
  <div class="calendar-container">
    <div class="calendar-header">
      <a href="?month=<?= $prev ?>&type=<?= $type ?>" class="month-btn">&lt;</a>
      <h2 class="calendar-title"><?= $currentMonthName ?></h2>
      <a href="?month=<?= $next ?>&type=<?= $type ?>" class="month-btn">&gt;</a>
    </div>

    <!-- WEEK LABELS -->
    <div class="calendar-grid text-center">
      <strong>Sun</strong><strong>Mon</strong><strong>Tue</strong><strong>Wed</strong>
      <strong>Thu</strong><strong>Fri</strong><strong>Sat</strong>

      <?php for($i=0;$i<$firstDay;$i++): ?>
        <div class="calendar-day calendar-empty"></div>
      <?php endfor; ?>

      <?php while($currentDay<=$totalDays):
        $fullDate = date("Y-m-d", strtotime("$startDate +".($currentDay-1)." days"));
        $count = $dailyCounts[$fullDate]??0;
      ?>
        <div class="calendar-day <?= ($fullDate==$selectedDay)?'selected':'' ?>" onclick="filterDay('<?= $fullDate ?>')">
          <div class="calendar-day-number"><?= $currentDay ?></div>
          <?php if($count>0): ?>
            <div class="calendar-count"><?= $count ?> <?= ucfirst(str_replace('_',' ',$type))?>(s)</div>
          <?php endif; ?>
        </div>
      <?php $currentDay++; endwhile; ?>
    </div>
  </div>

  <!-- RIGHT: MONTHLY TOTAL & LIST -->
  <div class="monthly-total-box">
    <h1>Total <?= ucfirst(str_replace('_',' ',$type)) ?> for <br><?= date("F Y", strtotime($startDate)) ?>
      <span class="month-total"><?= $totalMonthly ?></span>
    </h1>

    <div class="monthly-list">
      <ul>
        <?php if($monthlyResults->num_rows>0): ?>
          <?php while($row=$monthlyResults->fetch_assoc()): ?>
            <?php
              if($type=='wedding'){
                $fullname = "Groom: ".$row['groom_firstname']." ".$row['groom_middlename']." ".$row['groom_lastname']." ".$row['groom_suffix'].
                            "<br>Bride: ".$row['bride_firstname']." ".$row['bride_middlename']." ".$row['bride_lastname'];
              }elseif($type=='funeral'){
                $fullname = $row['deceased_firstname']." ".$row['deceased_middlename']." ".$row['deceased_lastname']." ".$row['deceased_suffix'];
              }else{
                $namefields = $fields;
                $fullnameArr=[];
                foreach($namefields as $f) $fullnameArr[]=$row[$f];
                $fullname = implode(' ',$fullnameArr);
              }
            ?>
            <li>
              <strong>ID:</strong> <?= htmlspecialchars($row['id']) ?><br>
              <span class="time"><strong>Time:</strong> <?= htmlspecialchars($row[$timeCol]) ?></span><br>
              <?= $fullname ?>
            </li>
          <?php endwhile; ?>
        <?php else: ?>
          <li style="text-align:center; background:#fff; border-left:none;">
            No <?= ucfirst(str_replace('_',' ',$type)) ?>s for this <?= $selectedDay?'day':'month' ?>.
          </li>
        <?php endif; ?>
      </ul>
    </div>

    <?php if($selectedDay): ?>
      <a href="?month=<?= $selected ?>&type=<?= $type ?>" style="margin-bottom:10px;">Deselect</a>
    <?php endif; ?>
  </div>

</div>

<script>
function filterDay(day){
    const url = new URL(window.location.href);
    url.searchParams.set('day', day);
    window.location.href = url.toString();
}
function changeType(type){
    const url = new URL(window.location.href);
    url.searchParams.set('type', type);
    url.searchParams.delete('day'); // reset day when type changes
    window.location.href = url.toString();
}
</script>

<script>
const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a'); // sidebar effect

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
