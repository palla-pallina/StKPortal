<?php
require_once "dbconfig.php";

// GET ID
if (!isset($_GET['id'])) {
    die("No announcement selected.");
}
$id = intval($_GET['id']);

// FETCH DATA
$stmt = $dbh->prepare("SELECT * FROM announcements WHERE ann_id = :id");
$stmt->bindParam(":id", $id);
$stmt->execute();
$ann = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ann) {
    die("Announcement not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Announcement</title>

<style>
    body {
        margin: 0;
        padding: 0;
        background: #f2f2f2;
        font-family: Arial, sans-serif;
    }

    .container {
        width: 100%;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: start;
        padding: 40px 0;
    }

    .card {
        width: 80%;
        max-width: 1000px;
        background: #fff;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    h2 {
        text-align: center;
        margin-bottom: 25px;
    }

    label {
        font-weight: bold;
        display: block;
        margin: 10px 0 5px;
    }

    input[type="text"],
    textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 16px;
    }

    textarea {
        height: 500px;
        resize: none;
    }

    .current-img {
        width: 54%;
        height: 47%;
        object-fit: cover;
        border-radius: 10px;
        margin-bottom: 15px;
    }

    .btn-row {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
    }

    .btn {
        padding: 12px 20px;
        font-size: 16px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
    }

    .save {
        background: #28a745;
        color: white;
    }

    .cancel {
        background: #dc3545;
        color: white;
    }
</style>
</head>

<body>

<div class="container">
    <div class="card">

        <h2>Edit Announcement</h2>

        <form action="update_announcement.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="ann_id" value="<?= $ann['ann_id']; ?>">

            <!-- CURRENT IMAGE -->
            <img src="<?= $ann['image_path']; ?>" class="current-img" alt="Announcement Image">

            <label>Replace Picture (Optional)</label>
            <input type="file" name="image">

            <!-- TITLE -->
            <label>Title</label>
            <input type="text" name="title" value="<?= htmlspecialchars($ann['title']); ?>" required>

            <!-- CONTENT -->
            <label>Content</label>
            <textarea name="content" required><?= htmlspecialchars($ann['content']); ?></textarea>

            <div class="btn-row">
                <button type="submit" class="btn save">Save Changes</button>
                <a href="all_announcements.php" class="btn cancel">Cancel</a>
            </div>

        </form>

    </div>
</div>

</body>
</html>
