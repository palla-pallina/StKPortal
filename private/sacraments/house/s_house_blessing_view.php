<?php
require_once '../connection.php';
session_start();

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: s_house.php"); // change if your listing page has a different filename
    exit();
}

$id = intval($_GET['id']);

try {
    // House Blessing
    $stmt = $dbh->prepare("SELECT * FROM house_blessings WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $house = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$house) {
        header("Location: s_house.php?error=notfound");
        exit();
    }

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// Safe helper
function v($arr, $key) {
    return htmlspecialchars($arr[$key] ?? '');
}

function fmtDate($d) {
    if (empty($d)) return '';
    try { return (new DateTime($d))->format('F j, Y'); } catch (Exception $e) { return htmlspecialchars($d); }
}
function fmtTime($t) {
    if (empty($t)) return '';
    try { return (new DateTime($t))->format('h:i A'); } catch (Exception $e) { return htmlspecialchars($t); }
}

$requesting_name = trim(
    ($house['requesting_firstname'] ?? '') . ' ' .
    ($house['requesting_middlename'] ?? '') . ' ' .
    ($house['requesting_lastname'] ?? '') . ' ' .
    ($house['requesting_suffix'] ?? '')
);

$address = trim(implode(' ', array_filter([
    $house['street_no'] ?? '',
    $house['barangay'] ?? '',
    $house['city'] ?? '',
    $house['province'] ?? ''
])));
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>View House Blessing Record</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet"/>
<link href='../../../stylesheets/private/sacrament_insert.css' rel='stylesheet'>
</head>
<body>

<div class="record-container">
  <h2>View House Blessing Record</h2>

  <?php if (!empty($house['decline_reason'])): ?>
    <div class="form-group" style="margin-top:15px;">
      <label>Decline Reason:</label>
      <p class="text-danger"><?= htmlspecialchars($house['decline_reason']) ?></p>
    </div>
  <?php endif; ?>

  <!-- ================= RESERVATION ================= -->
  <div class="record-section">
    <h3>Reservation Details</h3>

    <div class="row record-inline">
      <div class="col-md-2 record-row">
        <span class="record-label">Date</span>
        <div class="record-value"><?= fmtDate($house['date'] ?? '') ?></div>
      </div>

      <div class="col-md-2 record-row">
        <span class="record-label">Time</span>
        <div class="record-value"><?= fmtTime($house['time'] ?? '') ?></div>
      </div>

      <div class="col-md-3 record-row">
        <span class="record-label">Status</span>
        <div class="record-value"><?= v($house,'status') ?></div>
      </div>

      <div class="col-md-3 record-row">
        <span class="record-label">Date Submitted</span>
        <div class="record-value"><?= v($house,'created_at') ?></div>
      </div>
    </div>

    <div class="record-row" style="margin-top:10px;">
      <span class="record-label">Address</span>
      <div class="record-value"><?= htmlspecialchars($address) ?></div>
    </div>
  </div>

  <!-- ================= REQUESTING PERSON ================= -->
  <div class="record-section">
    <h3>Requesting Person</h3>

    <div class="record-row">
      <span class="record-label">Name</span>
      <div class="record-value"><?= htmlspecialchars($requesting_name) ?></div>
    </div>

    <div class="row record-inline">
      <div class="col-md-3 record-row">
        <span class="record-label">Phone</span>
        <div class="record-value"><?= v($house,'requesting_contact') ?></div>
      </div>

      <div class="col-md-4 record-row">
        <span class="record-label">Email</span>
        <div class="record-value"><?= v($house,'requesting_email') ?></div>
      </div>
    </div>
  </div>

  <!-- ================= NOTES / INTENTION ================= -->
  <div class="record-section">
    <h3>Notes / Special Intention</h3>

    <div class="record-row">
      <span class="record-label">Notes</span>
      <div class="record-value"><?= v($house,'notes') ?></div>
    </div>

    <div class="record-row" style="margin-top:10px;">
      <span class="record-label">Special Intention</span>
      <div class="record-value">
        <?php if (!empty($house['special_intention'])): ?>
          <?= nl2br(htmlspecialchars($house['special_intention'])) ?>
        <?php else: ?>
          <em>None</em>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- ================= BUTTON ================= -->
  <div class="record-buttons">
    <a href="#" class="btn btn-primary" onclick="window.history.back(); return false;">Back</a>
  </div>

</div>
</body>
</html>
