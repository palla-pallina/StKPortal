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

<div class="header-banner" style="background-image: url('../../stylesheets/assets/banner/eucharist.jpg');">
    <h1>Eucharist</h1>
</div>

<div class="breadcrumb">
    <a href="../sacraments.php">Sacraments</a><span>Eucharist</span>
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

    <h2>What is the Holy Eucharist?</h2>
    <img src="../../stylesheets/assets/banner/eucharist1.jpg" alt="Holy Eucharist" class="section-img">

    <p>
        The Holy Eucharist is the sacrament where Jesus Christ is truly present—Body, Blood, Soul, and Divinity—
        under the appearance of bread and wine. It is the center of Catholic worship and the summit of the Christian life.
    </p>

    <div class="divider"></div>


    <h2>Daily Communion — Why Attend Mass Daily?</h2>
    <img src="../../stylesheets/assets/banner/daily-mass.jpg" alt="Daily Mass" class="section-img">

    <p>The Church encourages daily communion because:</p>

    <ul class="info-list">
        <li>The Mass is the source and summit of the Catholic faith.</li>
        <li>Daily Mass allows us to hear the Gospel and reflect on the homily.</li>
        <li>Receiving Jesus in the Eucharist strengthens us spiritually.</li>
        <li>Praising God each morning sets the tone for the day.</li>
        <li>The Mass is ongoing spiritual formation.</li>
        <li>It brings us closer to God daily.</li>
    </ul>

    <div class="divider"></div>


    <h2>What are the dates for Daily Communion?</h2>
        <div class="mass-calendar">
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
    </p>

    <div class="divider"></div>


    <h2>First Communion</h2>
    <img src="../../stylesheets/assets/banner/first-communion.jpg" alt="First Communion" class="section-img">

    <p>
        First Holy Communion is a special sacrament where children receive the Eucharist for the first time.  
        A baptized child is now welcomed to the Lord’s table and participates fully in the Mass.
    </p>

    <p>
        The ceremony takes place during a regular Catholic Mass (60–90 minutes).  
        Children receive Holy Communion accompanied by their parents and godparents.
    </p>

    <div class="divider"></div>


    <h2>What are the dates for listing for First Communion?</h2>
    <p>
        Listing usually opens from <strong>January to March</strong>, depending on the parish schedule.
    </p>

    <div class="divider"></div>


    <h2>What are the required documents?</h2>
    <ul class="info-list">
        <li>• Baptismal Certificate of the child</li>
        <li>• Parents’ Marriage Certificate (if applicable)</li>
        <li>• Certificate of Residency (if required)</li>
        <li>• 2×2 ID picture of the child</li>
        <li>• Completed parish registration form</li>
    </ul>

</div>


<a href="../members/first_comm.php" class="reserve-btn">Reserve Holy Communion</a>



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

