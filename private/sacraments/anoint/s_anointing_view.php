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
    header("Location: s_anointing.php"); // change if your listing file is different
    exit();
}

$id = intval($_GET['id']);

try {
    // Anointing of the Sick request
    $stmt = $dbh->prepare("SELECT * FROM anointing_of_sick_requests WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $req = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$req) {
        header("Location: s_anointing.php?error=notfound");
        exit();
    }

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// Helpers
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

$sick_name = trim(implode(' ', array_filter([
    $req['sick_firstname'] ?? '',
    $req['sick_middlename'] ?? '',
    $req['sick_lastname'] ?? '',
    $req['sick_suffix'] ?? ''
])));

$contact_name = trim(implode(' ', array_filter([
    $req['contact_person_firstname'] ?? '',
    $req['contact_person_middlename'] ?? '',
    $req['contact_person_lastname'] ?? '',
    $req['contact_person_suffix'] ?? ''
])));

// Build address (best-effort)
$address_parts = [];
if (!empty($req['venue'])) $address_parts[] = $req['venue'];
if (!empty($req['hospital_name'])) $address_parts[] = $req['hospital_name'];
if (!empty($req['street_no'])) $address_parts[] = $req['street_no'];
if (!empty($req['barangay'])) $address_parts[] = $req['barangay'];
if (!empty($req['city'])) $address_parts[] = $req['city'];
if (!empty($req['province'])) $address_parts[] = $req['province'];
$full_address = implode(', ', array_filter($address_parts));
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>View Anointing of the Sick Request</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet"/>
<link href='../../../stylesheets/private/sacrament_insert.css' rel='stylesheet'>
</head>
<body>

<div class="record-container">
  <h2>View Anointing of the Sick Request</h2>

  <!-- ================= RESERVATION ================= -->
  <div class="record-section">
    <h3>Reservation Details</h3>

    <div class="row record-inline">
      <div class="col-md-3 record-row">
        <span class="record-label">Status</span>
        <div class="record-value"><?= v($req,'status') ?></div>
      </div>

      <div class="col-md-3 record-row">
        <span class="record-label">Date</span>
        <div class="record-value"><?= fmtDate($req['date'] ?? '') ?></div>
      </div>

      <div class="col-md-3 record-row">
        <span class="record-label">Time</span>
        <div class="record-value"><?= fmtTime($req['time'] ?? '') ?></div>
      </div>

      <div class="col-md-3 record-row">
        <span class="record-label">Date Submitted</span>
        <div class="record-value"><?= v($req,'created_at') ?></div>
      </div>
    </div>

    <div class="record-row" style="margin-top:10px;">
      <span class="record-label">Address / Venue</span>
      <div class="record-value">
        <?php if (!empty($full_address)): ?>
          <?= htmlspecialchars($full_address) ?>
        <?php else: ?>
          <em>Not specified</em>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- ================= SICK PERSON ================= -->
  <div class="record-section">
    <h3>Sick Person Details</h3>

    <div class="record-row">
      <span class="record-label">Name</span>
      <div class="record-value"><?= htmlspecialchars($sick_name) ?></div>
    </div>

    <div class="row record-inline">
      <div class="col-md-3 record-row">
        <span class="record-label">Age</span>
        <div class="record-value"><?= v($req,'age') ?></div>
      </div>

      <div class="col-md-9 record-row">
        <span class="record-label">Condition</span>
        <div class="record-value"><?= v($req,'sick_condition') ?></div>
      </div>
    </div>
  </div>

  <!-- ================= CONTACT PERSON ================= -->
  <div class="record-section">
    <h3>Contact Person</h3>

    <div class="record-row">
      <span class="record-label">Name</span>
      <div class="record-value"><?= htmlspecialchars($contact_name) ?></div>
    </div>

    <div class="row record-inline">
      <div class="col-md-4 record-row">
        <span class="record-label">Relation</span>
        <div class="record-value"><?= v($req,'contact_relation') ?></div>
      </div>

      <div class="col-md-4 record-row">
        <span class="record-label">Phone</span>
        <div class="record-value"><?= v($req,'contact_number') ?></div>
      </div>

      <div class="col-md-4 record-row">
        <span class="record-label">Email</span>
        <div class="record-value"><?= v($req,'contact_email') ?></div>
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
