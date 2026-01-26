<?php
$conn = new mysqli('localhost', 'root', '', 'test_tree_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
require_once '../connection.php';
$message = "";
session_start();

// ===============================
// UPDATE STATUS + NOTIFICATION
// ===============================
if (isset($_GET['ajax'], $_GET['status'], $_GET['id'])) {

    $id     = intval($_GET['id']);
    $status = $_GET['status']; // Pending | Accepted | Declined | Done | Cancelled
    $reason = $_GET['reason'] ?? null;

    $allowedStatuses = ['Pending', 'Accepted', 'Declined', 'Done', 'Cancelled'];
    if (!in_array($status, $allowedStatuses)) {
        http_response_code(400);
        exit("Invalid status");
    }

    // 1. Fetch booking info
    $stmt = $conn->prepare("
        SELECT reservation_user_id, status,
            CONCAT(bride_firstname,' ',bride_lastname,' & ',
                    groom_firstname,' ',groom_lastname) AS couple_name
        FROM weddings
        WHERE id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    $stmt->close();


    if (!$booking) {
        http_response_code(404);
        exit("Booking not found");
    }

    // ❌ LOCK if already Declined
    if ($booking['status'] === 'Declined') {
        http_response_code(403);
        exit("Declined reservations can no longer be updated.");
    }

    // ❌ Declined OR Cancelled requires reason
    if (in_array($status, ['Declined', 'Cancelled']) && empty(trim($reason))) {
        http_response_code(400);
        exit("$status reason is required.");
    }

    // 2. Update wedding status
    $stmt = $conn->prepare("
        UPDATE weddings
        SET status = ?, decline_reason = ?
        WHERE id = ?
    ");
    $stmt->bind_param("ssi", $status, $reason, $id);
    $stmt->execute();
    $stmt->close();


    $user_id    = $booking['reservation_user_id'];
    $couple = $booking['couple_name'];

    if ($status === "Accepted") {

        $title = "Wedding Request Accepted";
        $message =
            "We are pleased to inform you that your wedding request for <strong>{$couple}</strong> has been accepted.<br><br>
            Please wait for further instructions regarding seminar and rehearsal schedules.";

        $type = "wedding_accepted";

    }
    elseif ($status === "Declined") {

        $title = "Wedding Request Declined";
        $message =
            "Your wedding request for {$couple} has been declined for the following reason:<br><strong>{$reason}</strong>";

        $type = "wedding_declined";

    }
    elseif ($status === "Cancelled") {

        $title = "Wedding Request Cancelled";
        $message =
            "Your wedding request for {$couple} has been cancelled due to:<br><strong>{$reason}</strong>";

        $type = "wedding_cancelled";
    }
    else {
        echo "success";
        exit;
    }

    $stmt = $conn->prepare("
        INSERT INTO notifications (user_id, title, message, type, is_read, created_at)
        VALUES (?, ?, ?, ?, 0, NOW())
    ");
    $stmt->bind_param("isss", $user_id, $title, $message, $type);
    $stmt->execute();
    $stmt->close();

    echo "success";
    exit;
}

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../../../public/login.php");
    exit();
}
$admin_name = !empty($_SESSION['givenname']) ? $_SESSION['givenname'] : $_SESSION['username'];

/*--------- NOTIFICATION HANDLING START
- for notification icon at navbar
- retrieves all notifications belonging to logged in user */
$stmt = $conn->prepare("SELECT * FROM admin_notifications ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$unreadCount = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM admin_notifications WHERE status = 'Unread'");
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $unreadCount = $result['cnt'] ?? 0;
    $stmt->close();
}
/*--------- NOTIFICATION HANDLING END */

// Handle delete if del param is set
if (isset($_GET['del'])) {
    $wedding_id = intval($_GET['del']);

    try {
        $sql = "DELETE FROM weddings WHERE id = :id";
        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $wedding_id, PDO::PARAM_INT);
        $query->execute();

        echo "<script>alert('Wedding record deleted successfully!');</script>";
        exit();

    } catch (PDOException $e) {
        echo "<script>alert('Delete failed: " . $e->getMessage() . "');</script>";
    }
}

// ===============================
// AUTO-UPDATE STATUS TO DONE IF PAST DATE
// ===============================

// Get today's date
$today = date('Y-m-d');

// Update all weddings where the date has passed and status is Accepted
$updateDoneSQL = "
    UPDATE weddings
    SET status = 'Done'
    WHERE date < ? AND status = 'Accepted'
";
$stmt = $conn->prepare($updateDoneSQL);
$stmt->bind_param("s", $today);
$stmt->execute();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />
<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
<link href='../../../stylesheets/private/s_wedding.css' rel='stylesheet'>
<!-- NAVBAR, MAIN CONTENT, FAVICON -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../../../stylesheets/private/navbar_updated.css" />
<link rel='stylesheet' href='../../../stylesheets/private/main_content.css'>
<link rel="icon" type="image/x-icon" href="../../../stylesheets/assets/favicon.ico">
<!--------------------->
<title>StKPortal | Wedding Reservations</title>
</head>
<body>

<!-------------------- NAVBAR -------------------->
<header class="custom-header">
<nav class="navbar custom-navbar">

    <!-- LEFT: Logo -->
    <div class="nav-left">
        <a href="dashboard.php">
            <img src="../../santa krus logo.png"
                 alt="Parish Logo"
                 class="logo">
        </a>
    </div>

    <!-- CENTER: Navigation links -->
    <ul class="nav-links nav-center">
        <li><a href="../../dashboard.php">Home</a></li>
        <li><a href="../../stk/members.php">Members</a></li>

        <li class="li-dropdown">
            <a href="#">
                Publish <i class="fa-solid fa-angle-down"></i>
            </a>
            <ul class="dropdown">
                <li><a href="../../stk/announcement.php">Announcement</a></li>
                <li><a href="../../stk/event.php">Event</a></li>
                <li><a href="../../livemass/live_mass.php">Mass Livestream</a></li>
                <li><a href="../../mass/mass_schedule.php">Mass Schedule</a></li>
                <li><a href="../../prayer_admin.php">Prayer Requests</a></li>
            </ul>
        </li>

        <li><a href="../sacrament_dashboard.php" class="active">Sacrament Reservations</a></li>
        <li><a href="../certificate_req/certificate_request.php">Certificate Requests</a></li>
        <li><a href="../../donations.php">Donations</a></li>
    </ul>

    <!-- RIGHT: Notifications + User -->
    <div class="nav-right">
        <ul class="nav-links">
            <li class="nav-notification">
                <a href="../../notification/admin_notif.php">
                    <span class="bell">&#128276;</span>

                    <?php if (!empty($unreadCount) && $unreadCount > 0): ?>
                        <span class="badge"><?= $unreadCount ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <li class="divider">|</li>

            <li class="li-dropdown">
                <a href="#" style="margin-right: -10px;"><?= htmlspecialchars($admin_name) ?></a>
                <ul class="dropdown">
                    <li><a href="../../logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>

</nav>
</header>
<!----------------- NAVBAR END ----------------->

<!--------------------------------------- MAIN --------------------------------------------->
<div class="main-content">  
<div class="reservations-header">

<h1>Wedding Reservations</h1>
<div class="reservations-controls">

<!------------------------------- SORT BY -------------------------------->
<form method="GET" action="" class="form-inline">
  <label for="sort">Sort by:</label>

  <!-- DATE SORT -->
  <select name="sort" id="sort" class="form-control" onchange="this.form.submit()">
    <option value="submitted_desc" <?= ($_GET['sort'] ?? '') === 'submitted_desc' ? 'selected' : '' ?>>
      Submitted (Newest)
    </option>
    <option value="submitted_asc" <?= ($_GET['sort'] ?? '') === 'submitted_asc' ? 'selected' : '' ?>>
      Submitted (Oldest)
    </option>
    <option value="wedding_desc" <?= ($_GET['sort'] ?? '') === 'wedding_desc' ? 'selected' : '' ?>>
      Wedding Date (Recent)
    </option>
    <option value="wedding_asc" <?= ($_GET['sort'] ?? '') === 'wedding_asc' ? 'selected' : '' ?>>
      Wedding Date (Oldest)
    </option>
  </select>

  <!-- STATUS FILTER -->
  <select name="status" id="status" class="form-control" onchange="this.form.submit()">
    <option value="all">All Status</option>
    <option value="Pending"   <?= ($_GET['status'] ?? '') === 'Pending' ? 'selected' : '' ?>>Pending</option>
    <option value="Accepted"  <?= ($_GET['status'] ?? '') === 'Accepted' ? 'selected' : '' ?>>Accepted</option>
    <option value="Declined"  <?= ($_GET['status'] ?? '') === 'Declined' ? 'selected' : '' ?>>Declined</option>
    <option value="Done"      <?= ($_GET['status'] ?? '') === 'Done' ? 'selected' : '' ?>>Done</option>
    <option value="Cancelled" <?= ($_GET['status'] ?? '') === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
  </select>
</form>

<!------------------------------- SEARCH BOX -------------------------------->
<div class="search-box" title="Search by ID or Name">
  <form method="GET" action="" class="search-form">
    <div class="search-group">
      <input
        type="text"
        name="search"
        placeholder="Search by ID or Name"
        class="search-input"
        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search'] ?? '') : ''; ?>"
      />
      <button type="submit" class="search-btn" aria-label="Search">
        <span class="glyphicon glyphicon-search"></span>
      </button>
    </div>

    <?php if (!empty($_GET['search'])): ?>
      <a href="s_wedding.php" class="btn btn-warning clear-btn">Clear</a>
    <?php endif; ?>

  </form>
</div>

<!------------------------------- RECORD SACRAMENT RESERVATION -------------------------------->
<a href="s_wedding_insert.php">
    <button class="add-btn" id="addReservationBtn">
    <i class='bx bx-plus'></i>
    </button>
</a>
</div>
</div>

<!------------------------------- MODALS -------------------------------->
<div id="successModal" class="modal hidden">
  <div class="modal-content">
    <h3>Success</h3>

    <p id="successMessage">
      Status updated successfully!
    </p>

    <div class="modal-actions">
      <button class="btn approve" onclick="closeSuccessModal()">OK</button>
    </div>
  </div>
</div>

<script>
function openSuccessModal(message = "Status updated successfully!") {
  document.getElementById("successMessage").innerText = message;
  document.getElementById("successModal").classList.remove("hidden");
}

function closeSuccessModal() {
  document.getElementById("successModal").classList.add("hidden");
  location.reload(); // reload AFTER user confirms
}
</script>

<div id="acceptModal" class="modal hidden">
  <div class="modal-content">
    <h3>Accept Request</h3>

    <p class="accept-text">
      Are you sure you want to <strong>accept</strong> this wedding request?
    </p>

    <div class="modal-actions">
      <button class="btn cancel" onclick="closeAcceptModal()">Cancel</button>
      <button class="btn approve" onclick="confirmAccept()">Accept</button>
    </div>
  </div>
</div>

</script>
<div id="declineModal" class="modal hidden">
  <div class="modal-content">
    <h3>Decline Request</h3>

    <label for="declineReason">Select reason</label>
    <select id="declineReason">
      <option value="">-- Select --</option>
    </select>

    <textarea
      id="otherReason"
      class="hidden"
      placeholder="Specify reason..."
    ></textarea>

    <div class="modal-actions">
      <button class="btn cancel" onclick="closeDeclineModal()">Cancel</button>
      <button class="btn decline" onclick="confirmDecline()">Decline</button>
    </div>
  </div>
</div>

<div id="statusChangeModal" class="modal hidden">
  <div class="modal-content">
    <h3>Change Status</h3>

    <p id="statusChangeText"></p>

    <div class="modal-actions">
      <button class="btn cancel" onclick="closeStatusChangeModal()">Cancel</button>
      <button class="btn approve" onclick="confirmStatusChange()">Confirm</button>
    </div>
  </div>
</div>

<div id="deleteModal" class="modal hidden">
  <div class="modal-content">
    <h3>Delete Reservation</h3>

    <p style="margin-top:10px;">
      Are you sure you want to <strong>permanently delete</strong> this wedding
      reservation? This action <strong>cannot be undone</strong>.
    </p>

    <div class="modal-actions">
      <button class="btn cancel" onclick="closeDeleteModal()">Cancel</button>
      <button class="btn decline" onclick="confirmDelete()">Delete</button>
    </div>
  </div>
</div>

<hr class="divider">

<!------------------------------- TABLE -------------------------------->
<div class="sacraments-table-container">
  <table class="sacraments-table">

    <thead>
        <tr>
        <th>#</th>
        <th>Type</th>
        <th>Date</th>
        <th>Time</th>
        <th>Bride</th>
        <th>Contact</th>
        <th>Groom</th>
        <th>Contact</th>
        <th>Status</th>
        <th class="confirm-column">Confirm</th>
        <th class="action-column">Action</th>
        <th>Submitted On</th>
        </tr>
    </thead>

    <tbody>
      <?php

        $searchSQL = "";
        $whereSQL = [];
        $params = [];

        /* SEARCH */
        if (!empty($_GET['search'] ?? '')) {
            $whereSQL[] = "
                id LIKE :search
                OR bride_firstname LIKE :search
                OR bride_middlename LIKE :search
                OR bride_lastname LIKE :search
                OR groom_firstname LIKE :search
                OR groom_middlename LIKE :search
                OR groom_lastname LIKE :search
                OR CONCAT_WS(' ', bride_firstname, bride_middlename, bride_lastname) LIKE :search
                OR CONCAT_WS(' ', groom_firstname, groom_middlename, groom_lastname) LIKE :search
            ";
            $params[':search'] = '%' . trim($_GET['search']) . '%';
        }

        /* STATUS FILTER */
        if (!empty($_GET['status']) && $_GET['status'] !== 'all') {
            $whereSQL[] = "status = :status";
            $params[':status'] = $_GET['status'];
        }

        /* BUILD WHERE */
        $whereClause = !empty($whereSQL)
            ? ' WHERE ' . implode(' AND ', $whereSQL)
            : '';

        // ===============================
        // PAGINATION
        // ===============================
        $limit = 9; // records per page
        $page  = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $page  = max($page, 1);
        $offset = ($page - 1) * $limit;

        // ===============================
        // COUNT TOTAL RECORDS
        // ===============================
        $countSql = "SELECT COUNT(*) FROM weddings" . $whereClause;
        $countStmt = $dbh->prepare($countSql);
        $countStmt->execute($params);
        $totalRows = $countStmt->fetchColumn();
        $totalPages = ceil($totalRows / $limit);
        
        try {
            // Default sort
            $orderSQL = " ORDER BY created_at DESC";

            if (isset($_GET['sort'])) {
                switch ($_GET['sort']) {
                    case 'submitted_asc':
                        $orderSQL = " ORDER BY created_at ASC";
                        break;
                    case 'submitted_desc':
                        $orderSQL = " ORDER BY created_at DESC";
                        break;
                    case 'wedding_asc':
                        $orderSQL = " ORDER BY date ASC, time ASC";
                        break;
                    case 'wedding_desc':
                        $orderSQL = " ORDER BY date DESC, time DESC";
                        break;
                }
            }

            // MAIN QUERY WITH PAGINATION
            $sql = "SELECT * FROM weddings"
                . $whereClause
                . $orderSQL
                . " LIMIT :limit OFFSET :offset";

            $query = $dbh->prepare($sql);

            // Bind filters (search + status)
            foreach ($params as $key => $val) {
                $query->bindValue($key, $val);
            }

            // Bind pagination
            $query->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $query->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_OBJ);

          if ($query->rowCount() > 0) {
              foreach ($results as $result) {
                  ?>
                    <tr>
                    <td><?= $result->id ?></td>

                    <td><?= htmlentities($result->wedding_type) ?></td>

                    <td><?= date('F j, Y', strtotime($result->date)) ?></td>

                    <td>
                    <?= $result->time
                        ? date('h:i A', strtotime($result->time))
                        : '—' ?>
                    </td>

                    <td>
                    <?= htmlentities($result->bride_firstname) ?>
                    <?= htmlentities($result->bride_middlename) ?>
                    <?= htmlentities($result->bride_lastname) ?>
                    </td>

                    <td><?= htmlentities($result->bride_contact) ?></td>

                    <td>
                    <?= htmlentities($result->groom_firstname) ?>
                    <?= htmlentities($result->groom_middlename) ?>
                    <?= htmlentities($result->groom_lastname) ?>
                    <?= htmlentities($result->groom_suffix) ?>
                    </td>

                    <td><?= htmlentities($result->groom_contact) ?></td>


                    <!-- STATUS -->
                    <td class="
                        <?php
                        if ($result->status === 'Accepted') echo 'status-verified';
                        elseif ($result->status === 'Declined') echo 'status-unverified';
                        elseif ($result->status === 'Done') echo 'status-done';
                        elseif ($result->status === 'Cancelled') echo 'status-cancelled';
                        else echo 'status-pending';
                        ?>
                    ">
                    <?php if ($result->status === 'Accepted'): ?>

                        <select class="status-dropdown" data-id="<?php echo $result->id; ?>">
                            <option value="Accepted" selected>Accepted</option>
                            <option value="Done">Done</option>
                            <option value="Pending">Pending</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>

                    <?php else: ?>

                        <?php echo htmlentities($result->status); ?>

                    <?php endif; ?>
                    </td>


                    <!-- CONFIRM BUTTONS -->
                    <td class="confirm-column">
                        <?php if ($result->status !== 'Declined' && $result->status !== 'Done' && $result->status !== 'Accepted' && $result->status !== 'Cancelled'): ?>
                            <button class="btn-approve" data-id="<?php echo $result->id; ?>" title="Accept">
                                <i class="glyphicon glyphicon-ok"></i>
                            </button>

                            <button class="btn-reject" data-id="<?php echo $result->id; ?>" title="Reject">
                                <i class="glyphicon glyphicon-remove"></i>
                            </button>
                        <?php endif; ?>
                    </td>

                    <!-- ✅ ACTION BUTTON -->
                    <td class="action-column">
                        <div class="action-buttons">
                            <!-- View button always visible -->
                            <a href="s_wedding_view.php?id=<?php echo $result->id; ?>" class="btn-view" title="View Details">
                                <i class="glyphicon glyphicon-file"></i>
                            </a>

                            <!-- Only show Schedule, Edit, Delete if status is not Pending, Declined, or Done -->
                            <?php if (!in_array($result->status, ['Pending', 'Declined', 'Done', 'Cancelled'])): ?>
                                <a href="../../notification/wedding_rehearsal_schedule.php?sacrament=Wedding&id=<?php echo $result->id; ?>" class="btn-schedule" title="Set Schedule">
                                    <i class="glyphicon glyphicon-calendar"></i>
                                </a>

                                <a href="s_wedding_update.php?id=<?php echo $result->id; ?>" class="btn-edit" title="Edit Information">
                                    <i class="glyphicon glyphicon-pencil"></i>
                                </a>

                                <button
                                class="btn-delete"
                                title="Delete Reservation"
                                data-id="<?php echo $result->id; ?>"
                                >
                                <i class="glyphicon glyphicon-trash"></i>
                                </button>

                            <?php endif; ?>
                        </div>
                    </td>

                    <!--- SUBMITTED DATE --->
                    <td>
                        <?php
                            $date = new DateTime($result->created_at);
                            echo htmlentities($date->format('Y/m/d')) . '<br>' . htmlentities($date->format('h:i A'));
                        ?>
                    </td>
                </tr>
                  <?php
                  $cnt++;
              }
          } else {
              echo '<tr><td colspan="11">No records found.</td></tr>';
          }
      } catch (PDOException $e) {
          echo "<tr><td colspan='11'>Error: " . $e->getMessage() . "</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>

<?php if ($totalPages > 1): ?>
<div class="pagination-container">
    <ul class="pagination">

        <li class="<?= ($page <= 1) ? 'disabled' : '' ?>">
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">&laquo;</a>
        </li>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="<?= ($i == $page) ? 'active' : '' ?>">
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>

        <li class="<?= ($page >= $totalPages) ? 'disabled' : '' ?>">
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">&raquo;</a>
        </li>

    </ul>
</div>
<?php endif; ?>

</section>



<!----------------------------------- MAIN END ------------------------------------------>

<script>
document.addEventListener("click", function(e) {

    if (e.target.closest(".btn-approve")) {
        openAcceptModal(e.target.closest(".btn-approve").dataset.id);
    }


    if (e.target.closest(".btn-reject")) {
        handleDecline(e.target.closest(".btn-reject").dataset.id);
    }

});


function updateStatus(id, status, reason = "") {

    let url = `s_wedding.php?ajax=1&id=${id}&status=${status}`;

    if (status === "Declined" || status === "Cancelled") {
        url += `&reason=${encodeURIComponent(reason)}`;
    }

    fetch(url)
        .then(res => res.text())
        .then(data => {
            if (data.trim() === "success") {
                openSuccessModal();
            } else {
                alert(data);
            }
        })
        .catch(() => alert("Server error."));
}


document.addEventListener("change", function (e) {
    if (e.target.classList.contains("status-dropdown")) {
        const id = e.target.dataset.id;
        const newStatus = e.target.value;

        if (newStatus === "Cancelled") {
            handleDecline(id, "Cancelled");
            return;
        }

        openStatusChangeModal(id, newStatus, e.target);

    }
});

</script>

<!-- CONFIRM ACTIONS (DECLINE AND) -->
<script>
let currentDeclineId = null;
let currentReasonStatus = "Declined";

const reasons = [
  "Incomplete or invalid wedding requirements",
  "Child does not meet age requirements",
  "Schedule conflict or fully booked date",
  "Parents’ documents are incomplete",
  "Parish jurisdiction restriction",
  "Other (Specify)"
];

const reasonSelect = document.getElementById("declineReason");
const otherReason = document.getElementById("otherReason");

reasons.forEach(r => {
  const option = document.createElement("option");
  option.value = r;
  option.textContent = r;
  reasonSelect.appendChild(option);
});

reasonSelect.addEventListener("change", () => {
  otherReason.classList.toggle(
    "hidden",
    reasonSelect.value !== "Other (Specify)"
  );
});

function handleDecline(id, status = "Declined") {
  currentDeclineId = id;
  currentReasonStatus = status;

  document.querySelector("#declineModal h3").innerText =
    status === "Cancelled" ? "Cancel Request" : "Decline Request";

  reasonSelect.value = "";
  otherReason.value = "";
  otherReason.classList.add("hidden");

  document.getElementById("declineModal").classList.remove("hidden");
}

function closeDeclineModal() {
  document.getElementById("declineModal").classList.add("hidden");
}

function confirmDecline() {
  let reason = reasonSelect.value;

  if (!reason) {
    alert("Please select a reason.");
    return;
  }

  if (reason === "Other (Specify)") {
    reason = otherReason.value.trim();
    if (!reason) {
      alert("Decline reason is required.");
      return;
    }
  }

  closeDeclineModal();
  updateStatus(currentDeclineId, currentReasonStatus, reason);
}

let currentAcceptId = null;

function openAcceptModal(id) {
  currentAcceptId = id;
  document.getElementById("acceptModal").classList.remove("hidden");
}

function closeAcceptModal() {
  document.getElementById("acceptModal").classList.add("hidden");
}

function confirmAccept() {
  closeAcceptModal();
  updateStatus(currentAcceptId, "Accepted");
}

let statusChangeData = {
  id: null,
  status: null,
  selectEl: null
};

function openStatusChangeModal(id, status, selectEl) {
  statusChangeData = { id, status, selectEl };

  document.getElementById("statusChangeText").innerHTML =
    `Change status to <strong>${status}</strong>?`;

  document.getElementById("statusChangeModal").classList.remove("hidden");
}

function closeStatusChangeModal() {
  document.getElementById("statusChangeModal").classList.add("hidden");

  // Revert dropdown if cancelled
  if (statusChangeData.selectEl) {
    statusChangeData.selectEl.value = "Accepted";
  }
}

function confirmStatusChange() {
  closeStatusChangeModal();
  updateStatus(statusChangeData.id, statusChangeData.status);
}

let deleteReservationId = null;

// Open delete modal
document.addEventListener("click", function (e) {
  const deleteBtn = e.target.closest(".btn-delete");
  if (!deleteBtn) return;

  deleteReservationId = deleteBtn.dataset.id;
  document.getElementById("deleteModal").classList.remove("hidden");
});

// Close modal
function closeDeleteModal() {
  document.getElementById("deleteModal").classList.add("hidden");
  deleteReservationId = null;
}

// Confirm delete
function confirmDelete() {
  if (!deleteReservationId) return;

  // Redirect to existing PHP delete handler
  window.location.href =
    `s_wedding.php?del=${deleteReservationId}`;
}
</script>

</body>
</html>