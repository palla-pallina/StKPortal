<?php
require_once 'dbconfig.php';
session_start();

/* ===============================
   ADMIN ONLY
================================ */
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}

/* ===============================
   VALIDATE INPUT
================================ */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: certificate_request.php");
    exit();
}

$reqId = (int) $_GET['id'];

/* ===============================
   FETCH CERTIFICATE REQUEST
================================ */
$stmt = $dbh->prepare("
    SELECT certificate_type,
           owner_firstname,
           owner_middlename,
           owner_lastname,
           owner_suffix
    FROM certificate_request
    WHERE cert_req_id = ?
");
$stmt->execute([$reqId]);
$req = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$req) {
    header("Location: certificate_request.php");
    exit();
}

/* ===============================
   NORMALIZE VALUES
================================ */
$fn = trim($req['owner_firstname']);
$mn = trim($req['owner_middlename']);
$ln = trim($req['owner_lastname']);
$sx = trim($req['owner_suffix']);
$type = strtolower(trim($req['certificate_type']));

/* ===============================
   MATCH BY CERTIFICATE TYPE
================================ */
switch ($type) {

    /* -------- BAPTISM -------- */
    case 'baptism':
        $stmt = $dbh->prepare("
            SELECT id FROM baptisms
            WHERE LOWER(child_firstname)=LOWER(?)
              AND LOWER(child_lastname)=LOWER(?)
              AND (child_middlename = ? OR ? IS NULL)
              AND (child_suffix = ? OR ? IS NULL)
            LIMIT 1
        ");
        $stmt->execute([$fn, $ln, $mn ?: null, $mn ?: null, $sx ?: null, $sx ?: null]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            header("Location: ../baptism/s_baptism_view.php?id=" . $row['id']);
            exit();
        }
        break;

    /* -------- CONFIRMATION -------- */
    case 'confirmation':
        $stmt = $dbh->prepare("
            SELECT id FROM confirmations
            WHERE LOWER(confirm_firstname)=LOWER(?)
              AND LOWER(confirm_lastname)=LOWER(?)
              AND (confirm_middlename = ? OR ? IS NULL)
              AND (confirm_suffix = ? OR ? IS NULL)
            LIMIT 1
        ");
        $stmt->execute([$fn, $ln, $mn ?: null, $mn ?: null, $sx ?: null, $sx ?: null]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            header("Location: ../confirmation/s_confirmation_view.php?id=" . $row['id']);
            exit();
        }
        break;

    /* -------- FIRST COMMUNION -------- */
    case 'first communion':
        $stmt = $dbh->prepare("
            SELECT id FROM first_communion
            WHERE LOWER(child_firstname)=LOWER(?)
              AND LOWER(child_lastname)=LOWER(?)
              AND (child_middlename = ? OR ? IS NULL)
              AND (child_suffix = ? OR ? IS NULL)
            LIMIT 1
        ");
        $stmt->execute([$fn, $ln, $mn ?: null, $mn ?: null, $sx ?: null, $sx ?: null]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            header("Location: ../first_communion/s_communion_view.php?id=" . $row['id']);
            exit();
        }
        break;

    /* -------- WEDDING -------- */
    case 'wedding':
        $stmt = $dbh->prepare("
            SELECT id FROM weddings
            WHERE (
                LOWER(bride_firstname)=LOWER(?) AND LOWER(bride_lastname)=LOWER(?)
            ) OR (
                LOWER(groom_firstname)=LOWER(?) AND LOWER(groom_lastname)=LOWER(?)
            )
            LIMIT 1
        ");
        $stmt->execute([$fn, $ln, $fn, $ln]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            header("Location: ../wedding/s_wedding_view.php?id=" . $row['id']);
            exit();
        }
        break;
}

/* ===============================
   NOT FOUND → RETURN WITH FLAG
================================ */
header("Location: certificate_request.php?record_not_found=1");
exit();
