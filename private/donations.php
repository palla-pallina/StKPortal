<?php
require_once 'config.php';

session_start();

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}
$admin_name = !empty($_SESSION['givenname']) ? $_SESSION['givenname'] : $_SESSION['username'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            addDonation();
            break;
        case 'update_status':
            updateDonationStatus();
            break;
        case 'delete':
            deleteDonation();
            break;
        case 'get_donations':
            getDonations();
            break;
        default:
            echo json_encode(['error' => 'Invalid action']);
    }
}

function addDonation() {
    global $pdo;
    
    try {
        $username = $_POST['username'] ?? '';
        $donor_fullname = $_POST['donor_fullname'] ?? '';
        $donor_email = $_POST['donor_email'] ?? '';
        $donor_contact_no = $_POST['donor_contact_no'] ?? '';
        $donation_categ = $_POST['donation_categ'] ?? '';
        $donation_specified = $_POST['donation_specified'] ?? '';
        $donation_type = $_POST['donation_type'] ?? '';
        $mode_of_payment = $_POST['mode_of_payment'] ?? '';
        $proof_of_donation = $_POST['proof_of_donation'] ?? '';
        $donation_amount = $_POST['donation_amount'] ?? null;
        
        // Handle file upload for proof of donation
        if (isset($_FILES['proof_file']) && $_FILES['proof_file']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES['proof_file']['name'], PATHINFO_EXTENSION);
            $file_name = 'IMG_' . date('Ymd_His') . '_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['proof_file']['tmp_name'], $upload_path)) {
                $proof_of_donation = $file_name;
            }
        }
        
        $user_sql = "SELECT user_id FROM users WHERE username = ?";
        $user_stmt = $pdo->prepare($user_sql);
        $user_stmt->execute([$username]);
        $user = $user_stmt->fetch();
        
        $user_id = null;
        if ($user) {
            $user_id = $user['user_id'];
        } else {
            $create_user_sql = "INSERT INTO users (username, email, full_name) VALUES (?, ?, ?)";
            $create_user_stmt = $pdo->prepare($create_user_sql);
            $create_user_stmt->execute([$username, $donor_email, $donor_fullname]);
            $user_id = $pdo->lastInsertId();
        }
        
        $sql = "INSERT INTO donations (user_id, donor_fullname, donor_email, donor_contact_no, donation_categ, donation_specified, donation_type, mode_of_payment, proof_of_donation, donation_amount) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $donor_fullname, $donor_email, $donor_contact_no, $donation_categ, $donation_specified, $donation_type, $mode_of_payment, $proof_of_donation, $donation_amount]);
        
        echo json_encode(['success' => true, 'message' => 'Donation added successfully']);
        
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function updateDonationStatus() {
    global $pdo;
    
    try {
        $donate_id = $_POST['donate_id'] ?? '';
        $status = $_POST['status'] ?? '';
        
        $sql = "UPDATE donations SET donation_status = ? WHERE donate_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$status, $donate_id]);
        
        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
        
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function deleteDonation() {
    global $pdo;
    
    try {
        $donate_id = $_POST['donate_id'] ?? '';
        
        $sql = "DELETE FROM donations WHERE donate_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$donate_id]);
        
        echo json_encode(['success' => true, 'message' => 'Donation deleted successfully']);
        
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

function getDonations() {
    global $pdo;
    
    try {
        $search = $_POST['search'] ?? '';
        $sort = $_POST['sort'] ?? 'date_posted DESC';
        $page = $_POST['page'] ?? 1;
        $filterDonationCateg = $_POST['filter_donation_categ'] ?? '';
        $filterDonationType = $_POST['filter_donation_type'] ?? '';
        $limit = 8;
        $offset = ($page - 1) * $limit;
        
        $conditions = [];
        $params = [];
        
        if (!empty($search)) {
            $conditions[] = "(d.donor_fullname LIKE ? OR d.donor_email LIKE ? OR d.donation_type LIKE ? OR u.username LIKE ?)";
            $search_param = "%$search%";
            array_push($params, $search_param, $search_param, $search_param, $search_param);
        }
        if (!empty($filterDonationCateg)) {
            $conditions[] = "d.donation_categ = ?";
            $params[] = $filterDonationCateg;
        }
        if (!empty($filterDonationType)) {
            $conditions[] = "d.donation_type = ?";
            $params[] = $filterDonationType;
        }
        $where_clause = count($conditions) ? ('WHERE ' . implode(' AND ', $conditions)) : '';       
        $count_sql = "SELECT COUNT(*) as total FROM donations d LEFT JOIN users u ON d.user_id = u.user_id $where_clause";
        $count_stmt = $pdo->prepare($count_sql);
        $count_stmt->execute($params);
        $total = $count_stmt->fetch()['total'];       
        $sort_field = $sort;
        if (strpos($sort, 'username') !== false) {
            $sort_field = str_replace('username', 'u.username', $sort);
        } else {
            $sort_field = str_replace(['date_posted', 'donor_fullname', 'donation_amount'], ['d.date_posted', 'd.donor_fullname', 'd.donation_amount'], $sort);
        }
        
        $sql = "SELECT d.*, u.username FROM donations d LEFT JOIN users u ON d.user_id = u.user_id $where_clause ORDER BY $sort_field LIMIT $limit OFFSET $offset";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $donations = $stmt->fetchAll();
        
        $amount_sql = "SELECT SUM(d.donation_amount) as total_amount FROM donations d LEFT JOIN users u ON d.user_id = u.user_id $where_clause";
        $amount_stmt = $pdo->prepare($amount_sql);
        $amount_stmt->execute($params);
        $total_amount = $amount_stmt->fetch()['total_amount'] ?? 0;
        
        echo json_encode([
            'success' => true,
            'donations' => $donations,
            'total' => $total,
            'total_amount' => number_format($total_amount, 2),
            'pages' => ceil($total / $limit),
            'current_page' => $page
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="../stylesheets/private/donations.css" />
<title>Donations</title>

</head>

<body>


<!-- SIDEBAR -->
<section id="sidebar" <?php if ($hideSidebar) echo 'class="hide"'; ?>>
<a href="#" class="label">
<img src="santa krus logo.png" alt="Logo" class="logo" />
</a>
<ul class="side-menu top">
<li><a href="dashboard.php"><span class="text">Dashboard</span></a></li>
<li><a href="stk/filemanager.php"><span class="text">File Manager</span></a></li>
<li><a href="stk/announcement.php"><span class="text">Announcement</span></a></li>
<li><a href="#"><span class="text">Event</span></a></li> <!-- N/A -->
<li><a href="stk/members.php"><span class="text">Members</span></a></li>
<li><a href="certificate/certificate.php"><span class="text">Certificates</span></a></li> <!-- N/A -->
<li><a href="sacraments/s_dashboard.php"><span class="text">Reservations</span></a></li>
<li class="active"><a href="donations.php" data-section="dashboard"><span class="text">Donations</span></a></li>
<li><a href="prayer_admin.php"><span class="text">Prayer Requests</span></a></li>
</ul>
<ul class="side-menu">
<li><a href="logout.php" class="logout"><span class="text">Logout</span></a></li>
</ul>
</section>
<!-- SIDEBAR END -->

<section id="content">
<nav>
<i class='bx bx-menu'></i>
<div id="datetime"></div>
<div class="profile">
<span class="greeting"><?php echo htmlspecialchars($admin_name); ?></span>
<a href="#" class="profile-link"><i class='bx bx-user'></i></a>
</div>
</nav>

<!-- DONATIONS SECTION -->
<div class="main-content">
<div class="donations-header">
<h1>Donations</h1>
<div class="donations-controls">
<div class="search-box">
<input type="text" id="searchDonations" placeholder="Search...">
<i class='bx bx-search'></i>
</div>
<div class="sort-box">
<select id="sortDonations">
<option value="date_posted DESC">Date (Newest)</option>
<option value="date_posted ASC">Date (Oldest)</option>
<option value="u.username ASC">Username (A-Z)</option>
<option value="u.username DESC">Username (Z-A)</option>
<option value="donor_fullname ASC">Name (A-Z)</option>
<option value="donor_fullname DESC">Name (Z-A)</option>
<option value="donation_amount DESC">Amount (High-Low)</option>
<option value="donation_amount ASC">Amount (Low-High)</option>
</select>
<i class='bx bx-filter-alt'></i>
</div>
<button class="add-btn" id="addDonationBtn">
<i class='bx bx-plus'></i>
</button>
</div>
</div>

<!-- Donations Modal -->
<div id="donationModal" class="modal">
<div class="modal-content">
<div class="modal-header">
<h2>Add New Donation</h2>
<span class="close">&times;</span>
</div>
<form id="donationForm" enctype="multipart/form-data">
<div class="form-row">
<div class="form-group">
<label for="username">Username</label>
<input type="text" id="username" name="username">
</div>
<div class="form-group">
<label for="donor_fullname">Full Name</label>
<input type="text" id="donor_fullname" name="donor_fullname" >
</div>
</div>
<div class="form-row">
<div class="form-group">
<label for="donor_email">Email</label>
<input type="email" id="donor_email" name="donor_email">
</div>
<div class="form-group">
<label for="donor_contact_no">Contact Number</label>
<input type="text" id="donor_contact_no" name="donor_contact_no">
</div>
</div>
<div class="form-row">
<div class="form-group">
<label for="donation_categ">Donation Category *</label> <!-- required field -->
<select id="donation_categ" name="donation_categ" required>
<option value="" disabled selected>Select Category</option>
<option value="Cash">Cash</option>
<option value="Clothes">Clothes</option>
<option value="Goods">Goods</option>
<option value="Specified">Specified</option>
</select>
</div>
<div class="form-group">
<label for="donation_type">Donation Type *</label> <!-- required field -->
<select id="donation_type" name="donation_type" required>
<option value="" disabled selected>Select Type</option>
<option value="Parish Donation">Parish Donation</option>
<option value="Tithes">Tithes</option>
<option value="Charity">Charity</option>
</select>
</div>
</div>
<div class="form-row">
<div class="form-group">
<label for="mode_of_payment">Mode of Payment *</label> <!-- required field -->
<select id="mode_of_payment" name="mode_of_payment" required>
<option value="" disabled selected>Select Payment Method</option>
<option value="Onsite">Onsite</option>
<option value="Gcash">GCash</option>
<option value="Bank Transfer">Bank Transfer</option>
</select>
</div>
<div class="form-group">
<label for="donation_amount">Amount (PHP)</label>
<input type="number" id="donation_amount" name="donation_amount" step="0.01" min="0">
</div>
</div>
<div class="form-row">
<div class="form-group form-group--full" id="specifiedGroup" style="display: none;">
<label for="donation_specified">Specify Donation</label>
<input type="text" id="donation_specified" name="donation_specified">
</div>
</div>
<div class="form-group">
<label for="proof_file">Proof of Donation</label>
<input type="file" id="proof_file" name="proof_file" accept="image/*,.pdf">
<small>Upload image or PDF file</small>
</div>
<div class="form-actions">
<button type="button" class="btn-cancel" onclick="closeDonationModal()">Cancel</button>
<button type="submit" class="btn-submit">Add Donation</button>
</div>
</form>
</div>
</div>

<!-- Donations Table -->
<div class="donations-filter-bar">
<div class="filter-groups">
<div class="filter-group">
<span class="filter-label">DONATION</span>
<select id="filterDonationCateg" class="filter-select">
<option value="">...</option>
<option value="Cash">Cash</option>
<option value="Clothes">Clothes</option>
<option value="Goods">Goods</option>
<option value="Specified">Specified</option>
</select>
</div>
<div class="filter-group">
<span class="filter-label">TYPE</span>
<select id="filterDonationType" class="filter-select">
<option value="">...</option>
<option value="Parish Donation">Parish Donation</option>
<option value="Tithes">Tithes</option>
<option value="Charity">Charity</option>
</select>
</div>
</div>
<div class="filter-total">
<span class="total-label">TOTAL</span>
<span class="currency">₱</span><span id="totalAmount">0.00</span>
</div>
</div>

<div class="donations-table-container">
<table class="donations-table">
<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Username</th>
<th>Email</th>
<th>Donation</th>
<th>Type</th>
<th>Mode of Payment</th>
<th>Proof</th>
<th>Date</th>
<th>Amount</th>
<th>Confirm</th>
<th>Action</th>
</tr>
</thead>
<tbody id="donationsTableBody">
<!-- JS will load donations here -->
</tbody>
</table>
<div class="pagination">
<div class="pagination-info">
<span id="paginationInfo">0 - 0 of 0 Entries</span>
</div>
<div class="pagination-controls">
<button id="prevPage" class="pagination-btn">← Previous</button>
<div id="pageNumbers" class="page-numbers"></div>
<button id="nextPage" class="pagination-btn">Next →</button>
</div>
</div>
</div>
</div>

</section>

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

// Donations functionality
let currentPage = 1;
let currentSearch = '';
let currentSort = 'date_posted DESC';
let currentFilterCateg = '';
let currentFilterType = '';

// Load donations
function loadDonations() {
    const loadingEl = document.getElementById('donationsLoading');
    if (loadingEl) loadingEl.classList.add('active');

    const formData = new FormData();
    formData.append('action', 'get_donations');
    formData.append('search', currentSearch);
    formData.append('sort', currentSort);
    formData.append('page', currentPage);
    formData.append('filter_donation_categ', currentFilterCateg);
    formData.append('filter_donation_type', currentFilterType);

    fetch('donations_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayDonations(data.donations);
            updatePagination(data.total, data.pages, data.current_page);
            document.getElementById('totalAmount').textContent = data.total_amount;
        } else {
            console.error('Error loading donations:', data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    })
    .finally(() => {
        if (loadingEl) loadingEl.classList.remove('active');
    });
}

// Display donations in table
function displayDonations(donations) {
    const tbody = document.getElementById('donationsTableBody');
    tbody.innerHTML = '';

    if (!donations || donations.length === 0) {
        const row = document.createElement('tr');
        const cell = document.createElement('td');
        cell.colSpan = 12;
        cell.className = 'no-data';
        cell.textContent = 'No donations found.';
        row.appendChild(cell);
        tbody.appendChild(row);
        return;
    }

    donations.forEach(donation => {
        const row = document.createElement('tr');
        row.classList.add('appear');

        const donationDisplay = donation.donation_categ === 'Specified'
        ? `${donation.donation_categ} - ${donation.donation_specified}`
        : donation.donation_categ;

        const amountDisplay = donation.donation_amount
        ? `P${parseFloat(donation.donation_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}`
        : '-';

        const dateDisplay = new Date(donation.date_posted).toISOString().split('T')[0];

        row.innerHTML = `
        <td>${donation.donate_id}</td>
        <td>${donation.donor_fullname || 'Anonymous'}</td>
        <td>${donation.username || '-'}</td>
        <td>${donation.donor_email || '-'}</td>
        <td>${donationDisplay}</td>
        <td>${donation.donation_type}</td>
        <td>${donation.mode_of_payment}</td>
        <td>${donation.proof_of_donation ? `<a href="uploads/${donation.proof_of_donation}" target="_blank" class="proof-link">${donation.proof_of_donation}</a>` : '-'}</td>
        <td>${dateDisplay}</td>
        <td>${amountDisplay}</td>
        <td class="confirm-column">
        ${donation.donation_status === 'pending' ? `
        <button class="btn-reject" onclick="updateDonationStatus(${donation.donate_id}, 'unverified')" title="Reject">
        <i class='bx bx-x'></i>
        </button>
        <button class="btn-approve" onclick="updateDonationStatus(${donation.donate_id}, 'verified')" title="Approve">
        <i class='bx bx-check'></i>
        </button>
        ` : donation.donation_status === 'verified' ? `
        <i class='bx bx-check' style="color: #16a34a; font-size: 18px;"></i>
        ` : `
        <i class='bx bx-x' style="color: #dc2626; font-size: 18px;"></i>
        `}
        </td>
        <td class="action-column">
        <button class="btn-delete" onclick="deleteDonation(${donation.donate_id})" title="Delete">
        <i class='bx bx-trash'></i>
        </button>
        </td>
        `;

        tbody.appendChild(row);
    });
}

// Update pagination
function updatePagination(total, pages, currentPage) {
    const start = (currentPage - 1) * 8 + 1;
    const end = Math.min(currentPage * 8, total);

    document.getElementById('paginationInfo').textContent = `${start} - ${end} of ${total} Entries`;

    const pageNumbers = document.getElementById('pageNumbers');
    pageNumbers.innerHTML = '';

    for (let i = 1; i <= pages; i++) {
        const button = document.createElement('button');
        button.textContent = i;
        if (i === currentPage) {
            button.classList.add('active');
        }
        button.onclick = () => goToPage(i);
        pageNumbers.appendChild(button);
    }

    document.getElementById('prevPage').disabled = currentPage === 1;
    document.getElementById('nextPage').disabled = currentPage === pages;
}

// Go to specific page
function goToPage(page) {
    currentPage = page;
    loadDonations();
}

// Update donation status
function updateDonationStatus(donateId, status) {
    const formData = new FormData();
    formData.append('action', 'update_status');
    formData.append('donate_id', donateId);
    formData.append('status', status);

    fetch('donations_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadDonations();
        } else {
            console.error('Error updating status:', data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Delete donation
function deleteDonation(donateId) {
    if (confirm('Are you sure you want to delete this donation?')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('donate_id', donateId);

        fetch('donations_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadDonations();
            } else {
                console.error('Error deleting donation:', data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

// Modal functions
function openDonationModal() {
    document.getElementById('donationModal').style.display = 'block';
}

function closeDonationModal() {
    document.getElementById('donationModal').style.display = 'none';
    document.getElementById('donationForm').reset();
    document.getElementById('specifiedGroup').style.display = 'none';
}

// Event listeners for donations
document.addEventListener('DOMContentLoaded', function() {
    // Add donation button
    const addBtn = document.getElementById('addDonationBtn');
    if (addBtn) {
        addBtn.addEventListener('click', openDonationModal);
    }

    // Close modal
    const closeBtn = document.querySelector('.close');
    if (closeBtn) {
        closeBtn.addEventListener('click', closeDonationModal);
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('donationModal');
        if (event.target === modal) {
            closeDonationModal();
        }
    });

    // Search functionality
    const searchInput = document.getElementById('searchDonations');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentSearch = this.value;
                currentPage = 1;
                loadDonations();
            }, 500);
        });
    }

    // Sort functionality
    const sortSelect = document.getElementById('sortDonations');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            currentSort = this.value;
            currentPage = 1;
            loadDonations();
        });
    }

    // Filter functionality
    const filterDonationCateg = document.getElementById('filterDonationCateg');
    const filterDonationType = document.getElementById('filterDonationType');
    if (filterDonationCateg) {
        filterDonationCateg.addEventListener('change', function() {
            currentFilterCateg = this.value;
            currentPage = 1;
            loadDonations();
        });
    }
    if (filterDonationType) {
        filterDonationType.addEventListener('change', function() {
            currentFilterType = this.value;
            currentPage = 1;
            loadDonations();
        });
    }

    // Pagination buttons
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');

    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            if (currentPage > 1) {
                goToPage(currentPage - 1);
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            goToPage(currentPage + 1);
        });
    }

    // Donation form submission
    const donationForm = document.getElementById('donationForm');
    if (donationForm) {
        donationForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.append('action', 'add');

            fetch('donations_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeDonationModal();
                    loadDonations();
                    alert('Donation added successfully!');
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the donation.');
            });
        });
    }

    // Handle specified donation category
    const categorySelect = document.getElementById('donation_categ');
    const specifiedGroup = document.getElementById('specifiedGroup');

    if (categorySelect && specifiedGroup) {
        categorySelect.addEventListener('change', function() {
            if (this.value === 'Specified') {
                specifiedGroup.style.display = 'block';
                document.getElementById('donation_specified').required = true;
            } else {
                specifiedGroup.style.display = 'none';
                document.getElementById('donation_specified').required = false;
            }
        });
    }
});

</script>
</body>
</html>