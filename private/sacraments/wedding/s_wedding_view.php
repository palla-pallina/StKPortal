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
    header("Location: s_wedding.php");
    exit();
}

$id = intval($_GET['id']);

try {
    // Wedding
    $stmt = $dbh->prepare("SELECT * FROM weddings WHERE id=:id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $wedding = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$wedding) {
        header("Location: s_wedding.php?error=notfound");
        exit();
    }

    // MAIN NINONG
    $stmt = $dbh->prepare("SELECT * FROM ninong_tbl WHERE wedding_id=:id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $ninong = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    // MAIN NINANG
    $stmt = $dbh->prepare("SELECT * FROM ninang_tbl WHERE wedding_id=:id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $ninang = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    // ADDITIONAL NINONGS
    $stmt = $dbh->prepare("SELECT * FROM as_ninong_tbl WHERE wedding_id=:id");
    $stmt->execute([':id' => $id]);
    $asNinongs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ADDITIONAL NINANGS
    $stmt = $dbh->prepare("SELECT * FROM as_ninang_tbl WHERE wedding_id=:id");
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
<title>View Wedding Record</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet"/>
<link href='../../../stylesheets/private/sacrament_insert.css' rel='stylesheet'>
</head>
<body>

<div class="record-container">
  <h2>View Wedding Record</h2>

  <a href='../../certificate/wedding_pdf.php?id=<?= $wedding['id'] ?>' class='btn btn-success btn-sm'>Generate PDF</a>

  <!-- ================= RESERVATION ================= -->
  <div class="record-section">
    <h3>Reservation Details</h3>
    <div class="row record-inline">

      <div class="col-md-2 record-row"><span class="record-label">Type</span><div class="record-value"><?= v($wedding,'wedding_type') ?></div></div>
      <div class="col-md-2 record-row"><span class="record-label">Date</span><div class="record-value"><?= v($wedding,'date') ?></div></div>
      <div class="col-md-2 record-row"><span class="record-label">Time</span><div class="record-value"><?= v($wedding,'time') ?></div></div>

    </div>
  </div>

  <!-- ================= BRIDE ================= -->
  <div class="record-section">
    <h3>Bride Details</h3>

    <div class="record-row">
      <span class="record-label">Name</span>
      <div class="record-value">
        <?= v($wedding,'bride_firstname') ?>
        <?= v($wedding,'bride_middlename') ?>
        <?= v($wedding,'bride_lastname') ?>
        <?= v($wedding,'bride_suffix') ?>
      </div>
    </div>

    <div class="row record-inline">
      <div class="col-md-2 record-row"><span class="record-label">Birthdate</span><div class="record-value"><?= v($wedding,'bride_birthdate') ?></div></div>
      <div class="col-md-2 record-row"><span class="record-label">Nationality</span><div class="record-value"><?= v($wedding,'bride_nationality') ?></div></div>
      <div class="col-md-2 record-row"><span class="record-label">Contact Number</span><div class="record-value"><?= v($wedding,'bride_contact') ?></div></div>
      <div class="col-md-3 record-row"><span class="record-label">Email Address</span><div class="record-value"><?= v($wedding,'bride_email') ?></div></div>
    </div>

  </div>


  <!-- ================= BRIDE DOCUMENTS ================= -->
  <div class="record-section">
    <h3>Bride Documents</h3>

    <div class="record-row">
      <span class="record-label">Birth Certificate</span>
      <div class="record-value">
        <?php if (!empty($wedding['b_birth_certificate'])): ?>
          <a href="../view_file.php?file=<?= urlencode($wedding['b_birth_certificate']) ?>" target="_blank" class="glyphicon glyphicon-file">View File</a>
        <?php else: ?> <em>Not provided</em> <?php endif; ?>
      </div>
    </div>

    <div class="record-row">
      <span class="record-label">Baptismal Certificate</span>
      <div class="record-value">
        <?php if (!empty($wedding['b_baptismal_certificate'])): ?>
          <a href="../view_file.php?file=<?= urlencode($wedding['b_baptismal_certificate']) ?>" target="_blank" class="glyphicon glyphicon-file">View File</a>
        <?php else: ?> <em>Not provided</em> <?php endif; ?>
      </div>
    </div>

    <div class="record-row">
      <span class="record-label">Marriage Certificate</span>
      <div class="record-value">
        <?php if (!empty($wedding['b_marriage_license'])): ?>
          <a href="../view_file.php?file=<?= urlencode($wedding['b_marriage_license']) ?>" target="_blank" class="glyphicon glyphicon-file">View File</a>
        <?php else: ?> <em>Not provided</em> <?php endif; ?>
      </div>
    </div>

    <div class="record-row">
      <span class="record-label">Confirmation Certificate</span>
      <div class="record-value">
        <?php if (!empty($wedding['b_confirmation_certificate'])): ?>
          <a href="../view_file.php?file=<?= urlencode($wedding['b_confirmation_certificate']) ?>" target="_blank" class="glyphicon glyphicon-file">View File</a>
        <?php else: ?> <em>Not provided</em> <?php endif; ?>
      </div>
    </div>

    <div class="record-row">
      <span class="record-label">Certificate of No Marriage</span>
      <div class="record-value">
        <?php if (!empty($wedding['b_cenomar'])): ?>
          <a href="../view_file.php?file=<?= urlencode($wedding['b_cenomar']) ?>" target="_blank" class="glyphicon glyphicon-file">View File</a>
        <?php else: ?> <em>Not provided</em> <?php endif; ?>
      </div>
    </div>
  </div>


  <!-- ================= GROOM ================= -->
  <div class="record-section">
    <h3>Groom Details</h3>

    <div class="record-row">
      <span class="record-label">Name</span>
      <div class="record-value">
        <?= v($wedding,'groom_firstname') ?>
        <?= v($wedding,'groom_middlename') ?>
        <?= v($wedding,'groom_lastname') ?>
        <?= v($wedding,'groom_suffix') ?>
      </div>
    </div>

    <div class="row record-inline">
      <div class="col-md-2 record-row"><span class="record-label">Birthdate</span><div class="record-value"><?= v($wedding,'groom_birthdate') ?></div></div>
      <div class="col-md-2 record-row"><span class="record-label">Nationality</span><div class="record-value"><?= v($wedding,'groom_nationality') ?></div></div>
      <div class="col-md-2 record-row"><span class="record-label">Contact Number</span><div class="record-value"><?= v($wedding,'groom_contact') ?></div></div>
      <div class="col-md-3 record-row"><span class="record-label">Email Address</span><div class="record-value"><?= v($wedding,'groom_email') ?></div></div>

    </div>

    <div class="row record-inline">

    </div>
  </div>

  <!-- ================= GROOM DOCUMENTS ================= -->
  <div class="record-section">
    <h3>Groom Documents</h3>

    <div class="record-row">
      <span class="record-label">Birth Certificate</span>
      <div class="record-value">
        <?php if (!empty($wedding['g_birth_certificate'])): ?>
          <a href="../view_file.php?file=<?= urlencode($wedding['g_birth_certificate']) ?>" target="_blank" class="glyphicon glyphicon-file">View File</a>
        <?php else: ?> <em>Not provided</em> <?php endif; ?>
      </div>
    </div>

    <div class="record-row">
      <span class="record-label">Baptismal Certificate</span>
      <div class="record-value">
        <?php if (!empty($wedding['g_baptismal_certificate'])): ?>
          <a href="../view_file.php?file=<?= urlencode($wedding['g_baptismal_certificate']) ?>" target="_blank" class="glyphicon glyphicon-file">View File</a>
        <?php else: ?> <em>Not provided</em> <?php endif; ?>
      </div>
    </div>

    <div class="record-row">
      <span class="record-label">Marriage Certificate</span>
      <div class="record-value">
        <?php if (!empty($wedding['g_marriage_license'])): ?>
          <a href="../view_file.php?file=<?= urlencode($wedding['g_marriage_license']) ?>" target="_blank" class="glyphicon glyphicon-file">View File</a>
        <?php else: ?> <em>Not provided</em> <?php endif; ?>
      </div>
    </div>

    <div class="record-row">
      <span class="record-label">Confirmation Certificate</span>
      <div class="record-value">
        <?php if (!empty($wedding['g_confirmation_certificate'])): ?>
          <a href="../view_file.php?file=<?= urlencode($wedding['g_confirmation_certificate']) ?>" target="_blank" class="glyphicon glyphicon-file">View File</a>
        <?php else: ?> <em>Not provided</em> <?php endif; ?>
      </div>
    </div>

    <div class="record-row">
      <span class="record-label">Certificate of No Marriage</span>
      <div class="record-value">
        <?php if (!empty($wedding['g_cenomar'])): ?>
          <a href="../view_file.php?file=<?= urlencode($wedding['g_cenomar']) ?>" target="_blank" class="glyphicon glyphicon-file">View File</a>
        <?php else: ?> <em>Not provided</em> <?php endif; ?>
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

  <!-- ================= BUTTON ================= -->
  <div class="record-buttons">
    <a href="s_wedding.php" class="btn btn-primary">Back</a>
  </div>

</div>
</body>
</html>
