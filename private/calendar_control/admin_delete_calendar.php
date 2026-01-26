<?php
require_once '../connection.php';

$data = json_decode(file_get_contents('php://input'), true);

if(empty($data['id'])){
    echo json_encode(['success'=>false,'message'=>"ID missing"]);
    exit;
}

$id = $data['id'];

$stmt = $conn->prepare("DELETE FROM calendar_control WHERE id=?");
$stmt->bind_param("i",$id);
if($stmt->execute()){
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'message'=>"Delete failed"]);
}
