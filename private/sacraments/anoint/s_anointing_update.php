<?php
require_once '../connection.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: s_anointing.php"); // change if listing page differs
    exit();
}

$id  = intval($_GET['id']);
$msg = "";

/* ================= FETCH RECORD ================= */
try {
    $stmt = $dbh->prepare("SELECT * FROM anointing_of_sick_requests WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $req = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$req) {
        header("Location: s_anointing.php?error=notfound");
        exit();
    }
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

/* ================= UPDATE ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {

    $g = fn($k) => trim($_POST[$k] ?? '');

    // Required fields (based on schema)
    $date            = $g('date');
    $time            = $g('time');
    $sick_firstname  = $g('sick_firstname');
    $sick_lastname   = $g('sick_lastname');
    $age             = $g('age');
    $sick_condition  = $g('sick_condition');

    $contact_fn      = $g('contact_person_firstname');
    $contact_ln      = $g('contact_person_lastname');
    $contact_relation= $g('contact_relation');
    $contact_number  = $g('contact_number');
    $contact_email   = $g('contact_email');

    if (
        $date === '' || $time === '' ||
        $sick_firstname === '' || $sick_lastname === '' ||
        $age === '' || !ctype_digit($age) ||
        $sick_condition === '' ||
        $contact_fn === '' || $contact_ln === '' ||
        $contact_relation === '' ||
        $contact_number === '' ||
        $contact_email === ''
    ) {
        $msg = "Please fill in all required fields (date/time, sick person info, condition, and contact info). Age must be a number.";
    } else {
        try {
            $dbh->beginTransaction();

            $sql = "
                UPDATE anointing_of_sick_requests SET
                    date = :date,
                    time = :time,
                    venue = :venue,
                    province = :province,
                    city = :city,
                    street_no = :street_no,
                    barangay = :barangay,
                    hospital_name = :hospital_name,

                    sick_firstname = :sick_firstname,
                    sick_middlename = :sick_middlename,
                    sick_lastname = :sick_lastname,
                    sick_suffix = :sick_suffix,

                    contact_person_firstname = :contact_person_firstname,
                    contact_person_middlename = :contact_person_middlename,
                    contact_person_lastname = :contact_person_lastname,
                    contact_person_suffix = :contact_person_suffix,

                    age = :age,
                    sick_condition = :sick_condition,
                    contact_relation = :contact_relation,
                    contact_number = :contact_number,
                    contact_email = :contact_email

                WHERE id = :id
            ";

            $upd = $dbh->prepare($sql);
            $upd->execute([
                ':date' => $date,
                ':time' => $time,

                ':venue' => $g('venue') ?: null,
                ':province' => $g('province') ?: null,
                ':city' => $g('city') ?: null,
                ':street_no' => $g('street_no') ?: null,
                ':barangay' => $g('barangay') ?: null,
                ':hospital_name' => $g('hospital_name') ?: null,

                ':sick_firstname' => $sick_firstname,
                ':sick_middlename' => $g('sick_middlename') ?: null,
                ':sick_lastname' => $sick_lastname,
                ':sick_suffix' => $g('sick_suffix') ?: null,

                ':contact_person_firstname' => $contact_fn,
                ':contact_person_middlename' => $g('contact_person_middlename') ?: null,
                ':contact_person_lastname' => $contact_ln,
                ':contact_person_suffix' => $g('contact_person_suffix') ?: null,

                ':age' => (int)$age,
                ':sick_condition' => $sick_condition,
                ':contact_relation' => $contact_relation,
                ':contact_number' => $contact_number,
                ':contact_email' => $contact_email,

                ':id' => $id
            ]);

            $dbh->commit();
            header("Location: s_anointing.php?msg=updated");
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
<title>Update Anointing of the Sick Request</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet"/>
<link href='../../../stylesheets/private/sacrament_insert.css' rel='stylesheet'>
</head>
<body>

<div class="record-container">
  <h2>Update Anointing of the Sick Request</h2>

  <?php if ($msg): ?>
    <div class="alert alert-warning"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <form method="post" action="s_anointing_update.php?id=<?= $id ?>">
    <input type="hidden" name="id" value="<?= $id ?>">

    <!-- ================= RESERVATION ================= -->
    <div class="record-section">
      <h3>Reservation Details</h3>

      <div class="row record-inline">
        <div class="col-md-3 record-row">
          <span class="record-label">Date</span>
          <input type="date" name="date" class="form-control" value="<?= v($req,'date') ?>" required>
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Time</span>
          <input type="time" name="time" class="form-control" value="<?= v($req,'time') ?>" required>
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Venue</span>
          <input type="text" name="venue" class="form-control" value="<?= v($req,'venue') ?>" placeholder="Home / Hospital / Church...">
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Hospital Name (if applicable)</span>
          <input type="text" name="hospital_name" class="form-control" value="<?= v($req,'hospital_name') ?>">
        </div>
      </div>

      <div class="row record-inline" style="margin-top:10px;">
        <div class="col-md-3 record-row"><span class="record-label">Province</span><input name="province" class="form-control" value="<?= v($req,'province') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">City</span><input name="city" class="form-control" value="<?= v($req,'city') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Street No.</span><input name="street_no" class="form-control" value="<?= v($req,'street_no') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Barangay</span><input name="barangay" class="form-control" value="<?= v($req,'barangay') ?>"></div>
      </div>
    </div>

    <!-- ================= SICK PERSON ================= -->
    <div class="record-section">
      <h3>Sick Person Details</h3>

      <div class="row record-inline">
        <div class="col-md-3 record-row"><span class="record-label">First Name</span><input name="sick_firstname" class="form-control" value="<?= v($req,'sick_firstname') ?>" required></div>
        <div class="col-md-3 record-row"><span class="record-label">Middle Name</span><input name="sick_middlename" class="form-control" value="<?= v($req,'sick_middlename') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Last Name</span><input name="sick_lastname" class="form-control" value="<?= v($req,'sick_lastname') ?>" required></div>
        <div class="col-md-3 record-row"><span class="record-label">Suffix</span><input name="sick_suffix" class="form-control" value="<?= v($req,'sick_suffix') ?>"></div>
      </div>

      <div class="row record-inline" style="margin-top:10px;">
        <div class="col-md-2 record-row"><span class="record-label">Age</span><input name="age" class="form-control" value="<?= v($req,'age') ?>" required></div>
        <div class="col-md-10 record-row"><span class="record-label">Condition</span><input name="sick_condition" class="form-control" value="<?= v($req,'sick_condition') ?>" required></div>
      </div>
    </div>

    <!-- ================= CONTACT PERSON ================= -->
    <div class="record-section">
      <h3>Contact Person</h3>

      <div class="row record-inline">
        <div class="col-md-3 record-row"><span class="record-label">First Name</span><input name="contact_person_firstname" class="form-control" value="<?= v($req,'contact_person_firstname') ?>" required></div>
        <div class="col-md-3 record-row"><span class="record-label">Middle Name</span><input name="contact_person_middlename" class="form-control" value="<?= v($req,'contact_person_middlename') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Last Name</span><input name="contact_person_lastname" class="form-control" value="<?= v($req,'contact_person_lastname') ?>" required></div>
        <div class="col-md-3 record-row"><span class="record-label">Suffix</span><input name="contact_person_suffix" class="form-control" value="<?= v($req,'contact_person_suffix') ?>"></div>
      </div>

      <div class="row record-inline" style="margin-top:10px;">
        <div class="col-md-4 record-row"><span class="record-label">Relation</span><input name="contact_relation" class="form-control" value="<?= v($req,'contact_relation') ?>" required></div>
        <div class="col-md-4 record-row"><span class="record-label">Phone</span><input name="contact_number" class="form-control" value="<?= v($req,'contact_number') ?>" required></div>
        <div class="col-md-4 record-row"><span class="record-label">Email</span><input type="email" name="contact_email" class="form-control" value="<?= v($req,'contact_email') ?>" required></div>
      </div>
    </div>

    <!-- ================= BUTTONS ================= -->
    <div class="record-buttons">
      <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
      <a href="s_anointing.php" class="btn btn-default">Cancel</a>
    </div>

  </form>
</div>

</body>
</html>
