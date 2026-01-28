<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    exit("Unauthorized access.");
}

if (!isset($_GET['file'])) {
    exit("No file specified.");
}

/*
|--------------------------------------------------------------------------
| PHYSICAL UPLOAD DIRECTORY
| admin/view_file.php → admin/uploads/
|--------------------------------------------------------------------------
*/
$baseDir = realpath(__DIR__ . '/uploads');
$request  = basename($_GET['file']); // blocks ../ traversal
$filePath = $baseDir . '/' . $request;

/* ---------------- SECURITY CHECKS ---------------- */
if (!$baseDir || !file_exists($filePath)) {
    exit("File not found.");
}

/* ---------------- SERVE FILE ---------------- */
$mime = mime_content_type($filePath);
header("Content-Type: $mime");
header("Content-Disposition: inline; filename=\"" . basename($filePath) . "\"");
header("Content-Length: " . filesize($filePath));
readfile($filePath);
exit;
