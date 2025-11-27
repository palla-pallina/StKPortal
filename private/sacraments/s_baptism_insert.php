<?php
require_once 'connection.php';
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        $dbh->beginTransaction();

        // -------------------------------------------
        // COLLECT FORM DATA
        // -------------------------------------------
        $baptism_type = $_POST['baptism_type'];
        $date = $_POST['date'];
        $time = $_POST['time'];

        $child_firstname = $_POST['child_firstname'];
        $child_middlename = $_POST['child_middlename'];
        $child_lastname = $_POST['child_lastname'];
        $child_suffix = $_POST['child_suffix'];
        $child_gender = $_POST['child_gender'];
        $birth_date = $_POST['birth_date'];
        $birth_place = $_POST['birth_place'];
        $child_nat = $_POST['child_nat'];

        $father_firstname = $_POST['father_firstname'];
        $father_middlename = $_POST['father_middlename'];
        $father_lastname = $_POST['father_lastname'];
        $father_suffix = $_POST['father_suffix'];

        $mother_firstname = $_POST['mother_firstname'];
        $mother_middlename = $_POST['mother_middlename'];
        $mother_lastname = $_POST['mother_lastname'];
        $marriage_type = $_POST['marriage_type'];

        $address = $_POST['address'];
        $contact = $_POST['contact'];
        $email = $_POST['email'];

        // Ninong
        $ninong_firstname = $_POST['ninong_firstname'];
        $ninong_middlename = $_POST['ninong_middlename'];
        $ninong_lastname = $_POST['ninong_lastname'];
        $ninong_suffix = $_POST['ninong_suffix'];

        // Ninang
        $ninang_firstname = $_POST['ninang_firstname'];
        $ninang_middlename = $_POST['ninang_middlename'];
        $ninang_lastname = $_POST['ninang_lastname'];

        // Ninong
        $ninong_firstname = $_POST['as_ninong_firstname'];
        $ninong_middlename = $_POST['as_ninong_middlename'];
        $ninong_lastname = $_POST['as_ninong_lastname'];
        $ninong_suffix = $_POST['as_ninong_suffix'];

        // Ninang
        $ninang_firstname = $_POST['as_ninang_firstname'];
        $ninang_middlename = $_POST['as_ninang_middlename'];
        $ninang_lastname = $_POST['as_ninang_lastname'];

        // -------------------------------------------
        // INSERT INTO BAPTISMS
        // -------------------------------------------
        $sql1 = "INSERT INTO baptisms (
            baptism_type, date, time,
            child_firstname, child_middlename, child_lastname, child_suffix,
            child_gender, birth_date, birth_place, child_nat,
            father_firstname, father_middlename, father_lastname, father_suffix,
            mother_firstname, mother_middlename, mother_lastname, marriage_type,
            address, contact, email,
            ninong_firstname, ninong_middlename, ninong_lastname, ninong_suffix, 
            ninang_firstname, ninang_middlename, ninang_lastname,
        ) VALUES (
            :baptism_type, :date, :time,
            :child_firstname, :child_middlename, :child_lastname, :child_suffix,
            :child_gender, :birth_date, :birth_place, :child_nat,
            :father_firstname, :father_middlename, :father_lastname, :father_suffix,
            :mother_firstname, :mother_middlename, :mother_lastname, :marriage_type,
            :address, :contact, :email,
            :ninong_firstname, :ninong_middlename, :ninong_lastname, :ninong_suffix, 
            :ninang_firstname, :ninang_middlename, :ninang_lastname,
        )";

        $stmt1 = $dbh->prepare($sql1);
        $stmt1->execute([
            ':baptism_type' => $baptism_type,
            ':date' => $date,
            ':time' => $time,

            ':child_firstname' => $child_firstname,
            ':child_middlename' => $child_middlename,
            ':child_lastname' => $child_lastname,
            ':child_suffix' => $child_suffix,
            ':child_gender' => $child_gender,
            ':birth_date' => $birth_date,
            ':birth_place' => $birth_place,
            ':child_nat' => $child_nat,

            ':father_firstname' => $father_firstname,
            ':father_middlename' => $father_middlename,
            ':father_lastname' => $father_lastname,
            ':father_suffix' => $father_suffix,

            ':mother_firstname' => $mother_firstname,
            ':mother_middlename' => $mother_middlename,
            ':mother_lastname' => $mother_lastname,
            ':marriage_type' => $marriage_type,

            ':address' => $address,
            ':contact' => $contact,
            ':email' => $email,

            ':ninong_firstname' => $ninong_firstname,
            ':ninong_middlename' => $ninong_middlename,
            ':ninong_lastname' => $ninong_lastname,
            ':ninong_suffix' => $ninong_suffix,

            ':ninang_firstname' => $ninang_firstname,
            ':ninang_middlename' => $ninang_middlename,
            ':ninang_lastname' => $ninang_lastname
        ]);

        $baptism_id = $dbh->lastInsertId();

        // -------------------------------------------
        // INSERT INTO NINONG
        // -------------------------------------------
        $sql2 = "INSERT INTO as_ninong_tbl (
            baptism_id, as_ninong_firstname, as_ninong_middlename, as_ninong_lastname, as_ninong_suffix
        ) VALUES (
            :baptism_id, :as_ninong_firstname, :as_ninong_middlename, :as_ninong_lastname, :as_ninong_suffix
        )";

        $stmt2 = $dbh->prepare($sql2);
        $stmt2->execute([
            ':baptism_id' => $baptism_id,
            ':as_ninong_firstname' => $as_ninong_firstname,
            ':as_ninong_middlename' => $as_ninong_middlename,
            ':as_ninong_lastname' => $as_ninong_lastname,
            ':as_ninong_suffix' => $as_ninong_suffix
        ]);

        // -------------------------------------------
        // INSERT INTO NINANG
        // -------------------------------------------
        $sql3 = "INSERT INTO as_ninang_tbl (
            baptism_id, as_ninang_firstname, as_ninang_middlename, as_ninang_lastname
        ) VALUES (
            :baptism_id, :as_ninang_firstname, :as_ninang_middlename, :as_ninang_lastname
        )";
        $stmt3 = $dbh->prepare($sql3);
        $stmt3->execute([
            ':baptism_id' => $baptism_id,
            ':as_ninang_firstname' => $as_ninang_firstname,
            ':as_ninang_middlename' => $as_ninang_middlename,
            ':as_ninang_lastname' => $as_ninang_lastname
        ]);

        $dbh->commit();
        $message = "✅ Baptism reservation saved successfully!";

    } catch (PDOException $e) {
        $dbh->rollBack();
        $message = "❌ DB Error: " . $e->getMessage();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />
<link href='../../stylesheets/private/s_sacrament_record.css' rel='stylesheet'>
<title>Add Baptism Reservation</title>
</head>

<body>

<!--------------------------------------- MAIN --------------------------------------------->
<div class="main-content">

  <div class="record-container">
    <h2>Add Baptism Reservation</h2>

    <?php if (!empty($message)) : ?>
      <div class="alert alert-info">
        <?= htmlspecialchars($message); ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">

      <!-- ================= RESERVATION DETAILS ================= -->
      <div class="record-section">
        <h3>Reservation Details</h3>

        <div class="row record-inline">
          <div class="col-md-2 record-row">
            <span class="record-label">Baptism Type</span>
            <select name="baptism_type" class="form-control" required>
              <option value="" disabled selected></option>
              <option value="regular">Regular</option>
              <option value="solo">Solo</option>
            </select>
          </div>

          <div class="col-md-2 record-row">
            <span class="record-label">Baptism Date</span>
            <input type="date" name="date" class="form-control" required>
          </div>

          <div class="col-md-2 record-row">
            <span class="record-label">Baptism Time</span>
            <input type="time" name="time" class="form-control" required>
          </div>

          <div class="col-md-2 record-row">
            <span class="record-label">Contact Number</span>
            <input type="text" name="contact" class="form-control" required>
          </div>

          <div class="col-md-2 record-row">
            <span class="record-label">Email Address</span>
            <input type="email" name="email" class="form-control" required>
          </div>
        </div>
      </div>

      <!-- ================= CHILD DETAILS ================= -->
      <div class="record-section">
        <h3>Child Details</h3>

        <div class="row record-inline">
          <div class="col-md-3 record-row">
            <span class="record-label">Given Name</span>
            <input type="text" name="child_firstname" class="form-control" required>
          </div>

          <div class="col-md-3 record-row">
            <span class="record-label">Middle Name</span>
            <input type="text" name="child_middlename" class="form-control">
          </div>

          <div class="col-md-3 record-row">
            <span class="record-label">Last Name</span>
            <input type="text" name="child_lastname" class="form-control" required>
          </div>

          <div class="col-md-2 record-row">
            <span class="record-label">Suffix</span>
            <input type="text" name="child_suffix" class="form-control">
          </div>
        </div>

        <div class="row record-inline" style="margin-top:10px;">
          <div class="col-md-2 record-row">
            <span class="record-label">Gender</span>
            <select name="gender" class="form-control" required>
              <option value="" disabled selected>Sex</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>

          <div class="col-md-3 record-row">
            <span class="record-label">Birthday</span>
            <input type="date" name="birth_date" class="form-control" required>
          </div>

          <div class="col-md-4 record-row">
            <span class="record-label">Birth Place</span>
            <input type="text" name="birth_place" class="form-control" required>
          </div>

          <div class="col-md-2 record-row">
            <span class="record-label">Nationality</span>
            <input type="text" name="child_nat" class="form-control" required>
          </div>
        </div>
      </div>

      <!-- ================= FATHER ================= -->
      <div class="record-section">
        <h3>Father Details</h3>

        <div class="row record-inline">
          <div class="col-md-3 record-row">
            <span class="record-label">Given Name</span>
            <input type="text" name="father_firstname" class="form-control" required>
          </div>

          <div class="col-md-3 record-row">
            <span class="record-label">Middle Name</span>
            <input type="text" name="father_middlename" class="form-control">
          </div>

          <div class="col-md-3 record-row">
            <span class="record-label">Last Name</span>
            <input type="text" name="father_lastname" class="form-control" required>
          </div>

          <div class="col-md-2 record-row">
            <span class="record-label">Suffix</span>
            <input type="text" name="father_suffix" class="form-control">
          </div>
        </div>
      </div>

      <!-- ================= MOTHER ================= -->
      <div class="record-section">
        <h3>Mother Details</h3>

        <div class="row record-inline">
          <div class="col-md-3 record-row">
            <span class="record-label">Given Name</span>
            <input type="text" name="mother_firstname" class="form-control" required>
          </div>

          <div class="col-md-3 record-row">
            <span class="record-label">Middle Name</span>
            <input type="text" name="mother_middlename" class="form-control">
          </div>

          <div class="col-md-3 record-row">
            <span class="record-label">Last Name</span>
            <input type="text" name="mother_lastname" class="form-control" required>
          </div>
        </div>

        <div class="row record-inline" style="margin-top:10px;">
          <div class="col-md-2 record-row">
            <span class="record-label">Marriage Type</span>
            <select name="marriage_type" class="form-control">
              <option value="" disabled selected></option>
              <option value="Single">Single</option>
              <option value="Church Marriage">Church Marriage</option>
              <option value="Civil Marriage">Civil Marriage</option>
            </select>
          </div>

          <div class="col-md-5 record-row">
            <span class="record-label">Address</span>
            <input type="text" name="address" class="form-control">
          </div>
        </div>
      </div>

      <!-- ================= MAIN NINONG ================= -->
      <div class="record-section">
        <h3>Pangunahing Ninong</h3>

        <div class="row record-inline">
          <div class="col-md-3 record-row"><span class="record-label">Given Name</span><input type="text" name="ninong_firstname" class="form-control" required></div>
          <div class="col-md-3 record-row"><span class="record-label">Middle Name</span><input type="text" name="ninong_middlename" class="form-control"></div>
          <div class="col-md-3 record-row"><span class="record-label">Last Name</span><input type="text" name="ninong_lastname" class="form-control" required></div>
          <div class="col-md-3 record-row"><span class="record-label">Suffix</span><input type="text" name="ninong_suffix" class="form-control"></div>
        </div>
      </div>

      <!-- ================= MAIN NINANG ================= -->
      <div class="record-section">
        <h3>Pangunahing Ninang</h3>

        <div class="row record-inline">
          <div class="col-md-3 record-row"><span class="record-label">Given Name</span><input type="text" name="ninang_firstname" class="form-control" required></div>
          <div class="col-md-3 record-row"><span class="record-label">Middle Name</span><input type="text" name="ninang_middlename" class="form-control"></div>
          <div class="col-md-3 record-row"><span class="record-label">Last Name</span><input type="text" name="ninang_lastname" class="form-control" required></div>
        </div>
      </div>

      <!-- ================= ADDITIONAL ================= -->
      <div class="record-section">
        <h3>Ninong</h3>

        <div class="row record-inline">
          <div class="col-md-3 record-row"><span class="record-label">Given Name</span><input type="text" name="as_ninong_firstname" class="form-control" required></div>
          <div class="col-md-3 record-row"><span class="record-label">Middle Name</span><input type="text" name="as_ninong_middlename" class="form-control"></div>
          <div class="col-md-3 record-row"><span class="record-label">Last Name</span><input type="text" name="as_ninong_lastname" class="form-control" required></div>
          <div class="col-md-2 record-row"><span class="record-label">Suffix</span><input type="text" name="as_ninong_suffix" class="form-control"></div>
        </div>

        <h3 style="margin-top:15px;">Ninang</h3>
        <div class="row record-inline">
          <div class="col-md-3 record-row"><span class="record-label">Given Name</span><input type="text" name="as_ninang_firstname" class="form-control" required></div>
          <div class="col-md-3 record-row"><span class="record-label">Middle Name</span><input type="text" name="as_ninang_middlename" class="form-control"></div>
          <div class="col-md-2 record-row"><span class="record-label">Last Name</span><input type="text" name="as_ninang_lastname" class="form-control" required></div>
        </div>
      </div>

      <!-- ================= BUTTONS ================= -->
      <div class="record-buttons">
        <button type="submit" class="btn btn-primary">Add Reservation</button>
        <a href="s_baptism.php" class="btn btn-danger">Back</a>
      </div>

    </form>
  </div>
</div>

</body>
</html>