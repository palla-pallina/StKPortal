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
    header("Location: s_funeral.php"); // change to your listing file if different
    exit();
}

$id = intval($_GET['id']);

try {
    // Funeral
    $stmt = $dbh->prepare("SELECT * FROM funerals WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $funeral = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$funeral) {
        header("Location: s_funeral.php?error=notfound");
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

$deceased_name = trim(implode(' ', array_filter([
    $funeral['deceased_firstname'] ?? '',
    $funeral['deceased_middlename'] ?? '',
    $funeral['deceased_lastname'] ?? '',
    $funeral['deceased_suffix'] ?? ''
])));

$contact_name = trim(implode(' ', array_filter([
    $funeral['contact_person_firstname'] ?? '',
    $funeral['contact_person_middlename'] ?? '',
    $funeral['contact_person_lastname'] ?? '',
    $funeral['contact_person_suffix'] ?? ''
])));

// Address (your schema has funeral_address + also province/city/street_no/barangay)
$address_parts = [];
if (!empty($funeral['funeral_address'])) $address_parts[] = $funeral['funeral_address'];
if (!empty($funeral['street_no'])) $address_parts[] = $funeral['street_no'];
if (!empty($funeral['barangay'])) $address_parts[] = $funeral['barangay'];
if (!empty($funeral['city'])) $address_parts[] = $funeral['city'];
if (!empty($funeral['province'])) $address_parts[] = $funeral['province'];
$full_address = implode(', ', array_filter($address_parts));
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>View Funeral Record</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet"/>
<link href='../../../stylesheets/private/sacrament_insert.css' rel='stylesheet'>
</head>
<body>

<div class="record-container">
  <h2>View Funeral Record</h2>

  <?php if (!empty($funeral['decline_reason'])): ?>
    <div class="form-group" style="margin-top:15px;">
      <label>Decline Reason:</label>
      <p class="text-danger"><?= htmlspecialchars($funeral['decline_reason']) ?></p>
    </div>
  <?php endif; ?>

  <!-- ================= RESERVATION ================= -->
  <div class="record-section">
    <h3>Reservation Details</h3>

    <div class="row record-inline">
      <div class="col-md-3 record-row">
        <span class="record-label">Status</span>
        <div class="record-value"><?= v($funeral,'status') ?></div>
      </div>

      <div class="col-md-2 record-row">
        <span class="record-label">Mass Venue</span>
        <div class="record-value"><?= v($funeral,'mass_venue') ?></div>
      </div>

      <div class="col-md-3 record-row">
        <span class="record-label">Presider</span>
        <div class="record-value"><?= v($funeral,'presider') ?></div>
      </div>

      <div class="col-md-3 record-row">
        <span class="record-label">Date Submitted</span>
        <div class="record-value"><?= v($funeral,'created_at') ?></div>
      </div>
    </div>

    <div class="row record-inline">
      <div class="col-md-3 record-row">
        <span class="record-label">Funeral Mass Date</span>
        <div class="record-value"><?= fmtDate($funeral['funeral_date'] ?? '') ?></div>
      </div>

      <div class="col-md-3 record-row">
        <span class="record-label">Funeral Mass Time</span>
        <div class="record-value"><?= fmtTime($funeral['funeral_time'] ?? '') ?></div>
      </div>

      <div class="col-md-6 record-row">
        <span class="record-label">Address</span>
        <div class="record-value"><?= htmlspecialchars($full_address) ?></div>
      </div>
    </div>
  </div>

  <!-- ================= DECEASED ================= -->
  <div class="record-section">
    <h3>Deceased Details</h3>

    <div class="record-row">
      <span class="record-label">Name</span>
      <div class="record-value"><?= htmlspecialchars($deceased_name) ?></div>
    </div>

    <div class="row record-inline">
      <div class="col-md-3 record-row">
        <span class="record-label">Cause of Death</span>
        <div class="record-value">
          <?php if (!empty($funeral['cause_of_death'])): ?>
            <?= v($funeral,'cause_of_death') ?>
          <?php else: ?>
            <em>Not specified</em>
          <?php endif; ?>
        </div>
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
      <div class="col-md-3 record-row">
        <span class="record-label">Relation</span>
        <div class="record-value"><?= v($funeral,'contact_person_relation') ?></div>
      </div>

      <div class="col-md-4 record-row">
        <span class="record-label">Phone</span>
        <div class="record-value"><?= v($funeral,'contact_person_contact_no') ?></div>
      </div>

      <div class="col-md-4 record-row">
        <span class="record-label">Email</span>
        <div class="record-value"><?= v($funeral,'contact_person_email') ?></div>
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
