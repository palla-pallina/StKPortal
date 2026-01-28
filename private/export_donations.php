<?php
require_once 'config.php';

// Force download as Excel-compatible CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=donations_' . date('Y-m-d') . '.csv');

// Open output stream
$output = fopen('php://output', 'w');

// CSV HEADER ROW
fputcsv($output, [
    'ID',
    'Contact Number',
    'Donation Type',
    'Donation Cause',
    'Mode of Payment',
    'Amount',
    'Status',
    'Date'
]);

// Fetch donations
$sql = "SELECT donate_id, donor_contact_no, donation_type, donation_cause,
               mode_of_payment, donation_amount, donation_status, date_posted
        FROM donations
        ORDER BY donate_id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    // RULE: Clothes / Goods → blank amount
    $amount = '';
    if (!in_array($row['donation_type'], ['Clothes', 'Goods'])) {
        $amount = $row['donation_amount'];
    }

    fputcsv($output, [
        $row['donate_id'],
        $row['donor_contact_no'],
        $row['donation_type'],
        $row['donation_cause'],
        $row['mode_of_payment'],
        $amount,
        $row['donation_status'],
        $row['date_posted']
    ]);
}

fclose($output);
exit;
