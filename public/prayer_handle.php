<?php
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'test_tree_db';

try {
    $opts = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO("mysql:host={$DB_HOST};charset=utf8mb4", $DB_USER, $DB_PASS, $opts);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$DB_NAME}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$DB_NAME}`");

    $pdo->exec("CREATE TABLE IF NOT EXISTS prayer_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NULL,
        email VARCHAR(255) NULL,
        intention VARCHAR(100) NULL,
        request_text TEXT NOT NULL,
        shared_public TINYINT(1) NOT NULL DEFAULT 0,
        anonymous TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $colCheck = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'prayer_requests' AND COLUMN_NAME = ?");
    $oldColCheck = $pdo->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'prayer_requests' AND COLUMN_NAME = ?");
    $colCheck->execute([$DB_NAME, 'request_text']);
    $hasRequestText = (bool) $colCheck->fetch();
    if (! $hasRequestText) {
        $pdo->exec("ALTER TABLE prayer_requests ADD COLUMN request_text TEXT NULL AFTER intention");
        $oldColCheck->execute([$DB_NAME, 'request']);
        if ($oldColCheck->fetch()) {
            $pdo->exec("UPDATE prayer_requests SET request_text = `request` WHERE (request_text IS NULL OR request_text = '') AND (\`request\` IS NOT NULL AND \`request\` <> '')");
        }
    }

    $colCheck->execute([$DB_NAME, 'shared_public']);
    $hasShared = (bool) $colCheck->fetch();
    if (! $hasShared) {
        $pdo->exec("ALTER TABLE prayer_requests ADD COLUMN shared_public TINYINT(1) NOT NULL DEFAULT 0 AFTER request_text");
        $oldColCheck->execute([$DB_NAME, 'sharePublic']);
        if ($oldColCheck->fetch()) {
            $pdo->exec("UPDATE prayer_requests SET shared_public = CASE WHEN `sharePublic` IS NULL THEN 0 WHEN `sharePublic` = '' THEN 0 ELSE `sharePublic` END");
        }
    }

    $colCheck->execute([$DB_NAME, 'anonymous']);
    $hasAnon = (bool) $colCheck->fetch();
    if (! $hasAnon) {
        $pdo->exec("ALTER TABLE prayer_requests ADD COLUMN anonymous TINYINT(1) NOT NULL DEFAULT 0 AFTER shared_public");
        $oldColCheck->execute([$DB_NAME, 'anon']);
        if ($oldColCheck->fetch()) {
            $pdo->exec("UPDATE prayer_requests SET anonymous = CASE WHEN `anon` IS NULL THEN 0 WHEN `anon` = '' THEN 0 ELSE `anon` END");
        }
    }

    $colCheck->execute([$DB_NAME, 'created_at']);
    $hasCreatedAt = (bool) $colCheck->fetch();
    if (! $hasCreatedAt) {
        $pdo->exec("ALTER TABLE prayer_requests ADD COLUMN created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER anonymous");
        $pdo->exec("UPDATE prayer_requests SET created_at = NOW() WHERE created_at IS NULL");
    }

    $name = isset($_POST['name']) ? trim($_POST['name']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $intention = isset($_POST['intention']) ? trim($_POST['intention']) : null;
    $request_text = isset($_POST['request']) ? trim($_POST['request']) : null;
    $shared_public = isset($_POST['sharePublic']) ? 1 : 0;
    $anonymous = isset($_POST['anonymous']) ? 1 : 0;

    if ($email !== null) {
        if ($email === '') {
            $email = null;
        } else {
            if (strlen($email) > 255) {
                $email = substr($email, 0, 255);
            }
        }
    }

    $allowedIntentions = ['thanksgiving' => 'Thanksgiving', 'special' => 'Special Intention', 'soul' => 'Soul'];
    if ($intention === null || !array_key_exists($intention, $allowedIntentions)) {
        $intention = null;
    }

    if ($anonymous) {
        $name = null;
        $email = null;
    }

    if (empty($request_text)) {
        $msg = urlencode('Prayer request cannot be empty.');
        header("Location: prayer-page.php?status=error&msg={$msg}");
        exit;
    }

    $stmt = $pdo->prepare('INSERT INTO prayer_requests (name, email, intention, request_text, shared_public, anonymous) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$name, $email, $intention, $request_text, $shared_public, $anonymous]);

    header('Location: prayer-page.php?status=success');
    exit;

} catch (PDOException $e) {
    $msg = urlencode($e->getMessage());
    header("Location: prayer-page.php?status=error&msg={$msg}");
    exit;
}

?>
