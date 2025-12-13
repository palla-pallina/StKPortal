<?php
$conn = new mysqli('localhost', 'root', '', 'test_tree_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// ADMIN PROTECTION
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../../../public/login.php");
    exit();
}

// GET BOOKING ID
if (!isset($_GET['id'])) {
    die("Invalid Request");
}

$id = intval($_GET['id']);

// FETCH BOOKING
$stmt = $conn->prepare("SELECT * FROM confirmations WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if (!$booking) {
    die("Booking not found");
}

// HANDLE FORM SUBMIT
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $confirmation_date   = $_POST['confirmation_date'];
    $confirmation_time   = $_POST['confirmation_time'];
    $rehearsal_date = $_POST['rehearsal_date'];
    $rehearsal_time = $_POST['rehearsal_time'];

    // UPDATE SCHEDULE
    $stmt = $conn->prepare("
        UPDATE confirmations 
        SET date = ?, time = ?, rehearsal_date = ?, rehearsal_time = ?
        WHERE id = ?
    ");
    $stmt->bind_param("ssssi",
        $confirmation_date,
        $confirmation_time,
        $rehearsal_date,
        $rehearsal_time,
        $id
    );
    $stmt->execute();
    $stmt->close();

    // SEND REHEARSAL NOTIFICATION (ONLY IF SET)
    if (!empty($rehearsal_date) && !empty($rehearsal_time)) {

        $user_id = $booking['reservation_user_id'];
        $title   = "Baptism Rehearsal Schedule";
        $message = "Your confirmation rehearsal is scheduled on $rehearsal_date at $rehearsal_time. Please visit the parish on time.";
        $type    = "rehearsal_reminder";

        $stmt = $conn->prepare("
            INSERT INTO notifications (user_id, title, message, type, is_read, created_at)
            VALUES (?, ?, ?, ?, 0, NOW())
        ");
        $stmt->bind_param("isss", $user_id, $title, $message, $type);
        $stmt->execute();
        $stmt->close();
    }

    echo "<script>alert('Schedule updated successfully!'); window.location='s_confirmation.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Update Schedule</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet">

<style>
.container-box {
    max-width: 600px;
    margin: 60px auto;
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
}

h2 {
    text-align: center;
    margin-bottom: 25px;
}

.btn-save {
    background: #28a745;
    color: white;
}

.btn-save:hover {
    background: #218838;
}
</style>
</head>

<body>

<div class="container container-box">
    <h2>Update Baptism Schedule</h2>

    <form method="POST">

        <div class="form-group">
            <label>Baptism Date</label>
            <input type="date" name="confirmation_date" class="form-control"
                   value="<?php echo $booking['date']; ?>" required>
        </div>

        <div class="form-group">
            <label>Baptism Time</label>
            <input type="time" name="confirmation_time" class="form-control"
                   value="<?php echo $booking['time']; ?>" required>
        </div>

        <hr>

        <div class="form-group">
            <label>Rehearsal Date</label>
            <input type="date" name="rehearsal_date" class="form-control"
                   value="<?php echo $booking['rehearsal_date']; ?>">
        </div>

        <div class="form-group">
            <label>Rehearsal Time</label>
            <input type="time" name="rehearsal_time" class="form-control"
                   value="<?php echo $booking['rehearsal_time']; ?>">
        </div>

        <br>

        <button type="submit" class="btn btn-save btn-block">
            Save Schedule
        </button>

        <a href="s_confirmation.php" class="btn btn-default btn-block">Cancel</a>

    </form>
</div>

</body>
</html>
