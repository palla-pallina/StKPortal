<?php
require_once 'dbconfig.php';
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "test_tree_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

$user_id = $_SESSION['user_id'];

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

/* ===============================
   UPDATE STATUS (AJAX)
================================ */
if (isset($_GET['ajax'], $_GET['id'], $_GET['status'])) {

    $id     = (int) $_GET['id'];
    $status = $_GET['status'];
    $reason = $_GET['reason'] ?? null;

    $allowed = ['Pending', 'Accepted', 'Declined', 'Done', 'Cancelled'];
    if (!in_array($status, $allowed)) {
        exit("Invalid status");
    }

    // Fetch request
    $stmt = $dbh->prepare("
        SELECT user_id, certificate_status, certificate_type,
               CONCAT_WS(' ', owner_firstname, owner_middlename, owner_lastname, owner_suffix) AS owner_name
        FROM certificate_request
        WHERE cert_req_id = ?
    ");
    $stmt->execute([$id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) exit("Request not found");

    if ($request['certificate_status'] === 'Declined') {
        exit("Declined requests cannot be updated");
    }

    if (in_array($status, ['Declined', 'Cancelled']) && empty(trim($reason))) {
        exit("Reason is required");
    }

    $pickup_date = $_GET['pickup_date'] ?? null;

    // ✅ Update request first
    $stmt = $dbh->prepare("
        UPDATE certificate_request
        SET certificate_status = ?,
            decline_reason = ?,
            pickup_date = ?
        WHERE cert_req_id = ?
    ");
    $stmt->execute([$status, $reason, $pickup_date, $id]);

    // Default notification (your existing one)
    $title = "Certificate Request Update";
    $message = "Your certificate request for {$request['owner_name']} is now marked as {$status}.";
    if ($reason) {
        $message .= " Reason: {$reason}";
    }

    $stmt = $dbh->prepare("
        INSERT INTO notifications (user_id, title, message, type, is_read, created_at)
        VALUES (?, ?, ?, 'certificate_update', 0, NOW())
    ");
    $stmt->execute([$request['user_id'], $title, $message]);

    if ($status === 'Accepted' && !empty($pickup_date)) {

        // format pickup date for message
        $pickupFormatted = date('F j, Y', strtotime($pickup_date));

        $ownerName = $request['owner_name'];
        $certType  = $request['certificate_type'];

        $pickupTitle = "Certificate Pick-up Date Set";
        $pickupMsg =
            "Good day! Your pick up date for <strong>{$ownerName}</strong>'s <strong>{$certType}</strong> has been set to <strong>{$pickupFormatted}</strong>. " .
            "You may visit the parish at that that date for the releasing of the certificate.<br><br>" .
            "Please submit a payment of ₱50 for the release of the certificate. You may pay via " .
            "<a href=\"http://localhost/stkportal_src/src/public/certificate_payment.php\">GCash</a> or at the church office. God Bless!";

        $stmt = $dbh->prepare("
            INSERT INTO notifications (user_id, title, message, type, is_read, created_at)
            VALUES (?, ?, ?, 'certificate_pickup_set', 0, NOW())
        ");
        $stmt->execute([$request['user_id'], $pickupTitle, $pickupMsg]);
    }

    echo "success";
    exit;
}


/* ===============================
   DELETE CERTIFICATE REQUEST
================================ */
if (isset($_GET['delete'], $_GET['id'])) {

    $id = (int) $_GET['id'];

    // Optional: prevent deleting completed requests
    $stmt = $dbh->prepare("
        SELECT certificate_status
        FROM certificate_request
        WHERE cert_req_id = ?
    ");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        exit("Request not found");
    }

    if ($row['certificate_status'] === 'Done') {
        exit("Completed requests cannot be deleted");
    }

    $stmt = $dbh->prepare("
        DELETE FROM certificate_request
        WHERE cert_req_id = ?
    ");
    $stmt->execute([$id]);

    echo "success";
    exit;
}


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
<link href='../../../stylesheets/private/certificate_request.css' rel='stylesheet'>
<!-- NAVBAR, MAIN CONTENT, FAVICON -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../../../stylesheets/private/navbar_updated.css" />
<link rel='stylesheet' href='../../../stylesheets/private/main_content.css'>
<link rel="icon" type="image/x-icon" href="../../../stylesheets/assets/favicon.ico">
<!--------------------->
<title>StKPortal | Certificate Requests</title>
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

        <li><a href="../sacrament_dashboard.php">Sacrament Reservations</a></li>
        <li><a href="../certificate_req/certificate_request.php" class="active">Certificate Requests</a></li>
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

<h1>Certificate Requests</h1>
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
      <a href="certificate_request.php" class="btn btn-warning clear-btn">Clear</a>
    <?php endif; ?>

  </form>
</div>

</div>
</div>

<!----------------------- MODALS ----------------------->

<!-- ACCEPT MODAL -->
<div id="acceptModal" class="modal hidden">
  <div class="modal-content">
    <h3>Accept Request</h3>

    <p>
      Are you sure you want to <strong>accept</strong> this certificate request?
    </p>

    <div class="modal-actions">
      <button class="btn cancel" onclick="closeAcceptModal()">Cancel</button>
      <button class="btn approve" onclick="confirmAccept()">Accept</button>
    </div>
  </div>
</div>

<!-- DECLINE MODAL -->
<div id="declineModal" class="modal hidden">
  <div class="modal-content">
    <h3 id="declineTitle">Decline Request</h3>

    <label for="declineReason">Select reason</label>
    <select id="declineReason" class="form-control">
      <option value="">-- Select reason --</option>
      <option value="Incomplete or incorrect information submitted">
        Incomplete or incorrect information submitted
      </option>
      <option value="Required supporting documents were not provided">
        Required supporting documents were not provided
      </option>
      <option value="Certificate record could not be verified">
        Certificate record could not be verified
      </option>
      <option value="Requested certificate is not available in parish records">
        Requested certificate is not available in parish records
      </option>
      <option value="Schedule conflict or processing limitation">
        Schedule conflict or processing limitation
      </option>
      <option value="Other">Other (specify below)</option>
    </select>

    <textarea
      id="customDeclineReason"
      class="form-control hidden"
      rows="3"
      placeholder="Please specify the reason..."
      style="margin-top:10px;"
    ></textarea>

    <div class="modal-actions">
      <button class="btn cancel" onclick="closeDeclineModal()">Cancel</button>
      <button class="btn decline" onclick="confirmDecline()">Decline</button>
    </div>
  </div>
</div>

<!-- STATUS CHANGE MODAL -->
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

<!-- DELETE MODAL -->
<div id="deleteModal" class="modal hidden">
  <div class="modal-content">
    <h3>Delete Certificate Request</h3>

    <p style="margin-top:10px;">
      Are you sure you want to <strong>permanently delete</strong> this
      certificate request? This action <strong>cannot be undone</strong>.
    </p>

    <div class="modal-actions">
      <button class="btn cancel" onclick="closeDeleteModal()">Cancel</button>
      <button class="btn decline" onclick="confirmDelete()">Delete</button>
    </div>
  </div>
</div>

<!-- NO RECORD MODAL -->
<div id="recordNotFoundModal" class="modal hidden">
  <div class="modal-content">
    <h3>Certificate Record Not Found</h3>

    <p>
      The requested certificate record could not be found in the parish records.
      Please verify the certificate details or add the sacrament record first.
    </p>

    <div class="modal-actions">
      <button class="btn approve" onclick="closeRecordNotFoundModal()">OK</button>
    </div>
  </div>
</div>


<div class="sacraments-table-container">
<table class="sacraments-table">

<thead>
<tr>
  <th>#</th>
  <th>Certificate Type</th>
  <th>Certificate Owner</th>
  <th>Purpose</th>
  <th>Status</th>
  <th>Confirm</th>
  <th class="action-column">Action</th>
</tr>
</thead>


<tbody>
<?php
/* ===============================
   PAGINATION SETUP
================================ */
$limit = 10; // rows per page
$page  = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$where = [];
$params = [];

/* SEARCH */
if (!empty($_GET['search'] ?? '')) {
    $where[] = "
        cert_req_id LIKE :search
        OR owner_firstname LIKE :search
        OR owner_lastname LIKE :search
        OR certificate_type LIKE :search
    ";
    $params[':search'] = '%' . $_GET['search'] . '%';
}

/* STATUS FILTER */
if (!empty($_GET['status']) && $_GET['status'] !== 'all') {
    $where[] = "certificate_status = :status";
    $params[':status'] = $_GET['status'];
}

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

/* ===============================
   TOTAL COUNT (FOR PAGINATION)
================================ */
$countSql = "
    SELECT COUNT(*) 
    FROM certificate_request
    {$whereSQL}
";

$countStmt = $dbh->prepare($countSql);
$countStmt->execute($params);
$totalRows = (int) $countStmt->fetchColumn();

$totalPages = max(1, ceil($totalRows / $limit));

/* ===============================
   SORTING LOGIC
================================ */
$sort = $_GET['sort'] ?? 'submitted_desc';

switch ($sort) {
    case 'submitted_asc':
        $orderBy = 'created_at ASC';
        break;

    case 'submitted_desc':
    default:
        $orderBy = 'created_at DESC';
        break;
}

$sql = "
    SELECT *
    FROM certificate_request
    {$whereSQL}
    ORDER BY {$orderBy}
    LIMIT :limit OFFSET :offset
";

$stmt = $dbh->prepare($sql);

/* Bind filters */
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

/* Bind pagination */
$stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_OBJ);

if ($rows):
foreach ($rows as $row):
?>
<tr>
    <td><?= htmlentities($row->cert_req_id) ?></td>

    <td><?= htmlentities($row->certificate_type) ?></td>

    <td>
        <?= htmlentities(
            trim(
                "{$row->owner_firstname} {$row->owner_middlename} {$row->owner_lastname} {$row->owner_suffix}"
            )
        ) ?>
    </td>

    <td><?= htmlentities($row->request_reason) ?></td>

    <!-- STATUS -->
    <td class="
    <?php
    if ($row->certificate_status === 'Accepted') echo 'status-verified';
    elseif ($row->certificate_status === 'Declined') echo 'status-unverified';
    elseif ($row->certificate_status === 'Done') echo 'status-done';
    elseif ($row->certificate_status === 'Cancelled') echo 'status-cancelled';
    else echo 'status-pending';
    ?>
    ">

    <?php if ($row->certificate_status === 'Pending'): ?>

    <?php elseif ($row->certificate_status === 'Accepted'): ?>

    <select class="status-dropdown"
            data-id="<?= $row->cert_req_id ?>">
        <option value="Accepted" selected>Accepted</option>
        <option value="Done">Done</option>
        <option value="Cancelled">Cancelled</option>
    </select>

    <?php else: ?>

    <?= htmlentities($row->certificate_status) ?>

    <?php endif; ?>

    </td>

    <!-- CONFIRM + SET PICK UP DATE -->
    <td class="confirm-column">

    <?php if ($row->certificate_status === 'Pending'): ?>

    <div class="status-actions">
        <button class="btn-approve"
                data-id="<?= $row->cert_req_id ?>" title="Accept">
        <i class="glyphicon glyphicon-ok"></i>
        </button>

        <button class="btn-reject"
                data-id="<?= $row->cert_req_id ?>" title="Decline">
        <i class="glyphicon glyphicon-remove"></i>
        </button>
    </div>

    <?php elseif ($row->certificate_status === 'Accepted'): ?>

    <div class="confirm-controls">
        <input type="date"
            class="pickup-date"
            value="<?= $row->pickup_date ?? '' ?>" title="Set Pickup Date" style="width: 126px;"> <br>

        <button class="btn-confirm-status"
                data-id="<?= $row->cert_req_id ?>" title="Set Pickup Date">
        Set Pickup Date
        </button>
    </div>

    <?php else: ?>

    <span class="mute">—</span>

    <?php endif; ?>

    </td>



    <td class="action-column">
        <div class="action-buttons">

            <a href="certificate_view.php?id=<?= $row->cert_req_id ?>"
               class="btn-view" title="View">
                <i class="glyphicon glyphicon-file"></i>
            </a>

            <a href="search_record.php?id=<?= $row->cert_req_id ?>"
              class="btn-edit"
              title="Generate Certificate">
              <i class="glyphicon glyphicon-print"></i>
            </a>


            <button
            class="btn-delete"
            title="Delete Certificate Request"
            data-id="<?php echo $row->cert_req_id; ?>"
            >
            <i class="glyphicon glyphicon-trash"></i>
            </button>


        </div>
    </td>
</tr>
<?php endforeach; else: ?>
<tr>
    <td colspan="6">No certificate requests found.</td>
</tr>
<?php endif; ?>
</tbody>

</table>

<?php if ($totalPages > 1): ?>
<nav class="text-center" style="margin-top:20px;">
  <ul class="pagination">

    <!-- PREVIOUS -->
    <li class="<?= $page <= 1 ? 'disabled' : '' ?>">
      <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">
        &laquo;
      </a>
    </li>

    <?php
    $start = max(1, $page - 2);
    $end   = min($totalPages, $page + 2);
    ?>

    <?php for ($i = $start; $i <= $end; $i++): ?>
      <li class="<?= $i === $page ? 'active' : '' ?>">
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
          <?= $i ?>
        </a>
      </li>
    <?php endfor; ?>

    <!-- NEXT -->
    <li class="<?= $page >= $totalPages ? 'disabled' : '' ?>">
      <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">
        &raquo;
      </a>
    </li>

  </ul>
</nav>
<?php endif; ?>

</div>

<script>
/* ============================================
   FIXED: UPDATE STATUS FUNCTION
============================================ */
function updateStatus(id, status, reason = "", pickupDate = null) {
  if (!id) {
    alert("Invalid request ID.");
    return;
  }

  let url = `certificate_request.php?ajax=1&id=${encodeURIComponent(id)}&status=${encodeURIComponent(status)}`;

  if (reason) {
    url += `&reason=${encodeURIComponent(reason)}`;
  }

  if (pickupDate) {
    url += `&pickup_date=${encodeURIComponent(pickupDate)}`;
  }

  fetch(url)
    .then(res => res.text())
    .then(data => {
      if (data.trim() === "success") {
        location.reload();
      } else {
        alert(data);
      }
    })
    .catch(() => alert("Server error."));
}

/* ============================================
   FIXED: DECLINE MODAL STATE & FUNCTIONS
============================================ */
const declineState = {
  id: null,
  status: "Declined"
};

function handleDecline(id, status = "Declined") {
  console.log("handleDecline called with:", { id, status, idType: typeof id });
  
  if (!id) {
    alert("Invalid request ID in handleDecline.");
    return;
  }

  declineState.id = id;
  declineState.status = status;

  console.log("declineState set to:", declineState);

  document.getElementById("declineTitle").innerText =
    status === "Cancelled" ? "Cancel Request" : "Decline Request";

  document.getElementById("declineReason").value = "";
  document.getElementById("customDeclineReason").value = "";
  document.getElementById("customDeclineReason").classList.add("hidden");

  document.getElementById("declineModal").classList.remove("hidden");
}

function closeDeclineModal() {
  document.getElementById("declineModal").classList.add("hidden");
  declineState.id = null;
}

function confirmDecline() {
  console.log("confirmDecline called, declineState:", declineState);
  
  if (!declineState.id) {
    alert("Request ID missing in confirmDecline.");
    console.error("declineState.id is null/undefined");
    return;
  }

  const selected = document.getElementById("declineReason").value;
  const custom   = document.getElementById("customDeclineReason").value.trim();

  console.log("Decline reason:", { selected, custom });

  if (!selected) {
    alert("Please select a reason.");
    return;
  }

  const reason = selected === "Other" ? custom : selected;

  if (selected === "Other" && !custom) {
    alert("Please specify the reason.");
    return;
  }

  // Store values BEFORE closing modal (which clears declineState)
  const id = declineState.id;
  const status = declineState.status;
  
  closeDeclineModal();
  
  console.log("Calling updateStatus with:", { id, status, reason });
  updateStatus(id, status, reason);
}

// Handle decline reason dropdown change
document.addEventListener("DOMContentLoaded", function() {
  document.getElementById("declineReason").addEventListener("change", function () {
    const customBox = document.getElementById("customDeclineReason");
    this.value === "Other"
      ? customBox.classList.remove("hidden")
      : customBox.classList.add("hidden");
  });
});

/* ============================================
   FIXED: ACCEPT MODAL
============================================ */
let currentAcceptId = null;

function closeAcceptModal() {
  document.getElementById("acceptModal").classList.add("hidden");
}

function confirmAccept() {
  closeAcceptModal();
  updateStatus(currentAcceptId, "Accepted");
}

/* ============================================
   FIXED: STATUS CHANGE MODAL
============================================ */
let statusChangeData = {
  id: null,
  status: null,
  selectEl: null,
  pickupDate: null
};

function openStatusChangeModal(id, status, selectEl, pickupDate = null) {
  statusChangeData = { id, status, selectEl, pickupDate };

  document.getElementById("statusChangeText").innerHTML =
    `Change status to <strong>${status}</strong>?`;

  document.getElementById("statusChangeModal").classList.remove("hidden");
}

function closeStatusChangeModal() {
  document.getElementById("statusChangeModal").classList.add("hidden");

  // Revert dropdown if admin cancels
  if (statusChangeData.selectEl) {
    statusChangeData.selectEl.value = "Accepted";
  }
  
  statusChangeData = { id: null, status: null, selectEl: null, pickupDate: null };
}

function confirmStatusChange() {
  const { id, status, pickupDate } = statusChangeData;
  
  closeStatusChangeModal();
  
  if (status === "Done") {
    updateStatus(id, status, "", pickupDate);
  }
  // Note: Cancelled is handled separately through decline modal
}

/* ============================================
   FIXED: EVENT LISTENERS
============================================ */
document.addEventListener("click", function (e) {
  
  // Handle Accept button
  const acceptBtn = e.target.closest(".btn-approve");
  if (acceptBtn) {
    currentAcceptId = acceptBtn.dataset.id;
    document.getElementById("acceptModal").classList.remove("hidden");
    return;
  }

  // Handle Decline button (for Pending requests)
  const rejectBtn = e.target.closest(".btn-reject");
  if (rejectBtn) {
    const id = rejectBtn.dataset.id;
    console.log("Reject button clicked, id:", id, "element:", rejectBtn);
    handleDecline(id, "Declined");
    return;
  }

  // Handle Confirm/Set Pickup Date button
  const confirmBtn = e.target.closest(".btn-confirm-status");
  if (confirmBtn) {
    const id = confirmBtn.dataset.id;
    const row = confirmBtn.closest(".confirm-controls");
    const pickupDate = row.querySelector(".pickup-date").value;

    if (!pickupDate) {
      alert("Please select a pickup date.");
      return;
    }

    // Just update the pickup date, keep status as Accepted
    updateStatus(id, "Accepted", "", pickupDate);
    return;
  }
});

/* ============================================
   FIXED: STATUS DROPDOWN CHANGE
============================================ */
document.addEventListener("change", function (e) {
  if (!e.target.classList.contains("status-dropdown")) return;

  const id = e.target.dataset.id;
  const newStatus = e.target.value;

  // Get current pickup date if exists
  const row = e.target.closest("tr");
  const pickupDateInput = row.querySelector(".pickup-date");
  const pickupDate = pickupDateInput ? pickupDateInput.value : null;

  if (newStatus === "Cancelled") {
    console.log("Cancelled selected, id:", id, "dropdown element:", e.target);
    handleDecline(id, "Cancelled");
    return;
  }

  if (newStatus === "Done") {
    openStatusChangeModal(id, newStatus, e.target, pickupDate);
    return;
  }
});

/* ============================================
   DELETE MODAL
============================================ */
let deleteRequestId = null;

document.addEventListener("click", function (e) {
  const deleteBtn = e.target.closest(".btn-delete");
  if (!deleteBtn) return;

  deleteRequestId = deleteBtn.dataset.id;
  document.getElementById("deleteModal").classList.remove("hidden");
});

function closeDeleteModal() {
  document.getElementById("deleteModal").classList.add("hidden");
  deleteRequestId = null;
}

function confirmDelete() {
  if (!deleteRequestId) return;

  fetch(`certificate_request.php?delete=1&id=${deleteRequestId}`)
    .then(res => res.text())
    .then(data => {
      if (data.trim() === "success") {
        location.reload();
      } else {
        alert(data);
      }
    })
    .catch(() => alert("Server error."));
}

/* ============================================
   RECORD NOT FOUND MODAL
============================================ */

function closeRecordNotFoundModal() {
  document.getElementById("recordNotFoundModal").classList.add("hidden");
}

document.addEventListener("DOMContentLoaded", function () {
  const params = new URLSearchParams(window.location.search);
  if (params.get("record_not_found") === "1") {
    document.getElementById("recordNotFoundModal").classList.remove("hidden");
  }
});
</script>
</body>