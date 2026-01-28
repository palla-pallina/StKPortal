<?php
require_once '../connection.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $dbh->beginTransaction();

        $g = fn($k) => trim($_POST[$k] ?? '');

        // Required fields (based on schema)
        $date           = $g('date');
        $time           = $g('time');

        $sick_firstname = $g('sick_firstname');
        $sick_lastname  = $g('sick_lastname');
        $age            = $g('age');
        $sick_condition = $g('sick_condition');

        $contact_fn     = $g('contact_person_firstname');
        $contact_ln     = $g('contact_person_lastname');
        $contact_relation = $g('contact_relation');
        $contact_number = $g('contact_number');
        $contact_email  = $g('contact_email');

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
            throw new Exception("Please fill in all required fields. Age must be a number.");
        }

        $stmt = $dbh->prepare("
            INSERT INTO anointing_of_sick_requests
            (
              reservation_user_id,
              status,
              date, time,
              venue, province, city, street_no, barangay, hospital_name,

              sick_firstname, sick_middlename, sick_lastname, sick_suffix,
              contact_person_firstname, contact_person_middlename, contact_person_lastname, contact_person_suffix,

              age, sick_condition,
              contact_relation, contact_number, contact_email
            )
            VALUES
            (
              :reservation_user_id,
              'Pending',
              :date, :time,
              :venue, :province, :city, :street_no, :barangay, :hospital_name,

              :sick_firstname, :sick_middlename, :sick_lastname, :sick_suffix,
              :contact_person_firstname, :contact_person_middlename, :contact_person_lastname, :contact_person_suffix,

              :age, :sick_condition,
              :contact_relation, :contact_number, :contact_email
            )
        ");

        $stmt->execute([
            ':reservation_user_id' => $_SESSION['user_id'] ?? null,

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
        ]);

        $dbh->commit();
        header("Location: s_anointing.php?success=1"); // change if listing page differs
        exit();

    } catch (Exception $e) {
        if ($dbh->inTransaction()) $dbh->rollBack();
        $msg = "Insert Failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<link href="../../../stylesheets/private/sacrament_insert.css" rel="stylesheet">
<title>Create Anointing of the Sick</title>
</head>
<body>

<div class="record-container">
    <h2>Create New Anointing of the Sick Request</h2>

    <?php if (!empty($msg)): ?>
        <div class="alert alert-warning"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form method="POST">

        <!-- Reservation Details -->
        <div class="record-section">
            <h3>Reservation Details</h3>
            <div class="row record-inline">
                <div class="col-md-3 record-row">
                    <span class="record-label">Date</span>
                    <input type="date" name="date" class="form-control" required>
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Time</span>
                    <input type="time" name="time" class="form-control" required>
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Venue</span>
                    <input type="text" name="venue" class="form-control" placeholder="Home / Hospital / Church...">
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Hospital Name (if applicable)</span>
                    <input type="text" name="hospital_name" class="form-control">
                </div>
            </div>

            <div class="row record-inline" style="margin-top:10px;">
                <div class="col-md-3 record-row"><span class="record-label">Province</span><input type="text" name="province" class="form-control"></div>
                <div class="col-md-3 record-row"><span class="record-label">City</span><input type="text" name="city" class="form-control"></div>
                <div class="col-md-3 record-row"><span class="record-label">Street No.</span><input type="text" name="street_no" class="form-control"></div>
                <div class="col-md-3 record-row"><span class="record-label">Barangay</span><input type="text" name="barangay" class="form-control"></div>
            </div>
        </div>

        <!-- Sick Person -->
        <div class="record-section">
            <h3>Sick Person Details</h3>
            <div class="row record-inline">
                <div class="col-md-3 record-row"><span class="record-label">First Name</span><input type="text" name="sick_firstname" class="form-control" required></div>
                <div class="col-md-3 record-row"><span class="record-label">Middle Name</span><input type="text" name="sick_middlename" class="form-control"></div>
                <div class="col-md-3 record-row"><span class="record-label">Last Name</span><input type="text" name="sick_lastname" class="form-control" required></div>
                <div class="col-md-3 record-row"><span class="record-label">Suffix</span><input type="text" name="sick_suffix" class="form-control"></div>
            </div>

            <div class="row record-inline" style="margin-top:10px;">
                <div class="col-md-2 record-row"><span class="record-label">Age</span><input type="text" name="age" class="form-control" required></div>
                <div class="col-md-10 record-row"><span class="record-label">Condition</span><input type="text" name="sick_condition" class="form-control" required></div>
            </div>
        </div>

        <!-- Contact Person -->
        <div class="record-section">
            <h3>Contact Person</h3>
            <div class="row record-inline">
                <div class="col-md-3 record-row"><span class="record-label">First Name</span><input type="text" name="contact_person_firstname" class="form-control" required></div>
                <div class="col-md-3 record-row"><span class="record-label">Middle Name</span><input type="text" name="contact_person_middlename" class="form-control"></div>
                <div class="col-md-3 record-row"><span class="record-label">Last Name</span><input type="text" name="contact_person_lastname" class="form-control" required></div>
                <div class="col-md-3 record-row"><span class="record-label">Suffix</span><input type="text" name="contact_person_suffix" class="form-control"></div>
            </div>

            <div class="row record-inline" style="margin-top:10px;">
                <div class="col-md-4 record-row"><span class="record-label">Relation</span><input type="text" name="contact_relation" class="form-control" required></div>
                <div class="col-md-4 record-row"><span class="record-label">Phone</span><input type="text" name="contact_number" class="form-control" required></div>
                <div class="col-md-4 record-row"><span class="record-label">Email</span><input type="email" name="contact_email" class="form-control" required></div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="record-buttons" style="margin-top:15px;">
            <button type="submit" class="btn btn-success">Save Record</button>
            <a href="s_anointing.php" class="btn btn-primary">Back</a>
        </div>

    </form>
</div>

</body>
</html>
