<?php
require_once 'connection.php';
session_start();

$user_id = $_SESSION['user_id'];
$givenname = $_SESSION['givenname'] ?? null; 

/*--------- NOTIFICATION HANDLING START
- for notification icon at navbar
- retrieves all notifications belonging to logged in user */
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
/*--------- NOTIFICATION HANDLING END */

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
    <link rel="stylesheet" href="../stylesheets/public/homepage.css" />
    <link rel="stylesheet" href="../stylesheets/public/ann_modal.css" />
</head>
<body>
    
<!---- idk pero don't remove ---->
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

<!-------------------- NAVBAR -------------------->
<header>
    <nav class="navbar">
        <ul class="nav-links">
            <li><a href="#"><img src="../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" /></a></li>
            <li><a href="homepage.php" class="active">Home</a></li>
            <li><a href="history.php">History</a></li>
            <li><a href="events.php">News & Events</a></li>
            <li><a href="sacraments.php">Sacraments</a></li>
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
                    <a href="#"><?= htmlspecialchars($givenname) ?></a>
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


<!----------------- SLIDESHOW ----------------->
<section class="slideshow">
    <section class="hero">
        <h1>Santa Krus Parish</h1>
        <h2>DIOCESE OF NOVALICHES</h2>
    </section>

    <div class="slides">
        <img src="assets/sim1.jpg" alt="Slide 1" class="active">
        <img src="assets/sim2.jpg" alt="Slide 2">
        <img src="assets/sim3.jpg" alt="Slide 3">
        <img src="assets/sim4.jpg" alt="Slide 4">
    </div>
</section>

<script>
const slides = document.querySelectorAll(".slides img");
let currentSlide = 0;

function showSlide(index) {
    slides.forEach((slide, i) => {
        slide.classList.remove("active");
        if (i === index) slide.classList.add("active");
    });
}

setInterval(() => {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
}, 6000);
</script>

<!---------------- MASS SCHED ----------------->
<section class="mass-sched">
    <div class="mass-layout">

        <div class="mass-calendar">
                <h2 class="mass-sched-title">Mass Schedule</h2>
            <table>
                <thead>
                    <tr>
                        <th>Monday</th>
                        <th>Tuesday</th>
                        <th>Wednesday</th>
                        <th>Thursday</th>
                        <th>Friday</th>
                        <th>Saturday</th>
                        <th>Sunday</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>6:00 AM</td>
                        <td>6:00 AM</td>
                        <td>6:00 AM</td>
                        <td>6:00 AM</td>
                        <td>6:00 AM</td>
                        <td>6:00 AM</td>
                        <td>5:30 AM</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>6:00 PM</td>
                        <td>6:00 PM</td>
                        <td>6:00 PM</td>
                        <td>6:00 PM</td>
                        <td>6:00 PM</td>
                        <td>7:30 AM</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>10:30 AM</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>3:30 PM</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>5:30 PM</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- RIGHT: PARISH SERVICES BUTTONS -->
        <div class="parish-services">
            <h3>Parish Services</h3>

            <div class="services-buttons">
                <a href="members/certificate_request.php" class="service-btn">Certificate</a>
                <a href="members/baptism.php" class="service-btn">Baptism</a>
                <a href="members/first_comm.php" class="service-btn">Holy Communion</a>
                <a href="members/confirmation.php" class="service-btn">Confirmation</a>
                <a href="members/wedding.php" class="service-btn">Wedding</a>
                <a href="members/funeral.php" class="service-btn">Funeral Mass</a>
            </div>
                    <div class="office-hours">
                <b>Office Hours</b><br>
                9:00 AM – 6:00 PM (Tuesday to Sunday)
            </div>
        </div>

    </div>
</section>
<!-------------- MASS SCHED END --------------->

<!--------------- LIVE MASS ------------------->
<section class="livemass-header">
    <h2>Live Mass</h2>
    <h4>December 7, 2025 • 7:30 AM</h4>
</section>
<section class="livemass-section">
    <div class="livemass-video">
        <iframe
            src="//www.facebook.com/plugins/video.php?height=314&href=https%3A%2F%2Fwww.facebook.com%2FDioNova.StaKrusParish%2Fvideos%2F2356318521492438%2F&show_text=false&width=560&t=0"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            referrerpolicy="strict-origin-when-cross-origin"
            allowfullscreen>
        </iframe>
    </div>
</section>
<!------------- LIVE MASS END ----------------->

<!--------------- SACRAMENTS ------------------>
<section class="services-header">
    <h2>Sacraments</h2>
</section>
<section class="services-section">
    
    <div class="services-grid-1">
        <a href="sacraments/baptism_info.php">
            <div class="card baptism" style="background-image: url('assets/Baptism.jpg');">
                <div class="card-overlay">
                    <h3>Baptism</h3>
                    <p>Christian initiation sacrament</p>
                </div>
            </div>
        </a>

        <a href="sacraments/eucharist_info.php">
        <div class="card eucharist" style="background-image: url('assets/euharistia.jpg');">
            <div class="card-overlay">
                <h3>Eucharist</h3>
                <p>Receiving the body and blood of Christ</p>
            </div>
        </div>
        </a>


        <a href="sacraments/confirmation_info.php">
        <div class="card confirmation" style="background-image: url('assets/Confirmation.jpg');">
            <div class="card-overlay">
                <h3>Confirmation</h3>
                <p>Strengthening faith through the Holy Spirit</p>
            </div>
        </div>
        </a>


        <a href="sacraments/reconciliation_info.php">
        <div class="card reconciliation" style="background-image: url('assets/Reconciliatio.jpg');">
            <div class="card-overlay">
                <h3>Reconciliation</h3>
                <p>Confession and forgiveness of sins</p>
            </div>
        </div>
        </a>


    </div>

    <!-- row2 -------->
    <div class="services-grid-2">

        <a href="sacraments/annointing_info.php">
        <div class="card anointing" style="background-image: url('assets/Anointing of the Sick.jpg');">
            <div class="card-overlay">
                <h3>Anointing of the Sick</h3>
                <p>Healing and comfort for the ill</p>
            </div>
        </div>
        </a>

        <a href="sacraments/wedding_info.php">
            <div class="card marriage" style="background-image: url('assets/Marriage.jpg');">
                <div class="card-overlay">
                    <h3>Marriage</h3>
                    <p>Union of husband and wife in God</p>
                </div>
            </div>
        </a>

        <a href="sacraments/holyorders_info.php">
            <div class="card holyorders" style="background-image: url('assets/holy-orders.jpg');">
                <div class="card-overlay">
                    <h3>Holy Orders</h3>
                    <p>Ordination into the priesthood</p>
                </div>
            </div>
        </a>

    </div>
    
</section>
<!-------------- SACRAMENTS END --------------->

<!--------------- ANNOUNCEMENTS --------------->
<section class="announcements-header">
    <h2>News & Announcements</h2>
</section>

<section class="New">
    
    <div class="announcements-container">
        <?php
        $latestStmt = $dbh->query("SELECT * FROM announcements ORDER BY date_posted DESC LIMIT 4");
        $latestAnnouncements = $latestStmt->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <?php if (!empty($latestAnnouncements)) : ?>
            <?php foreach ($latestAnnouncements as $announcement) :

                $dbPath   = $announcement['image_path'];
                $realPath = "../private/stk/" . $dbPath;
                $webPath  = "../private/stk/" . $dbPath;

                $img = (!empty($dbPath) && file_exists($realPath))
                    ? $webPath
                    : "../stylesheets/assets/placeholder.jpg";
            ?>
                <div class="announcement-card">
                    <div class="card-image">
                        <img src="<?= htmlspecialchars($img); ?>" alt="Announcement Image">
                    </div>

                    <div class="card-content">
                        <h3><?= htmlspecialchars($announcement['title']); ?></h3>
                        <span class="date"><?= date("F j, Y", strtotime($announcement['date_posted'])); ?></span>
                        <p><?= htmlspecialchars(substr($announcement['content'], 0, 100)) . '...'; ?></p>
                        <a href="javascript:void(0)" 
                        class="read-more"
                        data-title="<?= htmlspecialchars($announcement['title']); ?>"
                        data-date="<?= date("F j, Y", strtotime($announcement['date_posted'])); ?>"
                        data-content="<?= htmlspecialchars($announcement['content']); ?>"
                        data-image="<?= htmlspecialchars($img); ?>"
                        >
                        Read More
                        </a>

                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>No announcements found.</p>
        <?php endif; ?>
    </div>

    <div class="see-more-container">
        <a href="announcements.php" class="see-more">view all...</a>
    </div>
</section>
<!------------ ANNOUNCEMENTS END -------------->

<!-- ANNOUNCEMENT MODAL -->
<div id="announcementModal" class="modal">
    <div class="modal-content">
        <span class="modal-close">&times;</span>

        <img id="modalImage" src="" alt="Announcement Image">

        <h2 id="modalTitle"></h2>
        <span class="date" id="modalDate"></span>
        <p id="modalContent"></p>
    </div>
</div>

<!-- FOOTER -->
<footer class="custom-footer">

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


<!------- ANNOUNCEMENT MODAL SCRIPT ---------->
<script>
document.querySelectorAll('.read-more').forEach(button => {
    button.addEventListener('click', function () {

        document.getElementById('modalTitle').textContent = this.dataset.title;
        document.getElementById('modalDate').textContent = this.dataset.date;
        document.getElementById('modalContent').textContent = this.dataset.content;
        document.getElementById('modalImage').src = this.dataset.image;

        document.getElementById('announcementModal').style.display = 'flex';
        document.body.classList.add('modal-open');
    });
});

/* CLOSE MODAL */
document.querySelector('.modal-close').addEventListener('click', () => {
    document.getElementById('announcementModal').style.display = 'none';
    document.body.classList.remove('modal-open');
});

/* CLOSE WHEN CLICKING OUTSIDE CONTENT */
window.addEventListener('click', function (e) {
    const modal = document.getElementById('announcementModal');
    if (e.target === modal) {
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');
    }
});
</script>

</body>
</html>

