<?php
require_once 'connection.php';
session_start();

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}
$admin_name = $_SESSION['username'] ?? 'Admin';

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: s_baptism.php');
    exit();
}

$id  = intval($_GET['id']);
$msg = "";

// --- fetch existing baptism + sponsors ---
try {
    $stmt = $dbh->prepare("SELECT * FROM baptisms WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $baptism = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$baptism) {
        header("Location: s_baptism.php?error=notfound");
        exit();
    }

    // fetch additional ninong (as_ninong_tbl) - assume one row per baptism_id
    $stmt = $dbh->prepare("SELECT * FROM as_ninong_tbl WHERE baptism_id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $asNinong = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    // fetch additional ninang (as_ninang_tbl)
    $stmt = $dbh->prepare("SELECT * FROM as_ninang_tbl WHERE baptism_id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $asNinang = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// --- POST: handle update ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {

    // helper to trim
    $g = function($k) { return isset($_POST[$k]) ? trim($_POST[$k]) : ''; };

    // collect fields (use same names as DB)
    $baptism_type     = $g('baptism_type');
    $date             = $g('date');
    $time             = $g('time');
    $contact          = $g('contact');
    $email            = $g('email');

    $child_firstname  = $g('child_firstname');
    $child_middlename = $g('child_middlename');
    $child_lastname   = $g('child_lastname');
    $child_suffix     = $g('child_suffix');
    $child_gender     = $g('child_gender');
    $birth_date       = $g('birth_date');
    $birth_place      = $g('birth_place');
    $child_nat        = $g('child_nat');

    $father_firstname  = $g('father_firstname');
    $father_middlename = $g('father_middlename');
    $father_lastname   = $g('father_lastname');
    $father_suffix     = $g('father_suffix');

    $mother_firstname  = $g('mother_firstname');
    $mother_middlename = $g('mother_middlename');
    $mother_lastname   = $g('mother_lastname');
    $marriage_type     = $g('marriage_type');
    $address           = $g('address');

    $ninong_firstname   = $g('ninong_firstname');
    $ninong_middlename  = $g('ninong_middlename');
    $ninong_lastname    = $g('ninong_lastname');
    $ninong_suffix      = $g('ninong_suffix');

    $ninang_firstname   = $g('ninang_firstname');
    $ninang_middlename  = $g('ninang_middlename');
    $ninang_lastname    = $g('ninang_lastname');

    // additional/secondary ninong/ninang
    $as_ninong_firstname  = $g('as_ninong_firstname');
    $as_ninong_middlename = $g('as_ninong_middlename');
    $as_ninong_lastname   = $g('as_ninong_lastname');
    $as_ninong_suffix     = $g('as_ninong_suffix');

    $as_ninang_firstname  = $g('as_ninang_firstname');
    $as_ninang_middlename = $g('as_ninang_middlename');
    $as_ninang_lastname   = $g('as_ninang_lastname');

    // Basic validation
    if ($child_firstname === '' || $child_lastname === '') {
        $msg = "Child first and last name are required.";
    } else {
        try {
            $dbh->beginTransaction();

            $sql = "UPDATE baptisms SET
                        baptism_type = :baptism_type,
                        date = :date,
                        time = :time,
                        contact = :contact,
                        email = :email,

                        child_firstname = :child_firstname,
                        child_middlename = :child_middlename,
                        child_lastname = :child_lastname,
                        child_suffix = :child_suffix,
                        child_gender = :child_gender,
                        birth_date = :birth_date,
                        birth_place = :birth_place,
                        child_nat = :child_nat,

                        father_firstname = :father_firstname,
                        father_middlename = :father_middlename,
                        father_lastname = :father_lastname,
                        father_suffix = :father_suffix,

                        mother_firstname = :mother_firstname,
                        mother_middlename = :mother_middlename,
                        mother_lastname = :mother_lastname,
                        marriage_type = :marriage_type,
                        address = :address,

                        ninong_firstname = :ninong_firstname,
                        ninong_middlename = :ninong_middlename,
                        ninong_lastname = :ninong_lastname,
                        ninong_suffix = :ninong_suffix,

                        ninang_firstname = :ninang_firstname,
                        ninang_middlename = :ninang_middlename,
                        ninang_lastname = :ninang_lastname
                    WHERE id = :id
                ";

            $upd = $dbh->prepare($sql);
            $upd->execute([
                ':baptism_type' => $baptism_type ?: null,
                ':date' => $date ?: null,
                ':time' => $time ?: null,
                ':contact' => $contact ?: null,
                ':email' => $email ?: null,

                ':child_firstname' => $child_firstname,
                ':child_middlename' => $child_middlename ?: null,
                ':child_lastname' => $child_lastname,
                ':child_suffix' => $child_suffix ?: null,
                ':child_gender' => $child_gender ?: null,
                ':birth_date' => $birth_date ?: null,
                ':birth_place' => $birth_place ?: null,
                ':child_nat' => $child_nat ?: null,

                ':father_firstname' => $father_firstname ?: null,
                ':father_middlename' => $father_middlename ?: null,
                ':father_lastname' => $father_lastname ?: null,
                ':father_suffix' => $father_suffix ?: null,

                ':mother_firstname' => $mother_firstname ?: null,
                ':mother_middlename' => $mother_middlename ?: null,
                ':mother_lastname' => $mother_lastname ?: null,
                ':marriage_type' => $marriage_type ?: null,
                ':address' => $address ?: null,

                ':ninong_firstname' => $ninong_firstname ?: null,
                ':ninong_middlename' => $ninong_middlename ?: null,
                ':ninong_lastname' => $ninong_lastname ?: null,
                ':ninong_suffix' => $ninong_suffix ?: null,

                ':ninang_firstname' => $ninang_firstname ?: null,
                ':ninang_middlename' => $ninang_middlename ?: null,
                ':ninang_lastname' => $ninang_lastname ?: null,

                ':id' => $id
            ]);

            // 2) upsert additional ninong (as_ninong_tbl)
            // check if exists
            $stmt = $dbh->prepare("SELECT id FROM as_ninong_tbl WHERE baptism_id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $exists = $stmt->fetchColumn();

            if ($exists) {
                $sqlN = "UPDATE as_ninong_tbl SET
                            as_ninong_firstname = :fn,
                            as_ninong_middlename = :mn,
                            as_ninong_lastname = :ln,
                            as_ninong_suffix = :sf
                         WHERE baptism_id = :id";
                $sth = $dbh->prepare($sqlN);
                $sth->execute([
                    ':fn' => $as_ninong_firstname ?: null,
                    ':mn' => $as_ninong_middlename ?: null,
                    ':ln' => $as_ninong_lastname ?: null,
                    ':sf' => $as_ninong_suffix ?: null,
                    ':id' => $id
                ]);
            } else {
                // insert only if at least one field provided
                if ($as_ninong_firstname !== '' || $as_ninong_lastname !== '') {
                    $sqlN = "INSERT INTO as_ninong_tbl (baptism_id, as_ninong_firstname, as_ninong_middlename, as_ninong_lastname, as_ninong_suffix)
                             VALUES (:id, :fn, :mn, :ln, :sf)";
                    $sth = $dbh->prepare($sqlN);
                    $sth->execute([
                        ':id' => $id,
                        ':fn' => $as_ninong_firstname ?: null,
                        ':mn' => $as_ninong_middlename ?: null,
                        ':ln' => $as_ninong_lastname ?: null,
                        ':sf' => $as_ninong_suffix ?: null
                    ]);
                }
            }

            // 3) upsert additional ninang (as_ninang_tbl)
            $stmt = $dbh->prepare("SELECT id FROM as_ninang_tbl WHERE baptism_id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $exists2 = $stmt->fetchColumn();

            if ($exists2) {
                $sqlNA = "UPDATE as_ninang_tbl SET
                            as_ninang_firstname = :fn,
                            as_ninang_middlename = :mn,
                            as_ninang_lastname = :ln
                          WHERE baptism_id = :id";
                $sth2 = $dbh->prepare($sqlNA);
                $sth2->execute([
                    ':fn' => $as_ninang_firstname ?: null,
                    ':mn' => $as_ninang_middlename ?: null,
                    ':ln' => $as_ninang_lastname ?: null,
                    ':id' => $id
                ]);
            } else {
                if ($as_ninang_firstname !== '' || $as_ninang_lastname !== '') {
                    $sqlNA = "INSERT INTO as_ninang_tbl (baptism_id, as_ninang_firstname, as_ninang_middlename, as_ninang_lastname)
                              VALUES (:id, :fn, :mn, :ln)";
                    $sth2 = $dbh->prepare($sqlNA);
                    $sth2->execute([
                        ':id' => $id,
                        ':fn' => $as_ninang_firstname ?: null,
                        ':mn' => $as_ninang_middlename ?: null,
                        ':ln' => $as_ninang_lastname ?: null
                    ]);
                }
            }

            $dbh->commit();

            // redirect on success
            header("Location: s_baptism.php?msg=updated");
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
<title>View Baptism Record</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet"/>
<link href='../../stylesheets/private/s_sacrament_record.css' rel='stylesheet'>
</head>
<body>
<div class="record-container">

<div class="record-container">
  <h2>View Baptism Record</h2>

  <?php if ($msg): ?>
    <div class="alert alert-warning"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <form method="post" action="update.php?id=<?= $id ?>">
    <input type="hidden" name="id" value="<?= $id ?>">

    <!-- ================= RESERVATION ================= -->
    <div class="record-section">
      <h3>Reservation Details</h3>
      <div class="row record-inline">

        <div class="col-md-2 record-row">
          <span class="record-label">Baptism Type</span>
          <div class="record-value"><?= v($baptism,'baptism_type') ?></div>
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Date</span>
          <div class="record-value"><?= v($baptism,'date') ?></div>
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Time</span>
          <div class="record-value"><?= v($baptism,'time') ?></div>
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Contact</span>
          <div class="record-value"><?= v($baptism,'contact') ?></div>
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Email</span>
          <div class="record-value"><?= v($baptism,'email') ?></div>
        </div>

      </div>
    </div>

    <!-- ================= CHILD ================= -->
    <div class="record-section">
      <h3>Child Details</h3>

      <div class="record-row">
        <div class="record-value">
          <?= v($baptism,'child_firstname') ?>
          <?= v($baptism,'child_middlename') ?>
          <?= v($baptism,'child_lastname') ?>
          <?= v($baptism,'child_suffix') ?>
        </div>
      </div>

      <div class="row record-inline">

        <div class="col-md-2 record-row">
          <span class="record-label">Gender</span>
          <div class="record-value"><?= v($baptism,'child_gender') ?></div>
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Birthdate</span>
          <div class="record-value"><?= v($baptism,'birth_date') ?></div>
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Birth Place</span>
          <div class="record-value"><?= v($baptism,'birth_place') ?></div>
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Nationality</span>
          <div class="record-value"><?= v($baptism,'child_nat') ?></div>
        </div>

      </div>
    </div>

    <!-- ================= PARENTS ================= -->
    <div class="record-section">
      <h3>Father</h3>
      <div class="record-row">
        <div class="record-value">
          <?= v($baptism,'father_firstname') ?>
          <?= v($baptism,'father_middlename') ?>
          <?= v($baptism,'father_lastname') ?>
          <?= v($baptism,'father_suffix') ?>
        </div>
      </div>

      <h3 style="margin-top:20px;">Mother</h3>
      <div class="record-row">
        <div class="record-value">
          <?= v($baptism,'mother_firstname') ?>
          <?= v($baptism,'mother_middlename') ?>
          <?= v($baptism,'mother_lastname') ?>
        </div>
      </div>

      <div class="record-row" style="margin-top:10px;">
        <span class="record-label">Marriage Type</span>
        <div class="record-value"><?= v($baptism,'marriage_type') ?></div>
      </div>

      <div class="record-row" style="margin-top:10px;">
        <span class="record-label">Address</span>
        <div class="record-address"><?= v($baptism,'address') ?></div>
      </div>
    </div>

    <!-- ================= NINONG / NINANG ================= -->
    <div class="record-section">
      <h3>Main Sponsors</h3>

      <div class="record-row">
        <div class="record-value">
          <?= v($baptism,'ninong_firstname') ?>
          <?= v($baptism,'ninong_middlename') ?>
          <?= v($baptism,'ninong_lastname') ?>
          <?= v($baptism,'ninong_suffix') ?>
        </div>
      </div>

      <div class="record-row" style="margin-top:10px;">
        <div class="record-value">
          <?= v($baptism,'ninang_firstname') ?>
          <?= v($baptism,'ninang_middlename') ?>
          <?= v($baptism,'ninang_lastname') ?>
        </div>
      </div>
    </div>

    <!-- ================= ADDITIONAL ================= -->
    <div class="record-section">
      <h3>Additional Sponsors</h3>

      <div class="record-row">
        <div class="record-value">
          <?= htmlspecialchars($asNinong['as_ninong_firstname'] ?? '') ?>
          <?= htmlspecialchars($asNinong['as_ninong_middlename'] ?? '') ?>
          <?= htmlspecialchars($asNinong['as_ninong_lastname'] ?? '') ?>
          <?= htmlspecialchars($asNinong['as_ninong_suffix'] ?? '') ?>
        </div>
      </div>

      <div class="record-row" style="margin-top:10px;">
        <div class="record-value">
          <?= htmlspecialchars($asNinang['as_ninang_firstname'] ?? '') ?>
          <?= htmlspecialchars($asNinang['as_ninang_middlename'] ?? '') ?>
          <?= htmlspecialchars($asNinang['as_ninang_lastname'] ?? '') ?>
        </div>
      </div>
    </div>

    <!-- ================= BUTTON ================= -->
    <div class="record-buttons">
      <a href="s_baptism.php" class="btn btn-primary">Back</a>
    </div>

  </form>
</div>

  </form>
</div>
</body>
</html>
