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
        $date  = $g('date');
        $time  = $g('time');

        $province  = $g('province');
        $city      = $g('city');
        $street_no = $g('street_no');
        $barangay  = $g('barangay');

        $notes = $g('notes');

        $requesting_contact = $g('requesting_contact');
        $requesting_email   = $g('requesting_email');

        if (
            $date === '' || $time === '' ||
            $province === '' || $city === '' || $street_no === '' || $barangay === '' ||
            $notes === '' ||
            $requesting_contact === '' || $requesting_email === ''
        ) {
            throw new Exception("Please fill in all required fields.");
        }

        $stmt = $dbh->prepare("
            INSERT INTO house_blessings
            (
              reservation_user_id,
              status,
              date, time,
              province, city, street_no, barangay,
              notes,
              special_intention,
              requesting_firstname, requesting_middlename, requesting_lastname, requesting_suffix,
              requesting_contact, requesting_email
            )
            VALUES
            (
              :reservation_user_id,
              'Pending',
              :date, :time,
              :province, :city, :street_no, :barangay,
              :notes,
              :special_intention,
              :requesting_firstname, :requesting_middlename, :requesting_lastname, :requesting_suffix,
              :requesting_contact, :requesting_email
            )
        ");

        $stmt->execute([
            ':reservation_user_id' => $_SESSION['user_id'] ?? null,

            ':date' => $date,
            ':time' => $time,

            ':province' => $province,
            ':city' => $city,
            ':street_no' => $street_no,
            ':barangay' => $barangay,

            ':notes' => $notes,
            ':special_intention' => $g('special_intention') ?: null,

            ':requesting_firstname' => $g('requesting_firstname') ?: null,
            ':requesting_middlename' => $g('requesting_middlename') ?: null,
            ':requesting_lastname' => $g('requesting_lastname') ?: null,
            ':requesting_suffix' => $g('requesting_suffix') ?: null,

            ':requesting_contact' => $requesting_contact,
            ':requesting_email' => $requesting_email,
        ]);

        $dbh->commit();
        header("Location: s_house_blessing.php?success=1");
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
<title>Create House Blessing</title>
</head>
<body>

<div class="record-container">
    <h2>Create New House Blessing</h2>

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
            </div>
        </div>

        <!-- Address -->
        <div class="record-section">
            <h3>Address</h3>
            <div class="row record-inline">
                <div class="col-md-3 record-row">
                    <span class="record-label">Province</span>
                    <input type="text" name="province" class="form-control" required>
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">City</span>
                    <input type="text" name="city" class="form-control" required>
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Street No.</span>
                    <input type="text" name="street_no" class="form-control" required>
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Barangay</span>
                    <input type="text" name="barangay" class="form-control" required>
                </div>
            </div>
        </div>

        <!-- Requesting Person -->
        <div class="record-section">
            <h3>Requesting Person</h3>
            <div class="row record-inline">
                <div class="col-md-3 record-row">
                    <span class="record-label">First Name</span>
                    <input type="text" name="requesting_firstname" class="form-control">
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Middle Name</span>
                    <input type="text" name="requesting_middlename" class="form-control">
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Last Name</span>
                    <input type="text" name="requesting_lastname" class="form-control">
                </div>
                <div class="col-md-3 record-row">
                    <span class="record-label">Suffix</span>
                    <input type="text" name="requesting_suffix" class="form-control">
                </div>
            </div>

            <div class="row record-inline" style="margin-top:10px;">
                <div class="col-md-6 record-row">
                    <span class="record-label">Phone</span>
                    <input type="text" name="requesting_contact" class="form-control" required>
                </div>
                <div class="col-md-6 record-row">
                    <span class="record-label">Email</span>
                    <input type="email" name="requesting_email" class="form-control" required>
                </div>
            </div>
        </div>

        <!-- Notes / Special Intention -->
        <div class="record-section">
            <h3>Notes / Special Intention</h3>

            <div class="record-row">
                <span class="record-label">Notes</span>
                <input type="text" name="notes" class="form-control" required>
            </div>

            <div class="record-row" style="margin-top:10px;">
                <span class="record-label">Special Intention</span>
                <textarea name="special_intention" class="form-control" rows="4" placeholder="Optional..."></textarea>
            </div>
        </div>

        <!-- Buttons -->
        <div class="record-buttons" style="margin-top:15px;">
            <button type="submit" class="btn btn-success">Save Record</button>
            <a href="s_house_blessing.php" class="btn btn-primary">Back</a>
        </div>

    </form>
</div>

</body>
</html>
