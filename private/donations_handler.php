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
        $donor_email = $_POST['donor_email'] ?? '';
        $donor_contact_no = $_POST['donor_contact_no'] ?? '';
        $donation_type = $_POST['donation_type'] ?? '';
        $donation_specified = $_POST['donation_specified'] ?? null;
        $donation_cause = $_POST['donation_cause'] ?? '';
        $mode_of_payment = $_POST['mode_of_payment'] ?? '';
        $donation_amount = $_POST['donation_amount'] ?? null;
        $proof_of_donation = null;

        // Handle file upload
        if (isset($_FILES['proof_of_donation']) && $_FILES['proof_of_donation']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'donation_proof/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $ext = pathinfo($_FILES['proof_of_donation']['name'], PATHINFO_EXTENSION);
            $file_name = 'IMG_' . date('Ymd_His') . '_' . uniqid() . '.' . $ext;
            $upload_path = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['proof_of_donation']['tmp_name'], $upload_path)) {
                $proof_of_donation = $file_name;
            }
        }

        $sql = "INSERT INTO donations 
                (donor_email, donor_contact_no, donation_type, donation_specified, donation_cause, mode_of_payment, proof_of_donation, donation_amount) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$donor_email, $donor_contact_no, $donation_type, $donation_specified, $donation_cause, $mode_of_payment, $proof_of_donation, $donation_amount]);

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
        $sort = $_POST['sort'] ?? 'donate_id DESC';
        $page = $_POST['page'] ?? 1;
        $filterType = $_POST['filter_type'] ?? '';
        $filterCause = $_POST['filter_cause'] ?? '';
        $filterPayment = $_POST['filter_payment'] ?? '';
        $limit = 8;
        $offset = ($page - 1) * $limit;

        $conditions = [];
        $params = [];

        // Search by email or contact
        if (!empty($search)) {
            $conditions[] = "(donor_email LIKE ? OR donor_contact_no LIKE ?)";
            $search_param = "%$search%";
            $params[] = $search_param;
            $params[] = $search_param;
        }

        if (!empty($filterType)) {
            $conditions[] = "donation_type = ?";
            $params[] = $filterType;
        }
        if (!empty($filterCause)) {
            $conditions[] = "donation_cause = ?";
            $params[] = $filterCause;
        }
        if (!empty($filterPayment)) {
            $conditions[] = "mode_of_payment = ?";
            $params[] = $filterPayment;
        }

        $where = count($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Whitelist sort columns to prevent SQL injection
        $allowedSort = [
            'donate_id DESC','donate_id ASC',
            'donation_amount DESC','donation_amount ASC',
            'donation_type ASC','donation_cause ASC','mode_of_payment ASC'
        ];
        if (!in_array($sort, $allowedSort)) $sort = 'donate_id DESC';

        // Count total
        $count_sql = "SELECT COUNT(*) as total FROM donations $where";
        $count_stmt = $pdo->prepare($count_sql);
        $count_stmt->execute($params);
        $total = $count_stmt->fetch()['total'];

        // Fetch donations
        $sql = "SELECT * FROM donations $where ORDER BY $sort LIMIT $limit OFFSET $offset";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $donations = $stmt->fetchAll();

        // Total amount
        $amount_sql = "SELECT SUM(donation_amount) as total_amount FROM donations $where";
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
