<?php
require_once "connection.php"; 

$email = trim($_GET["email"] ?? "");
$token = $_GET["token"] ?? "";

function fail($msg) {
    echo "<p style='font-family:Arial;padding:24px;'>" . htmlspecialchars($msg) . " <a href='login.php'>Go to login</a></p>";
    exit();
}

if ($email === "" || $token === "") {
    fail("Invalid reset link.");
}

// Load token info
$stmt = $conn->prepare("SELECT reset_token_hash, reset_token_expires FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user || empty($user["reset_token_hash"]) || empty($user["reset_token_expires"])) {
    fail("Invalid or expired reset link.");
}

if (strtotime($user["reset_token_expires"]) < time()) {
    fail("Reset link expired. Please request a new one.");
}

if (!password_verify($token, $user["reset_token_hash"])) {
    fail("Invalid reset link.");
}

$message = "";

// Handle new password submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newPass = $_POST["password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";

    if ($newPass === "" || strlen($newPass) < 8) {
        $message = "Password must be at least 8 characters.";
    } elseif ($newPass !== $confirm) {
        $message = "Passwords do not match.";
    } else {
        $hashed = password_hash($newPass, PASSWORD_BCRYPT);

        $upd = $conn->prepare("
            UPDATE users
            SET password = ?, reset_token_hash = NULL, reset_token_expires = NULL
            WHERE email = ?
        ");
        $upd->bind_param("ss", $hashed, $email);
        $upd->execute();
        $upd->close();

        echo "<script>alert('Password updated! You can now log in.'); window.location.href='login.php';</script>";
        exit();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Reset Password</title>
  <link rel="stylesheet" href="../stylesheets/public/login.css">
</head>
<body>
  <main>
    <section class="login-section">
      <div class="logo-container">
        <h1 class="site-name">Reset Password</h1>
      </div>

      <form class="login-form" method="POST">
        <div class="form-group">
          <input type="password" name="password" placeholder="New Password" required>
        </div>
        <div class="form-group">
          <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
        </div>
        <button type="submit" class="log-in-btn">UPDATE PASSWORD</button>
      </form>

      <?php if ($message): ?>
        <p style="text-align:center; margin-top:12px;"><?= htmlspecialchars($message) ?></p>
      <?php endif; ?>

      <div class="form-links" style="text-align:center; margin-top:12px;">
        <a href="login.php">Back to Login</a>
      </div>
    </section>
  </main>
</body>
</html>
