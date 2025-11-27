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
  <link rel="stylesheet" href="../stylesheets/public/contact.css" />
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
        <li><a href="donation.php">Donate</a></li>
        <li><a href="sacraments.php">Sacraments</a></li>
        <li><a href="prayer-page.php">Prayer Request</a></li>
          <li><a href="contact.php" class="active">Contact</a></li>
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

    </nav>
  </header>
  <!-- NAVBAR END-->

  <main>
    <section class="hero">
      <h1>SANTA KRUS PARISH</h1>
      <h2>DIOCESE OF NOVALICHES</h2>
      <div class="hero-image">
        <img src="../stylesheets/assets/banner.png" alt="Church Interior" />
      </div>
    </section>

<section class="sQuestions">
  <h3>For Your Questions</h3>
</section>

<section class="contact-section">
  <div class="contact-grid">
    <div class="contact-left">
      <img src="../stylesheets/assets/parish.png" alt="Santa Krus Parish" class="church-photo" />
      <div class="contact-details">
        <p><strong>Email Address</strong></p>
        <p>stakrusparish@gmail.com</a></p>
        <p><strong>Contact Number</strong></p>
        <p>0918 627 3344</p>
        <p><strong>Telephone</strong></p>
        <p>0289843933</p>
        <tr>
                  <p><strong>Social Media</strong></p>
        </tr>
      </div>
      <div class="social-icons">
        <a href="https://www.facebook.com/DioNova.StaKrusParish"><i class="fab fa-facebook-f"></i></a>
      </div>
    </div>

<form class="contact-form">
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
    <label for="topic">Topic</label>
    <input type="text" id="topic" name="topic" required />
  </div>

  <div class="form-group">
    <label for="question">Question</label>
    <textarea id="question" name="question" rows="4" required></textarea>
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

  </main>
    <footer class="site-footer-simple">
    <p>&copy; 2025 Santa Krus Parish, Bagumbong Caloocan. All Rights Reserved.</p>
  </footer>
  </body>
</html>