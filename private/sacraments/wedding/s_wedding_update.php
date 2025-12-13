<?php
require_once '../connection.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: s_wedding.php");
    exit();
}

$id = intval($_GET['id']);
$msg = "";

/* ================= FETCH ALL RECORDS ================= */

try {
    // wedding
    $stmt = $dbh->prepare("SELECT * FROM weddings WHERE id=:id");
    $stmt->execute([':id'=>$id]);
    $wedding = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$wedding) die("Record not found");

    // MAIN NINONG
    $stmt = $dbh->prepare("SELECT * FROM ninong_tbl WHERE wedding_id=:id");
    $stmt->execute([':id'=>$id]);
    $ninong = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    // MAIN NINANG
    $stmt = $dbh->prepare("SELECT * FROM ninang_tbl WHERE wedding_id=:id");
    $stmt->execute([':id'=>$id]);
    $ninang = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    // ADDITIONAL
$stmt = $dbh->prepare("SELECT * FROM as_ninong_tbl WHERE wedding_id = :id");
$stmt->execute([':id' => $id]);
$asNinong = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $dbh->prepare("SELECT * FROM as_ninang_tbl WHERE wedding_id = :id");
$stmt->execute([':id' => $id]);
$asNinang = $stmt->fetchAll(PDO::FETCH_ASSOC);



} catch(PDOException $e){
    die("DB Error: ".$e->getMessage());
}

/* ================= UPDATE ================= */

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update'])) {

    $g = fn($k)=>trim($_POST[$k]??'');

    $bride_firstname  = $g('bride_firstname');
    $bride_lastname   = $g('bride_lastname');

    if ($bride_firstname === '' || $bride_lastname === '') {
        $msg = "First and Last name required.";
    } else {
        try {
            $dbh->beginTransaction();

            /* === UPDATE WEDDING === */
            $dbh->prepare("
                UPDATE weddings SET
                    date = :date,
                    time = :time,

                    bride_firstname = :bf,
                    bride_middlename = :bm,
                    bride_lastname = :bl,
                    bride_birthdate = :bbd,
                    bride_nationality = :bn,
                    bride_contact = :bc,
                    bride_email = :be,

                    groom_firstname = :gf,
                    groom_middlename = :gm,
                    groom_lastname = :gl,
                    groom_suffix = :gs,
                    groom_birthdate = :gbd,
                    groom_nationality = :gn,
                    groom_contact = :gc,
                    groom_email = :ge

                WHERE id = :id
            ")->execute([
                ':date' => $g('date') ?: null,
                ':time' => $g('time') ?: null,

                // Bride
                ':bf'  => $g('bride_firstname') ?: null,
                ':bm'  => $g('bride_middlename') ?: null,
                ':bl'  => $g('bride_lastname') ?: null,
                ':bbd' => $g('bride_birthdate') ?: null,
                ':bn'  => $g('bride_nationality') ?: null,
                ':bc'  => $g('bride_contact') ?: null,
                ':be'  => $g('bride_email') ?: null,

                // Groom
                ':gf'  => $g('groom_firstname') ?: null,
                ':gm'  => $g('groom_middlename') ?: null,
                ':gl'  => $g('groom_lastname') ?: null,
                ':gs'  => $g('groom_suffix') ?: null,
                ':gbd' => $g('groom_birthdate') ?: null,
                ':gn'  => $g('groom_nationality') ?: null,
                ':gc'  => $g('groom_contact') ?: null,
                ':ge'  => $g('groom_email') ?: null,

                ':id' => $id
            ]);


/* === SAVE MAIN NINONG (NO DUPLICATE KEY REQUIRED) === */
$chk = $dbh->prepare("SELECT wedding_id FROM ninong_tbl WHERE wedding_id = ?");
$chk->execute([$id]);

if ($chk->rowCount() > 0) {
    // UPDATE
    $dbh->prepare("
        UPDATE ninong_tbl SET
            ninong_firstname = ?,
            ninong_middlename = ?,
            ninong_lastname = ?,
            ninong_suffix = ?
        WHERE wedding_id = ?
    ")->execute([
        $g('ninong_firstname'),
        $g('ninong_middlename') ?: null,
        $g('ninong_lastname'),
        $g('ninong_suffix') ?: null,
        $id
    ]);
} else {
    // INSERT
    $dbh->prepare("
        INSERT INTO ninong_tbl
        (wedding_id, ninong_firstname, ninong_middlename, ninong_lastname, ninong_suffix)
        VALUES (?, ?, ?, ?, ?)
    ")->execute([
        $id,
        $g('ninong_firstname'),
        $g('ninong_middlename') ?: null,
        $g('ninong_lastname'),
        $g('ninong_suffix') ?: null
    ]);
}


/* === SAVE MAIN NINANG (NO DUPLICATE KEY REQUIRED) === */
$chk = $dbh->prepare("SELECT wedding_id FROM ninang_tbl WHERE wedding_id = ?");
$chk->execute([$id]);

if ($chk->rowCount() > 0) {
    // UPDATE
    $dbh->prepare("
        UPDATE ninang_tbl SET
            ninang_firstname = ?,
            ninang_middlename = ?,
            ninang_lastname = ?
        WHERE wedding_id = ?
    ")->execute([
        $g('ninang_firstname'),
        $g('ninang_middlename') ?: null,
        $g('ninang_lastname'),
        $id
    ]);
} else {
    // INSERT
    $dbh->prepare("
        INSERT INTO ninang_tbl
        (wedding_id, ninang_firstname, ninang_middlename, ninang_lastname)
        VALUES (?, ?, ?, ?)
    ")->execute([
        $id,
        $g('ninang_firstname'),
        $g('ninang_middlename') ?: null,
        $g('ninang_lastname')
    ]);
}


            /* ===== RESET OLD DATA ===== */
            $dbh->prepare("DELETE FROM as_ninong_tbl WHERE wedding_id = ?")->execute([$id]);
            $dbh->prepare("DELETE FROM as_ninang_tbl WHERE wedding_id = ?")->execute([$id]);

            /* ===== INSERT NEW AS NINONG ===== */
            if (!empty($_POST['as_ninong_firstname'])) {
              $stmt = $dbh->prepare("
                INSERT INTO as_ninong_tbl 
                (wedding_id, as_ninong_firstname, as_ninong_middlename, as_ninong_lastname, as_ninong_suffix)
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
                (wedding_id, as_ninang_firstname, as_ninang_middlename, as_ninang_lastname)
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
            header("Location: s_wedding.php?msg=updated");
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
<title>Update wedding Record</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet"/>
<link href='../../../stylesheets/private/sacrament_insert.css' rel='stylesheet'>
</head>

<body>

<div class="record-container">
  <h2>Update Wedding Record</h2>

  <?php if ($msg): ?>
    <div class="alert alert-warning"><?= htmlspecialchars($msg) ?></div>
  <?php endif; ?>

  <form method="post" action="s_wedding_update.php?id=<?= $id ?>">
    <input type="hidden" name="id" value="<?= $id ?>">

    <!-- ================= RESERVATION ================= -->
    <div class="record-section">
      <h3>Reservation Details</h3>
      <div class="row record-inline">

        <div class="col-md-2 record-row">
          <span class="record-label">Wedding Type</span>
          <select name="wedding_type" class="form-control">
            <option value="regular" <?= (v($wedding,'wedding_type') === 'regular') ? 'selected' : '' ?>>Regular</option>
            <option value="solo" <?= (v($wedding,'wedding_type') === 'solo') ? 'selected' : '' ?>>Solo</option>
          </select>
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Date</span>
          <input type="date" name="date" class="form-control" value="<?= v($wedding,'date') ?>">
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Time</span>
          <input type="time" name="time" class="form-control" value="<?= v($wedding,'time') ?>">
        </div>

      </div>
    </div>

        <!-- ================= Bride ================= -->
    <div class="record-section">
      <h3>Bride Details</h3>

      <div class="row record-inline">

        <div class="col-md-3 record-row">
          <span class="record-label">Given Name</span>
          <input type="text" name="bride_firstname" class="form-control" value="<?= v($wedding,'bride_firstname') ?>" required>
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Middle Name</span>
          <input type="text" name="bride_middlename" class="form-control" value="<?= v($wedding,'bride_middlename') ?>">
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Last Name</span>
          <input type="text" name="bride_lastname" class="form-control" value="<?= v($wedding,'bride_lastname') ?>" required>
        </div>
        
      </div>

      <div class="row record-inline" style="margin-top:10px;">

        <div class="col-md-2 record-row">
          <span class="record-label">Birthdate</span>
          <input type="date" name="bride_birthdate" class="form-control" value="<?= v($wedding,'bride_birthdate') ?>">
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Nationality</span>
          <input type="text" name="bride_nationality" class="form-control" value="<?= v($wedding,'bride_nationality') ?>">
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Contact</span>
          <input type="text" name="bride_contact" class="form-control" value="<?= v($wedding,'bride_contact') ?>">
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Email</span>
          <input type="text" name="bride_email" class="form-control" value="<?= v($wedding,'bride_email') ?>">
        </div>
      </div>
    </div>

    <!-- ================= Groom ================= -->
    <div class="record-section">
      <h3>Groom Details</h3>

      <div class="row record-inline">

        <div class="col-md-3 record-row">
          <span class="record-label">Given Name</span>
          <input type="text" name="groom_firstname" class="form-control" value="<?= v($wedding,'groom_firstname') ?>" required>
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Middle Name</span>
          <input type="text" name="groom_middlename" class="form-control" value="<?= v($wedding,'groom_middlename') ?>">
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Last Name</span>
          <input type="text" name="groom_lastname" class="form-control" value="<?= v($wedding,'groom_lastname') ?>" required>
        </div>

        <div class="col-md-2 record-row">
          <span class="record-label">Suffix</span>
          <input type="text" name="groom_suffix" class="form-control" value="<?= v($wedding,'groom_suffix') ?>">
        </div>

      </div>

      <div class="row record-inline" style="margin-top:10px;">

        <div class="col-md-2 record-row">
          <span class="record-label">Birthdate</span>
          <input type="date" name="groom_birthdate" class="form-control" value="<?= v($wedding,'groom_birthdate') ?>">
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Nationality</span>
          <input type="text" name="groom_nationality" class="form-control" value="<?= v($wedding,'groom_nationality') ?>">
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Contact</span>
          <input type="text" name="groom_contact" class="form-control" value="<?= v($wedding,'groom_contact') ?>">
        </div>

        <div class="col-md-3 record-row">
          <span class="record-label">Email</span>
          <input type="text" name="groom_email" class="form-control" value="<?= v($wedding,'groom_email') ?>">
        </div>
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
      <a href="s_wedding.php" class="btn btn-default">Cancel</a>
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
