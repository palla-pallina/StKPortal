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
<title>Donations</title>

<style>
    
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

a {
    text-decoration: none;
}

li {
    list-style: none;
}

:root {
    --poppins: 'Poppins', sans-serif;
    --lato: 'Lato', sans-serif;

    --light: #F9F9F9;
    --blue: #3C91E6;
    --grey: #eee;
    --dark-grey: #AAAAAA;
    --dark: #342E37;
    --red: #DB504A;
    --green: #418033;
    --success: #16a34a;
    --warning: #f59e0b;
    --danger: #dc2626;
}

html, body {
    height: 100%;
    overflow-x: hidden;
}

body.dark {
    --light: #0C0C1E;
    --grey: #060714;
    --dark: #FBFBFB;
}

body {
    background: var(--grey);
    font-family: var(--lato);
}

#sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 280px;
    height: 100%;
    background: var(--light);
    z-index: 2000;
    transition: .3s ease;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 0;
}

#sidebar.hide {
    width: 0 !important;
    overflow: hidden;
    pointer-events: none;
}

#sidebar .label {
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding-top: 20px;
}

#sidebar .label .logo {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

#sidebar .side-menu { width: 100%; }
#sidebar .side-menu li { height: 48px; }
#sidebar .side-menu li a {
    display: flex; align-items: center; justify-content: center;
    height: 100%; font-size: 16px; color: var(--dark); text-decoration: none;
}
#sidebar .side-menu li.active {
    background: var(--grey);
}

#content {
    position: relative;
    width: calc(100% - 280px);
    left: 280px;
    transition: .3s ease;
    min-width: 0;
}

#sidebar.hide ~ #content {
    width: 100%;
    left: 0;
}

#content nav {
    height: 56px;
    background-color: #418033;
    padding: 0 15px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    grid-gap: 16px;
    position: sticky;
    top: 0;
    z-index: 1000;
}

#content nav .bx {
    font-size: 24px;
    color: var(--dark);
    cursor: pointer;
}

#content nav a {
    color: var(--dark);
}

#content nav .nav-link {
    font-size: 16px;
    transition: .3s ease;
}

#content nav #datetime {
    color: white;
    font-size: 14px;
    font-weight: 500;
    letter-spacing: 1px;
    margin-left: 16px;
    flex-grow: 1;
}

#content nav .profile {
    display: flex;
    align-items: center;
    gap: 10px;
}

#content nav .profile .greeting {
    font-size: 14px;
    color: var(--light);
    font-weight: 500;
}

#content nav .profile-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #ffffff;
    font-size: 24px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

#content nav .profile-link:hover {
    background-color: #9fecd3;
    transform: scale(1.05);
}

.content-section {
    display: none;
    padding: 20px;
    min-height: calc(100vh - 56px);
}

.content-section.active {
    display: block;
}

.main-content {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    box-sizing: border-box;
}

.main-content h1 {
    font-family: var(--poppins);
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 16px;
}

.main-content h2 {
    font-family: var(--poppins);
    font-size: 18px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 12px;
}

@media screen and (max-width: 768px) {
    #sidebar {
        width: 200px;
    }
    #content {
        width: calc(100% - 200px);
        left: 200px;
    }
    #content nav .nav-link {
        display: none;
    }
}

@media screen and (max-width: 576px) {
    #content nav .nav-link {
        display: none;
    }
}

.donations-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
}

.donations-header h1 {
    margin: 0;
    font-size: 32px;
    font-weight: 700;
    color: var(--dark);
}

.donations-controls {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

.search-box {
    position: relative;
    display: flex;
    align-items: center;
}

.search-box input {
    padding: 8px 12px 8px 40px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    width: 200px;
}

.search-box i {
    position: absolute;
    left: 12px;
    color: #666;
    font-size: 16px;
}

.sort-box {
    position: relative;
    display: flex;
    align-items: center;
}

.sort-box select {
    padding: 8px 12px 8px 40px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    background: white;
    cursor: pointer;
}

.sort-box i {
    position: absolute;
    left: 12px;
    color: #666;
    font-size: 16px;
}

.add-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--green);
    color: white;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    transition: all 0.3s ease;
}

.add-btn:hover {
    background: #2d5a25;
    transform: scale(1.05);
}

.total-amount {
    font-size: 16px;
    font-weight: 600;
    color: var(--dark);
    background: #f8f9fa;
    padding: 8px 16px;
    border-radius: 6px;
    border: 1px solid #e9ecef;
}

.donations-filter-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 4px 16px 4px;
    margin-bottom: 8px;
    border-top: 2px solid #e9ecef;
}

.donations-filter-bar .filter-groups {
    display: flex;
    align-items: center;
    gap: 24px;
}

.donations-filter-bar .filter-group {
    display: flex;
    align-items: center;
    gap: 8px;
}

.donations-filter-bar .filter-label {
    font-weight: 700;
    color: var(--dark);
    font-family: var(--poppins);
    letter-spacing: 0.3px;
}

.donations-filter-bar .filter-select {
    min-width: 140px;
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #e1e5e9;
    background: #eceff1;
    color: #333;
    font-size: 14px;
}

.donations-filter-bar .filter-total {
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 700;
    color: var(--dark);
}

.donations-filter-bar .filter-total .currency {
    font-weight: 700;
}

@media screen and (max-width: 768px) {
    .donations-filter-bar {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }
    .donations-filter-bar .filter-groups {
        justify-content: flex-start;
        flex-wrap: wrap;
        gap: 12px 16px;
    }
    .donations-filter-bar .filter-total {
        justify-content: flex-start;
    }
}

.modal {
    display: none;
    position: fixed;
    z-index: 3000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background-color: white;
    margin: 5% auto;
    padding: 0;
    border-radius: 12px;
    width: 90%;
    max-width: 600px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e9ecef;
    background: #f8f9fa;
    border-radius: 12px 12px 0 0;
}

.modal-header h2 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: var(--dark);
}

.close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 1;
}

.close:hover {
    color: var(--dark);
}

#donationForm {
    padding: 24px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group--full {
    grid-column: 1 / -1;
}

.form-group label {
    font-size: 14px;
    font-weight: 500;
    color: var(--dark);
    margin-bottom: 6px;
}

.form-group input,
.form-group select {
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: var(--green);
}

.form-group small {
    font-size: 12px;
    color: #666;
    margin-top: 4px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid #e9ecef;
}

.btn-cancel,
.btn-submit {
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    border: none;
    transition: all 0.3s ease;
}

.btn-cancel {
    background: #f8f9fa;
    color: #666;
    border: 1px solid #ddd;
}

.btn-cancel:hover {
    background: #e9ecef;
}

.btn-submit {
    background: var(--green);
    color: white;
}

.btn-submit:hover {
    background: #2d5a25;
}

.donations-table-container {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
}

.donations-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
    font-family: var(--poppins);
    table-layout: fixed;
}

.donations-table thead {
    background: #f1d3a8;
}

.donations-table th {
    padding: 14px 8px;
    text-align: left;
    font-weight: 600;
    color: var(--dark);
    border-bottom: 2px solid #e9ecef;
    font-size: 12px;
    position: relative;
    white-space: normal;
    word-wrap: break-word;
    line-height: 1.2;
}

.donations-table th:nth-child(1) { width: 4%; }
.donations-table th:nth-child(2) { width: 8%; }
.donations-table th:nth-child(3) { width: 7%; }
.donations-table th:nth-child(4) { width: 10%; }
.donations-table th:nth-child(5) { width: 7%; }
.donations-table th:nth-child(6) { width: 8%; }
.donations-table th:nth-child(7) { width: 11%; }
.donations-table th:nth-child(8) { width: 11%; }
.donations-table th:nth-child(9) { width: 8%; }
.donations-table th:nth-child(10) { width: 8%; }
.donations-table th:nth-child(11) { width: 6%; }
.donations-table th:nth-child(12) { width: 6%; }



.donations-table td {
    padding: 14px 8px;
    border-bottom: 1px solid #f1f3f4;
    vertical-align: middle;
    position: relative;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 150px;
}

.donations-table td:hover {
    overflow: visible;
    white-space: normal;
    word-wrap: break-word;
    z-index: 10;
    background: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.donations-table tbody tr:hover {
    background: #f8f9fa;
}

.donations-table .status-pending {
    color: #f59e0b;
    font-weight: 500;
}

.donations-table .status-verified {
    color: #16a34a;
    font-weight: 500;
}

.donations-table .status-unverified {
    color: #dc2626;
    font-weight: 500;
}

.donations-table .action-buttons {
    display: flex;
    gap: 8px;
    align-items: center;
    justify-content: center;
    min-width: 40px;
}

.donations-table td:last-child {
    text-align: center;
    width: 60px;
}

.donations-table th:last-child {
    text-align: center;
    width: 60px;
}

.donations-table td:nth-last-child(2) {
    text-align: center;
    width: 80px;
    vertical-align: middle;
}

.donations-table th:nth-last-child(2) {
    text-align: center;
    width: 80px;
}

.donations-table td:nth-last-child(2) i {
    display: block;
    margin: 0 auto;
}

.donations-table .confirm-column {
    text-align: center;
    vertical-align: middle;
}

.confirm-column .btn-approve,
.confirm-column .btn-reject {
    display: inline-block !important;
    margin: 0 1px;
    float: none;
    vertical-align: middle;
}

.donations-table .action-column {
    text-align: center;
    vertical-align: middle;
}

.donations-table .btn-approve,
.donations-table .btn-reject,
.donations-table .btn-delete {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.confirm-column .btn-approve,
.confirm-column .btn-reject {
    display: inline-flex !important;
    margin: 0 1px;
    float: none;
    vertical-align: middle;
}

.donations-table .btn-approve {
    background: #16a34a;
    color: white;
}

.donations-table .btn-reject {
    background: #dc2626;
    color: white;
}

.donations-table .btn-delete {
    background: #dc2626;
    color: white;
    position: relative;
    z-index: 1;
    margin: 0 auto;
}

.donations-table .btn-approve:hover,
.donations-table .btn-reject:hover,
.donations-table .btn-delete:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.donations-table .proof-link {
    color: #3C91E6;
    text-decoration: underline;
    font-size: 12px;
    font-weight: 500;
}

.donations-table .proof-link:hover {
    color: #2563eb;
    text-decoration: none;
}

.pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 24px;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.pagination-info {
    font-size: 14px;
    color: #666;
    font-weight: 500;
}

.pagination-controls {
    display: flex;
    align-items: center;
    gap: 8px;
}

.pagination-btn {
    padding: 8px 16px;
    border: 1px solid #ddd;
    background: white;
    color: #666;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.pagination-btn:hover:not(:disabled) {
    background: #6b7280;
    color: white;
    border-color: #6b7280;
}

.pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.page-numbers {
    display: flex;
    gap: 4px;
}

.page-numbers button {
    width: 36px;
    height: 36px;
    border: 1px solid #ddd;
    background: white;
    color: #666;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.page-numbers button.active {
    background: #6b7280;
    color: white;
    border-color: #6b7280;
}

.page-numbers button:hover:not(.active) {
    background: #f8f9fa;
}

@media screen and (max-width: 768px) {
    .donations-header {
        flex-direction: column;
        align-items: stretch;
    }

    .donations-controls {
        justify-content: space-between;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .donations-table {
        font-size: 11px;
    }

    .donations-table th,
    .donations-table td {
        padding: 6px 4px;
    }
}

@media screen and (max-width: 768px) {
    .donations-table th,
    .donations-table td {
        padding: 8px 4px;
    }

    .pagination {
        flex-direction: column;
        gap: 12px;
    }
}

</style>
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
<li><a href="booking_sacraments.php"><span class="text">Reservations</span></a></li>
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
<input type="text" id="donor_fullname" name="donor_fullname" required>
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