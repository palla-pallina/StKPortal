<?php
require_once 'connection.php';

$id = intval($_GET['id'] ?? 0);

if (!$id) {
    die("Invalid ID");
}

// Fetch wedding reservation
$stmt = $dbh->prepare("SELECT * FROM weddings WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Reservation not found");
}

// Handle form submission
if (isset($_POST['submit'])) {
    $bride = $_POST['bride'];
    $groom = $_POST['groom'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $reservation_type = $_POST['reservation_type'];

    $update = $dbh->prepare("UPDATE weddings SET bride = :bride, groom = :groom, date = :date, time = :time, reservation_type = :reservation_type WHERE id = :id");
    $update->bindParam(':bride', $bride);
    $update->bindParam(':groom', $groom);
    $update->bindParam(':date', $date);
    $update->bindParam(':time', $time);
    $update->bindParam(':reservation_type', $reservation_type);
    $update->bindParam(':id', $id, PDO::PARAM_INT);
    $update->execute();

    echo "<script>alert('Wedding reservation updated successfully!');window.location.href='certificate.php?type=Wedding';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Wedding Reservation</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container" style="margin-top:50px;">
    <h3>Edit Wedding Reservation</h3>
    <form method="post">
        <div class="form-group">
            <label>Bride Name</label>
            <input type="text" name="bride" class="form-control" value="<?= htmlspecialchars($data['bride']) ?>" required>
        </div>
        <div class="form-group">
            <label>Groom Name</label>
            <input type="text" name="groom" class="form-control" value="<?= htmlspecialchars($data['groom']) ?>" required>
        </div>
        <div class="form-group">
            <label>Wedding Date</label>
            <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($data['date']) ?>" required>
        </div>
        <div class="form-group">
            <label>Wedding Time</label>
            <input type="time" name="time" class="form-control" value="<?= htmlspecialchars($data['time']) ?>" required>
        </div>
        <div class="form-group">
            <label>Reservation Type</label>
            <select name="reservation_type" class="form-control">
                <option value="Full Wedding" <?= $data['reservation_type']=='Full Wedding'?'selected':'' ?>>Full Wedding</option>
                <option value="Simple Ceremony" <?= $data['reservation_type']=='Simple Ceremony'?'selected':'' ?>>Simple Ceremony</option>
            </select>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Update</button>
        <a href="certificate.php?type=Wedding" class="btn btn-default">Cancel</a>
    </form>
</div>
</body>
</html>
