<?php
require_once '../../connection.php';
session_start();

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
    header("Location: ../../homepage.php");
    exit();
}

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: s_confirmation.php");
    exit();
}

$id = intval($_GET['id']);

try {
    // Confirmation
    $stmt = $dbh->prepare("SELECT * FROM confirmations WHERE id=:id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $confirmation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$confirmation) {
        header("Location: s_confirmation.php?error=notfound");
        exit();
    }

    // MAIN NINONG
    $stmt = $dbh->prepare("SELECT * FROM ninong_tbl WHERE confirmation_id=:id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $ninong = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    // MAIN NINANG
    $stmt = $dbh->prepare("SELECT * FROM ninang_tbl WHERE confirmation_id=:id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $ninang = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    // ADDITIONAL NINONGS
    $stmt = $dbh->prepare("SELECT * FROM as_ninong_tbl WHERE confirmation_id=:id");
    $stmt->execute([':id' => $id]);
    $asNinongs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ADDITIONAL NINANGS
    $stmt = $dbh->prepare("SELECT * FROM as_ninang_tbl WHERE confirmation_id=:id");
    $stmt->execute([':id' => $id]);
    $asNinangs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// Safe helper
function v($arr, $key) {
    return htmlspecialchars($arr[$key] ?? '');
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>View Confirmation Record</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet"/>
<link href='view.css' rel='stylesheet'>
</head>
<body>

<div class="record-container">
  <h2>View Confirmation Record</h2>

  <!-- ================= RESERVATION ================= -->
  <div class="record-section">
    <h3>Reservation Details</h3>
    <div class="row record-inline">

      <div class="col-md-2 record-row"><span class="record-label">Type</span><div class="record-value"><?= v($confirmation,'confirmation_type') ?></div></div>
      <div class="col-md-2 record-row"><span class="record-label">Date</span><div class="record-value"><?= v($confirmation,'date') ?></div></div>
      <div class="col-md-2 record-row"><span class="record-label">Time</span><div class="record-value"><?= v($confirmation,'time') ?></div></div>
      <div class="col-md-3 record-row"><span class="record-label">Contact</span><div class="record-value"><?= v($confirmation,'contact') ?></div></div>
      <div class="col-md-3 record-row"><span class="record-label">Email</span><div class="record-value"><?= v($confirmation,'email') ?></div></div>

    </div>
  </div>

  <!-- ================= CONFIRMEE ================= -->
  <div class="record-section">
    <h3>Confirmee Details</h3>

    <div class="record-row">
      <span class="record-label">Name</span>
      <div class="record-value">
        <?= v($confirmation,'confirm_firstname') ?>
        <?= v($confirmation,'confirm_middlename') ?>
        <?= v($confirmation,'confirm_lastname') ?>
        <?= v($confirmation,'confirm_suffix') ?>
      </div>
    </div>

    <div class="row record-inline">
      <div class="col-md-2 record-row"><span class="record-label">Gender</span><div class="record-value"><?= v($confirmation,'confirm_gender') ?></div></div>
      <div class="col-md-2 record-row"><span class="record-label">Birthdate</span><div class="record-value"><?= v($confirmation,'birth_date') ?></div></div>
      <div class="col-md-4 record-row"><span class="record-label">Birth Place</span><div class="record-value"><?= v($confirmation,'birth_place') ?></div></div>
      <div class="col-md-4 record-row"><span class="record-label">Nationality</span><div class="record-value"><?= v($confirmation,'confirm_nat') ?></div></div>
    </div>
  </div>

  <!-- ================= PARENTS ================= -->
  <div class="record-section">
    <h3>Parents</h3>

    <div class="record-row">
      <span class="record-label">Father</span>
      <div class="record-value">
        <?= v($confirmation,'father_firstname') ?>
        <?= v($confirmation,'father_middlename') ?>
        <?= v($confirmation,'father_lastname') ?>
        <?= v($confirmation,'father_suffix') ?>
      </div>
    </div>

    <div class="record-row" style="margin-top:10px;">
      <span class="record-label">Mother</span>
      <div class="record-value">
        <?= v($confirmation,'mother_firstname') ?>
        <?= v($confirmation,'mother_middlename') ?>
        <?= v($confirmation,'mother_lastname') ?>
      </div>
    </div>
  </div>

  <!-- ================= MAIN SPONSORS ================= -->
  <div class="record-section">
    <h3>Main Sponsors</h3>

    <div class="record-row">
      <span class="record-label">Ninong</span>
      <div class="record-value">
        <?= htmlspecialchars(trim(($ninong['ninong_firstname'] ?? '').' '.($ninong['ninong_middlename'] ?? '').' '.($ninong['ninong_lastname'] ?? '').' '.($ninong['ninong_suffix'] ?? ''))) ?>
      </div>
    </div>

    <div class="record-row" style="margin-top:10px;">
      <span class="record-label">Ninang</span>
      <div class="record-value">
        <?= htmlspecialchars(trim(($ninang['ninang_firstname'] ?? '').' '.($ninang['ninang_middlename'] ?? '').' '.($ninang['ninang_lastname'] ?? ''))) ?>
      </div>
    </div>
  </div>

  <!-- ================= ADDITIONAL SPONSORS ================= -->
  <div class="record-section">
    <h3>Additional Sponsors</h3>

    <span class="record-label">Additional Ninongs</span>
    <?php if ($asNinongs): foreach ($asNinongs as $n): ?>
      <div class="record-row">
        <div class="record-value">
          <?= htmlspecialchars(trim($n['as_ninong_firstname']." ".$n['as_ninong_middlename']." ".$n['as_ninong_lastname']." ".$n['as_ninong_suffix'])) ?>
        </div>
      </div>
    <?php endforeach; else: ?>
      <em>No additional ninongs</em>
    <?php endif; ?>

    <span class="record-label" style="margin-top:12px;">Additional Ninangs</span>
    <?php if ($asNinangs): foreach ($asNinangs as $n): ?>
      <div class="record-row">
        <div class="record-value">
          <?= htmlspecialchars(trim($n['as_ninang_firstname']." ".$n['as_ninang_middlename']." ".$n['as_ninang_lastname'])) ?>
        </div>
      </div>
    <?php endforeach; else: ?>
      <em>No additional ninangs</em>
    <?php endif; ?>
  </div>

  <!-- ================= FILES ================= -->
  <div class="record-section">
    <h3>Documents</h3>

    <div class="record-row">
      <span class="record-label">Birth Certificate</span>
      <div class="record-value">
        <?php if (!empty($confirmation['birth_certificate'])): ?>
          <a href="../view_file.php?file=<?= urlencode($confirmation['birth_certificate']) ?>" target="_blank" class="glyphicon glyphicon-file">View File</a>
        <?php else: ?> <em>Not provided</em> <?php endif; ?>
      </div>
    </div>

    <div class="record-row">
      <span class="record-label">Baptismal Certificate</span>
      <div class="record-value">
        <?php if (!empty($confirmation['baptismal_certificate'])): ?>
          <a href="../view_file.php?file=<?= urlencode($confirmation['baptismal_certificate']) ?>" target="_blank" class="glyphicon glyphicon-file">View File</a>
        <?php else: ?> <em>Not provided</em> <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- ================= BUTTON ================= -->
  <div class="record-buttons">
    <a href="s_confirmation.php" class="btn btn-primary">Back</a>
  </div>

</div>
</body>
</html>
