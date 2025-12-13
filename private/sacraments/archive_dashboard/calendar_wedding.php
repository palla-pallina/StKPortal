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

/* ===============================
   DAY FILTER
   =============================== */
$selectedDay = $_GET['day'] ?? null; // format YYYY-MM-DD

if ($selectedDay) {
    // Fetch weddings for selected day
    $stmt = $conn->prepare("
        SELECT id, time,
               groom_firstname, groom_middlename, groom_lastname, groom_suffix,
               bride_firstname, bride_middlename, bride_lastname
        FROM weddings
        WHERE date = ?
        ORDER BY time
    ");
    $stmt->bind_param("s", $selectedDay);
} else {
    // Fetch weddings for the whole month
    $stmt = $conn->prepare("
        SELECT id, date, time,
               groom_firstname, groom_middlename, groom_lastname, groom_suffix,
               bride_firstname, bride_middlename, bride_lastname
        FROM weddings
        WHERE date BETWEEN ? AND ?
        ORDER BY date, time
    ");
    $stmt->bind_param("ss", $startDate, $endDate);
}

$stmt->execute();
$monthlyResults = $stmt->get_result();
$stmt->close();

/* ===============================
   MONTHLY TOTAL
   =============================== */
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM weddings
    WHERE date BETWEEN ? AND ?
");
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$totalMonthly = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

/* ===============================
   DAILY TOTALS
   =============================== */
$stmt = $conn->prepare("
    SELECT DATE(date) as day, COUNT(*) AS total
    FROM weddings
    WHERE date BETWEEN ? AND ?
    GROUP BY DATE(date)
");
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

$dailyCounts = [];
while ($row = $result->fetch_assoc()) {
    $dailyCounts[$row['day']] = $row['total'];
}
$stmt->close();

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
<link href='../../stylesheets/private/navbar.css' rel='stylesheet'>
<link href='../../stylesheets/private/s_calendar.css' rel='stylesheet'>
<title>StKPortal | Weddings Calendar</title>
</head>

<body>

<div class="main-content">

<div class="reservations-header">
  <h1>Weddings Calendar</h1>
</div>

<hr class="divider">

<div class="calendar-layout">

  <!-- LEFT: CALENDAR -->
  <div class="calendar-container">

    <div class="calendar-header">
      <a href="?month=<?= $prev ?>" class="month-btn">&lt;</a>
      <h2 class="calendar-title"><?= $currentMonthName ?></h2>
      <a href="?month=<?= $next ?>" class="month-btn">&gt;</a>
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
            <div class="calendar-count"><?= $count ?> Wedding(s)</div>
          <?php endif; ?>

        </div>
      <?php $currentDay++; endwhile; ?>
    </div>

  </div>

  <!-- RIGHT: MONTHLY TOTAL & LIST -->
  <div class="monthly-total-box">
    <div style="width:100%;">

      <h1>
        Total Weddings for <br><?= date("F Y", strtotime($startDate)) ?>
        <span class="month-total"><?= $totalMonthly ?></span>
      </h1>

      <div class="monthly-list">
        <ul>
          <?php if ($monthlyResults->num_rows > 0): ?>
            <?php while ($row = $monthlyResults->fetch_assoc()): ?>
              <?php
                $groom_fullname = trim(preg_replace('/\s+/', ' ', 
                    $row['groom_firstname'] . ' ' .
                    $row['groom_middlename'] . ' ' .
                    $row['groom_lastname'] . ' ' .
                    $row['groom_suffix']
                ));
                $bride_fullname = trim(preg_replace('/\s+/', ' ', 
                    $row['bride_firstname'] . ' ' .
                    $row['bride_middlename'] . ' ' .
                    $row['bride_lastname']
                ));
              ?>
              <li>
                <strong>ID:</strong> <?= htmlspecialchars($row['id']) ?><br>
                <span class="time">
                  <strong>Time:</strong> <?= htmlspecialchars($row['time']) ?>
                </span><br>
                <strong>Groom:</strong> <?= htmlspecialchars($groom_fullname) ?><br>
                <strong>Bride:</strong> <?= htmlspecialchars($bride_fullname) ?>
              </li>
            <?php endwhile; ?>
          <?php else: ?>
            <li style="text-align:center; background:#fff; border-left:none;">
              No weddings for this <?= $selectedDay ? 'day' : 'month' ?>.
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

<script>
function filterDay(day) {
    const url = new URL(window.location.href);
    url.searchParams.set('day', day);
    window.location.href = url.toString();
}
</script>

</body>
</html>
