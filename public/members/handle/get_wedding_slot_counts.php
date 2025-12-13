<?php
require_once '../connection.php'; // adjust path if needed

header('Content-Type: application/json');

// Validate date
if (!isset($_GET['date']) || empty($_GET['date'])) {
    echo json_encode([
        'batch' => 0,
        'solo'  => 0,
        'error' => 'No date provided'
    ]);
    exit;
}

$date = $_GET['date'];

// Basic date format validation (YYYY-MM-DD)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode([
        'batch' => 0,
        'solo'  => 0,
        'error' => 'Invalid date format'
    ]);
    exit;
}

try {

    /* ================= SOLO COUNT ================= */
    $stmtSolo = $dbh->prepare("
        SELECT COUNT(*) 
        FROM weddings 
        WHERE date = :date 
        AND wedding_type = 'solo'
        AND status != 'cancelled'
    ");
    $stmtSolo->execute([':date' => $date]);
    $soloCount = (int) $stmtSolo->fetchColumn();

    /* ================= BATCH COUNT ================= */
    $stmtBatch = $dbh->prepare("
        SELECT COUNT(*) 
        FROM weddings 
        WHERE date = :date 
        AND wedding_type = 'batch'
        AND status != 'cancelled'
    ");
    $stmtBatch->execute([':date' => $date]);
    $batchCount = (int) $stmtBatch->fetchColumn();

    /* ================= RESPONSE ================= */
    echo json_encode([
        'solo'  => $soloCount,
        'batch' => $batchCount
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'solo'  => 0,
        'batch' => 0,
        'error' => 'Database error'
    ]);
}
