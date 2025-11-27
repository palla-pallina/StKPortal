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
  <link rel="stylesheet" href="../stylesheets/public/history.css" />
</head>
<body>
 
  <!-- NAVBAR -->
  <header>
    <nav class="navbar">
      <img src="../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" />
      <ul class="nav-links">
        <li><a href="homepage.php">Home</a></li>
        <li><a href="history.php" class="active">History</a></li>
        <li><a href="events.php">Events</a></li> 
        <li><a href="donation.php">Donate</a></li>
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
    </nav>
  </header>
  <!-- NAVBAR END-->

  <main>

<section class="history">
  <h3>History</h3>

  <div class="hero-image">
    <img src="../stylesheets/assets/banner2.jpeg" alt="History Image" />
  </div>
        
  <div class="history-text">
    <p>
            Santa Krus Parish of the Roman Catholic Diocese of Novaliches was established on May 8, 2003. 
      It is located in Bagumbong, Caloocan City. The parish traces its humble beginnings to a small chapel built in 1940, 
      fondly called the “Bisita” by local residents. This chapel was originally located at Bagumbong Kanto, 
      the present-day site of Primark Bagumbong. In 1959, the Bisita was transferred to its current location, 
      where the parish church now stands.
    </p>
    <p>
      For many years, the chapel served as a spiritual center for the community. Eventually, on May 8, 2003, 
      it was officially elevated to parish status under the Diocese of Novaliches, led by Bishop Emeritus Teodoro Bacani Jr. 
      This marked a new chapter in the spiritual life of the people of Bagumbong.
    </p>
    <p>
      The Diocese of Novaliches, to which Santa Krus Parish belongs, was created by Pope John Paul II on December 7, 2002, 
      through the Apostolic Constitution <em>Animarum Utilitati</em>, and was canonically erected on January 16, 2003. 
      It was formed from the Archdiocese of Manila and covers the northern parts of Caloocan and Quezon City. 
      As of September 2018, the diocese is composed of 70 parishes and one mission area, grouped into twelve vicariates.
    </p>
    <p>
      Santa Krus Parish holds the special distinction of being the first parish established under the newly formed Diocese of Novaliches. 
      It was created to meet the growing spiritual and sacramental needs of the faithful in Bagumbong, 
      who were previously served by nearby parishes. Since its establishment, Santa Krus Parish has grown into a vibrant 
      and welcoming church community, offering regular Masses, sacraments, and programs that strengthen faith and unity. 
      With the help of committed priests and active laypeople, the parish continues to be a center of worship, 
      service, and evangelization in the area.
    </p>
    <p>____________________________________________________________________________________________________</p>

  </div>
</section>
<section class="historys">
<div class="history-texts">
  <p><strong>Here are the parish priests who have served Santa Krus Parish over the years:</strong></p>
</div>

<div class="priests-gallery">
  <div class="priest">
    <img src="../stylesheets/assets/FrBantog.png" alt="Priest 1">
    <p class="priest-role">Former Parish Priest</p>
    <p class="priest-years">2003 - 2006</p>
    <p class="priest-name">Rev. Fr. Efren Bantog, SOLT</p>
  </div>

  <div class="priest">
    <img src="../stylesheets/assets/FrVillanueva.png" alt="Priest 2">
    <p class="priest-role">Former Parish Priest </p>
    <p class="priest-years">2007 - 2013<p>
    <p class="priest-name">Rev. Fr. Wilfredo 
Villanueva, SOLT
</p>
  </div>

  <div class="priest">
    <img src="../stylesheets/assets/FrLopez.png" alt="Priest 3">
    <p class="priest-role">Former Parish Priest 
</p>
    <p class="priest-years">2013 - 2018</p>
    <p class="priest-name">Rev. Fr. Aldrin 
Lopez</p>
  </div>

  <div class="priest">
    <img src="../stylesheets/assets/FrNitollama.png" alt="Priest 4">
    <p class="priest-role">Parish Priest 
</p>
    <p class="priest-years">2018 - Present</p>
    <p class="priest-name">Rev. Fr. James 
Nitollama</p>
  </div>
</div>


</section>

<section class="mission-vision">
  <h3>Mission & Vision</h3>

</section>

<div class="mission-image">
  <img src="../stylesheets/assets/2.jpeg" alt="Mission Image">
</div>

<div class="mission-text">
    <p>
      We, the Church of Novaliches, are a Spirit-led Christ-centered community of disciples listening and 
      responding to the needs and aspirations of people, 
      journeying and evangelizing together with them, 
      and with the Blessed Virgin Mary as an exemplary model, 
      towards the newness and fullness of life.
    </p>
    
  </div>


  <section class="parish-priest">
  <h3>Parish Priest</h3>
</section>

<div class="priest-gallery">
  <div class="priest">
    <img src="../stylesheets/assets/FrNitolama.png" alt="Parish Priest 1">
    <p class="priest-name">Rev. Fr. James Nitollama</p>
  </div>

  <div class="priest">
    <img src="../stylesheets/assets/FrTatoy.png" alt="Parish Priest 2">
    <p class="priest-name">Rev. Fr. Noel TATOY</p>
  </div>
</div>


  
  <footer class="site-footer-simple">
    <p>&copy; 2025 Santa Krus Parish, Bagumbong Caloocan. All Rights Reserved.</p>
  </footer>
</body>
</html>
