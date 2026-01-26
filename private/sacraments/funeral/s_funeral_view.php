<?php
require_once '../connection.php';
session_start();

/* ================= ADMIN PROTECTION ================= */
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}

$admin_name = $_SESSION['username'] ?? 'Admin';

/* ================= VALIDATE ID ================= */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: s_funeral.php');
    exit();
}

$id  = intval($_GET['id']);
$msg = "";

/* ================= FETCH FUNERAL ================= */
try {
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

/* ================= UPDATE FUNERAL ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {

    $g = fn($k) => trim($_POST[$k] ?? '');

    // Funeral Schedule
    $funeral_date    = $g('funeral_date');
    $funeral_time    = $g('funeral_time');
    $province        = $g('province');
    $city            = $g('city');
    $street_no       = $g('street_no');
    $barangay        = $g('barangay');
    $presider        = $g('presider');

    // Deceased
    $df  = $g('deceased_firstname');
    $dm  = $g('deceased_middlename');
    $dl  = $g('deceased_lastname');
    $ds  = $g('deceased_suffix');
    $cod = $g('cause_of_death');

    // Contact Person
    $cpf = $g('contact_person_firstname');
    $cpm = $g('contact_person_middlename');
    $cpl = $g('contact_person_lastname');
    $cps = $g('contact_person_suffix');
    $cpr = $g('contact_person_relation');
    $cpc = $g('contact_person_contact_no');
    $cpe = $g('contact_person_email');

    /* ===== BASIC VALIDATION ===== */
    if ($df === '' || $dl === '' || $cpf === '' || $cpl === '' || $cpc === '' || $funeral_date === '' || $funeral_time === '') {
        $msg = "Please fill in all required fields.";
    } else {
        try {
            $dbh->beginTransaction();

            $sql = "UPDATE funerals SET
                        funeral_date = :funeral_date,
                        funeral_time = :funeral_time,
                        province = :province,
                        city = :city,
                        street_no = :street_no,
                        barangay = :barangay,
                        presider = :presider,

                        deceased_firstname = :df,
                        deceased_middlename = :dm,
                        deceased_lastname = :dl,
                        deceased_suffix = :ds,

                        contact_person_firstname = :cpf,
                        contact_person_middlename = :cpm,
                        contact_person_lastname = :cpl,
                        contact_person_suffix = :cps,
                        contact_person_relation = :cpr,
                        contact_person_contact_no = :cpc,
                        contact_person_email = :cpe,

                        cause_of_death = :cod
                    WHERE id = :id
                ";

            $upd = $dbh->prepare($sql);
            $upd->execute([
                ':funeral_date' => $funeral_date,
                ':funeral_time' => $funeral_time,
                ':province' => $province ?: null,
                ':city' => $city ?: null,
                ':street_no' => $street_no ?: null,
                ':barangay' => $barangay ?: null,
                ':presider' => $presider ?: null,

                ':df' => $df,
                ':dm' => $dm ?: null,
                ':dl' => $dl,
                ':ds' => $ds ?: null,

                ':cpf' => $cpf,
                ':cpm' => $cpm ?: null,
                ':cpl' => $cpl,
                ':cps' => $cps ?: null,
                ':cpr' => $cpr,
                ':cpc' => $cpc,
                ':cpe' => $cpe ?: null,

                ':cod' => $cod ?: null,
                ':id'  => $id
            ]);

            $dbh->commit();

            header("Location: s_funeral.php?msg=updated");
            exit();

        } catch (PDOException $e) {
            $dbh->rollBack();
            $msg = "Update failed: " . $e->getMessage();
        }
    }
}

/* ================= SAFE OUTPUT HELPER ================= */
function v($arr, $key) {
    return htmlspecialchars($arr[$key] ?? '');
}
?>


<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>View First Communion Record</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet"/>
<link href='../../../stylesheets/private/sacrament_insert.css' rel='stylesheet'>
</head>
<body>

<div class="record-container">
  <h2>View Funeral Mass Record</h2>

  <?php if ($msg): ?>
    <div class="alert alert-warning"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <form method="post">
    <input type="hidden" name="id" value="<?= $id ?>">

    <!-- ================= RESERVATION ================= -->
    <div class="record-section">
      <h3>Reservation Details</h3>
      <div class="row record-inline">

        <div class="col-md-2 record-row">
          <span class="record-label">Date</span>
          <div class="record-value"><?= v($funeral,'funeral_date') ?></div>
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Time</span>
          <div class="record-value"><?= v($funeral,'funeral_time') ?></div>
        </div>

        <div class="record-row">
          <span class="record-label">Address</span>
          <div class="record-value">
            <?= v($funeral,'province') ?>
            <?= v($funeral,'city') ?>
            <?= v($funeral,'street_no') ?>
            <?= v($funeral,'barangay') ?>
          </div>
        </div>
        
        <div class="col-md-3 record-row">
          <span class="record-label">Presiding Priest</span>
          <div class="record-value"><?= v($funeral,'presider') ?></div>
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Date Submitted</span>
          <div class="record-value"><?= v($funeral,'created_at') ?></div>
        </div>
      </div>
    </div>

    <!-- ================= DECEASED ================= -->
    <div class="record-section">
      <h3>Deceased Details</h3>

      <div class="record-row">
        <span class="record-label">Name</span>
          <div class="record-value">
            <?= v($funeral,'deceased_firstname') ?>
            <?= v($funeral,'deceased_middlename') ?>
            <?= v($funeral,'deceased_lastname') ?>
            <?= v($funeral,'deceased_suffix') ?>
          </div>
      </div>

      <div class="row record-inline">
        <div class="col-md-3 record-row">
          <span class="record-label">Cause of Death</span>
          <div class="record-value"><?= v($funeral,'cause_of_death') ?></div>
        </div>

      </div>
    </div>

    <!-- ================= CONTACT PERSON ================= -->
    <div class="record-section">
      <h3>Father</h3>
      <div class="record-row">
        <span class="record-label">Name</span>
        <div class="record-value">
          <?= v($funeral,'contact_person_firstname') ?>
          <?= v($funeral,'contact_person_middlename') ?>
          <?= v($funeral,'contact_person_lastname') ?>
          <?= v($funeral,'contact_person_suffix') ?>
        </div>
      </div>

      <div class="row record-inline">
        <div class="col-md-3 record-row">
          <span class="record-label">Relation to Deceased</span>
          <div class="record-value"><?= v($funeral,'contact_person_relation') ?></div>
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Contact Number</span>
          <div class="record-value"><?= v($funeral,'contact_person_contact_no') ?></div>
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Email</span>
          <div class="record-value"><?= v($funeral,'contact_person_email') ?></div>
        </div>

  </div>
</div>



    <!-- ================= BUTTON ================= -->
    <div class="record-buttons">
      <a href="s_funeral.php" class="btn btn-primary">Back</a>
    </div>

  </form>
</div>

  </form>
</div>
</body>
</html>
