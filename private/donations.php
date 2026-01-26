<?php
require_once 'stk/dbconfig.php';
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
    header("Location: ../public/login.php");
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

/* ================================
   ADD DONATION
================================ */
function addDonation() {
    global $pdo;

    $donor_email       = $_POST['donor_email'] ?? null;
    $donor_contact_no  = $_POST['donor_contact_no'] ?? null;
    $donation_type     = $_POST['donation_type'];
    $donation_specified= $_POST['donation_specified'] ?? null;
    $donation_cause    = $_POST['donation_cause'];
    $mode_of_payment   = $_POST['mode_of_payment'];
    $donation_amount   = $_POST['donation_amount'] ?? null;

    $proof = null;
    if (!empty($_FILES['proof_of_donation']['name'])) {
        $dir = "donation_proof/";
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $ext = pathinfo($_FILES['proof_of_donation']['name'], PATHINFO_EXTENSION);
        $proof = uniqid("don_") . "." . $ext;
        move_uploaded_file($_FILES['proof_of_donation']['tmp_name'], $dir . $proof);
    }

    $sql = "INSERT INTO donations (
        donor_email, donor_contact_no, donation_type, donation_specified,
        donation_cause, mode_of_payment, proof_of_donation, donation_amount
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $donor_email,
        $donor_contact_no,
        $donation_type,
        $donation_specified,
        $donation_cause,
        $mode_of_payment,
        $proof,
        $donation_amount
    ]);

    echo json_encode(['success' => true]);
}

/* ================================
   FETCH DONATIONS
================================ */
function getDonations() {
    global $pdo;

    $search = $_POST['search'] ?? '';
    $sort   = $_POST['sort'] ?? 'donate_id DESC';
    $page   = $_POST['page'] ?? 1;
    $limit  = 8;
    $offset = ($page - 1) * $limit;

    $conditions = [];
    $params = [];

    if ($search) {
        $conditions[] = "(donor_email LIKE ? OR donation_cause LIKE ? OR donation_type LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $where = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM donations $where");
    $countStmt->execute($params);
    $total = $countStmt->fetchColumn();

    $sql = "SELECT * FROM donations $where ORDER BY $sort LIMIT $limit OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sumStmt = $pdo->prepare("SELECT SUM(donation_amount) FROM donations $where");
    $sumStmt->execute($params);
    $total_amount = $sumStmt->fetchColumn() ?? 0;

    echo json_encode([
        'success' => true,
        'donations' => $donations,
        'total' => $total,
        'pages' => ceil($total / $limit),
        'current_page' => $page,
        'total_amount' => number_format($total_amount, 2)
    ]);
}

/* ================================
   UPDATE STATUS
================================ */
function updateDonationStatus() {
    global $pdo;

    $stmt = $pdo->prepare("UPDATE donations SET donation_status=? WHERE donate_id=?");
    $stmt->execute([$_POST['status'], $_POST['donate_id']]);

    echo json_encode(['success' => true]);
}

/* ================================
   DELETE
================================ */
function deleteDonation() {
    global $pdo;

    $stmt = $pdo->prepare("DELETE FROM donations WHERE donate_id=?");
    $stmt->execute([$_POST['donate_id']]);

    echo json_encode(['success' => true]);
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />
<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="../stylesheets/private/donations.css" />

<!-- NAVBAR, MAIN CONTENT, FAVICON -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../stylesheets/private/navbar_updated.css" />
<link rel='stylesheet' href='../stylesheets/private/main_content.css'>
<link rel="icon" type="image/x-icon" href="../stylesheets/assets/favicon.ico">
<!--------------------->
<title>StKPortal | Manage Donations</title>
</head>

<body>


<!-------------------- NAVBAR -------------------->
<header class="custom-header">
<nav class="navbar custom-navbar">

    <!-- LEFT: Logo -->
    <div class="nav-left">
        <a href="dashboard.php">
            <img src="santa krus logo.png"
                 alt="Parish Logo"
                 class="logo">
        </a>
    </div>

    <!-- CENTER: Navigation links -->
    <ul class="nav-links nav-center">
        <li><a href="dashboard.php">Home</a></li>
        <li><a href="stk/members.php">Members</a></li>

        <li class="li-dropdown">
            <a href="#">
                Publish <i class="fa-solid fa-angle-down"></i>
            </a>
            <ul class="dropdown">
                <li><a href="stk/announcement.php">Announcement</a></li>
                <li><a href="stk/event.php">Event</a></li>
                <li><a href="livemass/live_mass.php">Mass Livestream</a></li>
                <li><a href="mass/mass_schedule.php">Mass Schedule</a></li>
                <li><a href="prayer_admin.php">Prayer Requests</a></li>
            </ul>
        </li>

        <li><a href="sacraments/sacrament_dashboard.php">Sacrament Reservations</a></li>
        <li><a href="sacraments/certificate_req/certificate_request.php">Certificate Requests</a></li>
        <li><a href="#" class="active">Donations</a></li>
    </ul>

    <!-- RIGHT: Notifications + User -->
    <div class="nav-right">
        <ul class="nav-links">
            <li class="nav-notification">
                <a href="notification/admin_notif.php">
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
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </li>
        </ul>
    </div>

</nav>
</header>
<!----------------- NAVBAR END ----------------->

<section id="content">

<div class="main-content">

    <div class="donations-header">
        <h1>Donation Management</h1>

        <div class="donations-controls">

            <div class="search-box">
                <input type="text" id="searchDonations" placeholder="Search...">
                <i class='bx bx-search'></i>
            </div>

            <div class="sort-box">
                <select id="sortDonations">
                    <option value="donate_id DESC">Newest</option>
                    <option value="donate_id ASC">Oldest</option>
                    <option value="donation_amount DESC">Amount (High → Low)</option>
                    <option value="donation_amount ASC">Amount (Low → High)</option>
                    <option value="donation_type ASC">Donation Type (A → Z)</option>
                    <option value="donation_cause ASC">Donation Cause (A → Z)</option>
                    <option value="mode_of_payment ASC">Mode of Payment (A → Z)</option>
                </select>
                <i class='bx bx-filter-alt'></i>
            </div>

            <button class="add-btn" onclick="openModal()">
                <i class='bx bx-plus'></i>
            </button>
        </div>
    </div>

    <!-- Donations Modal -->
    <div id="donationModal" class="modal">
        <div class="modal-content">

            <div class="modal-header">
                <h2>Add Donation Record</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>

            <form id="donationForm" enctype="multipart/form-data">

                <div class="form-row">
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="text" name="donor_contact_no">
                    </div>
                    <div class="form-group">
                        <label>Donation Type</label>
                        <select name="donation_type" required>
                            <option value="Cash">Cash</option>
                            <option value="Clothes">Clothes</option>
                            <option value="Goods">Goods</option>
                            <option value="Item">Item</option>
                            <!--<option value="Specified">Specified</option>-->
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Donation Cause</label>
                        <select name="donation_cause" required>
                            <option value="Parish Donation">Parish Donation</option>
                            <option value="Tithes">Tithes</option>
                            <option value="Charity">Charity</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Mode of Payment</label>
                        <select name="mode_of_payment" required>
                            <option value="Mass">Mass</option>
                            <option value="Onsite">Onsite</option>
                            <option value="Gcash">GCash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Amount (PHP)</label>
                        <input type="number" name="donation_amount" step="0.01" value="0">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-group--full" id="specifiedGroup">
                        <label>Specify Donation</label>
                        <input type="text" name="donation_specified" id="donation_specified">
                    </div>
                </div>

                <div class="form-group">
                    <label>Proof of Donation</label>
                    <input type="file" name="proof_of_donation">
                </div>


                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-submit">Save</button>
                </div>

            </form>
        </div>
    </div>

    <!-- Donations Filter Bar -->
    <div class="donations-filter-bar">

        <div class="filter-groups">

            <div class="filter-group">
                <span class="filter-label">TYPE</span>
                <select id="filterType" class="filter-select">
                    <option value="">All Types</option>
                    <option value="Cash">Cash</option>
                    <option value="Clothes">Clothes</option>
                    <option value="Goods">Goods</option>
                    <!--<option value="Specified">Specified</option>-->
                </select>
            </div>

            <div class="filter-group">
                <span class="filter-label">CAUSE</span>
                <select id="filterCause" class="filter-select">
                    <option value="">All Causes</option>
                    <option value="Parish Donation">Parish Donation</option>
                    <option value="Tithes">Tithes</option>
                    <option value="Charity">Charity</option>
                </select>
            </div>

            <div class="filter-group">
                <span class="filter-label">PAYMENT</span>
                <select id="filterPayment" class="filter-select">
                    <option value="">All Payments</option>
                    <option value="Mass">Mass</option>
                    <option value="Onsite">Onsite</option>
                    <option value="Gcash">GCash</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                </select>
            </div>

        </div>

        <div class="filter-total">
            <span class="total-label">TOTAL</span>
            <span class="currency">₱</span>
            <span id="totalAmount">0.00</span>
        </div>
    </div>

    <!-- Donations Table -->
    <div class="donations-table-container">
        <table class="donations-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Contact Number</th>
                    <th>Type</th>
                    <th>Cause</th>
                    <th>Payment</th>
                    <th>Proof</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Confirm</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="donationsTableBody"></tbody>
        </table>

        <div class="pagination">
            <div id="pageNumbers" class="page-numbers"></div>
        </div>
    </div>

</div>


<script>
function openModal() {
    document.getElementById('donationModal').classList.add('show');
}

function closeModal() {
    document.getElementById('donationModal').classList.remove('show');
}


document.addEventListener('DOMContentLoaded', () => {

    /* ===============================
       STATE VARIABLES
    =============================== */
    let currentPage = 1;
    let currentSearch = '';
    let currentSort = 'donate_id DESC';
    let currentFilterType = '';
    let currentFilterCause = '';
    let currentFilterPayment = '';

    /* ===============================
       LOAD DONATIONS
    =============================== */
    function loadDonations() {
        const fd = new FormData();
        fd.append('action', 'get_donations');
        fd.append('search', currentSearch);
        fd.append('sort', currentSort);
        fd.append('page', currentPage);
        fd.append('filter_type', currentFilterType);
        fd.append('filter_cause', currentFilterCause);
        fd.append('filter_payment', currentFilterPayment);

        fetch('donations_handler.php', {
            method: 'POST',
            body: fd
        })
        .then(r => r.json())
        .then(d => {
            const tbody = document.getElementById('donationsTableBody');
            tbody.innerHTML = '';

            if (!d.success || d.donations.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="11" style="text-align:center;">No records found.</td>
                    </tr>`;
                document.getElementById('totalAmount').textContent = '0.00';
                return;
            }

            d.donations.forEach(x => {
                tbody.innerHTML += `
                <tr>
                    <td>${x.donate_id}</td>
                    <td>${x.donor_contact_no || '-'}</td>
                    <td>${x.donation_type}</td>
                    <td>${x.donation_cause}</td>
                    <td>${x.mode_of_payment}</td>
                    <td>
                        ${x.proof_of_donation 
                            ? `<a href="donation_proof/${x.proof_of_donation}" target="_blank">View</a>`
                            : '-'}
                    </td>
                    <td>₱${x.donation_amount ?? '0.00'}</td>
                    <td>${x.date_posted}</td>
                    <td>
                        ${x.donation_status === 'pending'
                            ? `
                            <button onclick="updateStatus(${x.donate_id}, 'verified')">✔</button>
                            <button onclick="updateStatus(${x.donate_id}, 'unverified')">✘</button>
                            `
                            : x.donation_status
                        }
                    </td>
                    <td>
                        <button onclick="deleteDonation(${x.donate_id})">Delete</button>
                    </td>
                </tr>`;
            });

            document.getElementById('totalAmount').textContent = d.total_amount;
            updatePagination(d.pages, d.current_page);
        })
        .catch(err => console.error(err));
    }

 



    /* ===============================
       UPDATE STATUS
    =============================== */
    window.updateStatus = function(id, status) {
        // Fetch the donation details to check the mode_of_payment
        const fd = new FormData();
        fd.append('action', 'get_donation_by_id');
        fd.append('donate_id', id);

        fetch('donations_handler.php', {
            method: 'POST',
            body: fd
        })
        .then(r => r.json())
        .then(donation => {
            if (donation.success) {
                // Check if mode_of_payment is Metrobank or GCash
                if (donation.data.mode_of_payment !== "Metrobank" && donation.data.mode_of_payment !== "GCash") {
                    alert('Only donations paid through Metrobank or GCash can be verified.');
                    return;
                }

                // Proceed with the status update if valid
                const updateFd = new FormData();
                updateFd.append('action', 'update_status');
                updateFd.append('donate_id', id);
                updateFd.append('status', status);

                fetch('donations_handler.php', {
                    method: 'POST',
                    body: updateFd
                }).then(() => loadDonations());
            } else {
                alert('Error fetching donation details.');
            }
        })
        .catch(err => console.error(err));
    }


    /* ===============================
       DELETE
    =============================== */
    window.deleteDonation = function(id) {
        if (confirm("Delete this record?")) {
            const fd = new FormData();
            fd.append('action', 'delete');
            fd.append('donate_id', id);

            fetch('donations_handler.php', {
                method: 'POST',
                body: fd
            }).then(() => loadDonations());
        }
    }

    /* ===============================
       SEARCH
    =============================== */
    const searchInput = document.getElementById('searchDonations');
    if (searchInput) {
        searchInput.addEventListener('input', e => {
            currentSearch = e.target.value;
            currentPage = 1;
            loadDonations();
        });
    }

    /* ===============================
       SORT
    =============================== */
    const sortSelect = document.getElementById('sortDonations');
    if (sortSelect) {
        sortSelect.addEventListener('change', e => {
            currentSort = e.target.value;
            currentPage = 1;
            loadDonations();
        });
    }

    /* ===============================
       FILTERS
    =============================== */
    const filterType = document.getElementById('filterType');
    const filterCause = document.getElementById('filterCause');
    const filterPayment = document.getElementById('filterPayment');

    if (filterType) {
        filterType.addEventListener('change', e => {
            currentFilterType = e.target.value;
            currentPage = 1;
            loadDonations();
        });
    }

    if (filterCause) {
        filterCause.addEventListener('change', e => {
            currentFilterCause = e.target.value;
            currentPage = 1;
            loadDonations();
        });
    }

    if (filterPayment) {
        filterPayment.addEventListener('change', e => {
            currentFilterPayment = e.target.value;
            currentPage = 1;
            loadDonations();
        });
    }

    /* ===============================
       PAGINATION
    =============================== */
    window.updatePagination = function(totalPages, current) {
        const container = document.getElementById('pagination');
        if (!container) return;

        container.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            container.innerHTML += `
                <button 
                    onclick="currentPage=${i}; loadDonations();" 
                    ${i === current ? 'disabled' : ''}>
                    ${i}
                </button>
            `;
        }
    }

    /* ===============================
       INITIAL LOAD
    =============================== */
    loadDonations();

});
</script>

<script>
document.getElementById('donationForm').addEventListener('submit', function(e){
    e.preventDefault();

    const formData = new FormData(this);
    formData.append('action', 'add'); // ✅ THIS WAS MISSING

    fetch('donations_handler.php', {   // ✅ MUST MATCH YOUR HANDLER FILE
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            alert('Donation saved successfully!');
            closeModal();
            this.reset();
            loadDonations();
        } else {
            alert('Error saving donation');
            console.log(data);
        }
    })
    .catch(err => console.error(err));
});
</script>

<script>
const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');

const menuBar = document.querySelector('#content nav .bx.bx-menu');
const sidebar = document.getElementById('sidebar');

menuBar.addEventListener('click', function () {
    sidebar.classList.toggle('hide');
});

function updateDateTime() {
    const now = new Date();

    const optionsDate = { year: 'numeric', month: 'short', day: 'numeric' };
    const date = now.toLocaleDateString(undefined, optionsDate);

    const optionsTime = { hour: '2-digit', minute: '2-digit' };
    const time = now.toLocaleTimeString(undefined, optionsTime);

    document.getElementById('datetime').textContent = `${date} | ${time}`;
}
setInterval(updateDateTime, 1000);
updateDateTime();
</script>
    
</body>
</html>
