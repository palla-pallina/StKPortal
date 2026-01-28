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
    header("Location: s_celebratory.php"); // change if your listing filename differs
    exit();
}

$id = intval($_GET['id']);

try {
    // Celebratory
    $stmt = $dbh->prepare("SELECT * FROM celebratory WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $cel = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cel) {
        header("Location: s_celebratory.php?error=notfound");
        exit();
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// Helpers
function v($arr, $key) { return htmlspecialchars($arr[$key] ?? ''); }
function fmtDate($d) {
    if (empty($d)) return '';
    try { return (new DateTime($d))->format('F j, Y'); } catch (Exception $e) { return htmlspecialchars($d); }
}
function fmtTime($t) {
    if (empty($t)) return '';
    try { return (new DateTime($t))->format('h:i A'); } catch (Exception $e) { return htmlspecialchars($t); }
}

$contact_name = trim(implode(' ', array_filter([
    $cel['contact_firstname'] ?? '',
    $cel['contact_middlename'] ?? '',
    $cel['contact_lastname'] ?? '',
    $cel['contact_suffix'] ?? '',
])));

$celebrant_name = trim(implode(' ', array_filter([
    $cel['celebrant_firstname'] ?? '',
    $cel['celebrant_middlename'] ?? '',
    $cel['celebrant_lastname'] ?? '',
    $cel['celebrant_suffix'] ?? '',
])));

$wife_name = trim(implode(' ', array_filter([
    $cel['wife_firstname'] ?? '',
    $cel['wife_middlename'] ?? '',
    $cel['wife_lastname'] ?? '',
])));

$husband_name = trim(implode(' ', array_filter([
    $cel['husband_firstname'] ?? '',
    $cel['husband_middlename'] ?? '',
    $cel['husband_lastname'] ?? '',
    $cel['husband_suffix'] ?? '',
])));

$address_parts = [];
if (!empty($cel['street_no'])) $address_parts[] = $cel['street_no'];
if (!empty($cel['barangay'])) $address_parts[] = $cel['barangay'];
if (!empty($cel['city'])) $address_parts[] = $cel['city'];
if (!empty($cel['province'])) $address_parts[] = $cel['province'];
$full_address = implode(', ', array_filter($address_parts));

$type = trim($cel['celebration_type'] ?? '');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>View Celebratory Mass Record</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet"/>
<link href='../../../stylesheets/private/sacrament_insert.css' rel='stylesheet'>
</head>
<body>

<div class="record-container">
  <h2>View Celebratory Mass Record</h2>

  <?php if (!empty($cel['decline_reason'])): ?>
    <div class="form-group" style="margin-top:15px;">
      <label>Decline Reason:</label>
      <p class="text-danger"><?= htmlspecialchars($cel['decline_reason']) ?></p>
    </div>
  <?php endif; ?>

  <!-- ================= RESERVATION ================= -->
  <div class="record-section">
    <h3>Reservation Details</h3>

    <div class="row record-inline">
      <div class="col-md-2 record-row">
        <span class="record-label">Status</span>
        <div class="record-value"><?= v($cel,'status') ?></div>
      </div>

      <div class="col-md-3 record-row">
        <span class="record-label">Mass Date</span>
        <div class="record-value"><?= fmtDate($cel['date'] ?? '') ?></div>
      </div>

      <div class="col-md-3 record-row">
        <span class="record-label">Mass Time</span>
        <div class="record-value"><?= fmtTime($cel['time'] ?? '') ?></div>
      </div>

      <div class="col-md-3 record-row">
        <span class="record-label">Date Submitted</span>
        <div class="record-value"><?= v($cel,'created_at') ?></div>
      </div>
    </div>

    <div class="row record-inline">
      <div class="col-md-3 record-row">
        <span class="record-label">Celebration Type</span>
        <div class="record-value"><?= v($cel,'celebration_type') ?></div>
      </div>

      <div class="col-md-3 record-row">
        <span class="record-label">Mass Venue</span>
        <div class="record-value"><?= v($cel,'mass_venue') ?></div>
      </div>

      <div class="col-md-4 record-row">
        <span class="record-label">Address</span>
        <div class="record-value"><?= htmlspecialchars($full_address) ?></div>
      </div>
    </div>
  </div>

  <!-- ================= CELEBRATION-SPECIFIC ================= -->
  <div class="record-section">
    <h3>
      <?php
        if ($type === 'Thanksgiving') echo 'Thanksgiving Details';
        elseif ($type === 'Birthday') echo 'Birthday Details';
        elseif ($type === 'Wedding Anniversary') echo 'Wedding Anniversary Details';
        else echo 'Special Intention Details';
      ?>
    </h3>

    <?php if ($type === 'Thanksgiving'): ?>
      <div class="record-row">
        <span class="record-label">Thanksgiving</span>
        <div class="record-value">
          <?php if (!empty($cel['thanksgiving'])): ?>
            <?= nl2br(htmlspecialchars($cel['thanksgiving'])) ?>
          <?php else: ?>
            <em>Not provided</em>
          <?php endif; ?>
        </div>
      </div>

    <?php elseif ($type === 'Birthday'): ?>
      <div class="record-row">
        <span class="record-label">Celebrant</span>
        <div class="record-value">
          <?php if (!empty($celebrant_name)): ?>
            <?= htmlspecialchars($celebrant_name) ?>
          <?php else: ?>
            <em>Not provided</em>
          <?php endif; ?>
        </div>
      </div>

      <div class="record-row" style="margin-top:10px;">
        <span class="record-label">Birthdate</span>
        <div class="record-value">
          <?php if (!empty($cel['celebrant_birthdate'])): ?>
            <?= fmtDate($cel['celebrant_birthdate']) ?>
          <?php else: ?>
            <em>Not provided</em>
          <?php endif; ?>
        </div>
      </div>

    <?php elseif ($type === 'Wedding Anniversary'): ?>
      <div class="record-row">
        <span class="record-label">Wife</span>
        <div class="record-value">
          <?php if (!empty($wife_name)): ?>
            <?= htmlspecialchars($wife_name) ?>
          <?php else: ?>
            <em>Not provided</em>
          <?php endif; ?>
        </div>
      </div>

      <div class="record-row" style="margin-top:10px;">
        <span class="record-label">Husband</span>
        <div class="record-value">
          <?php if (!empty($husband_name)): ?>
            <?= htmlspecialchars($husband_name) ?>
          <?php else: ?>
            <em>Not provided</em>
          <?php endif; ?>
        </div>
      </div>

      <div class="record-row" style="margin-top:10px;">
        <span class="record-label">Wedding Date</span>
        <div class="record-value">
          <?php if (!empty($cel['wedding_date'])): ?>
            <?= fmtDate($cel['wedding_date']) ?>
          <?php else: ?>
            <em>Not provided</em>
          <?php endif; ?>
        </div>
      </div>

    <?php else: // Special Intention ?>
      <div class="record-row">
        <span class="record-label">Special Intention</span>
        <div class="record-value">
          <?php if (!empty($cel['special_intention'])): ?>
            <?= nl2br(htmlspecialchars($cel['special_intention'])) ?>
          <?php else: ?>
            <em>Not provided</em>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- ================= CONTACT ================= -->
  <div class="record-section">
    <h3>Contact Person</h3>

    <div class="record-row">
      <span class="record-label">Name</span>
      <div class="record-value">
        <?php if (!empty($contact_name)): ?>
          <?= htmlspecialchars($contact_name) ?>
        <?php else: ?>
          <em>Not provided</em>
        <?php endif; ?>
      </div>
    </div>

    <div class="row record-inline">
      <div class="col-md-3 record-row">
        <span class="record-label">Relation</span>
        <div class="record-value"><?= v($cel,'contact_relation') ?></div>
      </div>

      <div class="col-md-4 record-row">
        <span class="record-label">Phone</span>
        <div class="record-value"><?= v($cel,'contact_number') ?></div>
      </div>

      <div class="col-md-4 record-row">
        <span class="record-label">Email</span>
        <div class="record-value"><?= v($cel,'contact_email') ?></div>
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
