<?php
require_once '../connection.php';
session_start();

/* ===============================
   RESTRICT ACCESS (ADMIN ONLY)
================================ */
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}

/* ===============================
   VALIDATE REQUEST ID
================================ */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: certificate_request.php");
    exit();
}

$id = (int) $_GET['id'];

/* ===============================
   FETCH CERTIFICATE REQUEST
================================ */
$stmt = $dbh->prepare("
    SELECT *
    FROM certificate_request
    WHERE cert_req_id = :id
    LIMIT 1
");
$stmt->execute([':id' => $id]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    header("Location: certificate_request.php?error=notfound");
    exit();
}

/* ===============================
   SAFE OUTPUT HELPER
================================ */
function v($arr, $key) {
    return htmlspecialchars($arr[$key] ?? '');
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>View Certificate Request</title>

<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet"/>
<link href='../../../stylesheets/private/sacrament_insert.css' rel='stylesheet'>

</head>
<body>

<div class="record-container">
<h2>Certificate Request Details</h2>

<!-- ================= STATUS ================= -->
<div class="record-section">
  <h3>Request Status</h3>

  <div class="record-row">
    <span class="record-label">Status</span>
    <div class="record-value"><strong><?= v($request,'certificate_status') ?></strong></div>
  </div>

  <?php if (!empty($request['pickup_date'])): ?>
  <div class="record-row">
    <span class="record-label">Pickup Date</span>
    <div class="record-value"><?= v($request,'pickup_date') ?></div>
  </div>
  <?php endif; ?>

  <?php if (!empty($request['decline_reason'])): ?>
  <div class="record-row">
    <span class="record-label">Decline Reason</span>
    <div class="record-value text-danger"><?= v($request,'decline_reason') ?></div>
  </div>
  <?php endif; ?>
</div>

<!-- ================= REQUEST DETAILS ================= -->
<div class="record-section">
<h3>Request Information</h3>

<div class="row record-inline">
  <div class="col-md-3 record-row">
    <span class="record-label">Certificate Type</span>
    <div class="record-value"><?= v($request,'certificate_type') ?></div>
  </div>

  <div class="col-md-3 record-row">
    <span class="record-label">Claiming For</span>
    <div class="record-value"><?= v($request,'claiming_for') ?></div>
  </div>

  <div class="col-md-3 record-row">
    <span class="record-label">Purpose</span>
    <div class="record-value"><?= v($request,'request_reason') ?></div>
  </div>

  <div class="col-md-3 record-row">
    <span class="record-label">Submitted</span>
    <div class="record-value"><?= v($request,'created_at') ?></div>
  </div>
</div>
</div>

<!-- ================= REQUESTER ================= -->
<div class="record-section">
<h3>Requester Details</h3>

<div class="record-row">
  <span class="record-label">Name</span>
  <div class="record-value">
    <?= v($request,'request_firstname') ?>
    <?= v($request,'request_middlename') ?>
    <?= v($request,'request_lastname') ?>
    <?= v($request,'request_suffix') ?>
  </div>
</div>

<div class="row record-inline">
  <div class="col-md-6 record-row">
    <span class="record-label">Contact</span>
    <div class="record-value"><?= v($request,'contact') ?></div>
  </div>

  <div class="col-md-6 record-row">
    <span class="record-label">Email</span>
    <div class="record-value"><?= v($request,'email') ?></div>
  </div>
</div>
</div>

<!-- ================= CERTIFICATE OWNER ================= -->
<div class="record-section">
<h3>Certificate Owner</h3>

<div class="record-row">
  <span class="record-label">Owner</span>
  <div class="record-value">
    <?= v($request,'owner_firstname') ?>
    <?= v($request,'owner_middlename') ?>
    <?= v($request,'owner_lastname') ?>
    <?= v($request,'owner_suffix') ?>
  </div>
</div>
</div>

<!-- ================= DOCUMENTS ================= -->
<div class="record-section">
<h3>Submitted Documents</h3>

<div class="record-row">
  <span class="record-label">Requester ID</span>
  <div class="record-value">
    <?php if (!empty($request['id_picture'])): ?>
      <a href="../view_file.php?file=<?= urlencode($request['id_picture']) ?>" target="_blank">
        <span class="glyphicon glyphicon-file"></span> View File
      </a>
    <?php else: ?>
      <em>Not provided</em>
    <?php endif; ?>
  </div>
</div>

<div class="record-row">
  <span class="record-label">Owner ID</span>
  <div class="record-value">
    <?php if (!empty($request['owner_id_picture'])): ?>
      <a href="../view_file.php?file=<?= urlencode($request['owner_id_picture']) ?>" target="_blank">
        <span class="glyphicon glyphicon-file"></span> View File
      </a>
    <?php else: ?>
      <em>Not provided</em>
    <?php endif; ?>
  </div>
</div>

<div class="record-row">
  <span class="record-label">Authorization Letter / ID</span>
  <div class="record-value">
    <?php if (!empty($request['authorized_id_picture'])): ?>
      <a href="../view_file.php?file=<?= urlencode($request['authorized_id_picture']) ?>" target="_blank">
        <span class="glyphicon glyphicon-file"></span> View File
      </a>
    <?php else: ?>
      <em>Not provided</em>
    <?php endif; ?>
  </div>
</div>
</div>

<!-- ================= ACTION ================= -->
<div class="record-buttons">
  <a href="#" class="btn btn-primary" onclick="window.history.back(); return false;">Back</a>
</div>

</div>
</body>
</html>
