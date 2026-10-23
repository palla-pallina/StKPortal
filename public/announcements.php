<?php
require_once 'connection.php';
session_start();
$user_id = $_SESSION['user_id'];


// ---------------------------------------------------------
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ------------------------------
// LOAD PHPMailer
// ------------------------------
require 'vendor/PHPMailer/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/PHPMailer/src/SMTP.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ------------------------------
    // COLLECT & SANITIZE INPUT
    // ------------------------------
    $name    = trim($_POST["name"]);
    $email   = trim($_POST["email"]);
    $subject = trim($_POST["subject"]);
    $msg     = trim($_POST["message"]);

    if ($name && $email && $subject && $msg) {

        try {
            $mail = new PHPMailer(true);

            // ------------------------------
            // SMTP SETTINGS (GMAIL EXAMPLE)
            // ------------------------------
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = ' ';     // EMAIL
            $mail->Password   = ' ';   // PASSWORD
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // ------------------------------
            // EMAIL HEADERS
            // ------------------------------
            $mail->setFrom(' ', 'StKPortal');
            $mail->addAddress(' '); 
            $mail->addReplyTo($email, $name);

            // ------------------------------
            // EMAIL CONTENT
            // ------------------------------
            $mail->isHTML(true);
            $mail->Subject = "Inquiry: " . $subject;

            $mail->Body = "
                <p><strong>Name:</strong> {$name}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Subject:</strong> {$subject}</p>
                <p><strong>Message:</strong><br>{$msg}</p>
            ";

            $mail->AltBody = "
                Name: $name
                Email: $email
                Subject: $subject
                Message: $msg
            ";

            // ------------------------------
            // SEND EMAIL
            // ------------------------------
            $mail->send();
            $message = "✅ Your message has been sent successfully!";

        } catch (Exception $e) {
            $message = "❌ Mail Error: {$mail->ErrorInfo}";
        }

    } else {
        $message = "❌ All fields are required.";
    }
}
// ---------------------------------------------------------


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

$user_name = $_SESSION['givenname'] ?? null; 

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../stylesheets/public/user_footer_navbar.css" />
    <link rel="stylesheet" href="../stylesheets/public/announcements.css" />
</head>
<body>


<!-------------------- NAVBAR -------------------->
<header>
    <nav class="navbar">
        <ul class="nav-links">
            <li><a href="#"><img src="../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" /></a></li>
            <li><a href="homepage.php">Home</a></li>
            <li><a href="history.php">History</a></li>
            <li><a href="events.php" class="active">News & Events</a></li>
                        <!--<li><a href="sacraments.php">Sacraments</a></li>-->

            <li class="li-dropdown">
                <a href="#">Parish <i class="fa-solid fa-angle-down"></i></a>
                <ul class="dropdown">
                    <li style="border-bottom: 1px solid var(--yellow);"><a href="sacraments.php">Sacraments</a></li>
                    <li><a href="services.php">Services</a></li>
                </ul>
            </li>
            
            <li><a href="donation.php">Donate</a></li>
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
                        <li style="border-bottom: 1px solid var(--yellow);"><a href="members/status.php">Status</a></li>
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

  <main>
    <div class="section-wrapper">
      <div class="breadcrumb">
          <a href="events.php">Events</a><span>Announcements</span>
      </div>

      <div class="container">
          <h2 class="container-title">News & Announcements</h2>
          
          <div class="feed">
          <?php if(!empty($announcements)): ?>
                <?php foreach($announcements as $ann): 

                    $dbPath  = $ann['image_path']; // uploads/picture.png
                    $realPath = "../private/stk/" . $dbPath;
                    $webPath  = "../private/stk/" . $dbPath;

                    $img = (!empty($dbPath) && file_exists($realPath))
                        ? $webPath
                        : "../stylesheets/assets/placeholder.png";
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
                          <p><?= htmlspecialchars(substr($ann['content'], 0, 500)) ?>...</p>
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


<!-- FOOTER -->
<footer class="custom-footer">

  <div class="footer-left">
    <h2>Santa Krus Parish</h2>
    <p>Serving the community of Bagumbong, Caloocan with faith and love.</p>
    <p><b>Email Address</b><br>stakrusparish@gmail.com</p>
    <p><b>Contact Number</b><br>(02) 8984-3933</p>

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