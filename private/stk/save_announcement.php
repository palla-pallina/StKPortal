<?php
include 'db.php';
session_start();

// Optional: if you want to save user_id when logged in
$user_id = $_SESSION['user_id'] ?? null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $imagePath = null;

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;

        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];

        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $imagePath = $targetFile;
            }
        }
    }

    // Insert announcement
    $sql = "INSERT INTO announcements (title, image_path, content, posted_by) 
            VALUES ('$title', " . ($imagePath ? "'$imagePath'" : "NULL") . ", '$content', " . ($user_id ? $user_id : "NULL") . ")";

    if ($conn->query($sql) === TRUE) {
        header("Location: announcement.php?success=1");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
