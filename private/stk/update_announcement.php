<?php
require_once 'dbconfig.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $ann_id = intval($_POST['ann_id']);
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    // First, fetch current announcement to get existing image
    $stmt = $dbh->prepare("SELECT image_path FROM announcements WHERE ann_id = :id");
    $stmt->bindParam(':id', $ann_id, PDO::PARAM_INT);
    $stmt->execute();
    $announcement = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$announcement) {
        die("Announcement not found.");
    }

    $imagePath = $announcement['image_path']; // default: existing image

    // Handle new image upload if any
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $fileTmp = $_FILES['image']['tmp_name'];
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetDir = "uploads/"; // make sure this folder exists and writable
        $targetFile = $targetDir . $fileName;

        // Optional: check file type (jpg, png, jpeg, gif)
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileType, $allowed)) {
            die("Only JPG, JPEG, PNG, GIF files are allowed.");
        }

        // Move uploaded file
        if (move_uploaded_file($fileTmp, $targetFile)) {
            $imagePath = $targetFile;
        } else {
            die("Failed to upload image.");
        }
    }

    // Update announcement
    $update = $dbh->prepare("UPDATE announcements SET title = :title, content = :content, image_path = :image WHERE ann_id = :id");
    $update->bindParam(':title', $title);
    $update->bindParam(':content', $content);
    $update->bindParam(':image', $imagePath);
    $update->bindParam(':id', $ann_id, PDO::PARAM_INT);

    if ($update->execute()) {
        header("Location: all_announcements.php");
        exit;
    } else {
        echo "Failed to update announcement.";
    }
} else {
    die("Invalid request method.");
}
?>
