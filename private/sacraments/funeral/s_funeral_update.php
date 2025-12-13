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

/* ================= POST: handle update ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {

    $g = fn($k) => trim($_POST[$k] ?? '');

    // Funeral Schedule
    $funeral_date    = $g('funeral_date');
    $funeral_time    = $g('funeral_time');
    $province        = $g('province');
    $city            = $g('city');
    $street_no       = $g('street_no');
    $barangay        = $g('barangay');

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
function v($arr, $key, $default = '') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        return htmlspecialchars($_POST[$key] ?? $default);
    }
    return htmlspecialchars($arr[$key] ?? $default);
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Update Funeral Record</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet"/>
<link href='../../../stylesheets/private/sacrament_insert.css' rel='stylesheet'>
</head>
<body>

<div class="record-container">
  <h2>Update Funeral Record</h2>

  <?php if ($msg): ?>
    <div class="alert alert-warning"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <form method="post">
    <input type="hidden" name="id" value="<?= $id ?>">

    <!-- ================= FUNERAL DETAILS ================= -->
    <div class="record-section">
      <h3>Funeral Details</h3>
      <div class="row record-inline">
        <div class="col-md-3 record-row">
          <span class="record-label">Date</span>
          <input type="date" name="funeral_date" class="form-control" value="<?= v($funeral,'funeral_date') ?>" required>
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Time</span>
          <input type="time" name="funeral_time" class="form-control" value="<?= v($funeral,'funeral_time') ?>" required>
        </div>
      </div>
    </div>

    <div class="record-section">
      <h3>Address</h3>
      <div class="row record-inline">
        <div class="col-md-2 record-row">
          <span class="record-label">Province</span>
          <input type="text" name="province" class="form-control" value="<?= v($funeral,'province') ?>" required>
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">City</span>
          <input type="text" name="city" class="form-control" value="<?= v($funeral,'city') ?>">
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Street Number</span>
          <input type="text" name="street_no" class="form-control" value="<?= v($funeral,'street_no') ?>" required>
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Barangay</span>
          <input type="text" name="barangay" class="form-control" value="<?= v($funeral,'barangay') ?>">
        </div>
      </div>
  </div>

    <!-- ================= DECEASED ================= -->
    <div class="record-section">
      <h3>Deceased Details</h3>
      <div class="row record-inline">
        <div class="col-md-3 record-row">
          <span class="record-label">First Name</span>
          <input type="text" name="deceased_firstname" class="form-control" value="<?= v($funeral,'deceased_firstname') ?>" required>
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Middle Name</span>
          <input type="text" name="deceased_middlename" class="form-control" value="<?= v($funeral,'deceased_middlename') ?>">
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Last Name</span>
          <input type="text" name="deceased_lastname" class="form-control" value="<?= v($funeral,'deceased_lastname') ?>" required>
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Suffix</span>
          <input type="text" name="deceased_suffix" class="form-control" value="<?= v($funeral,'deceased_suffix') ?>">
        </div>
      </div>

      <div class="row record-inline">
        <div class="col-md-3 record-row">
          <span class="record-label">Cause of Death</span>
          <input type="text" name="cause_of_death" class="form-control" value="<?= v($funeral,'cause_of_death') ?>" required>
        </div>
      </div>
    </div>



    <!-- ================= CONTACT PERSON ================= -->
    <div class="record-section">
      <h3>Contact Person</h3>
      <div class="row record-inline">
        <div class="col-md-3 record-row">
          <span class="record-label">First Name</span>
          <input type="text" name="contact_person_firstname" class="form-control" value="<?= v($funeral,'contact_person_firstname') ?>" required>
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Middle Name</span>
          <input type="text" name="contact_person_middlename" class="form-control" value="<?= v($funeral,'contact_person_middlename') ?>">
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Last Name</span>
          <input type="text" name="contact_person_lastname" class="form-control" value="<?= v($funeral,'contact_person_lastname') ?>" required>
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Suffix</span>
          <input type="text" name="contact_person_suffix" class="form-control" value="<?= v($funeral,'contact_person_suffix') ?>">
        </div>
      </div>

      <div class="row record-inline" style="margin-top:10px;">
        <div class="col-md-3 record-row">
          <span class="record-label">Relation to Deceased</span>
          <input type="text" name="contact_person_relation" class="form-control" value="<?= v($funeral,'contact_person_relation') ?>">
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Contact Number</span>
          <input type="text" name="contact_person_contact_no" class="form-control" value="<?= v($funeral,'contact_person_contact_no') ?>" required>
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Email</span>
          <input type="email" name="contact_person_email" class="form-control" value="<?= v($funeral,'contact_person_email') ?>">
        </div>
      </div>
    </div>

    <!-- ================= BUTTONS ================= -->
    <div class="record-buttons" style="margin-top:20px;">
      <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
      <a href="s_funeral.php" class="btn btn-default">Cancel</a>
    </div>

  </form>
</div>
</body>
</html>
