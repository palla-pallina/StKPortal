<?php
require_once '../connection.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: s_celebratory.php");
    exit();
}

$id  = intval($_GET['id']);
$msg = "";

/* ================= FETCH RECORD ================= */
try {
    $stmt = $dbh->prepare("SELECT * FROM celebratory WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $celebratory = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$celebratory) {
        header("Location: s_celebratory.php?error=notfound");
        exit();
    }
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

/* ================= UPDATE ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {

    $g = fn($k) => trim($_POST[$k] ?? '');

    // required-ish fields
    $date             = $g('date');
    $time             = $g('time');
    $celebration_type = $g('celebration_type');

    // basic validation
    if ($date === '' || $time === '' || $celebration_type === '') {
        $msg = "Mass date, time, and celebration type are required.";
    } else {
        try {
            // TYPE-SPECIFIC DATA (clear irrelevant fields)
            $thanksgiving = null;
            $special_intention = null;

            $celebrant_firstname = $celebrant_middlename = $celebrant_lastname = $celebrant_suffix = null;
            $celebrant_birthdate = null;

            $wife_firstname = $wife_middlename = $wife_lastname = null;
            $husband_firstname = $husband_middlename = $husband_lastname = $husband_suffix = null;
            $wedding_date = null;

            if ($celebration_type === 'Thanksgiving') {
                $thanksgiving = $g('thanksgiving') ?: null;

            } elseif ($celebration_type === 'Birthday') {
                $celebrant_firstname = $g('celebrant_firstname') ?: null;
                $celebrant_middlename = $g('celebrant_middlename') ?: null;
                $celebrant_lastname = $g('celebrant_lastname') ?: null;
                $celebrant_suffix = $g('celebrant_suffix') ?: null;
                $celebrant_birthdate = $g('celebrant_birthdate') ?: null;

            } elseif ($celebration_type === 'Wedding Anniversary') {
                $wife_firstname = $g('wife_firstname') ?: null;
                $wife_middlename = $g('wife_middlename') ?: null;
                $wife_lastname = $g('wife_lastname') ?: null;

                $husband_firstname = $g('husband_firstname') ?: null;
                $husband_middlename = $g('husband_middlename') ?: null;
                $husband_lastname = $g('husband_lastname') ?: null;
                $husband_suffix = $g('husband_suffix') ?: null;

                $wedding_date = $g('wedding_date') ?: null;

            } else { // Special Intention
                $special_intention = $g('special_intention') ?: null;
            }

            $dbh->beginTransaction();

            $sql = "
                UPDATE celebratory SET
                    date = :date,
                    time = :time,
                    celebration_type = :celebration_type,
                    mass_venue = :mass_venue,
                    province = :province,
                    city = :city,
                    street_no = :street_no,
                    barangay = :barangay,

                    thanksgiving = :thanksgiving,
                    special_intention = :special_intention,

                    celebrant_firstname = :celebrant_firstname,
                    celebrant_middlename = :celebrant_middlename,
                    celebrant_lastname = :celebrant_lastname,
                    celebrant_suffix = :celebrant_suffix,
                    celebrant_birthdate = :celebrant_birthdate,

                    wife_firstname = :wife_firstname,
                    wife_middlename = :wife_middlename,
                    wife_lastname = :wife_lastname,

                    husband_firstname = :husband_firstname,
                    husband_middlename = :husband_middlename,
                    husband_lastname = :husband_lastname,
                    husband_suffix = :husband_suffix,

                    wedding_date = :wedding_date,

                    contact_firstname = :contact_firstname,
                    contact_middlename = :contact_middlename,
                    contact_lastname = :contact_lastname,
                    contact_suffix = :contact_suffix,
                    contact_relation = :contact_relation,
                    contact_number = :contact_number,
                    contact_email = :contact_email

                WHERE id = :id
            ";

            $upd = $dbh->prepare($sql);
            $upd->execute([
                ':date' => $date,
                ':time' => $time,
                ':celebration_type' => $celebration_type,

                ':mass_venue' => $g('mass_venue') ?: null,
                ':province' => $g('province') ?: null,
                ':city' => $g('city') ?: null,
                ':street_no' => $g('street_no') ?: null,
                ':barangay' => $g('barangay') ?: null,

                ':thanksgiving' => $thanksgiving,
                ':special_intention' => $special_intention,

                ':celebrant_firstname' => $celebrant_firstname,
                ':celebrant_middlename' => $celebrant_middlename,
                ':celebrant_lastname' => $celebrant_lastname,
                ':celebrant_suffix' => $celebrant_suffix,
                ':celebrant_birthdate' => $celebrant_birthdate,

                ':wife_firstname' => $wife_firstname,
                ':wife_middlename' => $wife_middlename,
                ':wife_lastname' => $wife_lastname,

                ':husband_firstname' => $husband_firstname,
                ':husband_middlename' => $husband_middlename,
                ':husband_lastname' => $husband_lastname,
                ':husband_suffix' => $husband_suffix,

                ':wedding_date' => $wedding_date,

                ':contact_firstname' => $g('contact_firstname') ?: null,
                ':contact_middlename' => $g('contact_middlename') ?: null,
                ':contact_lastname' => $g('contact_lastname') ?: null,
                ':contact_suffix' => $g('contact_suffix') ?: null,
                ':contact_relation' => $g('contact_relation') ?: null,
                ':contact_number' => $g('contact_number') ?: null,
                ':contact_email' => $g('contact_email') ?: null,

                ':id' => $id
            ]);

            $dbh->commit();
            header("Location: s_celebratory.php?msg=updated");
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
<title>Update Celebratory Mass Record</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet"/>
<link href='../../../stylesheets/private/sacrament_insert.css' rel='stylesheet'>
<style>
.hidden { display:none; }
</style>
</head>
<body>

<div class="record-container">
  <h2>Update Celebratory Mass Record</h2>

  <?php if ($msg): ?>
    <div class="alert alert-warning"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <?php if (!empty($celebratory['decline_reason'])): ?>
    <div class="alert alert-danger">
      <strong>Decline Reason:</strong> <?= htmlspecialchars($celebratory['decline_reason']) ?>
    </div>
  <?php endif; ?>

  <form method="post" action="s_celebratory_update.php?id=<?= $id ?>">
    <input type="hidden" name="id" value="<?= $id ?>">

    <!-- ================= RESERVATION ================= -->
    <div class="record-section">
      <h3>Reservation Details</h3>

      <div class="row record-inline">

        <div class="col-md-3 record-row">
          <span class="record-label">Mass Date</span>
          <input type="date" name="date" class="form-control" value="<?= v($celebratory,'date') ?>" required>
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Mass Time</span>
          <input type="time" name="time" class="form-control" value="<?= v($celebratory,'time') ?>" required>
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Celebration Type</span>
          <select name="celebration_type" id="celebration_type" class="form-control" required>
            <option value="">-- Select --</option>
            <option value="Thanksgiving" <?= (v($celebratory,'celebration_type') === 'Thanksgiving') ? 'selected' : '' ?>>Thanksgiving</option>
            <option value="Birthday" <?= (v($celebratory,'celebration_type') === 'Birthday') ? 'selected' : '' ?>>Birthday</option>
            <option value="Wedding Anniversary" <?= (v($celebratory,'celebration_type') === 'Wedding Anniversary') ? 'selected' : '' ?>>Wedding Anniversary</option>
            <option value="Special Intention" <?= (v($celebratory,'celebration_type') === 'Special Intention') ? 'selected' : '' ?>>Special Intention</option>
          </select>
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Mass Venue</span>
          <input type="text" name="mass_venue" class="form-control" value="<?= v($celebratory,'mass_venue') ?>">
        </div>

      </div>

      <div class="row record-inline" style="margin-top:10px;">
        <div class="col-md-3 record-row"><span class="record-label">Province</span><input name="province" class="form-control" value="<?= v($celebratory,'province') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">City</span><input name="city" class="form-control" value="<?= v($celebratory,'city') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Street No.</span><input name="street_no" class="form-control" value="<?= v($celebratory,'street_no') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Barangay</span><input name="barangay" class="form-control" value="<?= v($celebratory,'barangay') ?>"></div>
      </div>
    </div>

    <!-- ================= TYPE-SPECIFIC ================= -->
    <div class="record-section">
      <h3>Celebration Details</h3>

      <!-- THANKSGIVING -->
      <div id="section_thanksgiving" class="type-section">
        <div class="record-row">
          <span class="record-label">Thanksgiving Message</span>
          <textarea name="thanksgiving" class="form-control" rows="4"><?= v($celebratory,'thanksgiving') ?></textarea>
        </div>
      </div>

      <!-- BIRTHDAY -->
      <div id="section_birthday" class="type-section hidden">
        <div class="row record-inline">
          <div class="col-md-3 record-row"><span class="record-label">Celebrant First Name</span><input name="celebrant_firstname" class="form-control" value="<?= v($celebratory,'celebrant_firstname') ?>"></div>
          <div class="col-md-3 record-row"><span class="record-label">Celebrant Middle Name</span><input name="celebrant_middlename" class="form-control" value="<?= v($celebratory,'celebrant_middlename') ?>"></div>
          <div class="col-md-3 record-row"><span class="record-label">Celebrant Last Name</span><input name="celebrant_lastname" class="form-control" value="<?= v($celebratory,'celebrant_lastname') ?>"></div>
          <div class="col-md-3 record-row"><span class="record-label">Suffix</span><input name="celebrant_suffix" class="form-control" value="<?= v($celebratory,'celebrant_suffix') ?>"></div>
        </div>
        <div class="row record-inline" style="margin-top:10px;">
          <div class="col-md-3 record-row"><span class="record-label">Celebrant Birthdate</span><input type="date" name="celebrant_birthdate" class="form-control" value="<?= v($celebratory,'celebrant_birthdate') ?>"></div>
        </div>
      </div>

      <!-- WEDDING ANNIVERSARY -->
      <div id="section_wedding" class="type-section hidden">
        <div class="row record-inline">
          <div class="col-md-4 record-row"><span class="record-label">Wife First Name</span><input name="wife_firstname" class="form-control" value="<?= v($celebratory,'wife_firstname') ?>"></div>
          <div class="col-md-4 record-row"><span class="record-label">Wife Middle Name</span><input name="wife_middlename" class="form-control" value="<?= v($celebratory,'wife_middlename') ?>"></div>
          <div class="col-md-4 record-row"><span class="record-label">Wife Last Name</span><input name="wife_lastname" class="form-control" value="<?= v($celebratory,'wife_lastname') ?>"></div>
        </div>

        <div class="row record-inline" style="margin-top:10px;">
          <div class="col-md-4 record-row"><span class="record-label">Husband First Name</span><input name="husband_firstname" class="form-control" value="<?= v($celebratory,'husband_firstname') ?>"></div>
          <div class="col-md-4 record-row"><span class="record-label">Husband Middle Name</span><input name="husband_middlename" class="form-control" value="<?= v($celebratory,'husband_middlename') ?>"></div>
          <div class="col-md-4 record-row"><span class="record-label">Husband Last Name</span><input name="husband_lastname" class="form-control" value="<?= v($celebratory,'husband_lastname') ?>"></div>
        </div>

        <div class="row record-inline" style="margin-top:10px;">
          <div class="col-md-3 record-row"><span class="record-label">Husband Suffix</span><input name="husband_suffix" class="form-control" value="<?= v($celebratory,'husband_suffix') ?>"></div>
          <div class="col-md-3 record-row"><span class="record-label">Wedding Date</span><input type="date" name="wedding_date" class="form-control" value="<?= v($celebratory,'wedding_date') ?>"></div>
        </div>
      </div>

      <!-- SPECIAL INTENTION -->
      <div id="section_intention" class="type-section hidden">
        <div class="record-row">
          <span class="record-label">Special Intention</span>
          <textarea name="special_intention" class="form-control" rows="4"><?= v($celebratory,'special_intention') ?></textarea>
        </div>
      </div>

    </div>

    <!-- ================= CONTACT ================= -->
    <div class="record-section">
      <h3>Contact Person</h3>

      <div class="row record-inline">
        <div class="col-md-3 record-row"><span class="record-label">First Name</span><input name="contact_firstname" class="form-control" value="<?= v($celebratory,'contact_firstname') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Middle Name</span><input name="contact_middlename" class="form-control" value="<?= v($celebratory,'contact_middlename') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Last Name</span><input name="contact_lastname" class="form-control" value="<?= v($celebratory,'contact_lastname') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Suffix</span><input name="contact_suffix" class="form-control" value="<?= v($celebratory,'contact_suffix') ?>"></div>
      </div>

      <div class="row record-inline" style="margin-top:10px;">
        <div class="col-md-4 record-row"><span class="record-label">Relation</span><input name="contact_relation" class="form-control" value="<?= v($celebratory,'contact_relation') ?>" required></div>
        <div class="col-md-4 record-row"><span class="record-label">Phone</span><input name="contact_number" class="form-control" value="<?= v($celebratory,'contact_number') ?>" required></div>
        <div class="col-md-4 record-row"><span class="record-label">Email</span><input type="email" name="contact_email" class="form-control" value="<?= v($celebratory,'contact_email') ?>" required></div>
      </div>
    </div>

    <!-- ================= BUTTONS ================= -->
    <div class="record-buttons">
      <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
      <a href="s_celebratory.php" class="btn btn-default">Cancel</a>
    </div>

  </form>
</div>

<script>
function toggleSections() {
  const type = document.getElementById('celebration_type').value;

  const secThanks = document.getElementById('section_thanksgiving');
  const secBday   = document.getElementById('section_birthday');
  const secWed    = document.getElementById('section_wedding');
  const secInt    = document.getElementById('section_intention');

  // hide all
  [secThanks, secBday, secWed, secInt].forEach(s => s.classList.add('hidden'));

  if (type === 'Thanksgiving') secThanks.classList.remove('hidden');
  else if (type === 'Birthday') secBday.classList.remove('hidden');
  else if (type === 'Wedding Anniversary') secWed.classList.remove('hidden');
  else if (type === 'Special Intention') secInt.classList.remove('hidden');
}

// initial + on change
toggleSections();
document.getElementById('celebration_type').addEventListener('change', toggleSections);
</script>

</body>
</html>
