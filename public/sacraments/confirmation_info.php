<?php
require_once '../connection.php';
session_start();

$user_name = $_SESSION['givenname'] ?? null; 
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
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Santa Krus Parish</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../stylesheets/public/sacrament_info.css" />
</head>
<body>

<div class="header-banner" style="background-image: url('../../stylesheets/assets/banner/confirmation.jpg');">
    <h1>Confirmation</h1>
</div>

<div class="breadcrumb">
    <a href="../sacraments.php">Sacraments</a><span>Confirmation</span>
</div>


<!-------------------- NAVBAR -------------------->
<header>
    <nav class="navbar">
        <ul class="nav-links">
            <li><a href="#"><img src="../../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" /></a></li>
            <li><a href="../homepage.php" class="active">Home</a></li>
            <li><a href="../history.php">History</a></li>
            <li><a href="../events.php">News & Events</a></li>
            <li><a href="../sacraments.php">Sacraments</a></li>
            <li><a href="../donation.php">Donate</a></li>
            <li><a href="../prayer_page.php">Prayer Request</a></li>
            <li><a href="../contact.php">Contact</a></li>

            <?php
            session_start();

            if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
                // USER NOT LOGGED IN → Show Login button
                echo '<li><a href="../login.php" id="login-btn" style="color:white;"><i class="fa-regular fa-user"></i>   Log In</a></li>';
            } else {
                // USER LOGGED IN → Show Username + Dropdown
                ?>
            <p style="color:white; padding-left: 17px; padding-right: 17px;">|</p>
                <li class="li-dropdown">
                    <a href="#"><?= htmlspecialchars($user_name) ?></a>
                    <ul class="dropdown">
                        <li><a href="../logout.php">Logout</a></li>
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



<div class="content">

    <!-- ORIGINAL SECTION + NEW TEXT INSERTED -->
    <h2>What is Confirmation?</h2>
    <img src="../../stylesheets/assets/banner/confirmation-ceremony.jpg" alt="Confirmation Ceremony" class="section-img">

    <!-- NEW CONTENT ADDED WITHOUT REMOVING OLD CONTENT -->
    <p>
        Confirmation is one of the seven sacraments through which Catholics pass in the process of their religious upbringing.
        It is a sacrament of initiation, which completes baptism through the sealing of the Holy Spirit and anoints the recipient
        as priest, prophet, and king. Our goal is to cultivate a relationship with Jesus Christ— inspiring to become an active
        witness to all we encounter during our lives.
    </p>

    <!-- ORIGINAL TEXT YOU HAD -->
    <p>
        Confirmation is the sacrament that strengthens and deepens the grace received at Baptism through the Holy Spirit.
    </p>

    <div class="divider"></div>

    <!-- ORIGINAL SECTION KEPT -->
    <h2>Importance of Confirmation</h2>
    <img src="../../stylesheets/assets/banner/holy-spirit.jpg" alt="Holy Spirit" class="section-img">
    <p>
        It empowers the faithful to live a Christian life courageously and witness to their faith.
    </p>

    <div class="divider"></div>

    <!-- INSERTING NEW DATES SECTIONS -->
    <h2>What are the dates for Confirmation?</h2>
    <p>Dates are announced yearly by the parish. Confirmations typically take place once a year.</p>

    <div class="divider"></div>

    <h2>What are the dates for listing?</h2>
    <p>Listing opens 1–2 months before the scheduled Confirmation day. Visit the parish office for the current schedule.</p>

    <div class="divider"></div>

    <!-- ORIGINAL REQUIREMENTS + NEW REQUIRED DOCUMENTS ADDED -->
    <h2>Requirements</h2>
    <ul class="info-list">
        <!-- NEW ITEMS -->
        <li>• Certificate of Baptism</li>
        <li>• Proof of Attendance in Catechesis Program</li>

        <!-- ORIGINAL ITEMS FROM YOUR OLD VERSION -->
        <li>• Baptized Catholic</li>
        <li>• Attend preparation classes</li>
        <li>• Have a sponsor who is a confirmed Catholic</li>
    </ul>

    <div class="divider"></div>

    <!-- ORIGINAL SCHEDULE KEPT -->
    <h2>Schedule</h2>
    <p>Confirmation ceremonies are usually held annually. Contact the parish for exact dates.</p>

    <div class="divider"></div>

    <!-- ORIGINAL CONTACT KEPT -->
    <h2>Contact</h2>
    <p>For inquiries, visit the parish office or call the church hotline.</p>

</div>


<a href="../members/confirmation.php" class="reserve-btn">Reserve Confirmation</a>



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

</body>
</html>

