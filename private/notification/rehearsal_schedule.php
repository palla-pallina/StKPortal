<?php
require '../conn.php';
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Admin') {
    exit("Access denied.");
}

$sacrament = $_GET['sacrament'] ?? '';
$reservation_id = (int)($_GET['id'] ?? 0);

if (!$reservation_id || !$sacrament) {
    exit("Invalid request.");
}

// Map sacrament to table and name fields for notifications
$sacraments = [
    'Baptism' => [
        'table' => 'baptisms',
        'name_field' => "CONCAT(child_firstname, ' ', COALESCE(child_middlename,''), ' ', child_lastname, ' ', COALESCE(child_suffix,''))",
        'type_field' => 'baptism_type'
    ],
    'First Communion' => [
        'table' => 'first_communion',
        'name_field' => "CONCAT(child_firstname, ' ', COALESCE(child_middlename,''), ' ', child_lastname, ' ', COALESCE(child_suffix,''))",
        'type_field' => 'first_comm_type'
    ],
    'Confirmation' => [
        'table' => 'confirmations',
        'name_field' => "CONCAT(confirm_firstname, ' ', COALESCE(confirm_middlename,''), ' ', confirm_lastname, ' ', COALESCE(confirm_suffix,''))",
        'type_field' => 'confirmation_type'
    ]
];

if (!isset($sacraments[$sacrament])) {
    exit("Invalid sacrament.");
}

$table = $sacraments[$sacrament]['table'];
$name_field = $sacraments[$sacrament]['name_field'];
$type_field = $sacraments[$sacrament]['type_field'];

// Fetch reservation info
$res = $conn->query("
    SELECT *, $name_field AS full_name, status, rehearsal_date, rehearsal_time, $type_field AS sac_type
    FROM $table
    WHERE id = $reservation_id
");
if (!$res || $res->num_rows === 0) {
    exit("Reservation not found.");
}
$reservation = $res->fetch_assoc();
$user_id = $reservation['reservation_user_id'] ?? 0;

// Extract existing rehearsal
$existing_rehearsal_date = $reservation['rehearsal_date'];
$existing_rehearsal_time = $reservation['rehearsal_time'];

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ============================
       Update Main Sacrament Schedule (if applicable)
    ============================ */
    if (isset($_POST['update_schedule'])) {
        $new_date = $_POST['service_date'] ?? null;
        $new_time = $_POST['service_time'] ?? null;

        if ($new_date !== $reservation['date'] || $new_time !== $reservation['time']) {
            $stmt = $conn->prepare("UPDATE $table SET date = ?, time = ? WHERE id = ?");
            $stmt->bind_param("ssi", $new_date, $new_time, $reservation_id);
            $stmt->execute();

            $title = "$sacrament Schedule Updated";
            $message = "Good day! This is to inform you that the $sacrament schedule for {$reservation['full_name']} "
                     . "has been updated to " . date('F d, Y', strtotime($new_date))
                     . ($new_time ? " at " . date('h:i A', strtotime($new_time)) : "") . ". Kindly arrive on time.";
            $type = strtolower(str_replace(' ', '_', $sacrament)) . "_schedule_updated";

            $stmt2 = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param("isss", $user_id, $title, $message, $type);
            $stmt2->execute();
        }
    }

    /* ============================
       Update Rehearsal
    ============================ */
if (isset($_POST['update_rehearsal'])) {
    $new_date = $_POST['rehearsal_date'] ?? null;
    $new_time = $_POST['rehearsal_time'] ?? null;

    if ($new_date !== $existing_rehearsal_date || $new_time !== $existing_rehearsal_time) {

        $stmt = $conn->prepare("UPDATE $table SET rehearsal_date = ?, rehearsal_time = ? WHERE id = ?");
        $stmt->bind_param("ssi", $new_date, $new_time, $reservation_id);
        $stmt->execute();

        $is_first = empty($existing_rehearsal_date) && empty($existing_rehearsal_time);

        $title = $is_first ? "Rehearsal Scheduled" : "Rehearsal Updated";
        $type = $is_first ? "rehearsal_schedule" : "rehearsal_updated";

        $formattedDate = date('F d, Y', strtotime($new_date));
        $formattedTime = date('h:i A', strtotime($new_time));

        // Updated messages with sacrament name
        if ($is_first) {
            $message = "Good day! This is to inform you that the rehearsal for {$reservation['full_name']}'s $sacrament "
                     . "has been scheduled for $formattedDate at $formattedTime. Kindly arrive on time to avoid any delays. "
                     . "We appreciate your cooperation. If you have any questions, feel free to contact or visit the church office. Thank you and God bless!";
        } else {
            $message = "Good day! This is to inform you that the rehearsal for {$reservation['full_name']}'s $sacrament "
                     . "has been UPDATED for $formattedDate at $formattedTime. Kindly arrive on time.";
        }

        $stmt2 = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("isss", $user_id, $title, $message, $type);
        $stmt2->execute();

        // Donation reminder on first-time rehearsal (existing code)
        if ($is_first) {
            $donation_costs = [
                'Baptism' => ['Solo' => 1000, 'Regular' => 500],
                'Confirmation' => ['Solo' => 1000, 'Regular' => 500],
                'First Communion' => ['Solo' => 1000, 'Regular' => 500]
            ];

            $sac_type = $reservation['sac_type'] ?? 'Regular';
            $cost = $donation_costs[$sacrament][$sac_type] ?? 0;

            $donation_msg = "Thank you for allowing us to serve you through our church. "
                            . "To help cover the costs of the $sacrament service, we kindly ask for a donation of at least ₱$cost. "
                            . "You can make your donation via <a href=\"http://localhost/stkportal_src/src/public/donation_reservation.php\">GCash</a> or at the church office. God bless!";


            $donation_title = "Donation Reminder";
            $donation_type = "donation_reminder";

            $stmt3 = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
            $stmt3->bind_param("isss", $user_id, $donation_title, $donation_msg, $donation_type);
            $stmt3->execute();
        }
    }

    echo "<script>alert('Rehearsal updated and user notified.'); window.location.href='admin_notif.php';</script>";
    exit;
}

    /* ============================
       Custom Notification
    ============================ */
    if (isset($_POST['send_custom']) && !empty($_POST['custom_title']) && !empty($_POST['custom_message'])) {
        $title = $_POST['custom_title'];
        $message = $_POST['custom_message'];

        // Prepend sacrament name if not already in message
        $sacrament_name = $_POST['sacrament_name'] ?? '';
        if ($sacrament_name && stripos($message, $sacrament_name) === false) {
            $message = "Regarding your $sacrament_name reservation: " . $message;
        }

        $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'custom_notif')");
        $stmt->bind_param("iss", $user_id, $title, $message);
        $stmt->execute();

        echo "<script>alert('Custom notification sent.'); window.location.href='admin_notif.php';</script>";
        exit;
    }

}

// Determine the person/child full name based on sacrament
$person_fullname = '';
switch ($sacrament) {
    case 'Baptism':
        $person_fullname = trim(
            ($reservation['child_firstname'] ?? '') . ' ' .
            (($reservation['child_middlename'] ?? '') ? $reservation['child_middlename'] . ' ' : '') .
            ($reservation['child_lastname'] ?? '') . ' ' .
            ($reservation['child_suffix'] ?? '')
        );
        break;
    case 'First Communion':
        $person_fullname = trim(
            ($reservation['child_firstname'] ?? '') . ' ' .
            (($reservation['child_middlename'] ?? '') ? $reservation['child_middlename'] . ' ' : '') .
            ($reservation['child_lastname'] ?? '') . ' ' .
            ($reservation['child_suffix'] ?? '')
        );
        break;
    case 'Confirmation':
        $person_fullname = trim(
            ($reservation['confirm_firstname'] ?? '') . ' ' .
            (($reservation['confirm_middlename'] ?? '') ? $reservation['confirm_middlename'] . ' ' : '') .
            ($reservation['confirm_lastname'] ?? '') . ' ' .
            ($reservation['confirm_suffix'] ?? '')
        );
        break;
}
?>



<!DOCTYPE html>
<html>
<head>
<title>Reservation Action</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href='../../stylesheets/private/reee.css' rel='stylesheet'>
</head>
<body>

<div class="page-wrapper">
  <div class="card-modern">

    <h3 class="mb-4">
      Manage <?= htmlspecialchars($sacrament) ?> Reservation #<?= $reservation_id ?>
    </h3>

    <form method="POST">

      <?php
      // Determine if main schedule exists for this sacrament
        $show_main_schedule = in_array($sacrament, ['Baptism','First Communion','Confirmation']);
        $show_rehearsal = in_array($sacrament, ['Baptism','First Communion','Confirmation']);



      $main_label = match($sacrament) {
          'Baptism' => 'Baptism',
          'First Communion' => 'First Communion',
          'Confirmation' => 'Confirmation',
          default => 'Service'
      };
      ?>

      <!-- ================= Main Schedule ================= -->
      <?php if ($show_main_schedule): ?>
        <h4><?= $main_label ?> Schedule</h4>
        <hr class="label-divider">
        <label><?= $main_label ?> Date:</label>
        <input type="date" name="service_date" class="form-control"
               value="<?= htmlspecialchars($reservation['date'] ?? '') ?>">

        <label><?= $main_label ?> Time:</label>
        <input type="time" name="service_time" class="form-control"
               value="<?= htmlspecialchars($reservation['time'] ?? '') ?>">

        <p id="scheduleWarning" class="text-danger mb-2" style="display:none;"></p>
        <button type="submit" name="update_schedule" class="btn-sched">
            Update <?= $main_label ?> Schedule
        </button>
        <hr class="divider">
      <?php endif; ?>

      <!-- ================= Rehearsal ================= -->
      <?php if ($show_rehearsal): ?>
        <h4>Rehearsal Schedule</h4>
        <hr class="label-divider">
        <label>Rehearsal Date:</label>
        <input type="date" name="rehearsal_date" class="form-control"
               value="<?= htmlspecialchars($existing_rehearsal_date ?? '') ?>">

        <label>Rehearsal Time:</label>
        <input type="time" name="rehearsal_time" class="form-control"
               value="<?= htmlspecialchars($existing_rehearsal_time ?? '') ?>">

        <p id="rehearsalDateWarning" class="text-danger mb-2" style="display:none;"></p>
        <p id="rehearsalWarning" class="text-danger mb-2" style="display:none;"></p>
        <!-- Modal trigger -->
        <button type="button" class="btn-rehearsal"
                onclick="openRehearsalModal()">
          Update Rehearsal
        </button>

        <!-- Hidden submit for modal -->
        <button type="submit" name="update_rehearsal" id="submitRehearsal" hidden></button>
        <hr class="divider">
      <?php endif; ?>

      <!-- ================= Custom Notification ================= -->
    <h4>Send Custom Notification</h4>
    <hr class="label-divider">

    <form method="POST" id="customNotifForm">
    <input type="hidden" name="sacrament_name" value="<?= htmlspecialchars($sacrament) ?>">

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
    </form>

  </div>
</div>

<!-- ================= Rehearsal Confirmation Modal ================= -->
<div class="modal fade" id="rehearsalConfirmModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Confirm Rehearsal Update</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
        ></button>
      </div>

      <div class="modal-body">
        <p>The rehearsal will be updated to:</p>
        <p class="fw-bold text-center" id="rehearsalPreview"></p>
        <p class="text-muted mb-0">
          The user will be notified if the schedule has changed.
        </p>
      </div>

      <div class="modal-footer">
        <button
          type="button"
          class="btn btn-secondary"
          data-bs-dismiss="modal"
        >
          Cancel
        </button>
        <button
          type="button"
          class="btn btn-primary"
          id="confirmRehearsalBtn"
        >
          Update
        </button>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openRehearsalModal() {
    const dateInput = document.querySelector('[name="rehearsal_date"]');
    const timeInput = document.querySelector('[name="rehearsal_time"]');
    const warning = document.getElementById('rehearsalWarning');

    // Reset previous warning
    warning.style.display = 'none';
    warning.textContent = '';

    // Validate date & time
    if (!dateInput.value || !timeInput.value) {
        warning.textContent = "Please select both rehearsal date and time before updating.";
        warning.style.display = 'block';
        return;
    }

    const formattedDate = new Date(dateInput.value).toLocaleDateString('en-US', {
        year: 'numeric', month: 'long', day: 'numeric'
    });
    const formattedTime = new Date(`1970-01-01T${timeInput.value}`).toLocaleTimeString('en-US', {
        hour: 'numeric', minute: '2-digit'
    });

    const sacrament = "<?= addslashes($sacrament) ?>";
    const fullname = "<?= addslashes($person_fullname) ?>";

    document.getElementById('rehearsalPreview').textContent =
        `${formattedDate} at ${formattedTime} for ${fullname}'s ${sacrament}`;

    // Show modal
    new bootstrap.Modal(document.getElementById('rehearsalConfirmModal')).show();
}

// Confirm button inside modal
document.getElementById('confirmRehearsalBtn').addEventListener('click', function () {
    const dateInput = document.querySelector('[name="rehearsal_date"]');
    const timeInput = document.querySelector('[name="rehearsal_time"]');
    const warning = document.getElementById('rehearsalWarning');

    // Reset previous warning
    warning.style.display = 'none';
    warning.textContent = '';

    // Prevent submission if empty
    if (!dateInput.value || !timeInput.value) {
        warning.textContent = "Rehearsal date and time cannot be empty.";
        warning.style.display = 'block';
        return;
    }

    // Submit hidden button to trigger PHP POST
    document.getElementById('submitRehearsal').click();
});
</script>

<script>
function validateRehearsalDate() {
    const serviceDateInput = document.querySelector('[name="service_date"]');
    const rehearsalDateInput = document.querySelector('[name="rehearsal_date"]');
    const warning = document.getElementById('rehearsalDateWarning');

    // Reset previous warning
    warning.style.display = 'none';
    warning.textContent = '';

    if (!serviceDateInput.value || !rehearsalDateInput.value) return true; // nothing to check yet

    const serviceDate = new Date(serviceDateInput.value);
    const rehearsalDate = new Date(rehearsalDateInput.value);

    if (rehearsalDate > serviceDate) {
        warning.textContent = "Rehearsal date cannot be after the sacrament date.";
        warning.style.display = 'block';
        return false;
    }

    return true;
}

// Validate when the date is picked
document.querySelector('[name="rehearsal_date"]').addEventListener('change', validateRehearsalDate);

// Validate before submitting the rehearsal form
document.getElementById('confirmRehearsalBtn').addEventListener('click', function () {
    if (!validateRehearsalDate()) return; // prevent modal confirmation
    document.getElementById('submitRehearsal').click();
});
</script>


</body>
</html>
