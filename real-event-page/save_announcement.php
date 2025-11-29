<?php
require_once 'dbconfig.php';

$title = $_POST['title'];
$content = $_POST['content'];
$imagePath = null;

// Handle Image Upload
if (!empty($_FILES['image']['name'])) {
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir);

    $fileName = time() . "_" . basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $fileName;

    move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
    $imagePath = $targetFile;
}

// Insert into DB
$stmt = $dbh->prepare("
    INSERT INTO announcements (title, image_path, content, posted_by)
    VALUES (:title, :image_path, :content, 1)
");

$stmt->bindParam(':title', $title);
$stmt->bindParam(':image_path', $imagePath);
$stmt->bindParam(':content', $content);

$stmt->execute();

header("Location: Announcement.php?posted=1");
exit;

?>
