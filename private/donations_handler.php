<?php
require_once 'config.php';
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
