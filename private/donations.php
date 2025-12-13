<?php
require_once 'config.php';
session_start();

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}
$admin_name = $_SESSION['givenname'] ?? null; 

/* ================================
   AJAX HANDLER
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add': addDonation(); break;
        case 'get_donations': getDonations(); break;
        case 'update_status': updateDonationStatus(); break;
        case 'delete': deleteDonation(); break;
        default:
            echo json_encode(['error' => 'Invalid Action']);
    }
    exit;
}

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
<title>Donation Management</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />
<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="../stylesheets/private/navbar.css" />
<link rel="stylesheet" href="../stylesheets/private/donations.css" />
</head>

<body>


    <!-------------------------- NAVBAR -------------------------->
    <section id="sidebar" <?php if ($hideSidebar) echo 'class="hide"'; ?>>
        <a href="#" class="label">
            <img src="santa krus logo.png" alt="Logo" class="logo" />
        </a>
        <ul class="side-menu top">
                <li><a href="dashboard.php"><span class="text">Dashboard</span></a></li>
                <li><a href="stk/filemanager.php"><span class="text">File Manager</span></a></li>
                <li><a href="stk/announcement.php"><span class="text">Announcement</span></a></li>
                <li><a href="event.php"><span class="text">Event</span></a></li>
                <li><a href="stk/members.php"><span class="text">Members</span></a></li>
                <li><a href="sacraments/certificate_req/certificate_request.php"><span class="text">Certificate Requests</span></a></li>
                <li><a href="sacraments/sacrament_dashboard.php"><span class="text">Reservations</span></a></li>
                <li class="active"><a href="donations.php" data-section="dashboard"><span class="text">Donations</span></a></li>
                <li><a href="prayer_admin.php"><span class="text">Prayer Requests</span></a></li>
            </ul>
            <ul class="side-menu">
                <li><a href="logout.php" class="logout"><span class="text">LOGOUT</span></a></li>
            </ul>
    </section>
    <!------------------------- NAVABAR END ------------------------>

    <section id="content">

    <!-------------------------- SIDEBAR -------------------------->
    <nav>
    <i class='bx bx-menu'></i>
    <div id="datetime"></div>
    <ul>
        <li class="li-dropdown">
        <a href="#"><span class="greeting"><?php echo htmlspecialchars($admin_name); ?></span></a>
        <ul class="dropdown">
            <li><a href="logout.php" class="logout">Logout</a></li>
        </ul>
    </li>
    </ul>
    </nav>
    <!------------------------- SIDEBAR END ------------------------>

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
                        <button onclick="deleteDonation(${x.donate_id})">Delete</button>
                    </td>
                </tr>`;
            });

            document.getElementById('totalAmount').textContent = d.total_amount;
            updatePagination(d.pages, d.current_page);
        })
        .catch(err => console.error(err));
    }

    /* 
                    <td>
                        ${x.donation_status === 'pending'
                            ? `
                            <button onclick="updateStatus(${x.donate_id}, 'verified')">✔</button>
                            <button onclick="updateStatus(${x.donate_id}, 'unverified')">✘</button>
                            `
                            : x.donation_status
                        }
                    </td>
    */

    /* ===============================
       UPDATE STATUS
    =============================== */
    window.updateStatus = function(id, status) {
        const fd = new FormData();
        fd.append('action', 'update_status');
        fd.append('donate_id', id);
        fd.append('status', status);

        fetch('donations_handler.php', {
            method: 'POST',
            body: fd
        }).then(() => loadDonations());
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

<!--
<script>
document.addEventListener('DOMContentLoaded', () => {
    const donationType = document.querySelector('select[name="donation_type"]');
    const specifiedGroup = document.getElementById('specifiedGroup');
    const specifiedInput = document.getElementById('donation_specified');

    function toggleSpecified() {
        if (donationType.value === 'Specified') {
            specifiedGroup.style.display = 'block';
            specifiedInput.required = true;
        } else {
            specifiedGroup.style.display = 'none';
            specifiedInput.value = '';
            specifiedInput.required = false;
        }
    }

    // Run on change
    donationType.addEventListener('change', toggleSpecified);

    // Run once on page load
    toggleSpecified();
});
</script>
-->

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
