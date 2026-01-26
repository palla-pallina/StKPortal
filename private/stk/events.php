<?php
require_once '../dbconfig.php';

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

// ================= GET EVENTS ==================
if ($method === "GET") {

    if (!isset($_GET['date'])) {
        echo json_encode(["error" => "Missing date"]);
        exit;
    }

    $date = $_GET['date'];

    $stmt = $dbh->prepare("SELECT id, text FROM events WHERE date = :date ORDER BY id DESC");
    $stmt->bindParam(":date", $date);
    $stmt->execute();

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

// ================= CREATE EVENT ==================
if ($method === "POST") {

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['date']) || !isset($data['text'])) {
        echo json_encode(["error" => "Missing data"]);
        exit;
    }

    $date = $data['date'];
    $text = $data['text'];

    $stmt = $dbh->prepare("INSERT INTO events (date, text) VALUES (:date, :text)");
    $stmt->bindParam(":date", $date);
    $stmt->bindParam(":text", $text);
    $stmt->execute();

    echo json_encode(["success" => true, "id" => $dbh->lastInsertId()]);
    exit;
}

// ================= DELETE EVENT ==================
if ($method === "DELETE") {

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'])) {
        echo json_encode(["error" => "Missing id"]);
        exit;
    }

    $id = intval($data['id']);

    $stmt = $dbh->prepare("DELETE FROM events WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    echo json_encode(["success" => true]);
    exit;
}

echo json_encode(["error" => "Invalid request"]);
exit;
