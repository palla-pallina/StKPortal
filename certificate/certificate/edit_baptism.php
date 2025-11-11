<?php
require_once 'dbconfig.php';

// Get record by ID
$id = $_GET['id'] ?? null;
if (!$id) { header("Location: certificate.php"); exit(); }

$stmt = $dbh->prepare("SELECT * FROM baptism_reservations WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) die("Record not found.");

// Update when submitted
if (isset($_POST['update'])) {
    $sql = "UPDATE baptism_reservations SET 
        child_name = :child_name,
        birth_date = :birth_date,
        birth_place = :birth_place,
        father_name = :father_name,
        mother_name = :mother_name,
        preferred_date = :preferred_date,
        reservation_type = :reservation_type
        WHERE id = :id";
    
    $stmt = $dbh->prepare($sql);
    $stmt->execute([
        ':child_name' => $_POST['child_name'],
        ':birth_date' => $_POST['birth_date'],
        ':birth_place' => $_POST['birth_place'],
        ':father_name' => $_POST['father_name'],
        ':mother_name' => $_POST['mother_name'],
        ':preferred_date' => $_POST['preferred_date'],
        ':reservation_type' => $_POST['reservation_type'],
        ':id' => $id
    ]);

    echo "<script>alert('Record updated successfully!');window.location='certificate.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Reservation</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Edit Reservation</h2>
    <form method="post">
        <div class="mb-3">
            <label>Child Name</label>
            <input type="text" name="child_name" class="form-control" value="<?= htmlspecialchars($data['child_name']) ?>">
        </div>
        <div class="mb-3">
            <label>Birth Date</label>
            <input type="date" name="birth_date" class="form-control" value="<?= $data['birth_date'] ?>">
        </div>
        <div class="mb-3">
            <label>Birth Place</label>
            <input type="text" name="birth_place" class="form-control" value="<?= htmlspecialchars($data['birth_place']) ?>">
        </div>
        <div class="mb-3">
            <label>Father Name</label>
            <input type="text" name="father_name" class="form-control" value="<?= htmlspecialchars($data['father_name']) ?>">
        </div>
        <div class="mb-3">
            <label>Mother Name</label>
            <input type="text" name="mother_name" class="form-control" value="<?= htmlspecialchars($data['mother_name']) ?>">
        </div>
        <div class="mb-3">
            <label>Preferred Date</label>
            <input type="date" name="preferred_date" class="form-control" value="<?= $data['preferred_date'] ?>">
        </div>
        <div class="mb-3">
            <label>Reservation Type</label>
            <input type="text" name="reservation_type" class="form-control" value="<?= htmlspecialchars($data['reservation_type']) ?>">
        </div>
        <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
        <a href="certificate.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
