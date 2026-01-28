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

    // =====================
    // DATA
    // =====================
    $groom_name = trim($_POST['groom_name'] ?? '');
    $bride_name = trim($_POST['bride_name'] ?? '');
    $marriage_place = trim($_POST['marriage_place'] ?? '');
    $marriage_date = $_POST['marriage_date'] ?? null;

    function uploadWB($field) {
        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) return null;
        $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp'])) return null;
        $path = 'uploads/' . uniqid($field.'_') . '.' . $ext;
        move_uploaded_file($_FILES[$field]['tmp_name'], $path);
        return $path;
    }

    $groom_photo = uploadWB('groom_photo');
    $bride_photo = uploadWB('bride_photo');

    // =====================
    // CANVAS
    // =====================
    $poster = imagecreatefrompng(__DIR__ . '/assets/wedding_bg.png');
    imagealphablending($poster, true);
    imagesavealpha($poster, true);

    $W = imagesx($poster);
    $black = imagecolorallocate($poster, 0, 0, 0);
    $gray  = imagecolorallocate($poster, 180, 180, 180);

    $font = __DIR__ . '/assets/fonts/GoudyBookletter1911-Regular.ttf';

    // =====================
    // HELPERS
    // =====================
    function centerText($img,$font,$size,$y,$text,$color){
        $box = imagettfbbox($size,0,$font,$text);
        $x = (imagesx($img)-($box[2]-$box[0]))/2;
        imagettftext($img,$size,0,$x,$y,$color,$font,$text);
    }

    function placeRect($canvas,$path,$x,$y,$w,$h){
        if(!$path || !file_exists($path)) return false;
        $src = imagecreatefromstring(file_get_contents($path));
        $sw = imagesx($src); $sh = imagesy($src);
        $scale = max($w/$sw,$h/$sh);
        $nw = $sw*$scale; $nh = $sh*$scale;

        $tmp = imagecreatetruecolor($w,$h);
        imagecopyresampled($tmp,$src,($w-$nw)/2,($h-$nh)/2,0,0,$nw,$nh,$sw,$sh);
        imagecopy($canvas,$tmp,$x,$y,0,0,$w,$h);

        imagedestroy($src);
        imagedestroy($tmp);
        return true;
    }

    // =====================
    // LOGOS
    // =====================
    $leftLogo  = imagecreatefrompng(__DIR__.'/assets/stakrusparish.png');
    $rightLogo = imagecreatefrompng(__DIR__.'/assets/dioceseofnovaliches.png');

    imagecopyresampled($poster,$leftLogo,130,105,0,0,140,140,imagesx($leftLogo),imagesy($leftLogo));
    imagecopyresampled($poster,$rightLogo,$W-260,105,0,0,130,130,imagesx($rightLogo),imagesy($rightLogo));

    imagedestroy($leftLogo);
    imagedestroy($rightLogo);

    // =====================
    // HEADER TEXT
    // =====================
    centerText($poster,$font,28,170,"Santa Krus Parish",$black);
    centerText($poster,$font,22,210,"DIOCESE OF NOVALICHES",$black);
    centerText($poster,$font,42,300,"WEDDING BANNS",$black);

    // =====================
    // PHOTO BOXES
    // =====================
    $boxW = 300;
    $boxH = 300;
    $boxY = 360;
    $leftX  = 170;
    $rightX = $W - $leftX - $boxW;

    imagefilledrectangle($poster,$leftX,$boxY,$leftX+$boxW,$boxY+$boxH,$gray);
    imagefilledrectangle($poster,$rightX,$boxY,$rightX+$boxW,$boxY+$boxH,$gray);

    if(!placeRect($poster,$groom_photo,$leftX,$boxY,$boxW,$boxH))
        imagettftext($poster,22,0,$leftX+55,$boxY+170,$black,$font,"GROOM PHOTO");

    if(!placeRect($poster,$bride_photo,$rightX,$boxY,$boxW,$boxH))
        imagettftext($poster,22,0,$rightX+55,$boxY+170,$black,$font,"BRIDE PHOTO");

    // =====================
    // DETAILS
    // =====================
    $y = 720;
    $gap = 44;
    $dateFmt = $marriage_date ? date("F j, Y", strtotime($marriage_date)) : "[Date]";

    imagettftext($poster,26,0,170,$y,$black,$font,"Name of Groom: ".($groom_name ?: "[Groom Fullname]"));
    imagettftext($poster,26,0,170,$y+$gap,$black,$font,"Name of Bride: ".($bride_name ?: "[Bride Fullname]"));
    imagettftext($poster,26,0,170,$y+$gap*2,$black,$font,"Date of Marriage: ".$dateFmt);
    imagettftext($poster,26,0,170,$y+$gap*3,$black,$font,"Place of Marriage: ".($marriage_place ?: "[Place]"));

    // =====================
    // SAVE + DB UPDATE
    // =====================
    $posterPath = 'uploads/wedding_banns_' . uniqid() . '.png';
    imagepng($poster, $posterPath);
    imagedestroy($poster);

    $stmt = $conn->prepare("UPDATE announcements SET image_path=? WHERE ann_id=?");
    $stmt->bind_param("si", $posterPath, $announcement_id);
    $stmt->execute();
    $stmt->close();
}

// --- Done ---
$conn->close();
header("Location: announcement.php?success=1");
exit;
?>
