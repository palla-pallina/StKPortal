<?php
require_once '../connection.php';
session_start();

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}
$admin_name = $_SESSION['username'] ?? 'Admin';

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: s_first_comm.php');
    exit();
}

$id  = intval($_GET['id']);
$msg = "";

// --- fetch existing first_communion + sponsors ---
try {
    $stmt = $dbh->prepare("SELECT * FROM first_communion WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $first_communion = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$first_communion) {
        header("Location: s_first_comm.php?error=notfound");
        exit();
    }

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// --- POST: handle update ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {

    // helper to trim
    $g = function($k) { return isset($_POST[$k]) ? trim($_POST[$k]) : ''; };

    // collect fields (use same names as DB)
    $date             = $g('date');
    $time             = $g('time');
    $contact          = $g('contact');
    $email            = $g('email');

    $child_firstname  = $g('child_firstname');
    $child_middlename = $g('child_middlename');
    $child_lastname   = $g('child_lastname');
    $child_suffix     = $g('child_suffix');
    $birth_date       = $g('birth_date');
    $birth_place      = $g('birth_place');

    $father_firstname  = $g('father_firstname');
    $father_middlename = $g('father_middlename');
    $father_lastname   = $g('father_lastname');
    $father_suffix     = $g('father_suffix');

    $mother_firstname  = $g('mother_firstname');
    $mother_middlename = $g('mother_middlename');
    $mother_lastname   = $g('mother_lastname');

    $presiding_priest   = $g('presiding_priest');
    

    // Basic validation
    if ($child_firstname === '' || $child_lastname === '') {
        $msg = "Child first and last name are required.";
    } else {
        try {
            $dbh->beginTransaction();

            $sql = "UPDATE first_communion SET
                        date = :date,
                        time = :time,
                        contact = :contact,
                        email = :email,

                        child_firstname = :child_firstname,
                        child_middlename = :child_middlename,
                        child_lastname = :child_lastname,
                        child_suffix = :child_suffix,
                        birth_date = :birth_date,
                        birth_place = :birth_place,

                        father_firstname = :father_firstname,
                        father_middlename = :father_middlename,
                        father_lastname = :father_lastname,
                        father_suffix = :father_suffix,

                        mother_firstname = :mother_firstname,
                        mother_middlename = :mother_middlename,
                        mother_lastname = :mother_lastname,
                        
                        presiding_priest = :presiding_priest

                    WHERE id = :id
                ";

            $upd = $dbh->prepare($sql);
            $upd->execute([
                ':date' => $date ?: null,
                ':time' => $time ?: null,
                ':contact' => $contact ?: null,
                ':email' => $email ?: null,

                ':child_firstname' => $child_firstname,
                ':child_middlename' => $child_middlename ?: null,
                ':child_lastname' => $child_lastname,
                ':child_suffix' => $child_suffix ?: null,
                ':birth_date' => $birth_date ?: null,
                ':birth_place' => $birth_place ?: null,

                ':father_firstname' => $father_firstname ?: null,
                ':father_middlename' => $father_middlename ?: null,
                ':father_lastname' => $father_lastname ?: null,
                ':father_suffix' => $father_suffix ?: null,

                ':mother_firstname' => $mother_firstname ?: null,
                ':mother_middlename' => $mother_middlename ?: null,
                ':mother_lastname' => $mother_lastname ?: null,
                ':presiding_priest' => $presiding_priest ?: null,

                ':id' => $id
            ]);

            $dbh->commit();

            // redirect on success
            header("Location: s_first_comm.php?msg=updated");
            exit();

        } catch (PDOException $e) {
            $dbh->rollBack();
            $msg = "Update failed: " . $e->getMessage();
        }
    }
}

// --- Helper to echo safely and keep old values on POST fallback ---
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
<title>Update First Communion Record</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet"/>
<link href='../../../stylesheets/private/sacrament_insert.css' rel='stylesheet'>
</head>
<body>

<div class="record-container">
  <h2>Update First Communion Record</h2>

  <?php if ($msg): ?>
    <div class="alert alert-warning"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <form method="post" action="s_first_comm_update.php?id=<?= $id ?>">
    <input type="hidden" name="id" value="<?= $id ?>">

    <!-- ================= RESERVATION ================= -->
    <div class="record-section">
      <h3>Reservation Details</h3>
      <div class="row record-inline">


        <div class="col-md-2 record-row">
          <span class="record-label">Date</span>
          <input type="date" name="date" class="form-control" value="<?= v($first_communion,'date') ?>">
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Time</span>
          <input type="time" name="time" class="form-control" value="<?= v($first_communion,'time') ?>">
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Contact</span>
          <input type="text" name="contact" class="form-control" value="<?= v($first_communion,'contact') ?>">
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Email</span>
          <input type="email" name="email" class="form-control" value="<?= v($first_communion,'email') ?>">
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Presiding Priest</span>
          <input type="text" name="presiding_priest" class="form-control" value="<?= v($first_communion,'presiding_priest') ?>">
        </div>
      </div>


    </div>

    <!-- ================= CHILD ================= -->
    <div class="record-section">
      <h3>Child Details</h3>

      <div class="row record-inline">

        <div class="col-md-3 record-row">
          <span class="record-label">First Name</span>
          <input type="text" name="child_firstname" class="form-control" value="<?= v($first_communion,'child_firstname') ?>" required>
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Middle Name</span>
          <input type="text" name="child_middlename" class="form-control" value="<?= v($first_communion,'child_middlename') ?>">
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Last Name</span>
          <input type="text" name="child_lastname" class="form-control" value="<?= v($first_communion,'child_lastname') ?>" required>
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Suffix</span>
          <input type="text" name="child_suffix" class="form-control" value="<?= v($first_communion,'child_suffix') ?>">
        </div>

      </div>

      <div class="row record-inline" style="margin-top:10px;">

        <div class="col-md-2 record-row">
          <span class="record-label">Birthdate</span>
          <input type="date" name="birth_date" class="form-control" value="<?= v($first_communion,'birth_date') ?>">
        </div>

        <div class="col-md-4 record-row">
          <span class="record-label">Birth Place</span>
          <input type="text" name="birth_place" class="form-control" value="<?= v($first_communion,'birth_place') ?>">
        </div>

      </div>
    </div>

    <!-- ================= FATHER ================= -->
    <div class="record-section">
      <h3>Father</h3>
      <div class="row record-inline">
        <div class="col-md-3 record-row"><span class="record-label">First Name</span><input name="father_firstname" class="form-control" value="<?= v($first_communion,'father_firstname') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Middle Name</span><input name="father_middlename" class="form-control" value="<?= v($first_communion,'father_middlename') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Last Name</span><input name="father_lastname" class="form-control" value="<?= v($first_communion,'father_lastname') ?>"></div>
        <div class="col-md-2 record-row"><span class="record-label">Suffix</span><input name="father_suffix" class="form-control" value="<?= v($first_communion,'father_suffix') ?>"></div>
      </div>
    </div>

    <!-- ================= MOTHER ================= -->
    <div class="record-section">
      <h3>Mother</h3>
      <div class="row record-inline">
        <div class="col-md-3 record-row"><span class="record-label">First Name</span><input name="mother_firstname" class="form-control" value="<?= v($first_communion,'mother_firstname') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Middle Name</span><input name="mother_middlename" class="form-control" value="<?= v($first_communion,'mother_middlename') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Last Name</span><input name="mother_lastname" class="form-control" value="<?= v($first_communion,'mother_lastname') ?>"></div>

      </div>

    <!-- ================= BUTTONS ================= -->
    <div class="record-buttons">
      <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
      <a href="s_first_comm.php" class="btn btn-default">Cancel</a>
    </div>

  </form>
</div>

</body>

</html>
