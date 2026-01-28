<?php
require_once '../connection.php';
session_start();

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        $dbh->beginTransaction();

        // -------------------------------------------
        // COLLECT FORM DATA
        // -------------------------------------------
        $date = $_POST['date'];
        $time = $_POST['time'];

        $child_firstname = $_POST['child_firstname'];
        $child_middlename = $_POST['child_middlename'];
        $child_lastname = $_POST['child_lastname'];
        $child_suffix = $_POST['child_suffix'];
        $birth_date = $_POST['birth_date'];
        $birth_place = $_POST['birth_place'];

        $father_firstname = $_POST['father_firstname'];
        $father_middlename = $_POST['father_middlename'];
        $father_lastname = $_POST['father_lastname'];
        $father_suffix = $_POST['father_suffix'];

        $mother_firstname = $_POST['mother_firstname'];
        $mother_middlename = $_POST['mother_middlename'];
        $mother_lastname = $_POST['mother_lastname'];

        $contact = $_POST['contact'];
        $email = $_POST['email'];

        // -------------------------------------------
        // INSERT INTO first_communion
        // -------------------------------------------
        $sql1 = "INSERT INTO first_communion (
            first_comm_type, date, time,
            child_firstname, child_middlename, child_lastname, child_suffix,
            birth_date, birth_place,
            father_firstname, father_middlename, father_lastname, father_suffix,
            mother_firstname, mother_middlename, mother_lastname,
            contact, email
        ) VALUES (
            :first_comm_type, :date, :time,
            :child_firstname, :child_middlename, :child_lastname, :child_suffix,
            :birth_date, :birth_place,
            :father_firstname, :father_middlename, :father_lastname, :father_suffix,
            :mother_firstname, :mother_middlename, :mother_lastname,
            :contact, :email
        )";

        $stmt1 = $dbh->prepare($sql1);
        $stmt1->execute([
            ':first_comm_type' => $first_comm_type,
            ':date' => $date,
            ':time' => $time,

            ':child_firstname' => $child_firstname,
            ':child_middlename' => $child_middlename,
            ':child_lastname' => $child_lastname,
            ':child_suffix' => $child_suffix,
            ':birth_date' => $birth_date,
            ':birth_place' => $birth_place,

            ':father_firstname' => $father_firstname,
            ':father_middlename' => $father_middlename,
            ':father_lastname' => $father_lastname,
            ':father_suffix' => $father_suffix,

            ':mother_firstname' => $mother_firstname,
            ':mother_middlename' => $mother_middlename,
            ':mother_lastname' => $mother_lastname,

            ':contact' => $contact,
            ':email' => $email
        ]);

        $first_comm_id = $dbh->lastInsertId();

        $dbh->commit();
        $message = "✅ First Communion reservation saved successfully!";

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
<link href='../../../stylesheets/private/sacrament_insert.css' rel='stylesheet'>
<title>Add First Communion Reservation</title>
</head>

<body>

<!--------------------------------------- MAIN --------------------------------------------->
<div class="main-content">

  <div class="record-container">
    <h2>Add First Communion Reservation</h2>

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
            <span class="record-label">First Communion Type</span>
            <select name="first_comm_type" class="form-control" required>
              <option value="" disabled selected></option>
              <option value="regular">Regular</option>
              <option value="solo">Solo</option>
            </select>
          </div>

          <div class="col-md-2 record-row">
            <span class="record-label">First Communion Date</span>
            <input type="date" name="date" class="form-control" required>
          </div>

          <div class="col-md-2 record-row">
            <span class="record-label">First Communion Time</span>
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
        <h3>Communion Candidate Details</h3>

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
          <div class="col-md-3 record-row">
            <span class="record-label">Birthday</span>
            <input type="date" name="birth_date" class="form-control" required>
          </div>

          <div class="col-md-4 record-row">
            <span class="record-label">Birth Place</span>
            <input type="text" name="birth_place" class="form-control" required>
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
      </div>

      <!-- Documents -->
      <div class="record-section">
          <h3>Documents</h3>
          <div class="row record-inline">
              <div class="col-md-3 record-row">
                  <span class="record-label">Birth Certificate</span>
                  <input type="file" name="birth_certificate" class="form-control" required>
              </div>
              <div class="col-md-3 record-row">
                  <span class="record-label">Baptismal Certificate</span>
                  <input type="file" name="baptismal_certificate" class="form-control">
              </div>
          </div>
      </div>

      <!-- ================= BUTTONS ================= -->
      <div class="record-buttons">
        <button type="submit" class="btn btn-primary">Add Reservation</button>
        <a href="s_first_comm.php" class="btn btn-danger">Back</a>
      </div>

    </form>
  </div>
</div>

</body>
</html>