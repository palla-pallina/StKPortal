<?php
require_once '../connection.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: s_confirmation.php");
    exit();
}

$id = intval($_GET['id']);
$msg = "";

/* ================= FETCH ALL RECORDS ================= */

try {
    // confirmation
    $stmt = $dbh->prepare("SELECT * FROM confirmations WHERE id=:id");
    $stmt->execute([':id'=>$id]);
    $confirmation = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$confirmation) die("Record not found");

    // MAIN NINONG
    $stmt = $dbh->prepare("SELECT * FROM ninong_tbl WHERE confirmation_id=:id");
    $stmt->execute([':id'=>$id]);
    $ninong = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    // MAIN NINANG
    $stmt = $dbh->prepare("SELECT * FROM ninang_tbl WHERE confirmation_id=:id");
    $stmt->execute([':id'=>$id]);
    $ninang = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    // ADDITIONAL
    $stmt = $dbh->prepare("SELECT * FROM as_ninong_tbl WHERE confirmation_id = :id");
    $stmt->execute([':id' => $id]);
    $asNinong = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $dbh->prepare("SELECT * FROM as_ninang_tbl WHERE confirmation_id = :id");
    $stmt->execute([':id' => $id]);
    $asNinang = $stmt->fetchAll(PDO::FETCH_ASSOC);


} catch(PDOException $e){
    die("DB Error: ".$e->getMessage());
}

/* ================= UPDATE ================= */

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update'])) {

    $g = fn($k)=>trim($_POST[$k]??'');

    $confirm_firstname  = $g('confirm_firstname');
    $confirm_lastname   = $g('confirm_lastname');

    if ($confirm_firstname === '' || $confirm_lastname === '') {
        $msg = "First and Last name required.";
    } else {
        try {
            $dbh->beginTransaction();

            /* === UPDATE CONFIRMATION === */
            $dbh->prepare("
                UPDATE confirmations SET
                    date=:date, time=:time, contact=:contact, email=:email,
                    confirm_firstname=:cf, confirm_middlename=:cm, confirm_lastname=:cl,
                    confirm_suffix=:cs, confirm_gender=:cg, birth_date=:bd,
                    birth_place=:bp, confirm_nat=:cn,
                    father_firstname=:ff, father_middlename=:fm, father_lastname=:fl, father_suffix=:fs,
                    mother_firstname=:mf, mother_middlename=:mm, mother_lastname=:ml
                WHERE id=:id
            ")->execute([
                ':date'=>$g('date')?:null, ':time'=>$g('time')?:null,
                ':contact'=>$g('contact')?:null, ':email'=>$g('email')?:null,

                ':cf'=>$confirm_firstname, ':cm'=>$g('confirm_middlename')?:null,
                ':cl'=>$confirm_lastname, ':cs'=>$g('confirm_suffix')?:null,
                ':cg'=>$g('confirm_gender')?:null,
                ':bd'=>$g('birth_date')?:null, ':bp'=>$g('birth_place')?:null,
                ':cn'=>$g('confirm_nat')?:null,

                ':ff'=>$g('father_firstname')?:null,
                ':fm'=>$g('father_middlename')?:null,
                ':fl'=>$g('father_lastname')?:null,
                ':fs'=>$g('father_suffix')?:null,

                ':mf'=>$g('mother_firstname')?:null,
                ':mm'=>$g('mother_middlename')?:null,
                ':ml'=>$g('mother_lastname')?:null,

                ':id'=>$id
            ]);

            /* === UPSERT MAIN NINONG === */
            $dbh->prepare("
                INSERT INTO ninong_tbl 
                (confirmation_id,ninong_firstname,ninong_middlename,ninong_lastname,ninong_suffix)
                VALUES (:id,:fn,:mn,:ln,:sf)
                ON DUPLICATE KEY UPDATE
                    ninong_firstname=VALUES(ninong_firstname),
                    ninong_middlename=VALUES(ninong_middlename),
                    ninong_lastname=VALUES(ninong_lastname),
                    ninong_suffix=VALUES(ninong_suffix)
            ")->execute([
                ':id'=>$id, ':fn'=>$g('ninong_firstname')?:null,
                ':mn'=>$g('ninong_middlename')?:null,
                ':ln'=>$g('ninong_lastname')?:null,
                ':sf'=>$g('ninong_suffix')?:null
            ]);

            /* === UPSERT MAIN NINANG === */
            $dbh->prepare("
                INSERT INTO ninang_tbl 
                (confirmation_id,ninang_firstname,ninang_middlename,ninang_lastname)
                VALUES (:id,:fn,:mn,:ln)
                ON DUPLICATE KEY UPDATE
                    ninang_firstname=VALUES(ninang_firstname),
                    ninang_middlename=VALUES(ninang_middlename),
                    ninang_lastname=VALUES(ninang_lastname)
            ")->execute([
                ':id'=>$id, ':fn'=>$g('ninang_firstname')?:null,
                ':mn'=>$g('ninang_middlename')?:null,
                ':ln'=>$g('ninang_lastname')?:null
            ]);

            /* ===== RESET OLD DATA ===== */
            $dbh->prepare("DELETE FROM as_ninong_tbl WHERE confirmation_id = ?")->execute([$id]);
            $dbh->prepare("DELETE FROM as_ninang_tbl WHERE confirmation_id = ?")->execute([$id]);

            /* ===== INSERT NEW AS NINONG ===== */
            if (!empty($_POST['as_ninong_firstname'])) {
              $stmt = $dbh->prepare("
                INSERT INTO as_ninong_tbl 
                (confirmation_id, as_ninong_firstname, as_ninong_middlename, as_ninong_lastname, as_ninong_suffix)
                VALUES (?, ?, ?, ?, ?)
              ");

              foreach ($_POST['as_ninong_firstname'] as $i => $fn) {
                $stmt->execute([
                  $id,
                  trim($fn),
                  $_POST['as_ninong_middlename'][$i] ?? null,
                  $_POST['as_ninong_lastname'][$i] ?? null,
                  $_POST['as_ninong_suffix'][$i] ?? null
                ]);
              }
            }

            /* ===== INSERT NEW AS NINANG ===== */
            if (!empty($_POST['as_ninang_firstname'])) {
              $stmt = $dbh->prepare("
                INSERT INTO as_ninang_tbl 
                (confirmation_id, as_ninang_firstname, as_ninang_middlename, as_ninang_lastname)
                VALUES (?, ?, ?, ?)
              ");

              foreach ($_POST['as_ninang_firstname'] as $i => $fn) {
                $stmt->execute([
                  $id,
                  trim($fn),
                  $_POST['as_ninang_middlename'][$i] ?? null,
                  $_POST['as_ninang_lastname'][$i] ?? null
                ]);
              }
            }


            $dbh->commit();
            header("Location: s_confirmation.php?msg=updated");
            exit();

        } catch(PDOException $e){
            $dbh->rollBack();
            $msg = "Update failed: ".$e->getMessage();
        }
    }
}

/* ================= SAFE VALUE HELPER ================= */
function v($src,$k){ return htmlspecialchars($_POST[$k] ?? $src[$k] ?? ''); }
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Update confirmation Record</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet"/>
<link href='../../../stylesheets/private/sacrament_insert.css' rel='stylesheet'>
</head>

<body>

<div class="record-container">
  <h2>Update confirmation Record</h2>

  <?php if ($msg): ?>
    <div class="alert alert-warning"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <form method="post" action="s_confirmation_update.php?id=<?= $id ?>">
    <input type="hidden" name="id" value="<?= $id ?>">

    <!-- ================= RESERVATION ================= -->
    <div class="record-section">
      <h3>Reservation Details</h3>
      <div class="row record-inline">

        <div class="col-md-2 record-row">
          <span class="record-label">confirmation Type</span>
          <select name="confirmation_type" class="form-control">
            <option value="regular" <?= (v($confirmation,'confirmation_type') === 'regular') ? 'selected' : '' ?>>Regular</option>
            <option value="solo" <?= (v($confirmation,'confirmation_type') === 'solo') ? 'selected' : '' ?>>Solo</option>
          </select>
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Date</span>
          <input type="date" name="date" class="form-control" value="<?= v($confirmation,'date') ?>">
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Time</span>
          <input type="time" name="time" class="form-control" value="<?= v($confirmation,'time') ?>">
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Contact</span>
          <input type="text" name="contact" class="form-control" value="<?= v($confirmation,'contact') ?>">
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Email</span>
          <input type="email" name="email" class="form-control" value="<?= v($confirmation,'email') ?>">
        </div>

      </div>
    </div>

    <!-- ================= confirm ================= -->
    <div class="record-section">
      <h3>confirm Details</h3>

      <div class="row record-inline">

        <div class="col-md-3 record-row">
          <span class="record-label">Given Name</span>
          <input type="text" name="confirm_firstname" class="form-control" value="<?= v($confirmation,'confirm_firstname') ?>" required>
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Middle Name</span>
          <input type="text" name="confirm_middlename" class="form-control" value="<?= v($confirmation,'confirm_middlename') ?>">
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Last Name</span>
          <input type="text" name="confirm_lastname" class="form-control" value="<?= v($confirmation,'confirm_lastname') ?>" required>
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Suffix</span>
          <input type="text" name="confirm_suffix" class="form-control" value="<?= v($confirmation,'confirm_suffix') ?>">
        </div>

      </div>

      <div class="row record-inline" style="margin-top:10px;">

        <div class="col-md-2 record-row">
          <span class="record-label">Gender</span>
          <select name="confirm_gender" class="form-control">
            <option value="">--</option>
            <option value="Male" <?= (v($confirmation,'confirm_gender') === 'Male') ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= (v($confirmation,'confirm_gender') === 'Female') ? 'selected' : '' ?>>Female</option>
          </select>
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Birthdate</span>
          <input type="date" name="birth_date" class="form-control" value="<?= v($confirmation,'birth_date') ?>">
        </div>

        <div class="col-md-4 record-row">
          <span class="record-label">Birth Place</span>
          <input type="text" name="birth_place" class="form-control" value="<?= v($confirmation,'birth_place') ?>">
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Nationality</span>
          <input type="text" name="confirm_nat" class="form-control" value="<?= v($confirmation,'confirm_nat') ?>">
        </div>

      </div>
    </div>

    <!-- ================= FATHER ================= -->
    <div class="record-section">
      <h3>Father</h3>
      <div class="row record-inline">
        <div class="col-md-3 record-row"><span class="record-label">First</span><input name="father_firstname" class="form-control" value="<?= v($confirmation,'father_firstname') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Middle</span><input name="father_middlename" class="form-control" value="<?= v($confirmation,'father_middlename') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Last</span><input name="father_lastname" class="form-control" value="<?= v($confirmation,'father_lastname') ?>"></div>
        <div class="col-md-2 record-row"><span class="record-label">Suffix</span><input name="father_suffix" class="form-control" value="<?= v($confirmation,'father_suffix') ?>"></div>
      </div>
    </div>

    <!-- ================= MOTHER ================= -->
    <div class="record-section">
      <h3>Mother</h3>
      <div class="row record-inline">
        <div class="col-md-3 record-row"><span class="record-label">First</span><input name="mother_firstname" class="form-control" value="<?= v($confirmation,'mother_firstname') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Middle</span><input name="mother_middlename" class="form-control" value="<?= v($confirmation,'mother_middlename') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Last</span><input name="mother_lastname" class="form-control" value="<?= v($confirmation,'mother_lastname') ?>"></div>
      </div>
    </div>

    <!-- ================= MAIN NINONG / NINANG ================= -->
    <div class="record-section">
      <h3>Main Ninong / Ninang</h3>
      <div class="row record-inline">
        <div class="col-md-3 record-row"><span class="record-label">Ninong First</span><input name="ninong_firstname" class="form-control" value="<?= v($ninong,'ninong_firstname') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Ninong Middle</span><input name="ninong_middlename" class="form-control" value="<?= v($ninong,'ninong_middlename') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Ninong Last</span><input name="ninong_lastname" class="form-control" value="<?= v($ninong,'ninong_lastname') ?>"></div>
        <div class="col-md-2 record-row"><span class="record-label">Ninong Suffix</span><input name="ninong_suffix" class="form-control" value="<?= v($ninong,'ninong_suffix') ?>"></div>
      </div>

      <div class="row record-inline" style="margin-top:10px;">
        <div class="col-md-3 record-row"><span class="record-label">Ninang First</span><input name="ninang_firstname" class="form-control" value="<?= v($ninang,'ninang_firstname') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Ninang Middle</span><input name="ninang_middlename" class="form-control" value="<?= v($ninang,'ninang_middlename') ?>"></div>
        <div class="col-md-3 record-row"><span class="record-label">Ninang Last</span><input name="ninang_lastname" class="form-control" value="<?= v($ninang,'ninang_lastname') ?>"></div>
      </div>
    </div>

    <!-- ================= ADDITIONAL ================= -->
    <div class="record-section">
      <h3>Additional (As) Ninong / Ninang</h3>

      <!-- ===== AS NINONG ===== -->
      <h4>Additional Ninong</h4>
      <div id="asNinongWrapper">
        <?php if (!empty($asNinong)): ?>
          <?php foreach ($asNinong as $n): ?>
            <div class="row record-inline sponsor-row">
              <div class="col-md-3"><input name="as_ninong_firstname[]" class="form-control" value="<?= htmlspecialchars($n['as_ninong_firstname']) ?>" placeholder="First"></div>
              <div class="col-md-2"><input name="as_ninong_middlename[]" class="form-control" value="<?= htmlspecialchars($n['as_ninong_middlename']) ?>" placeholder="Middle"></div>
              <div class="col-md-3"><input name="as_ninong_lastname[]"  class="form-control" value="<?= htmlspecialchars($n['as_ninong_lastname']) ?>" placeholder="Last"></div>
              <div class="col-md-1"><input name="as_ninong_suffix[]" class="form-control" value="<?= htmlspecialchars($n['as_ninong_suffix']) ?>" placeholder="Suffix"></div>
              <div class="col-md-1"><button type="button" class="btn btn-danger btn-remove">X</button></div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      
      <br>
      <button type="button" id="addAsNinong" class="btn btn-success btn-sm">+ Add Ninong</button>

      <hr>

      <!-- ===== AS NINANG ===== -->
      <h4>Additional Ninang</h4>
      <div id="asNinangWrapper">
        <?php if (!empty($asNinang)): ?>
          <?php foreach ($asNinang as $n): ?>
            <div class="row record-inline sponsor-row">
              <div class="col-md-3"><input name="as_ninang_firstname[]" class="form-control" value="<?= htmlspecialchars($n['as_ninang_firstname']) ?>" placeholder="First"></div>
              <div class="col-md-2"><input name="as_ninang_middlename[]" class="form-control" value="<?= htmlspecialchars($n['as_ninang_middlename']) ?>" placeholder="Middle"></div>
              <div class="col-md-3"><input name="as_ninang_lastname[]" class="form-control" value="<?= htmlspecialchars($n['as_ninang_lastname']) ?>" placeholder="Last"></div>
              <div class="col-md-1"><button type="button" class="btn btn-danger btn-remove">X</button></div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <br>
      <button type="button" id="addAsNinang" class="btn btn-success btn-sm">+ Add Ninang</button>

    </div>


    <!-- ================= BUTTONS ================= -->
    <div class="record-buttons">
      <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
      <a href="s_confirmation.php" class="btn btn-default">Cancel</a>
    </div>

  </form>
</div>


<script>
function bindRemoveButtons() {
  document.querySelectorAll('.btn-remove').forEach(btn => {
    btn.onclick = () => btn.closest('.sponsor-row').remove();
  });
}
bindRemoveButtons();

document.getElementById('addAsNinong').onclick = () => {
  document.getElementById('asNinongWrapper').insertAdjacentHTML('beforeend', `
    <div class="row record-inline sponsor-row">
      <div class="col-md-3"><input name="as_ninong_firstname[]" class="form-control" placeholder="First"></div>
      <div class="col-md-2"><input name="as_ninong_middlename[]" class="form-control" placeholder="Middle"></div>
      <div class="col-md-3"><input name="as_ninong_lastname[]"  class="form-control" placeholder="Last"></div>
      <div class="col-md-1"><input name="as_ninong_suffix[]" class="form-control" placeholder="Suffix"></div>
      <div class="col-md-1"><button type="button" class="btn btn-danger btn-remove">X</button></div>
    </div>
  `);
  bindRemoveButtons();
};

document.getElementById('addAsNinang').onclick = () => {
  document.getElementById('asNinangWrapper').insertAdjacentHTML('beforeend', `
    <div class="row record-inline sponsor-row">
      <div class="col-md-3"><input name="as_ninang_firstname[]" class="form-control" placeholder="First"></div>
      <div class="col-md-2"><input name="as_ninang_middlename[]" class="form-control" placeholder="Middle"></div>
      <div class="col-md-3"><input name="as_ninang_lastname[]" class="form-control" placeholder="Last"></div>
      <div class="col-md-1"><button type="button" class="btn btn-danger btn-remove">X</button></div>
    </div>
  `);
  bindRemoveButtons();
};
</script>

</body>
</html>
