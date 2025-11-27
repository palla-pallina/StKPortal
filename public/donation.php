<?php
// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "test_tree_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
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

$user_name = $_SESSION['username'] ?? null; 
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Santa Krus Parish</title>
  <link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../stylesheets/public/donation.css" />
</head>
<body>

  <!-- NAVBAR -->
  <header>
    <nav class="navbar">
      <img src="../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" />
      <ul class="nav-links">
        <li><a href="homepage.php">Home</a></li>
        <li><a href="history.php">History</a></li>
        <li><a href="events.php">Events</a></li> 
        <li><a href="donation.php" class="active">Donate</a></li>
        <li><a href="sacraments.php">Sacraments</a></li>
        <li><a href="prayer-page.php">Prayer Request</a></li>
        <li><a href="contact.php">Contact</a></li>
          <?php
          session_start();

          if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
              // USER NOT LOGGED IN → Show Login button
              echo '<li><a href="login.php" class="login-btn">Login</a></li>';
          } else {
              // USER LOGGED IN → Show Username + Dropdown
              ?>
            <p style="color:white;">|</p>
              <li class="logout-dropdown">
                  <a href="#"><?= htmlspecialchars($user_name) ?></a>
                  <ul class="dropdown">
                      <li><a href="logout.php">Logout</a></li>
                  </ul>
              </li>

              <li class="nav-notification">
                  <a href="notifications.php">
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

      </ul>
    </nav>
  </header>
  <!-- NAVBAR END-->

  <main>
    <section class="verse-text">
      <p class="verse-line"><strong>
        “Each of you should give what you have decided in your heart to give, 
        not reluctantly or under compulsion, for God loves a cheerful giver.”
      </strong></p>
      <p class="verse-ref">2 Corinthians 9:7</p>
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

          <button type="submit" class="give-btn">GIVE</button>
        </form>
      </section>
    </div>
  </main>

  <footer class="site-footer-simple">
    <p>&copy; 2025 Santa Krus Parish, Bagumbong Caloocan. All Rights Reserved.</p>
  </footer>

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
</body>
</html>
