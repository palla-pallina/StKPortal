<?php
require_once '../connection.php';
session_start();

$message = "";

/* ================= ADMIN PROTECTION ================= */
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}

$admin_name = $_SESSION['username'] ?? 'Admin';

/* ================= POST: handle create ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $g = fn($k) => trim($_POST[$k] ?? '');

    // Funeral Details
    $funeral_date    = $g('funeral_date');
    $funeral_time    = $g('funeral_time');
    $province        = $g('province');
    $city            = $g('city');
    $street_no       = $g('street_no');
    $barangay        = $g('barangay');


    // Deceased Details
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
        $message = "Please fill in all required fields.";
    } else {
        try {
            $dbh->beginTransaction();

            $sql = "INSERT INTO funerals (
                        funeral_date, funeral_time, 
                        province, city, street_no, barangay,
                        deceased_firstname, deceased_middlename, deceased_lastname, deceased_suffix, cause_of_death,
                        contact_person_firstname, contact_person_middlename, contact_person_lastname, contact_person_suffix,
                        contact_person_relation, contact_person_contact_no, contact_person_email
                    ) VALUES (
                        :funeral_date, :funeral_time,
                        :province, :city, :street_no, :barangay,
                        :df, :dm, :dl, :ds, :cod,
                        :cpf, :cpm, :cpl, :cps,
                        :cpr, :cpc, :cpe
                    )";

            $stmt = $dbh->prepare($sql);
            $stmt->execute([
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
                ':cod' => $cod ?: null,

                ':cpf' => $cpf,
                ':cpm' => $cpm ?: null,
                ':cpl' => $cpl,
                ':cps' => $cps ?: null,
                ':cpr' => $cpr ?: null,
                ':cpc' => $cpc,
                ':cpe' => $cpe ?: null,
            ]);

            $dbh->commit();
            $message = "✅ Funeral record added successfully!";

        } catch (PDOException $e) {
            $dbh->rollBack();
            $message = "❌ DB Error: " . $e->getMessage();
        }
    }
}

/* ================= SAFE VALUE HELPER ================= */
function v($key) {
    return htmlspecialchars($_POST[$key] ?? '');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet"/>
<link href='../../../stylesheets/private/sacrament_insert.css' rel='stylesheet'>
<title>Add Funeral Record</title>
</head>
<body>

<div class="main-content">
  <div class="record-container">
    <h2>Add Funeral Record</h2>

    <?php if ($message): ?>
      <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">

      <!-- ================= FUNERAL DETAILS ================= -->
      <div class="record-section">
        <h3>Funeral Details</h3>
        <div class="row record-inline">
          <div class="col-md-3 record-row">
            <span class="record-label">Date</span>
            <input type="date" name="funeral_date" class="form-control" value="<?= v('funeral_date') ?>" required>
          </div>

          <div class="col-md-3 record-row">
            <span class="record-label">Time</span>
            <input type="time" name="funeral_time" class="form-control" value="<?= v('funeral_time') ?>" required>
          </div>
        </div>
                <!-- Address -->
        <div class="record-section">
            <h3>Address</h3>
            <div class="row record-inline">
                <div class="col-md-3 record-row">
                    <span class="record-label">Province</span>
                    <input type="text" name="province" class="form-control" required>
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">City</span>
                    <input type="text" name="city" class="form-control">
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Street Number</span>
                    <input type="text" name="street_no" class="form-control" required>
                </div>
                <div class="col-md-2 record-row">
                    <span class="record-label">Barangay</span>
                    <input type="text" name="barangay" class="form-control">
                </div>
            </div>
        </div>
      </div>

      <!-- ================= DECEASED DETAILS ================= -->
      <div class="record-section">
        <h3>Deceased Details</h3>
        <div class="row record-inline">
          <div class="col-md-3 record-row">
            <span class="record-label">First Name</span>
            <input type="text" name="deceased_firstname" class="form-control" value="<?= v('deceased_firstname') ?>" required>
          </div>

          <div class="col-md-3 record-row">
            <span class="record-label">Middle Name</span>
            <input type="text" name="deceased_middlename" class="form-control" value="<?= v('deceased_middlename') ?>">
          </div>

          <div class="col-md-3 record-row">
            <span class="record-label">Last Name</span>
            <input type="text" name="deceased_lastname" class="form-control" value="<?= v('deceased_lastname') ?>" required>
          </div>

          <div class="col-md-2 record-row">
            <span class="record-label">Suffix</span>
            <input type="text" name="deceased_suffix" class="form-control" value="<?= v('deceased_suffix') ?>">
          </div>
        </div>

        <div class="row record-inline"">
          <div class="col-md-3 record-row">
            <span class="record-label">Cause of Death</span>
            <input type="text" name="cause_of_death" class="form-control" value="<?= v('cause_of_death') ?>">
          </div>
        </div>
      </div>

      <!-- ================= CONTACT PERSON ================= -->
      <div class="record-section">
        <h3>Contact Person</h3>
        <div class="row record-inline">
          <div class="col-md-3 record-row">
            <span class="record-label">First Name</span>
            <input type="text" name="contact_person_firstname" class="form-control" value="<?= v('contact_person_firstname') ?>" required>
          </div>

          <div class="col-md-3 record-row">
            <span class="record-label">Middle Name</span>
            <input type="text" name="contact_person_middlename" class="form-control" value="<?= v('contact_person_middlename') ?>">
          </div>

          <div class="col-md-3 record-row">
            <span class="record-label">Last Name</span>
            <input type="text" name="contact_person_lastname" class="form-control" value="<?= v('contact_person_lastname') ?>" required>
          </div>

          <div class="col-md-2 record-row">
            <span class="record-label">Suffix</span>
            <input type="text" name="contact_person_suffix" class="form-control" value="<?= v('contact_person_suffix') ?>">
          </div>
        </div>

        <div class="row record-inline" style="margin-top:10px;">
          <div class="col-md-3 record-row">
            <span class="record-label">Relation to Deceased</span>

            <select name="contact_person_relation" class="form-control" required>
                <option value="" disabled selected></option>
                <option value="Spouse">Spouse</option>
                <option value="Parent">Parent</option>
                <option value="Sibling">Sibling</option>
                <option value="Relative">Relative</option>
            </select>
          </div>

          <div class="col-md-3 record-row">
            <span class="record-label">Contact Number</span>
            <input type="text" name="contact_person_contact_no" class="form-control" value="<?= v('contact_person_contact_no') ?>" required>
          </div>

          <div class="col-md-3 record-row">
            <span class="record-label">Email</span>
            <input type="email" name="contact_person_email" class="form-control" value="<?= v('contact_person_email') ?>">
          </div>
        </div>
      </div>

      <!-- ================= BUTTONS ================= -->
      <div class="record-buttons" style="margin-top:20px;">
        <button type="submit" class="btn btn-primary">Add Funeral Record</button>
        <a href="s_funeral.php" class="btn btn-danger">Back</a>
      </div>

    </form>
  </div>
</div>

</body>
</html>
