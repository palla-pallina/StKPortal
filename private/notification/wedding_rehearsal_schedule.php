<?php
require '../conn.php';
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Admin') {
    exit("Access denied.");
}

$reservation_id = (int)($_GET['id'] ?? 0);
if (!$reservation_id) exit("Invalid request.");

// Fetch wedding reservation
$stmt = $conn->prepare("
    SELECT *,
        CONCAT(bride_firstname, ' ', COALESCE(bride_middlename,''), ' ', bride_lastname) AS bride_fullname,
        CONCAT(groom_firstname, ' ', COALESCE(groom_middlename,''), ' ', groom_lastname) AS groom_fullname,
        CONCAT(
            bride_firstname, ' ',
            COALESCE(bride_middlename,''), ' ',
            bride_lastname, ' & ',
            groom_firstname, ' ',
            COALESCE(groom_middlename,''), ' ',
            groom_lastname
        ) AS full_name
    FROM weddings
    WHERE id = ?
");
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$reservation = $stmt->get_result()->fetch_assoc();
if (!$reservation) exit("Wedding reservation not found.");

$user_id = $reservation['reservation_user_id'] ?? 0;
function buildCustomWeddingNotification($customMessage) {
    return "Regarding your wedding reservation: " . trim($customMessage);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ===== MAIN WEDDING DATE/TIME =====
    $new_wedding_date = $_POST['service_date'] ?? null;
    $new_wedding_time = $_POST['service_time'] ?? null;

    if ($new_wedding_date && $new_wedding_time &&
        ($new_wedding_date !== $reservation['date'] || $new_wedding_time !== $reservation['time'])) {

        $stmt = $conn->prepare("UPDATE weddings SET date = ?, time = ? WHERE id = ?");
        $stmt->bind_param("ssi", $new_wedding_date, $new_wedding_time, $reservation_id);
        $stmt->execute();

        $title = "Wedding Schedule Updated";
        $message = "Good day! This is to inform you that Ms. {$reservation['bride_fullname']} and Mr. {$reservation['groom_fullname']}'s wedding's schedule has been updated to "
                 . date('F d, Y', strtotime($new_wedding_date)) . " at " . date('h:i A', strtotime($new_wedding_time)) . ". Thank you and God bless!";

        $stmt2 = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'wedding_main_updated')");
        $stmt2->bind_param("iss", $user_id, $title, $message);
        $stmt2->execute();

        // update reservation array for later checks
        $reservation['date'] = $new_wedding_date;
        $reservation['time'] = $new_wedding_time;
    }

    // ===== EXTRA WEDDING SCHEDULES =====
    $schedules = [
        'Pre-Cana Seminar' => ['date' => $_POST['precana_semi_date'] ?? null, 'time' => $_POST['precana_semi_time'] ?? null, 'db_fields' => ['precana_semi_date','precana_semi_time']],
        'Canonical Interview' => ['date' => $_POST['canon_interview_date'] ?? null, 'time' => $_POST['canon_interview_time'] ?? null, 'db_fields' => ['canon_interview_date','canon_interview_time']],
        'Rehearsal' => ['date' => $_POST['rehearsal_date'] ?? null, 'time' => $_POST['rehearsal_time'] ?? null, 'db_fields' => ['rehearsal_date','rehearsal_time']],
    ];

    $first_time = [];
    $updated = [];

    foreach ($schedules as $name => $info) {
        $existing_date = $reservation[$info['db_fields'][0]] ?? null;
        $existing_time = $reservation[$info['db_fields'][1]] ?? null;
        $new_date = $info['date'];
        $new_time = $info['time'];

        if ($new_date && $new_time &&
            ($new_date !== $existing_date || $new_time !== $existing_time)) {

            $stmt = $conn->prepare("UPDATE weddings SET {$info['db_fields'][0]} = ?, {$info['db_fields'][1]} = ? WHERE id = ?");
            $stmt->bind_param("ssi", $new_date, $new_time, $reservation_id);
            $stmt->execute();

            if (empty($existing_date) && empty($existing_time)) {
                $first_time[$name] = $info;
            } else {
                $updated[$name] = $info;
            }
        }
    }

    // ===== NOTIFICATIONS =====

    // First-time schedules
    if ($first_time) {
        if (count($first_time) === 1) {
            $name = key($first_time);
            $info = $first_time[$name];
            $title = "{$name} Scheduled";
            $msg = "Good day! This is to inform you that the {$name}'s schedule has been set to "
                 . date('F d, Y', strtotime($info['date'])) . " at " . date('h:i A', strtotime($info['time'])) . ". "
                 . "Kindly ensure that Ms. {$reservation['bride_fullname']} and Mr. {$reservation['groom_fullname']} arrive on time to keep things running smoothly. "
                 . "If you have any questions, feel free to reach out or visit the church office. Thank you and God bless!";
            $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'first_time_schedule')");
            $stmt->bind_param("iss", $user_id, $title, $msg);
            $stmt->execute();
        } else {
            $title = "Upcoming Wedding Schedules";
            $msg = "Good day! We’d like to share the upcoming schedules with you:\n\n";
            foreach ($first_time as $name => $info) {
                $msg .= "{$name} – " . date('F d, Y', strtotime($info['date'])) . " at " . date('h:i A', strtotime($info['time'])) . "\n";
            }
            $msg .= "\nPlease make sure that Ms. {$reservation['bride_fullname']} and Mr. {$reservation['groom_fullname']} arrive on time to keep things running smoothly. "
                  . "If you have any questions, feel free to reach out or visit the church office. Thank you and God bless!";

            $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'first_time_schedules_multiple')");
            $stmt->bind_param("iss", $user_id, $title, $msg);
            $stmt->execute();
        }
    }

    // Updated schedules
    if ($updated) {
        if (count($updated) === 1) {
            $name = key($updated);
            $info = $updated[$name];
            $title = "{$name} Updated";
            $msg = "Good day! This is to inform you that the {$name}'s schedule has been UPDATED to "
                 . date('F d, Y', strtotime($info['date'])) . " at " . date('h:i A', strtotime($info['time'])) . ". "
                 . "Kindly arrive on time to keep things running smoothly. If you have any questions, feel free to reach out or visit the church office. Thank you and God bless!";

            $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'updated_schedule')");
            $stmt->bind_param("iss", $user_id, $title, $msg);
            $stmt->execute();
        } else {
            $title = "Updated Wedding Schedules";
            $msg = "Good day! Here are the updated schedules:\n\n";
            foreach ($updated as $name => $info) {
                $msg .= "{$name} – " . date('F d, Y', strtotime($info['date'])) . " at " . date('h:i A', strtotime($info['time'])) . "\n";
            }
            $msg .= "We appreciate your cooperation. If you have any questions, feel free to contact or visit the church office. Thank you and God bless!";

            $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'updated_schedules_multiple')");
            $stmt->bind_param("iss", $user_id, $title, $msg);
            $stmt->execute();
        }
    }


    if ($action === 'send_custom_notification') {

        $rawMessage = trim($_POST['custom_message'] ?? '');

        if ($rawMessage === '') {
            echo "<script>alert('Message cannot be empty.');</script>";
            exit;
        }

        $finalMessage = buildCustomWeddingNotification($rawMessage);

        $stmt = $conn->prepare("
            INSERT INTO notifications (user_id, title, message, type)
            VALUES (?, 'Wedding Notification', ?, 'custom')
        ");
        $stmt->bind_param("is", $user_id, $finalMessage);
        $stmt->execute();

        echo "<script>
            alert('Custom notification sent successfully.');
            window.location.href = 'admin_notif.php';
        </script>";
        exit;
    }


    echo "<script>
        alert('Wedding schedule(s) updated and notifications sent.');
        window.location.href='admin_notif.php';
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Wedding Rehearsal Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='../../stylesheets/private/reee.css' rel='stylesheet'>

</head>
<body>
<div class="page-wrapper">
  <div class="card-modern">
    <h3>Manage Wedding Reservation #<?= $reservation_id ?></h3>
    <!--<p>Bride & Groom: <?= htmlspecialchars($reservation['full_name']) ?></p>-->

    <form method="POST">
    <input type="hidden" name="action" value="update_wedding_schedule">

        <h4>Main Wedding Schedule</h4>
        <hr class="label-divider">
        <label>Date:</label>
        <input type="date" name="service_date" class="form-control" value="<?= $reservation['date'] ?>" required>
        <label>Time:</label>
        <input type="time" name="service_time" class="form-control" value="<?= $reservation['time'] ?>" required>


        <h4 style="margin-top:30px;">Additional Schedules</h4>
        <hr class="label-divider">
        <label>Pre-Cana Seminar Date:</label>
        <input type="date" name="precana_semi_date" class="form-control" value="<?= $reservation['precana_semi_date'] ?>">
        <p id="precanaDateWarning" class="text-danger small" style="display:none;"></p>

        <label>Pre-Cana Seminar Time:</label>
        <input type="time" name="precana_semi_time" class="form-control" value="<?= $reservation['precana_semi_time'] ?>">

        <hr class="divider" style="margin-top:25px; border-top: 2px dotted #1d1d1d;">

        <label>Canonical Interview Date:</label>
        <input type="date" name="canon_interview_date" class="form-control" value="<?= $reservation['canon_interview_date'] ?>">
        <p id="canonDateWarning" class="text-danger small" style="display:none;"></p>

        <label>Canonical Interview Time:</label>
        <input type="time" name="canon_interview_time" class="form-control" value="<?= $reservation['canon_interview_time'] ?>">

        <hr class="divider" style="margin-top:25px; border-top: 2px dotted #1d1d1d;">

        <label>Rehearsal Date:</label>
        <input type="date" name="rehearsal_date" class="form-control" value="<?= $reservation['rehearsal_date'] ?>">
        <p id="rehearsalDateWarning" class="text-danger small" style="display:none;"></p>

        <label>Rehearsal Time:</label>
        <input type="time" name="rehearsal_time" class="form-control" value="<?= $reservation['rehearsal_time'] ?>">

        <button type="submit" class="btn-rehearsal">Update Wedding Schedule</button>
    </form>

    <form method="POST">
        <h4>Send Custom Notification</h4>
        <hr class="label-divider">
        
        <input type="hidden" name="action" value="send_custom_notification">

        <div class="mb-3">
            <label class="form-label">Title:</label>
            <input type="text" name="custom_title" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Remarks:</label>
            <textarea name="custom_message" class="form-control" rows="4"></textarea>
        </div>

        <div class="button-row">
            <button type="submit" name="send_custom" class="btn-custom">
                Send Custom Notification
            </button>

            <button type="button" class="btn-back" onclick="window.history.back();">
                Back
            </button>
        </div>
    </form>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function validateScheduleDate(scheduleInputName, warningId, label) {
    const weddingDateInput = document.querySelector('[name="service_date"]');
    const scheduleInput = document.querySelector(`[name="${scheduleInputName}"]`);
    const warning = document.getElementById(warningId);

    warning.style.display = 'none';
    warning.textContent = '';

    if (!weddingDateInput.value || !scheduleInput.value) return true;

    const weddingDate = new Date(weddingDateInput.value);
    const scheduleDate = new Date(scheduleInput.value);

    if (scheduleDate > weddingDate) {
        warning.textContent = `${label} date cannot be after the Wedding date.`;
        warning.style.display = 'block';
        return false;
    }

    return true;
}

// Attach listeners
document.querySelector('[name="precana_semi_date"]')
    .addEventListener('change', () =>
        validateScheduleDate('precana_semi_date', 'precanaDateWarning', 'Pre-Cana Seminar')
    );

document.querySelector('[name="canon_interview_date"]')
    .addEventListener('change', () =>
        validateScheduleDate('canon_interview_date', 'canonDateWarning', 'Canonical Interview')
    );

document.querySelector('[name="rehearsal_date"]')
    .addEventListener('change', () =>
        validateScheduleDate('rehearsal_date', 'rehearsalDateWarning', 'Rehearsal')
    );

// Block submission if any date is invalid
document.querySelector('form[action=""] button[type="submit"]')
    .addEventListener('click', function (e) {

        const valid =
            validateScheduleDate('precana_semi_date', 'precanaDateWarning', 'Pre-Cana Seminar') &&
            validateScheduleDate('canon_interview_date', 'canonDateWarning', 'Canonical Interview') &&
            validateScheduleDate('rehearsal_date', 'rehearsalDateWarning', 'Rehearsal');

        if (!valid) {
            e.preventDefault();
        }
    });
</script>

</body>
</html>
