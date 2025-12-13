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
    header('Location: s_baptism.php');
    exit();
}

$id  = intval($_GET['id']);
$msg = "";

// --- fetch existing first_communion ---
try {
    $stmt = $dbh->prepare("SELECT * FROM first_communion WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $first_communion = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$first_communion) {
        header("Location: s_baptism.php?error=notfound");
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

    $birth_certificate = $g('birth_certificate');
    $baptismal_certificate = $g('baptismal_certificate');

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

                        birth_certificate = :birth_certificate,
                        baptismal_certificate = :baptismal_certificate
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

                'birth_certificate' => $birth_certificate,
                'baptismal_certificate' => $baptismal_certificate ?: null,

                ':id' => $id
            ]);

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
<title>View First Communion Record</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet"/>
<link href='../../../stylesheets/private/sacrament_insert.css' rel='stylesheet'>
</head>
<body>

<div class="record-container">
  <h2>View First Communion Record</h2>

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
          <span class="record-label">Date</span>
          <div class="record-value"><?= v($first_communion,'date') ?></div>
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Time</span>
          <div class="record-value"><?= v($first_communion,'time') ?></div>
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Contact</span>
          <div class="record-value"><?= v($first_communion,'contact') ?></div>
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Email</span>
          <div class="record-value"><?= v($first_communion,'email') ?></div>
        </div>

      <div class="row record-inline">

      
      </div>
      </div>
    </div>

    <!-- ================= CHILD ================= -->
    <div class="record-section">
      <h3>Child Details</h3>

      <div class="record-row">
        <span class="record-label">Name</span>
        <div class="record-value">
          <?= v($first_communion,'child_firstname') ?>
          <?= v($first_communion,'child_middlename') ?>
          <?= v($first_communion,'child_lastname') ?>
          <?= v($first_communion,'child_suffix') ?>
        </div>
      </div>

      <div class="row record-inline">

        <div class="col-md-3 record-row">
          <span class="record-label">Birthdate</span>
          <div class="record-value"><?= v($first_communion,'birth_date') ?></div>
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Birth Place</span>
          <div class="record-value"><?= v($first_communion,'birth_place') ?></div>
        </div>

      </div>
    </div>

    <!-- ================= PARENTS ================= -->
    <div class="record-section">
      <h3>Father</h3>
      <div class="record-row">
        <span class="record-label">Name</span>
        <div class="record-value">
          <?= v($first_communion,'father_firstname') ?>
          <?= v($first_communion,'father_middlename') ?>
          <?= v($first_communion,'father_lastname') ?>
          <?= v($first_communion,'father_suffix') ?>
        </div>
      </div>

      <h3 style="margin-top:20px;">Mother</h3>
      <div class="record-row">
        <span class="record-label">Name</span>
        <div class="record-value">
          <?= v($first_communion,'mother_firstname') ?>
          <?= v($first_communion,'mother_middlename') ?>
          <?= v($first_communion,'mother_lastname') ?>
        </div>
      </div>

    <!-- ================= FILES ================= -->
<div class="record-row">
  <h3>Documents</h3>
  <span class="record-label">Birth Certificate</span>
  <div class="record-value">
      <?php if (!empty($first_communion['birth_certificate'])): ?>
          <a href="view_file.php?file=<?= urlencode($first_communion['birth_certificate']) ?>"
            target="_blank"
            class="glyphicon glyphicon-file"">
            View Birth Certificate
          </a>
      <?php endif; ?>
  </div>
</div>

<div class="record-row" style="margin-top:10px;">
  <span class="record-label">Baptismal Certificate</span>
  <div class="record-value">

<?php if (!empty($first_communion['baptismal_certificate'])): ?>
    <a href="view_file.php?file=<?= urlencode($first_communion['baptismal_certificate']) ?>"
       target="_blank"
       class="glyphicon glyphicon-file">
       View Baptismal Certificate
    </a>
<?php endif; ?>

  </div>
</div>



    <!-- ================= BUTTON ================= -->
    <div class="record-buttons">
      <a href="s_first_comm.php" class="btn btn-primary">Back</a>
    </div>

  </form>
</div>

  </form>
</div>
</body>
</html>
