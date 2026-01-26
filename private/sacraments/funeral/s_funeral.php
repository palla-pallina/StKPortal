<?php
$conn = new mysqli('localhost', 'root', '', 'test_tree_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
require_once '../connection.php';
session_start();

// ===============================
// UPDATE STATUS + NOTIFICATION
// ===============================
if (isset($_GET['ajax'], $_GET['status'], $_GET['id'])) {

    $id     = intval($_GET['id']);
    $status = $_GET['status'];
    $reason = $_GET['reason'] ?? null;

    $allowedStatuses = ['Pending', 'Accepted', 'Declined', 'Done', 'Cancelled'];
    if (!in_array($status, $allowedStatuses)) {
        http_response_code(400);
        exit("Invalid status");
    }

    $stmt = $conn->prepare("
        SELECT reservation_user_id, status,
               CONCAT_WS(' ', deceased_firstname, deceased_middlename, deceased_lastname, deceased_suffix) AS deceased_name
        FROM funerals
        WHERE id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$booking) {
        http_response_code(404);
        exit("Record not found");
    }

    if ($booking['status'] === 'Declined') {
        http_response_code(403);
        exit("Declined reservations can no longer be updated.");
    }

    if (in_array($status, ['Declined', 'Cancelled']) && empty(trim($reason))) {
        http_response_code(400);
        exit("$status reason is required.");
    }

    $stmt = $conn->prepare("
        UPDATE funerals
        SET status = ?, decline_reason = ?
        WHERE id = ?
    ");
    $stmt->bind_param("ssi", $status, $reason, $id);
    $stmt->execute();
    $stmt->close();

    $user_id = $booking['reservation_user_id'];
    $name    = $booking['deceased_name'];

    if ($status === 'Accepted') {
        $title = "Funeral Mass Request Approved";
        $message = "Your funeral mass request for <strong>{$name}</strong> has been <strong>approved</strong>. Please await further instructions from the parish office.";
        $type = "reservation_accepted";
    } elseif ($status === 'Declined') {
        $title = "Funeral Mass Request Declined";
        $message = "We regret to inform you that the funeral mass request for <strong>{$name}</strong> was declined due to the following reason:<br><br><strong>{$reason}</strong>.";
        $type = "reservation_declined";
    } elseif ($status === 'Cancelled') {
        $title = "Funeral Mass Request Cancelled";
        $message = "Your funeral mass request for <strong>{$name}</strong> has been cancelled for the following reason:<br><br><strong>{$reason}</strong>.";
        $type = "reservation_cancelled";
    } else {
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

// Restrict access
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../../../public/login.php");
    exit();
}

// AUTO DONE
$today = date('Y-m-d');
$stmt = $conn->prepare("UPDATE funerals SET status='Done' WHERE funeral_date < ? AND status='Accepted'");
$stmt->bind_param("s", $today);
$stmt->execute();
$stmt->close();

// ===============================
// SEARCH + FILTER + SORT
// ===============================
$where = [];
$params = [];

if (!empty($_GET['search'] ?? '')) {
    $where[] = "(
        id LIKE :search OR
        deceased_firstname LIKE :search OR deceased_lastname LIKE :search OR
        contact_person_firstname LIKE :search OR contact_person_lastname LIKE :search
    )";
    $params[':search'] = '%' . trim($_GET['search']) . '%';
}

if (!empty($_GET['status']) && $_GET['status'] !== 'all') {
    $where[] = "status = :status";
    $params[':status'] = $_GET['status'];
}

$whereSQL = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$orderSQL = " ORDER BY created_at DESC";
if (!empty($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'date_asc':  $orderSQL = " ORDER BY funeral_date ASC"; break;
        case 'date_desc': $orderSQL = " ORDER BY funeral_date DESC"; break;
        case 'submitted_asc':  $orderSQL = " ORDER BY created_at ASC"; break;
    }
}

// PAGINATION
$limit = 9;
$page = max((int)($_GET['page'] ?? 1), 1);
$offset = ($page - 1) * $limit;

$countStmt = $dbh->prepare("SELECT COUNT(*) FROM funerals $whereSQL");
$countStmt->execute($params);
$totalRows = $countStmt->fetchColumn();
$totalPages = ceil($totalRows / $limit);

$sql = "SELECT * FROM funerals $whereSQL $orderSQL LIMIT :limit OFFSET :offset";
$query = $dbh->prepare($sql);
foreach ($params as $k => $v) $query->bindValue($k, $v);
$query->bindValue(':limit', $limit, PDO::PARAM_INT);
$query->bindValue(':offset', $offset, PDO::PARAM_INT);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Funeral Reservations</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet">
<link href='../../../stylesheets/private/s_baptism.css' rel='stylesheet'>
</head>
<body>
<div class="main-content">
<h1>Funeral Mass Reservations</h1>

<form method="GET" class="form-inline">
<select name="sort" class="form-control" onchange="this.form.submit()">
  <option value="submitted_desc">Submitted (Newest)</option>
  <option value="submitted_asc">Submitted (Oldest)</option>
  <option value="date_desc">Funeral Date (Recent)</option>
  <option value="date_asc">Funeral Date (Oldest)</option>
</select>
<select name="status" class="form-control" onchange="this.form.submit()">
  <option value="all">All Status</option>
  <option>Pending</option><option>Accepted</option><option>Declined</option><option>Done</option><option>Cancelled</option>
</select>
<input type="text" name="search" class="form-control" placeholder="Search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
<button class="btn btn-default">Search</button>
</form>

<table class="sacraments-table">
<thead>
<tr>
<th>#</th><th>Date</th><th>Time</th><th>Deceased</th><th>Contact</th><th>Phone</th><th>Email</th><th>Status</th><th>Confirm</th><th>Action</th>
</tr>
</thead>
<tbody>
<?php if ($results): foreach ($results as $r): ?>
<tr>
<td><?= $r->id ?></td>
<td><?= date('F j, Y', strtotime($r->funeral_date)) ?></td>
<td><?= date('h:i A', strtotime($r->funeral_time)) ?></td>
<td><?= htmlentities($r->deceased_firstname . ' ' . $r->deceased_lastname) ?></td>
<td><?= htmlentities($r->contact_person_firstname . ' ' . $r->contact_person_lastname) ?></td>
<td><?= htmlentities($r->contact_person_contact_no) ?></td>
<td><?= htmlentities($r->contact_person_email) ?></td>
<td><?= htmlentities($r->status) ?></td>
<td>
<?php if ($r->status === 'Pending'): ?>
<button onclick="updateStatus(<?= $r->id ?>,'Accepted')">✔</button>
<button onclick="decline(<?= $r->id ?>)">✖</button>
<?php endif; ?>
</td>
<td>
<a href="s_funeral_view.php?id=<?= $r->id ?>">View</a>
<?php if (!in_array($r->status,['Accepted','Done'])): ?>
<a href="s_funeral_update.php?id=<?= $r->id ?>">Edit</a>
<a href="s_funeral.php?del=<?= $r->id ?>">Delete</a>
<?php endif; ?>
</td>
</tr>
<?php endforeach; else: ?>
<tr><td colspan="10">No records found.</td></tr>
<?php endif; ?>
</tbody>
</table>

</div>
<script>
function updateStatus(id,status){fetch(`s_funeral.php?ajax=1&id=${id}&status=${status}`).then(()=>location.reload())}
function decline(id){const r=prompt('Reason:');if(r)fetch(`s_funeral.php?ajax=1&id=${id}&status=Declined&reason=${encodeURIComponent(r)}`).then(()=>location.reload())}
</script>
</body>
</html>
