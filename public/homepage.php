
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


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Santa Krus Parish</title>
  <link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../stylesheets/public/homepage.css" />
</head>
<body>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const links = document.querySelectorAll(".nav-links a");
    const currentPage = window.location.pathname.split("/").pop(); 

    links.forEach(link => {
        if (link.getAttribute("href") === currentPage) {
            link.classList.add("active");
        }
    });
});
</script>
 
  <!-- NAVBAR -->
  <header>
    <nav class="navbar">
      <img src="../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" />
      <ul class="nav-links">
          <li><a href="homepage.php" class="active">Home</a></li>
          <li><a href="history.php">History</a></li>
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
    <section class="hero">
      <h1>SANTA KRUS PARISH</h1>
      <h2>DIOCESE OF NOVALICHES</h2>
      <div class="hero-image">
      </div>
    </section>

  <!-- Slide-->
<section class="slideshow">
    <div class="slides">
        <img src="assets/sim1.jpg" alt="Slide 1">
        <img src="assets/sim2.jpg" alt="Slide 2">
        <img src="assets/sim3.jpg" alt="Slide 3">
        <img src="assets/sim4.jpg" alt="Slide 4">
    </div>
</section>

<script>
const slidesContainer = document.querySelector(".slides");
const slides = document.querySelectorAll(".slides img");
let currentSlide = 0;

// Show slide function
function showSlide(index) {
    slidesContainer.style.transform = `translateX(-${index * 100}%)`;
}

// Auto-slide every 3 seconds
setInterval(() => {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
}, 3000);

// Show first slide initially
showSlide(currentSlide);
</script>


<section class="New">
   <h2>News and Announcement</h2>

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

</section>

    <section class="livemass-header">
      <h2>Live Mass</h2>
      <h4>November 23, 2025 | 7:30 AM</h4>
      </div>
    </section>

<div class="livemass-section">
  
  <div class="livemass-video">
    <iframe 
      src="https://www.facebook.com/plugins/video.php?height=314&href=https%3A%2F%2Fwww.facebook.com%2FDioNova.StaKrusParish%2Fvideos%2F2234770327044655%2F&show_text=false&width=560&t=0"
      frameborder="0" 
      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
      referrerpolicy="strict-origin-when-cross-origin" 
      allowfullscreen>
    </iframe>
  </div>
</div>


 <section class="services-section">

    <h2 class="section-title">SACRAMENTS</h2>

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
