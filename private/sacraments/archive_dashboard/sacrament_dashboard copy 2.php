<?php
$conn = new mysqli('localhost', 'root', '', 'test_tree_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// ✅ Restrict to Admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../../../public/login.php");
    exit();
}
$admin_name = !empty($_SESSION['givenname']) ? $_SESSION['givenname'] : $_SESSION['username'];

/* ===============================
   RESERVATION TYPE FILTER
================================ */
$resType = $_GET['type'] ?? 'all';

$tableMap = [
  'baptism' => 'baptisms',
  'first_communion' => 'first_communion',
  'confirmation' => 'confirmations',
  'wedding' => 'weddings',
  'funeral_mass' => 'funerals'
];

$dateColumnMap = [
  'baptisms'         => 'date',
  'first_communion'  => 'date',
  'confirmations'    => 'date',
  'weddings'         => 'date',
  'funerals'         => 'funeral_date'
];

if ($resType !== 'all' && !isset($tableMap[$resType])) {
  $resType = 'all';
}


/* ===============================
   MONTH CONTROLLER
   =============================== */
$selected = $_GET['month'] ?? date('Y-m');   // YYYY-MM
$year  = date('Y', strtotime($selected));
$month = date('m', strtotime($selected));

$startDate = "$year-$month-01";
$endDate   = date("Y-m-t", strtotime($startDate));

$currentMonthName = date("F Y", strtotime($startDate));

/* ===============================
   PREV / NEXT MONTH
   =============================== */
$prev = date("Y-m", strtotime("-1 month", strtotime($startDate)));
$next = date("Y-m", strtotime("+1 month", strtotime($startDate)));
$selectedDay = $_GET['day'] ?? null;

if ($selectedDay) {

    if ($resType === 'all') {

        $sql = "
          SELECT 'Baptism' AS type, id, time, child_firstname AS fname, child_lastname AS lname
          FROM baptisms WHERE date = ?

          UNION ALL

          SELECT 'Confirmation', id, time, confirm_firstname, confirm_lastname
          FROM confirmations WHERE date = ?

          UNION ALL

          SELECT 'Wedding', id, time, bride_firstname, bride_lastname
          FROM weddings WHERE date = ?

          UNION ALL

          SELECT 'Funeral', id, time, deceased_firstname, deceased_lastname
          FROM funerals WHERE date = ?

          ORDER BY time
        ";

        $stmt = $conn->prepare($sql);
        if (!$stmt) die("SQL Error: " . $conn->error);

        $stmt->bind_param("ssss",
            $selectedDay,
            $selectedDay,
            $selectedDay,
            $selectedDay
        );

    } else {

        $tbl = $tableMap[$resType];

        $sql = "SELECT * FROM $tbl WHERE date = ? ORDER BY time";
        $stmt = $conn->prepare($sql);
        if (!$stmt) die("SQL Error: " . $conn->error);

        $stmt->bind_param("s", $selectedDay);
    }

} else {

/* ===============================
   MONTHLY TOTAL (DYNAMIC)
================================ */

/* ===============================
   MONTHLY TOTAL (DYNAMIC + SAFE)
================================ */

if ($resType === 'all') {

    $sql = "
      SELECT SUM(total) AS total FROM (
          SELECT COUNT(*) total FROM baptisms 
              WHERE {$dateColumnMap['baptisms']} BETWEEN ? AND ?

          UNION ALL
          SELECT COUNT(*) FROM confirmations 
              WHERE {$dateColumnMap['confirmations']} BETWEEN ? AND ?

          UNION ALL
          SELECT COUNT(*) FROM weddings 
              WHERE {$dateColumnMap['weddings']} BETWEEN ? AND ?

          UNION ALL
          SELECT COUNT(*) FROM funerals 
              WHERE {$dateColumnMap['funerals']} BETWEEN ? AND ?
      ) AS combined
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) die("SQL Error (Monthly Total ALL): " . $conn->error);

    $stmt->bind_param(
        "ssssssss",
        $startDate, $endDate,
        $startDate, $endDate,
        $startDate, $endDate,
        $startDate, $endDate
    );

} else {

    $tbl  = $tableMap[$resType];
    $dateCol = $dateColumnMap[$tbl];

    $sql = "
      SELECT COUNT(*) AS total
      FROM $tbl
      WHERE $dateCol BETWEEN ? AND ?
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) die("SQL Error (Monthly Total Single): " . $conn->error);

    $stmt->bind_param("ss", $startDate, $endDate);
}

$stmt->execute();
$totalMonthly = $stmt->get_result()->fetch_assoc()['total'];

/* ===============================
   MONTHLY LIST (DYNAMIC)
================================ */

if ($resType === 'all') {

    $sql = "
      SELECT 'Baptism' AS type, id, time,
             child_firstname AS fname,
             child_middlename AS mname,
             child_lastname AS lname,
             child_suffix AS suffix,
             date AS event_date
      FROM baptisms
      WHERE date BETWEEN ? AND ?

      UNION ALL

      SELECT 'Confirmation', id, time,
             confirm_firstname,
             confirm_middlename,
             confirm_lastname,
             '',
             date
      FROM confirmations
      WHERE date BETWEEN ? AND ?

      UNION ALL

      SELECT 'Wedding', id, time,
             bride_firstname,
             bride_middlename,
             bride_lastname,
             '',
             date
      FROM weddings
      WHERE date BETWEEN ? AND ?

      UNION ALL

      SELECT 'Funeral', id, time,
             deceased_firstname,
             deceased_middlename,
             deceased_lastname,
             '',
             funeral_date
      FROM funerals
      WHERE funeral_date BETWEEN ? AND ?

      ORDER BY event_date, time
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) die("SQL Error (Monthly List ALL): " . $conn->error);

    $stmt->bind_param(
        "ssssssss",
        $startDate, $endDate,
        $startDate, $endDate,
        $startDate, $endDate,
        $startDate, $endDate
    );

} else {

    $tbl  = $tableMap[$resType];
    $dateCol = $dateColumnMap[$tbl];

    $sql = "SELECT * FROM $tbl WHERE $dateCol BETWEEN ? AND ? ORDER BY $dateCol, time";
    $stmt = $conn->prepare($sql);
    if (!$stmt) die("SQL Error (Monthly List Single): " . $conn->error);

    $stmt->bind_param("ss", $startDate, $endDate);
}

$stmt->execute();
$monthlyResults = $stmt->get_result();
$stmt->close();


}

/* ===============================
   CALENDAR METRICS
   =============================== */
$firstDay   = date('w', strtotime($startDate));
$totalDays  = date('t', strtotime($startDate));
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
<link href='../../stylesheets/private/s_calendar.css' rel='stylesheet'>
<title>StKPortal | Baptism Reservations</title>
</head>



<body>
<div class="main-content">

<div class="reservations-header">
<h1>Reservations Dashboard</h1>

<select id="eventType" style="height:39px; font-size:18px;">
  <option value="all" <?= $resType=='all'?'selected':'' ?>>All</option>
  <option value="baptism" <?= $resType=='baptism'?'selected':'' ?>>Baptism</option>
  <option value="first_communion" <?= $resType=='first_communion'?'selected':'' ?>>First Communion</option>
  <option value="confirmation" <?= $resType=='confirmation'?'selected':'' ?>>Confirmation</option>
  <option value="wedding" <?= $resType=='wedding'?'selected':'' ?>>Wedding</option>
  <option value="funeral_mass" <?= $resType=='funeral_mass'?'selected':'' ?>>Funeral Mass</option>
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
      <a href="?month=<?= $prev ?>&type=<?= $resType ?>" class="month-btn">&lt;</a>
      <h2 class="calendar-title"><?= $currentMonthName ?></h2>
      <a href="?month=<?= $next ?>&type=<?= $resType ?>" class="month-btn">&gt;</a>
    </div>

    <!-- WEEK LABELS -->
    <div class="calendar-grid text-center">
      <strong>Sun</strong><strong>Mon</strong><strong>Tue</strong><strong>Wed</strong>
      <strong>Thu</strong><strong>Fri</strong><strong>Sat</strong>

      <?php for ($i = 0; $i < $firstDay; $i++): ?>
        <div class="calendar-day calendar-empty"></div>
      <?php endfor; ?>

      <?php while ($currentDay <= $totalDays): 
        $fullDate = date("Y-m-d", strtotime("$startDate +".($currentDay-1)." days"));
        $count = $dailyCounts[$fullDate] ?? 0;
      ?>
      <div class="calendar-day <?= ($fullDate === $selectedDay) ? 'selected' : '' ?>" 
          onclick="filterDay('<?= $fullDate ?>')">
          <div class="calendar-day-number"><?= $currentDay ?></div>
          <?php if ($count > 0): ?>
            <div class="calendar-count"><?= $count ?> Baptism(s)</div>
          <?php endif; ?>
      </div>

        
      <?php $currentDay++; endwhile; ?>
    </div>

  </div>

  <!-- RIGHT: MONTHLY TOTAL & LIST -->
  <div class="monthly-total-box">
    <div style="width:100%;">
<?php
$typeLabel = $resType === 'all'
    ? 'All Reservations'
    : ucwords(str_replace('_', ' ', $resType));
?>

<h1>
  Total <?= $typeLabel ?> for <br><?= date("F Y", strtotime($startDate)) ?>
  <span class="month-total"><?= $totalMonthly ?></span>
</h1>

      <div class="monthly-list">
        <ul>
          <?php if ($monthlyResults->num_rows > 0): ?>
            <?php while ($row = $monthlyResults->fetch_assoc()): ?>
              <?php
                $fullname = trim(preg_replace('/\s+/', ' ', 
                    $row['child_firstname'] . ' ' .
                    $row['child_middlename'] . ' ' .
                    $row['child_lastname'] . ' ' .
                    $row['child_suffix']
                ));
              ?>
              <li>
                <strong>ID:</strong> <?= htmlspecialchars($row['id']) ?><br>
                <span class="time"><strong>Time:</strong> <?= htmlspecialchars($row['time']) ?></span><br>
                <?= htmlspecialchars($fullname) ?>
              </li>
            <?php endwhile; ?>
          <?php else: ?>
            <li style="text-align:center; background:#fff; border-left:none;">
              No <?= strtolower($typeLabel) ?> for this <?= $selectedDay ? 'day' : 'month' ?>.
            </li>
          <?php endif; ?>
        </ul>
      </div>

      <?php if ($selectedDay): ?>
        <a href="?month=<?= $selected ?>" style="margin-bottom:10px;">
            Deselect
        </a>
      <?php endif; ?>
    </div>
  </div>
</div>
<!--------------------------------------- MAIN END --------------------------------------------->


<script>
function filterDay(day) {
    const url = new URL(window.location.href);
    url.searchParams.set('day', day);
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

<script>
document.getElementById("eventType").addEventListener("change", function () {
    const type = this.value;
    const url = new URL(window.location.href);
    url.searchParams.set("type", type);
    url.searchParams.delete("day");
    window.location.href = url.toString();
});
</script>

</body>
</html>
