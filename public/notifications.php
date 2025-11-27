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

// Restrict access to registered users only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
    echo "<script>
            alert('Please log in with an account.');
            window.location.href='homepage.php';
          </script>";
    exit();
}
$user_name = $_SESSION['username'] ?? null;

// Ensure user session data is populated
$user_id = $_SESSION['user_id'] ?? null;
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Santa Krus Parish</title>
  <link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../stylesheets/public/notifications.css" />
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
        <li><a href="homepage.php">Home</a></li>
        <li><a href="history.php">History</a></li>
        <li><a href="events.php">Events</a></li>
        <li><a href="donation.php">Donate</a></li>
        <li><a href="sacraments.php">Sacraments</a></li>
        <li><a href="prayer-page.php">Prayer Request</a></li>
        <li><a href="contact.php">Contact</a></li>
        
        <p style="color:white;">|</p>
        <?php
        session_start(); // required

        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {

            // USER NOT LOGGED IN → Show Login button
            echo '<li><a href="login.php" class="login-btn">Login</a></li>';

        } else {

            // USER LOGGED IN → Show Username + Dropdown
            echo '<li class="logout-dropdown">';
            echo '<a href="#">' . htmlspecialchars($user_name) . '</a>';
            echo '<ul class="dropdown">';
            echo '<li><a href="logout.php">Logout</a></li>';
            echo '</ul>';
            echo '</li>';
        }
        ?>

        <li class="nav-notification">
            <a href="notifications.php">
                <span class="bell">&#128276;</span>
                <?php if ($unreadCount > 0): ?>
                    <span class="badge"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>
        </li>   

      </ul>
    </nav>
  </header>
  <!-- NAVBAR END-->

  <main>


    <section class="hero">
      <h2>Notifications</h2>
      </div>
    </section>

<div class="notification-container">

    <?php foreach ($notifications as $note): ?>
    <div class="notification <?= $note['type']; ?>">
        <div class="text">
            <strong><?= $note['title']; ?></strong>
            <p><?= $note['message']; ?></p>
            <small><?= date("F j, Y g:i A", strtotime($note['created_at'])); ?></small>
        </div>
    </div>
    <?php endforeach; ?>

</div>

<a href="#" class="btn-clear">Clear All</a>



  </main>
   <footer class="site-footer-simple">
    <p>&copy; 2025 Santa Krus Parish, Bagumbong Caloocan. All Rights Reserved.</p>
  </footer>


</body>
</html>
