<?php
require_once 'connection.php';

session_start();
$user_name = $_SESSION['givenname'] ?? null; 

$user_id = $_SESSION['user_id'];

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
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Santa Krus Parish</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../stylesheets/public/user_footer_navbar.css" />
    <link rel="stylesheet" href="../stylesheets/public/donation.css" />
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
            <li><a href="donation.php" class="active">Donate</a></li>
            <li><a href="prayer_page.php">Prayer Request</a></li>
            <li><a href="contact.php">Contact</a></li>

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

<section class="verse-text">
  <p class="verse-line"><strong>
    “Each of you should give what you have decided in your heart to give, 
    not reluctantly or under compulsion, for God loves a cheerful giver.”
  </strong></p>
  <p class="verse-ref">– 2 Corinthians 9:7</p>
</section>

<section class="donations">
  <h3>Donations</h3>
</section>

<div class="donation-flex">
  <section class="left-image-strip">
    <img src="../stylesheets/assets/donation.png" alt="Inspirational Verse" class="left-image" />
  </section>

  <section class="donation-form-section">
    <form class="donation-form">
      <label for="donation-cause"><h3>Donation Cause</h3></label>
      <select id="donation-cause" required>
        <option value="" disabled selected>Select</option>
        <option value="parishdonations">Parish Donations</option>
        <option value="tithes">Tithes</option>
        <option value="charity">Charity</option>
      </select>

      <label for="amount">Amount</label>
      <input type="number" id="amount" min="1" placeholder="Amount:" required />

      <div class="payment-method-section">
        <span>Payment Method</span>
        <div class="payment-options">
          <label class="payment-option">
            <input type="radio" name="payment-method" value="metrobank" required />
            <img src="../stylesheets/assets/metrobank.png" alt="Metrobank" class="payment-logo" />
          </label>
          <label class="payment-option">
            <input type="radio" name="payment-method" value="gcash" required />
            <img src="../stylesheets/assets/gcash.png" alt="GCash" class="payment-logo" />
          </label>
        </div>
      </div>

      <button type="submit" class="give-btn">Donate</button>
    </form>
  </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('.donation-form');
  form.addEventListener('submit', (e) => {
    e.preventDefault();
    const cause = document.getElementById('donation-cause').value;
    const amount = document.getElementById('amount').value;
    const payment = document.querySelector('input[name="payment-method"]:checked');
    
    if (!payment) { form.submit(); return; }

    if (payment.value === 'gcash') {
      const params = new URLSearchParams({ cause, amount });
      window.location.href = 'handle/qr_gcash.html?' + params.toString();
    } else if (payment.value === 'metrobank') {
      const params = new URLSearchParams({ cause, amount });
      window.location.href = 'handle/qr_metrobank.html?' + params.toString();
    } else {
      form.submit();
    }
  });
});
</script>


<!-- FOOTER -->
<footer class="custom-footer" style="margin-top:0px;">
  <div class="footer-left">
    <h2>Santa Krus Parish</h2>
    <p>Serving the community of Bagumbong, Caloocan with faith and love.</p>
    <p><b>Email Address</b><br>stakrusparish@gmail.com</p>
    <p><b>Phone Number</b><br>(02) 8984-3933</p>

    <ul class="social-icons">
      <li><a href="https://www.facebook.com/DioNova.StaKrusParish"><span class="fab fa-facebook-f"></span></a></li>
    </ul>
  </div>
  <!-- footer-left was not closed before -->

  <!-- LOCATION SECTION (Center) -->
  <div class="footer-location">
    <h3>Location</h3>
    <p>205 Deparo Rd, Caloocan City, 1420 Metro Manila</p>

    <div class="map-container">
      <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3858.6890401821715!2d121.00261717591935!3d14.737954572658468!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b7bb4e6b1b65%3A0x424ba9f332da9e86!2s205%20Deparo%20Rd%2C%20Caloocan%2C%201420%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1731985610001"
        width="100%"
        height="250"
        style="border:0;"
        allowfullscreen=""
        loading="lazy">
      </iframe>
    </div>
  </div>

  <!-- CONTACT SECTION -->
  <div class="footer-contact">
    <?php if (!empty($message)) : ?>
        <div class="alert alert-info text-center">
            <?= htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <h3>Contact Us</h3>
    <form  method="POST">
      <input type="text" name="name" placeholder="Name" required>
      <input type="email" name="email" placeholder="Email Address" required>
      <input type="text" name="subject" placeholder="Subject" required>
      <textarea name="message" placeholder="Message" required></textarea>
      <button type="submit">SUBMIT</button>
    </form>
  </div>

</footer>

</body>
</html>
