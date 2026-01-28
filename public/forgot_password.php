<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "connection.php"; 

require 'vendor/autoload.php';

require 'vendor/PHPMailer/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");

    if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } else {
        // Check if user exists
        $stmt = $conn->prepare("SELECT user_id, givenname FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        // Always show generic message (prevents email enumeration)
        $message = "If that email exists, a password reset link has been sent.";

        if ($user) {
            // Create token and store HASH
            $token = bin2hex(random_bytes(32));
            $tokenHash = password_hash($token, PASSWORD_DEFAULT);
            $expires = date("Y-m-d H:i:s", time() + 3600); // 1 hour

            $upd = $conn->prepare("UPDATE users SET reset_token_hash = ?, reset_token_expires = ? WHERE email = ?");
            $upd->bind_param("sss", $tokenHash, $expires, $email);
            $upd->execute();
            $upd->close();

            // Reset link (match your base URL)
            $resetLink = "http://localhost/stkportal_src/src/public/reset_password.php"
                       . "?email=" . urlencode($email)
                       . "&token=" . urlencode($token);

            // Send email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'stakrusp@gmail.com';
                $mail->Password   = 'pegx lmst lzje vuie'; // 16-char App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('stakrusp@gmail.com', 'Santa Krus Parish');
                $mail->addAddress($email, $user['givenname'] ?? '');

                $mail->isHTML(true);
                $mail->Subject = "Reset your password";
                $mail->Body = "
                    <p>Hi <strong>" . htmlspecialchars($user['givenname'] ?? 'there') . "</strong>,</p>
                    <p>Click the link below to reset your password (expires in 1 hour):</p>
                    <p><a href='{$resetLink}'>Reset Password</a></p>
                    <p>If you did not request this, you can ignore this email.</p>
                ";
                $mail->AltBody = "Reset your password: $resetLink";

                $mail->send();
            } catch (Exception $e) {
                // Keep generic message
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Forgot Password</title>
  <link rel="stylesheet" href="../stylesheets/public/login.css">
</head>
<body>
  <main>
    <section class="login-section">
      <div class="logo-container">
        <h1 class="site-name">Forgot Password</h1>
      </div>

      <form class="login-form" method="POST" action="forgot_password.php">
        <div class="form-group">
          <input type="email" name="email" placeholder="Email Address" required>
        </div>
        <button type="submit" class="log-in-btn">SEND RESET LINK</button>
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
_