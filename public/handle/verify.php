<?php
require_once "../connection.php";

$email = trim($_GET['email'] ?? '');
$token = $_GET['token'] ?? '';

if (!$email || !$token) {
    die("Invalid verification link.");
}

// Load user by email
$sql = "SELECT user_id, email_verified, verify_token_hash, verify_token_expires
        FROM users
        WHERE email = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("Invalid verification link.");
}

if ((int)$user['email_verified'] === 1) {
    die("Email is already verified. You may log in.");
}

if (empty($user['verify_token_hash']) || empty($user['verify_token_expires'])) {
    die("Verification token is missing. Please register again or request a new link.");
}

// Check expiry
if (strtotime($user['verify_token_expires']) < time()) {
    die("Verification link expired. Please request a new verification email.");
}

// Verify token against stored hash
if (!password_verify($token, $user['verify_token_hash'])) {
    die("Invalid verification token.");
}

// Mark verified + clear token fields
$update = "UPDATE users
           SET email_verified = 1,
               verify_token_hash = NULL,
               verify_token_expires = NULL
           WHERE user_id = ?";

$stmt = $conn->prepare($update);
$stmt->bind_param("i", $user['user_id']);
$stmt->execute();
$stmt->close();

// Redirect to login
header("Location: ../login.php?verified=1");
exit;
