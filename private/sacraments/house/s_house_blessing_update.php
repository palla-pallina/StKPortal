<?php
require_once '../connection.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: s_house_blessing.php"); // change if your listing page name differs
    exit();
}

$id  = intval($_GET['id']);
$msg = "";

/* ================= FETCH RECORD ================= */
try {
    $stmt = $dbh->prepare("SELECT * FROM house_blessings WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $house = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$house) {
        header("Location: s_house_blessing.php?error=notfound");
        exit();
    }
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

/* ================= UPDATE ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {

    $g = fn($k) => trim($_POST[$k] ?? '');

    // Required fields (based on schema)
    $date  = $g('date');
    $time  = $g('time');

    $province  = $g('province');
    $city      = $g('city');
    $street_no = $g('street_no');
    $barangay  = $g('barangay');

    $notes = $g('notes');

    $requesting_contact = $g('requesting_contact');
    $requesting_email   = $g('requesting_email');

    if (
        $date === '' || $time === '' ||
        $province === '' || $city === '' || $street_no === '' || $barangay === '' ||
        $notes === '' ||
        $requesting_contact === '' || $requesting_email === ''
    ) {
        $msg = "Please fill in all required fields (date/time, address, notes, contact number, email).";
    } else {
        try {
            $dbh->beginTransaction();

            $sql = "
                UPDATE house_blessings SET
                    date = :date,
                    time = :time,

                    province = :province,
                    city = :city,
                    street_no = :street_no,
                    barangay = :barangay,

                    notes = :notes,
                    special_intention = :special_intention,

                    requesting_firstname = :requesting_firstname,
                    requesting_middlename = :requesting_middlename,
                    requesting_lastname = :requesting_lastname,
                    requesting_suffix = :requesting_suffix,

                    requesting_contact = :requesting_contact,
                    requesting_email = :requesting_email

                WHERE id = :id
            ";

            $upd = $dbh->prepare($sql);
            $upd->execute([
                ':date' => $date,
                ':time' => $time,

                ':province' => $province,
                ':city' => $city,
                ':street_no' => $street_no,
                ':barangay' => $barangay,

                ':notes' => $notes,
                ':special_intention' => $g('special_intention') ?: null,

                ':requesting_firstname' => $g('requesting_firstname') ?: null,
                ':requesting_middlename' => $g('requesting_middlename') ?: null,
                ':requesting_lastname' => $g('requesting_lastname') ?: null,
                ':requesting_suffix' => $g('requesting_suffix') ?: null,

                ':requesting_contact' => $requesting_contact,
                ':requesting_email' => $requesting_email,

                ':id' => $id
            ]);

            $dbh->commit();
            header("Location: s_house_blessing.php?msg=updated");
            exit();

        } catch (PDOException $e) {
            if ($dbh->inTransaction()) $dbh->rollBack();
            $msg = "Update failed: " . $e->getMessage();
        }
    }
}

/* ================= SAFE VALUE HELPER ================= */
function v($src, $k) {
    return htmlspecialchars($_POST[$k] ?? $src[$k] ?? '');
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Update House Blessing Record</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet"/>
<link href='../../../stylesheets/private/sacrament_insert.css' rel='stylesheet'>
</head>
<body>

<div class="record-container">
  <h2>Update House Blessing Record</h2>

  <?php if ($msg): ?>
    <div class="alert alert-warning"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <?php if (!empty($house['decline_reason'])): ?>
    <div class="alert alert-danger">
      <strong>Decline Reason:</strong> <?= htmlspecialchars($house['decline_reason']) ?>
    </div>
  <?php endif; ?>

  <form method="post" action="s_house_blessing_update.php?id=<?= $id ?>">
    <input type="hidden" name="id" value="<?= $id ?>">

    <!-- ================= RESERVATION ================= -->
    <div class="record-section">
      <h3>Reservation Details</h3>

      <div class="row record-inline">
        <div class="col-md-3 record-row">
          <span class="record-label">Date</span>
          <input type="date" name="date" class="form-control" value="<?= v($house,'date') ?>" required>
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Time</span>
          <input type="time" name="time" class="form-control" value="<?= v($house,'time') ?>" required>
        </div>
      </div>
    </div>

    <!-- ================= ADDRESS ================= -->
    <div class="record-section">
      <h3>Address</h3>

      <div class="row record-inline">
        <div class="col-md-3 record-row"><span class="record-label">Province</span><input name="province" class="form-control" value="<?= v($house,'province') ?>" required></div>
        <div class="col-md-3 record-row"><span class="record-label">City</span><input name="city" class="form-control" value="<?= v($house,'city') ?>" required></div>
        <div class="col-md-3 record-row"><span class="record-label">Street No.</span><input name="street_no" class="form-control" value="<?= v($house,'street_no') ?>" required></div>
        <div class="col-md-3 record-row"><span class="record-label">Barangay</span><input name="barangay" class="form-control" value="<?= v($house,'barangay') ?>" required></div>
      </div>
    </div>

    <!-- ================= REQUESTING PERSON ================= -->
    <div class="record-section">
      <h3>Requesting Person</h3>

      <div class="row record-inline">
        <div class="col-md-3 record-row"><span class="record-label">First Name</span><input name="requesting_firstname" class="form-control" value="<?= v($house,'requesting_firstname') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Middle Name</span><input name="requesting_middlename" class="form-control" value="<?= v($house,'requesting_middlename') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Last Name</span><input name="requesting_lastname" class="form-control" value="<?= v($house,'requesting_lastname') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Suffix</span><input name="requesting_suffix" class="form-control" value="<?= v($house,'requesting_suffix') ?>"></div>
      </div>

      <div class="row record-inline" style="margin-top:10px;">
        <div class="col-md-6 record-row"><span class="record-label">Contact Number</span><input name="requesting_contact" class="form-control" value="<?= v($house,'requesting_contact') ?>" required></div>
        <div class="col-md-6 record-row"><span class="record-label">Email</span><input type="email" name="requesting_email" class="form-control" value="<?= v($house,'requesting_email') ?>" required></div>
      </div>
    </div>

    <!-- ================= NOTES / INTENTION ================= -->
    <div class="record-section">
      <h3>Notes / Special Intention</h3>

      <div class="record-row">
        <span class="record-label">Notes</span>
        <input type="text" name="notes" class="form-control" value="<?= v($house,'notes') ?>" required>
      </div>

      <div class="record-row" style="margin-top:10px;">
        <span class="record-label">Special Intention</span>
        <textarea name="special_intention" class="form-control" rows="4"><?= v($house,'special_intention') ?></textarea>
      </div>
    </div>

    <!-- ================= BUTTONS ================= -->
    <div class="record-buttons">
      <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
      <a href="s_house_blessing.php" class="btn btn-default">Cancel</a>
    </div>

  </form>
</div>

</body>
</html>
