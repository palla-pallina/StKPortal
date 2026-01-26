<?php
require_once '../connection.php';
$data = $_POST;

if(empty($data['date']) || empty($data['service_type'])){
    echo json_encode(['success'=>false,'message'=>"Date and Service Type required"]); exit;
}

$id = $data['id'] ?? null;
$is_enabled = isset($data['is_enabled']) ? (int)$data['is_enabled'] : 1;
$max_slots = $data['max_slots'] ?? 15;

if($id){ // update
    $stmt = $conn->prepare("UPDATE calendar_control SET date=?, time=?, service_type=?, type=?, is_enabled=?, max_slots=?, note=? WHERE id=?");
    $stmt->bind_param("ssssiisi",$data['date'],$data['time'],$data['service_type'],$data['type'],$is_enabled,$max_slots,$data['note'],$id);
}else{ // insert
    $stmt = $conn->prepare("INSERT INTO calendar_control(date,time,service_type,type,is_enabled,max_slots,note) VALUES(?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssis",$data['date'],$data['time'],$data['service_type'],$data['type'],$is_enabled,$max_slots,$data['note']);
}

$stmt->execute();
echo json_encode(['success'=>true]);
