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
$stmt = $conn->prepare("SELECT * FROM weddings WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if (!$booking) {
    die("Booking not found");
}

/* ================= HANDLE FORM SUBMIT ================= */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // SAFE INPUT
    $wedding_date   = trim($_POST['wedding_date'] ?? null);
    $wedding_time   = trim($_POST['wedding_time'] ?? null);
    $rehearsal_date = trim($_POST['rehearsal_date'] ?? null);
    $rehearsal_time = trim($_POST['rehearsal_time'] ?? null);
    $canon_interview_date = trim($_POST['canon_interview_date'] ?? null);
    $canon_interview_time = trim($_POST['canon_interview_time'] ?? null);
    $precana_semi_date = trim($_POST['precana_semi_date'] ?? null);
    $precana_semi_time = trim($_POST['precana_semi_time'] ?? null);

    // OLD VALUES FOR CHANGE DETECTION
    $old_rehearsal_date = $booking['rehearsal_date'];
    $old_rehearsal_time = $booking['rehearsal_time'];
    $old_canon_date    = $booking['canon_interview_date'];
    $old_canon_time    = $booking['canon_interview_time'];
    $old_precana_date  = $booking['precana_semi_date'];
    $old_precana_time  = $booking['precana_semi_time'];

    $user_id = $booking['reservation_user_id'] ?? null;

    $conn->begin_transaction();

    try {

        /* ===== UPDATE SCHEDULE ===== */
        $stmt = $conn->prepare("
            UPDATE weddings 
            SET date = ?, time = ?, 
                rehearsal_date = ?, rehearsal_time = ?, 
                canon_interview_date = ?, canon_interview_time = ?, 
                precana_semi_date = ?, precana_semi_time = ?
            WHERE id = ?
        ");
        $stmt->bind_param(
            "ssssssssi",
            $wedding_date,
            $wedding_time,
            $rehearsal_date,
            $rehearsal_time,
            $canon_interview_date,
            $canon_interview_time,
            $precana_semi_date,
            $precana_semi_time,
            $id
        );
        $stmt->execute();
        $stmt->close();

        /* ===== SEND REHEARSAL NOTIFICATION (ONLY IF CHANGED) ===== */
        if (
            $user_id &&
            $rehearsal_date && $rehearsal_time &&
            ($rehearsal_date !== $old_rehearsal_date || $rehearsal_time !== $old_rehearsal_time)
        ) {
            $title = "Wedding Rehearsal Schedule";
            $message = htmlspecialchars(
                "Your wedding rehearsal is scheduled on $rehearsal_date at $rehearsal_time. Please visit the parish on time.",
                ENT_QUOTES
            );

            $stmt = $conn->prepare("
                INSERT INTO notifications (user_id, title, message, type, is_read, created_at)
                VALUES (?, ?, ?, 'rehearsal_reminder', 0, NOW())
            ");
            $stmt->bind_param("iss", $user_id, $title, $message);
            $stmt->execute();
            $stmt->close();
        }

        /* ===== SEND CANONICAL INTERVIEW NOTIFICATION (ONLY IF CHANGED) ===== */
        if (
            $user_id &&
            $canon_interview_date && $canon_interview_time &&
            ($canon_interview_date !== $old_canon_date || $canon_interview_time !== $old_canon_time)
        ) {
            $title = "Wedding Canonical Interview Schedule";
            $message = htmlspecialchars(
                "Your canonical interview is scheduled on $canon_interview_date at $canon_interview_time. Please visit the parish on time.",
                ENT_QUOTES
            );

            $stmt = $conn->prepare("
                INSERT INTO notifications (user_id, title, message, type, is_read, created_at)
                VALUES (?, ?, ?, 'canon_interview_reminder', 0, NOW())
            ");
            $stmt->bind_param("iss", $user_id, $title, $message);
            $stmt->execute();
            $stmt->close();
        }

        /* ===== SEND PRE-CANA SEMINAR NOTIFICATION (ONLY IF CHANGED) ===== */
        if (
            $user_id &&
            $precana_semi_date && $precana_semi_time &&
            ($precana_semi_date !== $old_precana_date || $precana_semi_time !== $old_precana_time)
        ) {
            $title = "Wedding Pre-cana Seminar Schedule";
            $message = htmlspecialchars(
                "Your pre-cana seminar is scheduled on $precana_semi_date at $precana_semi_time. Please visit the parish on time.",
                ENT_QUOTES
            );

            $stmt = $conn->prepare("
                INSERT INTO notifications (user_id, title, message, type, is_read, created_at)
                VALUES (?, ?, ?, 'precana_semi_reminder', 0, NOW())
            ");
            $stmt->bind_param("iss", $user_id, $title, $message);
            $stmt->execute();
            $stmt->close();
            
        }

        $conn->commit();

        $_SESSION['success'] = "Wedding schedule and notifications were updated successfully.";
        header("Location: s_wedding.php");
        exit();


    } catch (Exception $e) {
        $conn->rollback();
        die("Update failed: " . $e->getMessage());
    }
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
    <h2>Update Wedding Schedule</h2>

    <form method="POST">

        <div class="form-group">
            <label>Wedding Date</label>
            <input type="date" name="wedding_date" class="form-control"
                   value="<?php echo $booking['date']; ?>" required>
        </div>

        <div class="form-group">
            <label>Wedding Time</label>
            <input type="time" name="wedding_time" class="form-control"
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

        <div class="form-group">
            <label>Canonical Interview Date</label>
            <input type="date" name="canon_interview_date" class="form-control"
                   value="<?php echo $booking['canon_interview_date']; ?>">
        </div>

        <div class="form-group">
            <label>Canonical Interview Time</label>
            <input type="time" name="canon_interview_time" class="form-control"
                   value="<?php echo $booking['canon_interview_time']; ?>">
        </div>

        <div class="form-group">
            <label>Pre-cana Seminar</label>
            <input type="date" name="precana_semi_date" class="form-control"
                   value="<?php echo $booking['precana_semi_date']; ?>">
        </div>

        <div class="form-group">
            <label>Pre-cana Seminar</label>
            <input type="time" name="precana_semi_time" class="form-control"
                   value="<?php echo $booking['precana_semi_time']; ?>">
        </div>

        <br>

        <button type="submit" class="btn btn-save btn-block">
            Save Schedule
        </button>

        <a href="s_wedding.php" class="btn btn-default btn-block">Cancel</a>

    </form>
</div>

</body>
</html>
