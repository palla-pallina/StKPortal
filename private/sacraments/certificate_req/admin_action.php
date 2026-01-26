<?php
require_once "pdo_connection.php";

$id = $_POST['id'] ?? null;
$action = $_POST['action'] ?? null;
$pickup_date = $_POST['pickup_date'] ?? null;

if (!$id || !$action) die("Invalid request.");

// Function to send notification
function sendNotification($pdo, $user_id, $title, $message, $type = "certificate") {
    $stmt = $pdo->prepare("
        INSERT INTO notifications (user_id, title, message, type, is_read, created_at)
        VALUES (:user_id, :title, :message, :type, 0, NOW())
    ");
    $stmt->execute([
        ":user_id" => $user_id,
        ":title" => $title,
        ":message" => $message,
        ":type" => $type
    ]);
}

if ($action === "accept") {

    if (strtotime($pickup_date) < strtotime(date("Y-m-d"))) {
        die("Pickup date cannot be in the past.");
    }

    // Update certificate request status and pickup date
    $stmt = $pdo->prepare("
        UPDATE certificate_request
        SET certificate_status='Accepted',
            pickup_date=:pickup_date
        WHERE cert_req_id=:id
    ");
    $stmt->execute([
        ":pickup_date" => $pickup_date,
        ":id" => $id
    ]);

    // Get the user_id and requester name for notification
    $stmt = $pdo->prepare("SELECT user_id, request_firstname, request_lastname FROM certificate_request WHERE cert_req_id=:id");
    $stmt->execute([":id" => $id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($request) {
        $user_id = $request['user_id'];
        $name = $request['request_firstname'] . ' ' . $request['request_lastname'];
        $title = "Certificate Ready for Pickup";
        $message = "Hello $name, your certificate will be ready for pickup on $pickup_date.";
        sendNotification($pdo, $user_id, $title, $message);
    }

} elseif ($action === "decline") {

    $stmt = $pdo->prepare("
        UPDATE certificate_request
        SET certificate_status='Declined'
        WHERE cert_req_id=:id
    ");
    $stmt->execute([":id" => $id]);
}

header("Location: certificate_request.php");
exit;
