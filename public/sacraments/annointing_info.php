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

<div class="header-banner" style="background-image: url('../../stylesheets/assets/banner/aots.jpg');">
    <h1>Anointing of the Sick</h1>
</div>

<div class="breadcrumb">
    <a href="../sacraments.php">Sacraments</a><span>Annointing of the Sick</span>
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

    <!-- TITLE SECTION -->
    <h2>What is Anointing of the Sick?</h2> 

    <!-- NEW FULL DESCRIPTION INSERTED -->
    <p>
        The Sacrament of the Anointing of the Sick is the Church’s sacrament for those who are sick and aging,
        and is a sacred moment when we pray for God’s grace to strengthen someone who is suffering from any form
        of illness. An anointing Mass is generally offered during the seasons of Lent and Easter. Arrangements
        can be made for a priest to come to your home if you are homebound or too ill to come to daily Mass.
    </p>

    <!-- ORIGINAL TEXT KEPT -->
    <p>
        This sacrament brings spiritual and sometimes physical healing to those who are seriously ill or elderly.
    </p>

    <div class="divider"></div>

    <!-- IMPORTANCE SECTION WITH NEW INSERTED CONTENT -->
    <h2>Importance</h2>
    <img src="../../stylesheets/assets/banner/holy-oil.jpg" alt="Holy Oil" class="section-img">

    <p>
        The healing ministry of the Church follows that of Jesus and is concerned with the healing of the whole person—
        the body, mind, and spirit. Sometimes a person is cured of a bodily illness. Sometimes there is psychological
        or spiritual healing. What is certain is that the healing taking place strengthens a sick person and helps
        them to bear illness.
    </p>

    <!-- ORIGINAL IMPORTANCE TEXT KEPT -->
    <p>
        Anointing of the Sick offers strength, peace, and courage in the face of illness or old age.
    </p>

    <div class="divider"></div>

    <!-- NEW SECTION INSERTED -->
    <h2>When Should a Person Be Anointed?</h2>
    <p>
        The Rite of Anointing indicates there is no need to wait until a person is at the point of death.
        A careful judgment about the serious nature of the illness is sufficient. The Sacrament may be repeated
        if the sick person recovers after the anointing but becomes ill again, or if the illness worsens.
    </p>

    <p>
        A person should be anointed before surgery when a dangerous illness is the reason for the intervention.
        Parishioners who are seriously ill in any way should request the Sacrament.
    </p>

    <div class="divider"></div>

    <!-- ORIGINAL REQUIREMENTS KEPT -->
    <h2>Requirements</h2>
    <ul class="info-list">
        <li>• Baptized Catholic</li>
        <li>• Serious illness or age-related infirmity</li>
        <li>• Confession recommended before the sacrament</li>
    </ul>

    <div class="divider"></div>

    <!-- ORIGINAL SCHEDULE -->
    <h2>Schedule</h2>
    <p>Available upon request. Contact the parish office to arrange a priest.</p>

    <div class="divider"></div>

    <!-- ORIGINAL CONTACT -->
    <h2>Contact</h2>
    <p>For inquiries, visit the parish office or call the church hotline.</p>

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

</body>
</html>

