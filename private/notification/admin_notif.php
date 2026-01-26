<?php
require_once '../conn.php';
session_start();

$user_id = $_SESSION['user_id'];

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../../public/login.php");
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
   PAGINATION
================================ */
$limit  = 10;
$page   = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

/* ===============================
   FILTERS
================================ */
$where  = "WHERE 1=1";
$params = [];
$types  = "";

/* Unread filter */
if (isset($_GET['unread'])) {
    $where .= " AND n.status = 'Unread'";
}

/* Sacrament filter */
if (!empty($_GET['sacrament'])) {
    $where   .= " AND n.sacrament = ?";
    $params[] = $_GET['sacrament'];
    $types   .= "s";
}

/* ===============================
   TOTAL COUNT
================================ */
$countSql = "
    SELECT COUNT(*) AS total
    FROM admin_notifications n
    $where
";

$stmt = $conn->prepare($countSql);

if ($types) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$total = (int)$stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$totalPages = ceil($total / $limit);

/* ===============================
   FETCH ADMIN NOTIFICATIONS
================================ */
$sql = "
SELECT
    n.id,
    n.sacrament,
    n.reference_id,
    n.title,
    n.message,
    n.status AS notif_status,
    n.created_at,

    COALESCE(
        b.status, fc.status, c.status, w.status,
        a.status, cr.certificate_status,
        ce.status, f.status, hb.status
    ) AS reservation_status

FROM admin_notifications n

LEFT JOIN baptisms b ON n.sacrament='Baptism' AND n.reference_id=b.id
LEFT JOIN first_communion fc ON n.sacrament='First Communion' AND n.reference_id=fc.id
LEFT JOIN confirmations c ON n.sacrament='Confirmation' AND n.reference_id=c.id
LEFT JOIN weddings w ON n.sacrament='Wedding' AND n.reference_id=w.id
LEFT JOIN anointing_of_sick_requests a ON n.sacrament='Anointing of the Sick' AND n.reference_id=a.id
LEFT JOIN certificate_request cr ON n.sacrament='Certificate Request' AND n.reference_id=cr.cert_req_id
LEFT JOIN celebratory ce ON n.sacrament='Celebratory Mass' AND n.reference_id=ce.id
LEFT JOIN funerals f ON n.sacrament='Funeral' AND n.reference_id=f.id
LEFT JOIN house_blessings hb ON n.sacrament='House Blessing' AND n.reference_id=hb.id

$where
ORDER BY n.created_at DESC
LIMIT ? OFFSET ?
";

$typesFetch  = $types . "ii";
$paramsFetch = [...$params, $limit, $offset];

$stmt = $conn->prepare($sql);
$stmt->bind_param($typesFetch, ...$paramsFetch);
$stmt->execute();

$result = $stmt->get_result();

/* ===============================
   VIEW LINK MAP
================================ */
$viewLinks = [
    'Baptism'             => '../sacraments/baptism/s_baptism_view.php?id=',
    'First Communion'     => '../sacraments/first_communion/s_first_comm_view.php?id=',
    'Confirmation'        => '../sacraments/confirmation/s_confirmation_view.php?id=',
    'Wedding'             => '../sacraments/wedding/s_wedding_view.php?id=',
    'Anointing of the Sick'=> '../sacraments/anointing_of_the_sick/s_anointing_view.php?id=',
    'Certificate Request' => '../certificates/certificate_view.php?id=',
    'Celebratory Mass'         => '../celebratory/s_celebratory_view.php?id=',
    'Funeral'             => '../funeral/s_funeral_view.php?id=',
    'House Blessing'      => '../house_blessing/s_house_blessing_view.php?id='
];
?>
  
<?php
$currentSacrament = $_GET['sacrament'] ?? '';
$isUnread         = isset($_GET['unread']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="admin_notif.css">
<!-- NAVBAR, MAIN CONTENT, FAVICON -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../../stylesheets/private/navbar_updated.css" />
<link rel="icon" type="image/x-icon" href="../../stylesheets/assets/favicon.ico">
<!--------------------->
<title>StKPortal | Notifications</title>
</head>
<body style="margin:0;">

<!-------------------- NAVBAR -------------------->
<header class="custom-header">
<nav class="navbar custom-navbar">

    <!-- LEFT: Logo -->
    <div class="nav-left">
        <a href="dashboard.php">
            <img src="../santa krus logo.png"
                 alt="Parish Logo"
                 class="logo">
        </a>
    </div>

    <!-- CENTER: Navigation links -->
    <ul class="nav-links nav-center">
        <li><a href="../dashboard.php">Home</a></li>
        <li><a href="../stk/members.php">Members</a></li>

        <li class="li-dropdown">
            <a href="#">
                Publish <i class="fa-solid fa-angle-down"></i>
            </a>
            <ul class="dropdown">
                <li><a href="../stk/announcement.php">Announcement</a></li>
                <li><a href="../stk/event.php">Event</a></li>
                <li><a href="../livemass/live_mass.php" class="active">Mass Livestream</a></li>
                <li><a href="../mass/mass_schedule.php">Mass Schedule</a></li>
                <li><a href="../prayer_admin.php">Prayer Requests</a></li>
            </ul>
        </li>

        <li><a href="../sacraments/sacrament_dashboard.php">Sacrament Reservations</a></li>
        <li><a href="../sacraments/certificate_req/certificate_request.php">Certificate Requests</a></li>
        <li><a href="../donations.php">Donations</a></li>
    </ul>

    <!-- RIGHT: Notifications + User -->
    <div class="nav-right">
        <ul class="nav-links">
            <li class="nav-notification">
                <a href="#">
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
                    <li><a href="../logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>

</nav>
</header>
<!----------------- NAVBAR END ----------------->
<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>Your Notifications</h1>

        <div class="dashboard-filter">
            <a href="admin_notif.php?clear=all" class="filter-btn">Clear All</a>
            <a href="admin_notif.php?unread=1" class="filter-btn">Unread</a>

        </div>
    </div>

    <!-- TABS -->
    <div class="summary-bar">

        <a href="admin_notif.php"
        class="summary-item">
            All
        </a>

        <a href="admin_notif.php?sacrament=Anointing%20of%20the%20Sick"
        class="summary-item <?= $currentSacrament==='Anointing of the Sick' ? 'active' : '' ?>">
            Anointing of the Sick
        </a>

        <a href="admin_notif.php?sacrament=Baptism"
        class="summary-item <?= $currentSacrament==='Baptism' ? 'active' : '' ?>">
            Baptism
        </a>

        <a href="admin_notif.php?sacrament=First%20Communion"
        class="summary-item <?= $currentSacrament==='First Communion' ? 'active' : '' ?>">
            First Communion
        </a>

        <a href="admin_notif.php?sacrament=Confirmation"
        class="summary-item <?= $currentSacrament==='Confirmation' ? 'active' : '' ?>">
            Confirmation
        </a>

        <a href="admin_notif.php?sacrament=Wedding"
        class="summary-item <?= $currentSacrament==='Wedding' ? 'active' : '' ?>">
            Wedding
        </a>
        
        <a href="admin_notif.php?sacrament=Certificate%20Request"
        class="summary-item <?= $currentSacrament==='Certificate Request' ? 'active' : '' ?>">
            Certificate Requests
        </a>

        <a href="admin_notif.php?sacrament=Funeral"
        class="summary-item <?= $currentSacrament==='Funeral' ? 'active' : '' ?>">
            Funeral Mass
        </a>

        <a href="admin_notif.php?sacrament=Celebratory%20Mass"
        class="summary-item <?= $currentSacrament==='Celebratory Mass' ? 'active' : '' ?>">
            Celebratory Mass
        </a>

        <a href="admin_notif.php?sacrament=House%20Blessing"
        class="summary-item <?= $currentSacrament==='House Blessing' ? 'active' : '' ?>">
            House Blessing
        </a>


    </div>
    </div>

    <div class="alerts-container">

    <?php if ($result->num_rows === 0): ?>
        <div class="no-alerts"><p>No notifications found.</p></div>
    <?php endif; ?>

    <?php while ($row = $result->fetch_assoc()):
        $isUnread = $row['notif_status'] === 'Unread';
        $alertCls = $isUnread ? 'alert-new unread' : 'read';
        $viewUrl  = ($viewLinks[$row['sacrament']] ?? '#') . $row['reference_id'];

        $showAction = in_array($row['sacrament'], ['Baptism','First Communion','Confirmation','Wedding','Anointing of the Sick'])
                      && $row['reservation_status'] === 'Accepted';

        $actionLink = match ($row['sacrament']) {
            'Wedding' => 'wedding_rehearsal_schedule.php',
            'Anointing of the Sick' => 'anointing_sick_schedule.php',
            default => 'rehearsal_schedule.php'
        };

        $alertCls = $isUnread ? 'alert-new unread' : 'read';

        /* Add special class for Anointing of the Sick */
        if ($row['sacrament'] === 'Anointing of the Sick') {
            $alertCls .= ' alert-anointing';
        }

    ?>

    <div class="alert alert-info <?= $alertCls ?>" data-id="<?= $row['id'] ?>">
        <div class="alert-icon"><i class="fa-solid fa-bell"></i></div>
        
        <div class="alert-content">
            <div class="alert-title">
                <?= htmlspecialchars($row['title']) ?>
                <span class="alert-time">
                    <?= date('M j, Y g:i A', strtotime($row['created_at'])) ?>
                </span>
            </div>

            <div class="alert-message">
                <?= $row['message'] ?>
            </div>

            <div class="alert-actions">
                <a href="<?= $viewUrl ?>" class="alert-btn primary-action">
                    View Request
                </a>

                <?php if ($showAction): ?>
                <a href="<?= $actionLink ?>?sacrament=<?= urlencode($row['sacrament']) ?>&id=<?= $row['reference_id'] ?>"
                   class="alert-btn primary-action">
                    Set Schedule
                </a>
                <?php endif; ?>
            </div>
        </div>

        <a href="#" class="alert-dismiss js-delete" data-id="<?= $row['id'] ?>">
            <i class="fa-solid fa-xmark"></i>
        </a>

    </div>
    <?php endwhile; ?>

    </div>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= $i === $page ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

</div>

<script>
document.addEventListener('click', e => {

    const card = e.target.closest('.alert.unread');
    if (card && !e.target.closest('a') && !e.target.closest('button')) {
        fetch('mark_read.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + card.dataset.id
        }).then(() => card.classList.remove('unread','alert-new'));
    }

    const del = e.target.closest('.js-delete');
    if (del) {
        e.preventDefault();
        if (!confirm('Delete this notification?')) return;

        fetch('delete_notification.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + del.dataset.id
        }).then(() => del.closest('.alert').remove());
    }
});

// ===============================
// MARK ALL AS READ
// ===============================
document.getElementById('markAllRead')?.addEventListener('click', () => {
    if (!confirm('Mark all notifications as read?')) return;

    fetch('mark_all_read.php', { method: 'POST' })
        .then(() => {
            document.querySelectorAll('.alert.unread').forEach(a => {
                a.classList.remove('unread','alert-new');
            });
        });
});

</script>

</body>
</html>
