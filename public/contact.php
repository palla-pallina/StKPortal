<?php
require_once 'connection.php';
session_start();

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['givenname'] ?? null; 

/*--------- NOTIFICATION HANDLING START
- for notification icon at navbar
- retrieves all notifications belonging to logged in user */
$stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);

$unreadCount = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $unreadCount = $result['cnt'] ?? 0;
    $stmt->close();
}
/*--------- NOTIFICATION HANDLING END */

/*--------- FOR AUTO FILL
- retrieves user information */
$stmt = $dbh->prepare("
    SELECT givenname, lastname, email, contact
    FROM users WHERE user_id = :id
");
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Store in session for JS access
foreach ($user as $key => $value) {
    $_SESSION[$key] = $value;
}
/*--------- FOR AUTO FILL END */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Santa Krus Parish</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../stylesheets/public/user_footer_navbar.css" />
    <link rel="stylesheet" href="../stylesheets/public/contact.css" />
</head>
<body>


<!-------------------- NAVBAR -------------------->
<header>
    <nav class="navbar">
        <ul class="nav-links">
            <li><a href="#"><img src="../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" /></a></li>
            <li><a href="homepage.php">Home</a></li>
            <li><a href="history.php">History</a></li>
            <li><a href="events.php">News & Events</a></li>
            <li><a href="sacraments.php">Sacraments</a></li>
            <li><a href="donation.php">Donate</a></li>
            <li><a href="prayer_page.php">Prayer Request</a></li>
            <li><a href="contact.php" class="active">Contact</a></li>

            <?php
            session_start();

            if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
                // USER NOT LOGGED IN → Show Login button
                echo '<li><a href="login.php" id="login-btn" style="color:white;"><i class="fa-regular fa-user"></i>   Log In</a></li>';
            } else {
                // USER LOGGED IN → Show Username + Dropdown
                ?>
            <p style="color:white; padding-left: 17px; padding-right: 17px;">|</p>
                <li class="li-dropdown">
                    <a href="#"><?= htmlspecialchars($user_name) ?></a>
                    <ul class="dropdown">
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </li>

                <li class="nav-notification">
                    <a href="members/notifications.php">
                        <span class="bell">&#128276;</span>
                        <?php if (!empty($unreadCount) && $unreadCount > 0): ?>
                            <span class="badge"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php
            }
            ?>
        </ul>
    </nav>
</header>
<!----------------- NAVBAR END----------------->

<div class="hero-image" role="img" aria-label="Interior of church">
  <img src="../stylesheets/assets/bannernight.jpg" alt="Church Exterior" />
</div>

<section class="contact-header">
    <h2>For Your Questions</h2>
</section>

<?php if (!empty($message)) : ?>
    <div class="alert alert-info text-center">
        <?= htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<section class="contact-section">
  <div class="contact-grid">
    <div class="contact-left">
      <img src="../stylesheets/assets/parish.png" alt="Santa Krus Parish" class="church-photo" />
      <div class="contact-details">
        <p><strong>Email Address</strong></p>
        <p>stakrusparish@gmail.com</a></p>
        <p><strong>Telephone</strong></p>
        <p>(02) 8984-3933</p>
        <tr>
          <p><strong>Social Media</strong></p>
        </tr>
      </div>
      <div class="social-icons">
        <a href="https://www.facebook.com/DioNova.StaKrusParish"><i class="fab fa-facebook-f"></i></a>
      </div>
    </div>

<form class="contact-form" method="POST">
  <div class="form-logo">
    <img src="../stylesheets/assets/santa krus logo.png" alt="Santa Krus Logo" />
  </div>

  <div class="form-group">
    <label for="name">Name</label>
    <input type="text" id="name" name="name" required />
  </div>

  <div class="form-group">
    <label for="email">Email Address</label>
    <input type="email" id="email" name="email" required />
  </div>

  <div class="form-group">
    <label for="subject">Subject</label>
    <input type="text" id="subject" name="subject" required />
  </div>

  <div class="form-group">
    <label for="message">Message</label>
    <textarea id="message" name="message" rows="4" required></textarea>
  </div>

 <div class="form-button-wrapper">
  <button type="submit">SUBMIT</button>
</div>
</form>

<section class="map-section">
  <div class="map-container">
    <iframe
      src="https://www.google.com/maps?q=814-A,+Bagumbong+Road,+Bagumbong,+1421+Caloocan,+Philippines&output=embed"
      allowfullscreen
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade"
    ></iframe>
  </div>
        <br><strong>📌️ 814-A, Bagumbong Road, Bagumbong 1421 Caloocan, Philippines</strong>
</section>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const name = "<?php echo ($_SESSION['givenname'] ?? '') . ' ' . ($_SESSION['lastname'] ?? ''); ?>";
    const email = "<?php echo $_SESSION['email'] ?? ''; ?>";
    const contact = "<?php echo $_SESSION['contact'] ?? ''; ?>";

    document.getElementById('name').value = name;
    document.getElementById('email').value = email;
    document.getElementById('contact').value = contact;
});
</script>
</body>
</html>