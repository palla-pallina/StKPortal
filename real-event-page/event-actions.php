<?php
require_once "dbconfig.php";

// ADD EVENT
if (isset($_POST['add'])) {
    $stmt = $dbh->prepare("INSERT INTO events (date, text) VALUES (:date, :text)");
    $stmt->execute([
        ':date' => $_POST['date'],
        ':text' => $_POST['text']
    ]);
    header("Location: admin-events.php");
    exit;
}

// EDIT EVENT
if (isset($_POST['edit'])) {
    $stmt = $dbh->prepare("UPDATE events SET date=:date, text=:text WHERE id=:id");
    $stmt->execute([
        ':date' => $_POST['date'],
        ':text' => $_POST['text'],
        ':id' => $_POST['id']
    ]);
    header("Location: admin-events.php");
    exit;
}

// DELETE EVENT
if (isset($_GET['delete'])) {
    $stmt = $dbh->prepare("DELETE FROM events WHERE id=:id");
    $stmt->execute([':id' => $_GET['delete']]);
    header("Location: admin-events.php");
    exit;
}

?>