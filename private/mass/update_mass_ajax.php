<?php
session_start();
include "../public/connection.php";

// Make sure required POST data exists
if(isset($_POST['id'], $_POST['time'])) {
    $id = $_POST['id'];
    $time = $_POST['time'];

    // Update query
    $stmt = $conn->prepare("UPDATE mass_schedule SET mass_time=? WHERE id=?");
    $stmt->bind_param("si", $time, $id);

    if($stmt->execute()){
        echo json_encode(["status"=>"success"]);
    } else {
        http_response_code(500);
        echo json_encode(["status"=>"error", "message"=>"DB update failed"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status"=>"error", "message"=>"Missing fields"]);
}

$conn->close();
?>
