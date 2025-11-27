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

// Pagination setup
$perPage = 3;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Get total announcements
$totalStmt = $dbh->query("SELECT COUNT(*) FROM announcements");
$totalAnnouncements = $totalStmt->fetchColumn();
$totalPages = ceil($totalAnnouncements / $perPage);

// Fetch current page announcements
$announcements = [];
try {
    $stmt = $dbh->prepare("SELECT * FROM announcements ORDER BY date_posted DESC LIMIT :offset, :perpage");
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':perpage', $perPage, PDO::PARAM_INT);
    $stmt->execute();
    $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $db_error = $e->getMessage();
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Santa Krus Parish – Diocese of Novaliches</title>  
  <link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../stylesheets/public/events.css">
</head>
<body>

  <!-- NAVBAR -->
  <header>
    <nav class="navbar">
      <img src="../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" />
      <ul class="nav-links">
        <li><a href="homepage.php">Home</a></li>
        <li><a href="history.php">History</a></li>
        <li><a href="events.php" class="active">Events</a></li> 
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

  <section class="masthead">
    <div class="container">
      <h1 class="site-title">SANTA KRUS PARISH</h1>
      <p class="site-subtitle">DIOCESE OF NOVALICHES</p>
    </div>
    <div class="hero-image" role="img" aria-label="Interior of church">
      <img src="../stylesheets/assets/banner.png" alt="Church Interior" />
    </div>
  </section>

  <section class="events-header">
    <div class="container">
      <h2>Parish Events</h2>
    </div>
  </section>

<section class="ic-section">
  <div class="container ic-grid">
    <div class="calendar" aria-label="Calendar">
      <div class="calendar-header">
        <button class="chev left" id="ic-prev" aria-label="Previous month">&lsaquo;</button>
        <div class="month"><span id="ic-month"></span></div>
        <button class="chev right" id="ic-next" aria-label="Next month">&rsaquo;</button>
      </div>
      <div class="dow">
        <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
      </div>
      <ol class="days" id="ic-days" aria-live="polite"></ol>
    </div>

    <aside class="ic-panel" aria-live="polite">
      <h3 id="ic-title">Select a date</h3>
      <p id="ic-text">Click any highlighted day to see details here.</p>
    </aside>
  </div>
</section>

<!--

  <section class="events-header">
    <div class="container">
      <h2>Announcements</h2>
    </div>
  </section>

  <section class="announcements">
  <div class="container">
    <div class="announcement">
      <img src="../stylesheets/assets/FEAST.jpeg" alt="Feast of the Day">
      <div class="text">
        <h3>FEAST OF THE DAY | March 25, 2025</h3>
        <p>The Solemnity of the Annunciation, celebrated on March 25, honors the angel Gabriel’s message to Mary that she would bear Jesus. Her faithful "yes" began God’s plan of salvation. This feast, observed since the 7th century, reminds us of the power of trust in God and inspired prayers like the Ave Maria and Angelus.</p>
      </div>
    </div>

    <div class="announcement">
      <img src="../stylesheets/assets/cross.jpeg" alt="Parish Announcement">
      <div class="text">
        <h3>PARISH ANNOUNCEMENTS | February 14, 2025</h3>
        <p>Please bring your old palm branches (palaspas) to the parish. These will be burned on Monday, February 15, at 5:00 PM. A Holy Mass will follow at 6:00 PM...<strong>More</strong></p>
      </div>
    </div>

    <div class="announcement">
      <img src="../stylesheets/assets/white.jpeg" alt="Diocese of Novaliches">
      <div class="text">
        <h3>DIOCESE OF NOVALICHES | Feb 2, 2025</h3>
        <p>In Loving Memory of Rev. Fr. Luciano Ariel Felloni (28 May 1973 – 2 February 2025) With deep sorrow and gratitude, we mourn the passing of Rev. Fr. Luciano Ariel Felloni, who returned to the Lord on February 2, 2025, the Feast of the Presentation...<strong>More</strong></p>
      </div>
    </div>
      -->

      <section class="New">
   <h2>News and <span class="brown-text">Announcement</span></h2>

  <div class="announcements-container">
    <?php
      $latestStmt = $dbh->query("SELECT * FROM announcements ORDER BY date_posted DESC LIMIT 3");
      $latestAnnouncements = $latestStmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <?php if (!empty($latestAnnouncements)) : ?>
        <?php foreach ($latestAnnouncements as $announcement) : ?>
            <a href="announcement_feed.php?id=<?php echo $announcement['ann_id']; ?>" class="announcement-card">
                <div class="card-image">
                    <img src="<?php echo !empty($announcement['image_path']) ? htmlspecialchars($announcement['image_path']) : 'placeholder.jpg'; ?>" alt="Announcement Image">
                </div>

                <div class="card-content">
                    <h3><?php echo htmlspecialchars($announcement['title']); ?></h3>
                    <p><?php echo htmlspecialchars(substr($announcement['content'], 0, 120)) . '...'; ?></p>
                    <span class="date"><?php echo date("F j, Y", strtotime($announcement['date_posted'])); ?></span>
                </div>
            </a>
        <?php endforeach; ?>
    <?php else : ?>
        <p>No announcements found.</p>
    <?php endif; ?>
  </div>
<div class="see-more-container">
    <a href="announcements.php" class="see-more">see more..</a>
</div>
  </div>
</section>

  <footer class="site-footer-simple">
    <div class="container">
      <p>&copy; <span id="year"></span> Santa Krus Parish. All rights reserved.</p>
    </div>
  </footer>

  
  <script src="events.js"></script>
</body>
</html>
