<?php
require_once 'connection.php';
session_start();

$user_name = $_SESSION['username'] ?? null;

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

$user_id = $_SESSION['user_id'];
$gender = $_SESSION['gender'];

$stmt = $dbh->prepare("SELECT givenname, middlename, lastname, gender FROM users WHERE user_id = :id");
$stmt->bindParam(':id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$givenname = $user['givenname'] ?? '';
$middlename = $user['middlename'] ?? '';
$lastname = $user['lastname'] ?? '';
$suffix = $user['suffix'] ?? '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Santa Krus Parish</title>
  <link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../stylesheets/public/sacraments.css" />
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
        <li><a href="sacraments.php" class="active">Sacraments</a></li>
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
  <section class="hero">
    <h1>SANTA KRUS PARISH</h1>
    <h2>DIOCESE OF NOVALICHES</h2>
    <div class="hero-image">
      <img src="../stylesheets/assets/banner.png" alt="Church Interior" />
    </div>
  </section>

 <section class="services-section">

    <h2 class="section-title">SACRAMENTS</h2><br><br>

  <div class="services-grid">
    
    <a href="baptism.php">
    <div class="card baptism" style="background-image: url('assets/Baptism.jpg');">
        <div class="card-overlay">
            <h3>Baptism</h3>
            <p>Christian initiation sacrament</p>
        </div>
    </div>
    </a>

    <div class="card eucharist" style="background-image: url('assets/euharistia.jpg');">
        <div class="card-overlay">
            <h3>Eucharist</h3>
            <p>Receiving the body and blood of Christ</p>
        </div>
    </div>

    <div class="card confirmation" style="background-image: url('assets/Confirmation.jpg');">
        <div class="card-overlay">
            <h3>Confirmation</h3>
            <p>Strengthening faith through the Holy Spirit</p>
        </div>
    </div>

    <div class="card reconciliation" style="background-image: url('assets/Reconciliatio.jpg');">
        <div class="card-overlay">
            <h3>Reconciliation</h3>
            <p>Confession and forgiveness of sins</p>
        </div>
    </div>

    <div class="card anointing" style="background-image: url('assets/Anointing of the Sick.jpg');">
        <div class="card-overlay">
            <h3>Anointing of the Sick</h3>
            <p>Healing and comfort for the ill</p>
        </div>
    </div>

    <a href="wedding.php">
    <div class="card marriage" style="background-image: url('assets/Marriage.jpg');">
        <div class="card-overlay">
            <h3>Marriage</h3>
            <p>Union of husband and wife in God</p>
        </div>
    </div>
    </a>

    <div class="card holyorders" style="background-image: url('assets/holy-orders.jpg');">
        <div class="card-overlay">
            <h3>Holy Orders</h3>
            <p>Ordination into the priesthood</p>
        </div>
    </div>

  </div>
</section>

  
</main>

<footer class="site-footer-simple">
  <p>&copy; 2025 Santa Krus Parish, Bagumbong Caloocan. All Rights Reserved.</p>
</footer>
</body>
</html>
