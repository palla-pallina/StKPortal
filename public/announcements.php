<?php
require_once 'connection.php';
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
  <title>Santa Krus Parish - Announcements</title>
  <link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../stylesheets/public/announcements.css" />
</head>
<body>

  <!-- NAVBAR -->
  <header>
    <nav class="navbar">
      <img src="../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" />
      <ul class="nav-links">
        <li><a href="homepage.php" class="active">Home</a></li>
        <li><a href="history.php">History</a></li>
        <li><a href="events.php">Events</a></li> 
        <li><a href="donation.php">Donate</a></li>
        <li><a href="baptism.php">Sacraments</a></li>
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
    <div class="section-wrapper">
      <div class="breadcrumb">
          <a href="events.php">Events</a><span>Announcements</span>
      </div>

      <div class="container">
          <h1>Announcements</h1><br></br>
          
          <div class="feed">
          <?php if(!empty($announcements)): ?>
              <?php foreach($announcements as $ann): 
                  $img = !empty($ann['image_path']) && file_exists($ann['image_path']) 
                      ? $ann['image_path'] 
                      : 'placeholder.png';
              ?>
                  <div class="card" 
                       data-title="<?= htmlspecialchars($ann['title']) ?>" 
                       data-content="<?= htmlspecialchars($ann['content']) ?>" 
                       data-img="<?= htmlspecialchars($img) ?>"
                       data-date="<?= date('F j, Y', strtotime($ann['date_posted'])) ?>">
                      <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($ann['title']) ?>">
                      <div class="card-content">
                          <div class="date"><?= date('F j, Y', strtotime($ann['date_posted'])) ?></div>
                          <h2><?= htmlspecialchars($ann['title']) ?></h2>
                          <p><?= htmlspecialchars(substr($ann['content'], 0, 120)) ?>...</p>
                          <a href="javascript:void(0)" class="read-more">Read More</a>
                      </div>
                  </div>
              <?php endforeach; ?>

              <!-- Pagination -->
              <div class="pagination">
                  <?php if($page > 1): ?>
                      <a href="?page=<?= $page-1 ?>">« Previous</a>
                  <?php else: ?>
                      <span class="disabled">« Previous</span>
                  <?php endif; ?>

                  <?php for($i=1; $i<=$totalPages; $i++): 
                      if($i == $page): ?>
                          <span class="active"><?= $i ?></span>
                      <?php else: ?>
                          <a href="?page=<?= $i ?>"><?= $i ?></a>
                      <?php endif;
                  endfor; ?>

                  <?php if($page < $totalPages): ?>
                      <a href="?page=<?= $page+1 ?>">Next »</a>
                  <?php else: ?>
                      <span class="disabled">Next »</span>
                  <?php endif; ?>
              </div>

          <?php else: ?>
              <p style="text-align:center; font-size:1.1rem;">No announcements posted yet.</p>
          <?php endif; ?>
          </div>
      </div>
    </div>
  </main>

  <!-- Modal -->
  <div class="modal" id="modal">
      <div class="modal-content">
          <span class="modal-close" id="modalClose">&times;</span>
          <img id="modalImg" src="" alt="">
          <div class="date" id="modalDate"></div>
          <h2 id="modalTitle"></h2>
          <p id="modalContent"></p>
      </div>
  </div>

  <script>
const cards = document.querySelectorAll('.card');
const modal = document.getElementById('modal');
const modalImg = document.getElementById('modalImg');
const modalTitle = document.getElementById('modalTitle');
const modalContent = document.getElementById('modalContent');
const modalDate = document.getElementById('modalDate');
const modalClose = document.getElementById('modalClose');

cards.forEach(card => {
      const readMore = card.querySelector('.read-more');
      readMore.addEventListener('click', (e) => {
          e.stopPropagation();
          modalImg.src = card.dataset.img;
          modalTitle.textContent = card.dataset.title;
          modalContent.textContent = card.dataset.content;
          modalDate.textContent = card.dataset.date;
          modal.style.display = 'flex';
      });

      card.addEventListener('click', () => {
          modalImg.src = card.dataset.img;
          modalTitle.textContent = card.dataset.title;
          modalContent.textContent = card.dataset.content;
          modalDate.textContent = card.dataset.date;
          modal.style.display = 'flex';
      });
});

modalClose.addEventListener('click', () => modal.style.display = 'none');
modal.addEventListener('click', e => { if(e.target === modal) modal.style.display = 'none'; });
  </script>

  <footer class="site-footer-simple">
    <p>&copy; 2025 Santa Krus Parish, Bagumbong Caloocan. All Rights Reserved.</p>
  </footer>
</body>
</html>