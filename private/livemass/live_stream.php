<?php
require_once __DIR__ . '/../../snippets/connection.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM live_streams");
    header("Location: live_mass.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	$embed = trim($_POST['live_embed']);

	if (empty($embed)) {
		header("Location: live_mass.php");
		exit;
	}

	$conn->query("DROP TABLE IF EXISTS live_streams");
	$conn->query("CREATE TABLE live_streams (
		id INT AUTO_INCREMENT PRIMARY KEY,
		embed TEXT NOT NULL,
		created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
	)");

	$conn->query("DELETE FROM live_streams");

	$stmt = $conn->prepare("INSERT INTO live_streams (embed) VALUES (?)");
	$stmt->bind_param("s", $embed);
	$stmt->execute();

	header("Location: live_mass.php?success=1");
	exit;
}
