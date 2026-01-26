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
    header("Location: s_baptism.php");
    exit();
}

$id = intval($_GET['id']);

try {
    // Baptism
    $stmt = $dbh->prepare("SELECT * FROM baptisms WHERE id=:id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $baptism = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$baptism) {
        header("Location: s_baptism.php?error=notfound");
        exit();
    }

    // MAIN NINONG
    $stmt = $dbh->prepare("SELECT * FROM ninong_tbl WHERE baptism_id=:id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $ninong = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    // MAIN NINANG
    $stmt = $dbh->prepare("SELECT * FROM ninang_tbl WHERE baptism_id=:id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $ninang = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    // ADDITIONAL NINONGS
    $stmt = $dbh->prepare("SELECT * FROM as_ninong_tbl WHERE baptism_id=:id");
    $stmt->execute([':id' => $id]);
    $asNinongs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ADDITIONAL NINANGS
    $stmt = $dbh->prepare("SELECT * FROM as_ninang_tbl WHERE baptism_id=:id");
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
<title>View Baptism Record</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet"/>
<link href='../../../stylesheets/private/sacrament_insert.css' rel='stylesheet'>
</head>
<body>

<div class="record-container">
  <h2>View Baptism Record</h2>

<!-- GENERATE PDF -->
<a href='../../certificate/baptism_cert.php?id=<?= $baptism['id'] ?>' class='btn btn-success btn-sm'>Generate PDF</a>

<?php if (!empty($result->decline_reason)): ?>
    <div class="form-group">
        <label>Decline Reason:</label>
        <p class="text-danger"><?php echo htmlspecialchars($result->decline_reason); ?></p>
    </div>
<?php endif; ?>

  <!-- ================= RESERVATION ================= -->
  <div class="record-section">
    <h3>Reservation Details</h3>
    <div class="row record-inline">

      <div class="col-md-2 record-row"><span class="record-label">Type</span><div class="record-value"><?= v($baptism,'baptism_type') ?></div></div>
      <div class="col-md-2 record-row"><span class="record-label">Baptism Date</span><div class="record-value"><?= v($baptism,'date') ?></div></div>
      <div class="col-md-2 record-row"><span class="record-label">Baptism Time</span><div class="record-value"><?= v($baptism,'time') ?></div></div>
      <div class="col-md-2 record-row"><span class="record-label">Contact</span><div class="record-value"><?= v($baptism,'contact') ?></div></div>
      <div class="col-md-2 record-row"><span class="record-label">Email</span><div class="record-value"><?= v($baptism,'email') ?></div></div>
    </div>
    <div class="row record-inline">
      <div class="col-md-3 record-row">
        <span class="record-label">Presiding Priest</span><div class="record-value"><?= v($baptism,'presiding_priest') ?></div>
      </div>
      <div class="col-md-3 record-row">
          <span class="record-label">Date Submitted</span><div class="record-value"><?= v($baptism,'created_at') ?></div>
      </div>
    </div>
  </div>

  <!-- ================= CONFIRMEE ================= -->
  <div class="record-section">
    <h3>Child Details</h3>

    <div class="record-row">
      <span class="record-label">Name</span>
      <div class="record-value">
        <?= v($baptism,'child_firstname') ?>
        <?= v($baptism,'child_middlename') ?>
        <?= v($baptism,'child_lastname') ?>
        <?= v($baptism,'child_suffix') ?>
      </div>
    </div>

    <div class="row record-inline">
      <div class="col-md-2 record-row"><span class="record-label">Gender</span><div class="record-value"><?= v($baptism,'child_gender') ?></div></div>
      <div class="col-md-2 record-row"><span class="record-label">Birthdate</span><div class="record-value"><?= v($baptism,'birth_date') ?></div></div>
      <div class="col-md-4 record-row"><span class="record-label">Birth Place</span><div class="record-value"><?= v($baptism,'birth_place') ?></div></div>
      <div class="col-md-4 record-row"><span class="record-label">Nationality</span><div class="record-value"><?= v($baptism,'child_nat') ?></div></div>
    </div>
  </div>

  <!-- ================= PARENTS ================= -->
  <div class="record-section">
    <h3>Parents</h3>

    <div class="record-row">
      <span class="record-label">Father</span>
      <div class="record-value">
        <?= v($baptism,'father_firstname') ?>
        <?= v($baptism,'father_middlename') ?>
        <?= v($baptism,'father_lastname') ?>
        <?= v($baptism,'father_suffix') ?>
      </div>
    </div>

    <div class="record-row" style="margin-top:10px;">
      <span class="record-label">Mother</span>
      <div class="record-value">
        <?= v($baptism,'mother_firstname') ?>
        <?= v($baptism,'mother_middlename') ?>
        <?= v($baptism,'mother_lastname') ?>
      </div>
    </div>

    <div class="record-row">
      <span class="record-label">Address</span>
      <div class="record-value">
        <?= v($baptism,'province') ?>
        <?= v($baptism,'city') ?>
        <?= v($baptism,'street_no') ?>
        <?= v($baptism,'barangay') ?>
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
        <?php if (!empty($baptism['birth_certificate'])): ?>
          <a href="../view_file.php?file=<?= urlencode($baptism['birth_certificate']) ?>" target="_blank" class="glyphicon glyphicon-file">View File</a>
        <?php else: ?> <em>Not provided</em> <?php endif; ?>
      </div>
    </div>

    <div class="record-row">
      <span class="record-label">Marriage Certificate</span>
      <div class="record-value">
        <?php if (!empty($baptism['marriage_certificate'])): ?>
          <a href="../view_file.php?file=<?= urlencode($baptism['marriage_certificate']) ?>" target="_blank" class="glyphicon glyphicon-file">View File</a>
        <?php else: ?> <em>Not provided</em> <?php endif; ?>
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
