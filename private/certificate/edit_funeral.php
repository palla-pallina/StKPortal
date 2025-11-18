<?php
require_once 'connection.php';

// Get record ID safely
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch existing data
$sql = "SELECT * FROM funerals WHERE id = :id";
$query = $dbh->prepare($sql);
$query->bindParam(':id', $id, PDO::PARAM_INT);
$query->execute();
$reservation = $query->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    echo "<script>alert('Funeral record not found.'); window.location.href='certificate.php?type=Funeral';</script>";
    exit;
}

// Update record
if (isset($_POST['update'])) {
    $deceased_fullname = $_POST['deceased_fullname'] ?? '';
    $deceased_dob = $_POST['deceased_dob'] ?? '';
    $cause_of_death = $_POST['cause_of_death'] ?? '';
    $funeral_date = $_POST['funeral_date'] ?? '';
    $funeral_time = $_POST['funeral_time'] ?? '';
    $funeral_address = $_POST['funeral_address'] ?? '';
    $contact_person_firstname = $_POST['contact_person_firstname'] ?? '';
    $contact_person_middlename = $_POST['contact_person_middlename'] ?? '';
    $contact_person_lastname = $_POST['contact_person_lastname'] ?? '';
    $contact_person_relation = $_POST['contact_person_relation'] ?? '';
    $contact_person_contact_no = $_POST['contact_person_contact_no'] ?? '';
    $contact_person_email = $_POST['contact_person_email'] ?? '';

    $sql = "UPDATE funerals SET 
                deceased_fullname = :deceased_fullname,
                deceased_dob = :deceased_dob,
                cause_of_death = :cause_of_death,
                funeral_date = :funeral_date,
                funeral_time = :funeral_time,
                funeral_address = :funeral_address,
                contact_person_firstname = :contact_person_firstname,
                contact_person_middlename = :contact_person_middlename,
                contact_person_lastname = :contact_person_lastname,
                contact_person_relation = :contact_person_relation,
                contact_person_contact_no = :contact_person_contact_no,
                contact_person_email = :contact_person_email
            WHERE id = :id";

    $query = $dbh->prepare($sql);
    $query->bindParam(':deceased_fullname', $deceased_fullname);
    $query->bindParam(':deceased_dob', $deceased_dob);
    $query->bindParam(':cause_of_death', $cause_of_death);
    $query->bindParam(':funeral_date', $funeral_date);
    $query->bindParam(':funeral_time', $funeral_time);
    $query->bindParam(':funeral_address', $funeral_address);
    $query->bindParam(':contact_person_firstname', $contact_person_firstname);
    $query->bindParam(':contact_person_middlename', $contact_person_middlename);
    $query->bindParam(':contact_person_lastname', $contact_person_lastname);
    $query->bindParam(':contact_person_relation', $contact_person_relation);
    $query->bindParam(':contact_person_contact_no', $contact_person_contact_no);
    $query->bindParam(':contact_person_email', $contact_person_email);
    $query->bindParam(':id', $id, PDO::PARAM_INT);
    $query->execute();

    // Redirect back to funeral section
    echo "<script>
            alert('Funeral record updated successfully!');
            window.location.href='certificate.php?type=Funeral';
          </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Funeral Reservation</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<style>
body { background-color: #f5f5f5; font-family: 'Lato', sans-serif; }
.container { max-width: 800px; background: #fff; margin-top: 60px; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
h2 { text-align: center; color: #418033; margin-bottom: 30px; font-weight: bold; }
.form-group label { font-weight: 600; }
.btn-primary { background-color: #418033; border: none; }
.btn-primary:hover { background-color: #2f6025; }
.back-btn { margin-top: 15px; display: inline-block; color: #418033; }
</style>
</head>
<body>

<div class="container">
    <h2>Edit Funeral Reservation</h2>
    <form method="post">
        <div class="form-group">
            <label>Deceased Full Name</label>
            <input type="text" name="deceased_fullname" class="form-control" 
                   value="<?= htmlspecialchars($reservation['deceased_fullname'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Date of Birth</label>
            <input type="date" name="deceased_dob" class="form-control" 
                   value="<?= htmlspecialchars($reservation['deceased_dob'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Cause of Death</label>
            <textarea name="cause_of_death" class="form-control"><?= htmlspecialchars($reservation['cause_of_death'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Funeral Date</label>
            <input type="date" name="funeral_date" class="form-control" 
                   value="<?= htmlspecialchars($reservation['funeral_date'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Funeral Time</label>
            <input type="time" name="funeral_time" class="form-control" 
                   value="<?= htmlspecialchars($reservation['funeral_time'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Funeral Address</label>
            <textarea name="funeral_address" class="form-control"><?= htmlspecialchars($reservation['funeral_address'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Contact First Name</label>
            <input type="text" name="contact_person_firstname" class="form-control" 
                   value="<?= htmlspecialchars($reservation['contact_person_firstname'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Contact Middle Name</label>
            <input type="text" name="contact_person_middlename" class="form-control" 
                   value="<?= htmlspecialchars($reservation['contact_person_middlename'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Contact Last Name</label>
            <input type="text" name="contact_person_lastname" class="form-control" 
                   value="<?= htmlspecialchars($reservation['contact_person_lastname'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Contact Relation</label>
            <input type="text" name="contact_person_relation" class="form-control" 
                   value="<?= htmlspecialchars($reservation['contact_person_relation'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Contact Number</label>
            <input type="text" name="contact_person_contact_no" class="form-control" 
                   value="<?= htmlspecialchars($reservation['contact_person_contact_no'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Contact Email</label>
            <input type="email" name="contact_person_email" class="form-control" 
                   value="<?= htmlspecialchars($reservation['contact_person_email'] ?? '') ?>">
        </div>

        <button type="submit" name="update" class="btn btn-primary btn-block">Update Record</button>
        <a href="certificate.php?type=Funeral" class="back-btn"><i class='bx bx-arrow-back'></i> Back to Funeral Certificates</a>
    </form>
</div>

</body>
</html>
