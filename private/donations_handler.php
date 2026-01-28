<?php
header('Content-Type: application/json');

require_once 'config.php'; // MUST define $pdo (PDO)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        addDonation();
        break;

    case 'get_donations':
        getDonations();
        break;

    case 'update_status':
        updateDonationStatus();
        break;

    case 'delete':
        deleteDonation();
        break;

    case 'get_donation_by_id':
        getDonationById();
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}

/* ================================
   ADD DONATION
================================ */
function addDonation() {
    global $pdo;

    try {
        $donor_email        = $_POST['donor_email'] ?? null;
        $donor_contact_no   = $_POST['donor_contact_no'] ?? null;
        $donation_type      = $_POST['donation_type'] ?? '';
        $donation_specified = $_POST['donation_specified'] ?? null;
        $donation_cause     = $_POST['donation_cause'] ?? '';
        $mode_of_payment    = $_POST['mode_of_payment'] ?? '';
        $donation_amount    = $_POST['donation_amount'] ?? 0;

        $proof = null;
        if (!empty($_FILES['proof_of_donation']['name'])) {
            $dir = 'donation_proof/';
            if (!is_dir($dir)) mkdir($dir, 0777, true);

            $ext = pathinfo($_FILES['proof_of_donation']['name'], PATHINFO_EXTENSION);
            $proof = uniqid('don_') . '.' . $ext;
            move_uploaded_file($_FILES['proof_of_donation']['tmp_name'], $dir . $proof);
        }

        $sql = "INSERT INTO donations (
            donor_email,
            donor_contact_no,
            donation_type,
            donation_specified,
            donation_cause,
            mode_of_payment,
            proof_of_donation,
            donation_amount,
            donation_status,
            date_posted
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";

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
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

/* ================================
   FETCH DONATIONS (TABLE)
================================ */
function getDonations() {
    global $pdo;

    try {
        $search        = $_POST['search'] ?? '';
        $sort          = $_POST['sort'] ?? 'donate_id DESC';
        $page          = (int)($_POST['page'] ?? 1);
        $filterType    = $_POST['filter_type'] ?? '';
        $filterCause   = $_POST['filter_cause'] ?? '';
        $filterPayment = $_POST['filter_payment'] ?? '';

        $limit  = 8;
        $offset = ($page - 1) * $limit;

        $conditions = [];
        $params = [];

        if ($search) {
            $conditions[] = "(donor_email LIKE ? OR donor_contact_no LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if ($filterType) {
            $conditions[] = "donation_type = ?";
            $params[] = $filterType;
        }

        if ($filterCause) {
            $conditions[] = "donation_cause = ?";
            $params[] = $filterCause;
        }

        if ($filterPayment) {
            $conditions[] = "mode_of_payment = ?";
            $params[] = $filterPayment;
        }

        $where = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";

        // Whitelist ORDER BY
        $allowedSort = [
            'donate_id DESC','donate_id ASC',
            'donation_amount DESC','donation_amount ASC',
            'donation_type ASC','donation_cause ASC',
            'mode_of_payment ASC'
        ];
        if (!in_array($sort, $allowedSort)) {
            $sort = 'donate_id DESC';
        }

        // Total count
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM donations $where");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Fetch rows
        $sql = "SELECT * FROM donations
                $where
                ORDER BY $sort
                LIMIT $limit OFFSET $offset";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // TOTAL: include ONLY verified donations
        $sumWhere = $where
            ? ($where . " AND LOWER(TRIM(donation_status)) = 'verified'")
            : "WHERE LOWER(TRIM(donation_status)) = 'verified'";

        $sumStmt = $pdo->prepare("SELECT COALESCE(SUM(donation_amount), 0) FROM donations $sumWhere");
        $sumStmt->execute($params);
        $total_amount = (float) $sumStmt->fetchColumn();


        echo json_encode([
            'success' => true,
            'donations' => $donations,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page,
            'total_amount' => number_format($total_amount, 2)
        ]);
    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

/* ================================
   GET SINGLE DONATION
================================ */
function getDonationById() {
    global $pdo;

    $id = $_POST['donate_id'] ?? 0;

    $stmt = $pdo->prepare("SELECT * FROM donations WHERE donate_id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        echo json_encode(['success' => true, 'data' => $row]);
    } else {
        echo json_encode(['success' => false]);
    }
}

/* ================================
   UPDATE STATUS
================================ */
function updateDonationStatus() {
    global $pdo;

    $stmt = $pdo->prepare(
        "UPDATE donations SET donation_status = ? WHERE donate_id = ?"
    );
    $stmt->execute([
        $_POST['status'],
        $_POST['donate_id']
    ]);

    echo json_encode(['success' => true]);
}

/* ================================
   DELETE DONATION
================================ */
function deleteDonation() {
    global $pdo;

    $stmt = $pdo->prepare("DELETE FROM donations WHERE donate_id = ?");
    $stmt->execute([$_POST['donate_id']]);

    echo json_encode(['success' => true]);
}
