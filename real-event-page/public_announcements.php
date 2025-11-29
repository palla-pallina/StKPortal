<?php
require_once 'dbconfig.php';

// Pagination settings
$limit = 3; // number of announcements per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $limit;

// Count total announcements
$countStmt = $dbh->prepare("SELECT COUNT(*) FROM announcements");
$countStmt->execute();
$total_announcements = $countStmt->fetchColumn();
$total_pages = ceil($total_announcements / $limit);

// Fetch announcements for public view (latest first)
$stmt = $dbh->prepare("
    SELECT ann_id, title, content, date_posted, image_path 
    FROM announcements 
    ORDER BY date_posted DESC 
    LIMIT :start, :limit
");
$stmt->bindParam(':start', $start, PDO::PARAM_INT);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
