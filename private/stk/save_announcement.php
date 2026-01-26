<?php
session_start();
include 'db.php'; // Your DB connection file

$user_id = $_SESSION['user_id'] ?? null;

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request.");
}

// --- 1️⃣ Collect form data ---
$title = $_POST['title'] ?? '';
$category = $_POST['ann_category'] ?? 'General';
$content = $_POST['content'] ?? '';
$date = !empty($_POST['date']) ? $_POST['date'] : null;
$posted_by = $user_id;

// --- 2️⃣ Handle main announcement image ---
$image_path = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $fileTmpPath = $_FILES['image']['tmp_name'];
    $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $newFileName = uniqid('img_', true) . '.' . $fileExt;
    $destPath = $uploadDir . $newFileName;

    if (move_uploaded_file($fileTmpPath, $destPath)) {
        $image_path = $destPath;
    } else {
        die("Error uploading main image.");
    }
}

// --- 3️⃣ Insert announcement (without Wedding Banns poster yet) ---
$stmt = $conn->prepare("INSERT INTO announcements (title, image_path, ann_categ, content, posted_by) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssssi", $title, $image_path, $category, $content, $posted_by);

if (!$stmt->execute()) {
    die("Error inserting announcement: " . $stmt->error);
}

$announcement_id = $stmt->insert_id;
$stmt->close();

// --- 4️⃣ If date is set, insert into events ---
if ($date) {
    $stmtEvent = $conn->prepare("INSERT INTO events (announcement_id, date, text, repeat_type) VALUES (?, ?, ?, 'none')");
    $stmtEvent->bind_param("iss", $announcement_id, $date, $title);
    $stmtEvent->execute();
    $stmtEvent->close();
}

// --- 5️⃣ Handle Wedding Banns ---
if ($category === 'Wedding Banns') {

    // Collect Wedding Banns fields
    $groom_name = $_POST['groom_name'] ?? '';
    $bride_name = $_POST['bride_name'] ?? '';
    $marriage_place = $_POST['marriage_place'] ?? '';
    $marriage_date = $_POST['marriage_date'] ?? null;

    // Upload groom photo
    $groom_photo_path = null;
    if (isset($_FILES['groom_photo']) && $_FILES['groom_photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['groom_photo']['tmp_name'];
        $fileExt = pathinfo($_FILES['groom_photo']['name'], PATHINFO_EXTENSION);
        $groom_photo_path = 'uploads/' . uniqid('groom_', true) . '.' . $fileExt;
        move_uploaded_file($fileTmpPath, $groom_photo_path);
    }

    // Upload bride photo
    $bride_photo_path = null;
    if (isset($_FILES['bride_photo']) && $_FILES['bride_photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['bride_photo']['tmp_name'];
        $fileExt = pathinfo($_FILES['bride_photo']['name'], PATHINFO_EXTENSION);
        $bride_photo_path = 'uploads/' . uniqid('bride_', true) . '.' . $fileExt;
        move_uploaded_file($fileTmpPath, $bride_photo_path);
    }

    // --- Poster settings ---
    $posterWidth = 1000;
    $posterHeight = 1400;

    $backgroundPath = __DIR__ . '/assets/wedding_bg.png';
    if (!file_exists($backgroundPath)) die("Background image not found.");
    $poster = imagecreatefrompng($backgroundPath);

    // --- Colors ---
    $white = imagecolorallocate($poster, 255, 255, 255);
    $gold = imagecolorallocate($poster, 201, 162, 77); // #C9A24D
    $shadow = imagecolorallocatealpha($poster, 0, 0, 0, 80); // subtle shadow

    // --- Fonts ---
    $fontPathSerif = __DIR__ . '/assets/fonts/GoudyBookletter1911-Regular.ttf';
    $fontPathScript = __DIR__ . '/assets/fonts/GreatVibes-Regular.ttf';

    if (!file_exists($fontPathSerif) || !file_exists($fontPathScript)) {
        die("Font file(s) missing.");
    }

    // --- Parish Logo ---
    $logoPath = __DIR__ . '/assets/church_logo.png';
    if (file_exists($logoPath)) {
        $logo = imagecreatefrompng($logoPath);
        $logoW = imagesx($logo);
        $logoH = imagesy($logo);
        $targetW = 180;
        $targetH = intval($logoH * ($targetW / $logoW));
        imagecopyresampled($poster, $logo, ($posterWidth - $targetW)/2, 40, 0, 0, $targetW, $targetH, $logoW, $logoH);
        imagedestroy($logo);
    }

    // --- Title & Subtitle ---
    $title = "WEDDING BANNS";
    $subtitle = "Notice of Intended Marriage";

    $titleBox = imagettfbbox(50, 0, $fontPathSerif, $title);
    $xTitle = ($posterWidth - ($titleBox[2]-$titleBox[0]))/2;
    imagettftext($poster, 50, 0, $xTitle, 300, $gold, $fontPathSerif, $title);

    $subBox = imagettfbbox(26, 0, $fontPathSerif, $subtitle);
    $xSub = ($posterWidth - ($subBox[2]-$subBox[0]))/2;
    imagettftext($poster, 26, 0, $xSub, 360, $white, $fontPathSerif, $subtitle);

    // --- Divider Line ---
    $lineColor = imagecolorallocatealpha($poster, 255, 255, 255, 60);
    imageline($poster, 180, 380, 820, 380, $lineColor);

    // --- Function: Circular Portrait ---
    function placeCircularPortrait($poster, $imagePath, $x, $y, $diameter = 300) {
        if (!file_exists($imagePath)) return;

        $img = imagecreatefromstring(file_get_contents($imagePath));
        $w = imagesx($img);
        $h = imagesy($img);
        $scale = $diameter / max($w, $h);
        $nw = intval($w * $scale);
        $nh = intval($h * $scale);

        $resized = imagecreatetruecolor($diameter, $diameter);
        imagesavealpha($resized, true);
        $trans = imagecolorallocatealpha($resized, 0,0,0,127);
        imagefill($resized, 0, 0, $trans);
        imagecopyresampled($resized, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);

        // Circular mask
        for ($i=0; $i<$diameter; $i++) {
            for ($j=0; $j<$diameter; $j++) {
                $dx = $i - $diameter/2;
                $dy = $j - $diameter/2;
                if ($dx*$dx + $dy*$dy > ($diameter/2)*($diameter/2)) {
                    imagesetpixel($resized, $i, $j, $trans);
                }
            }
        }

        // Gold frame
        $gold = imagecolorallocate($poster, 201, 162, 77);
        imagearc($poster, $x + $diameter/2, $y + $diameter/2, $diameter+10, $diameter+10, 0, 360, $gold);

        imagecopy($poster, $resized, $x, $y, 0, 0, $diameter, $diameter);
        imagedestroy($img);
        imagedestroy($resized);
    }

    // --- Place Groom & Bride Portraits ---
    placeCircularPortrait($poster, $groom_photo_path, 120, 420);
    placeCircularPortrait($poster, $bride_photo_path, 580, 420);

    // --- Groom & Bride Names ---
    imagettftext($poster, 34, 0, 180, 780, $gold, $fontPathScript, strtoupper($groom_name));
    imagettftext($poster, 28, 0, 470, 780, $white, $fontPathSerif, "&");
    imagettftext($poster, 34, 0, 620, 780, $gold, $fontPathScript, strtoupper($bride_name));

    // --- Marriage Date & Place ---
    if ($marriage_date) {
        $formattedDate = date("F j, Y", strtotime($marriage_date));
        imagettftext($poster, 22, 0, 200, 850, $white, $fontPathSerif, "Place: $marriage_place");
        imagettftext($poster, 22, 0, 200, 890, $white, $fontPathSerif, "Date: $formattedDate");
    }

    // --- Canonical Wording ---
    $bannsText = "This is to announce that the marriage between\n$groom_name and $bride_name\nis to take place at $marriage_place.\n\nAny person who knows of any lawful impediment\nto this marriage is obliged to declare it.";
    $lines = explode("\n", wordwrap($bannsText, 45));
    $y = 960;
    foreach ($lines as $line) {
        $box = imagettfbbox(22, 0, $fontPathSerif, $line);
        $x = ($posterWidth - ($box[2]-$box[0]))/2;
        imagettftext($poster, 22, 0, $x, $y, $white, $fontPathSerif, $line);
        $y += 34;
    }

    // --- Footer: Parish name ---
    $footer = "St. Mary's Parish, 2026";
    $box = imagettfbbox(18, 0, $fontPathSerif, $footer);
    $x = ($posterWidth - ($box[2]-$box[0])) / 2;
    imagettftext($poster, 18, 0, $x, $posterHeight - 50, $white, $fontPathSerif, $footer);

    // --- Save poster ---
    $posterPath = 'uploads/wedding_banns_' . uniqid() . '.png';
    imagepng($poster, $posterPath);
    imagedestroy($poster);

    // Update announcement image_path
    $stmt = $conn->prepare("UPDATE announcements SET image_path = ? WHERE ann_id = ?");
    $stmt->bind_param("si", $posterPath, $announcement_id);
    $stmt->execute();
    $stmt->close();
}

// --- Done ---
$conn->close();
header("Location: announcement.php?success=1");
exit;
?>
