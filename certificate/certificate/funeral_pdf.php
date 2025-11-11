<?php
require_once 'dbconfig.php';
require_once __DIR__ . '/vendor/autoload.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    die("No ID provided.");
}

// Fetch reservation
$stmt = $dbh->prepare("SELECT * FROM funeral_reservation WHERE id = :id");
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Reservation not found.");
}

// Build PDF content using deceased_fullname
$pdfContent = "";
$pdfContent .= "<h1>Funeral Reservation Details</h1>";
$pdfContent .= "<strong>Deceased Name:</strong> " . $data['deceased_fullname'] . "<br>";
$pdfContent .= "<strong>Cause of Death:</strong> " . $data['cause_of_death'] . "<br>";
$pdfContent .= "<strong>Birthdate:</strong> " . $data['deceased_dob'] . "<br>";
$pdfContent .= "<strong>Funeral Date:</strong> " . $data['funeral_date'] . "<br>";
$pdfContent .= "<strong>Funeral Time:</strong> " . $data['funeral_time'] . "<br>";
$pdfContent .= "<strong>Funeral Address:</strong> " . $data['funeral_address'] . "<br>";
$pdfContent .= "<strong>Contact Person:</strong> " . $data['contact_person_firstname'] . " " . $data['contact_person_lastname'] . "<br>";
$pdfContent .= "<strong>Relation to Deceased:</strong> " . $data['contact_person_relation'] . "<br>";
$pdfContent .= "<strong>Contact Number:</strong> " . $data['contact_person_contact_no'] . "<br>";
$pdfContent .= "<strong>Email:</strong> " . $data['contact_person_email'] . "<br>";

// Generate PDF
$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($pdfContent);
$mpdf->Output("funeral_reservation_$id.pdf", "D");
?>
