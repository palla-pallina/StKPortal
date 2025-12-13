<?php
require_once '../connection.php';
session_start();

/*--------- USER ACCESS RESTRICT START
- restrict access to registered users only */
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
    echo "<script>
            alert('Please log in with an account.');
            window.history.back();
          </script>";
    exit();
}
$user_name = $_SESSION['givenname'] ?? null; 
// retrieves logged in user's username
$user_id = $_SESSION['user_id']; 
// retrieves logged in user ID
/*--------- USER ACCESS RESTRICT END */

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


/*--------- FOR AUTO FILL
- retrieves user information */
$stmt = $dbh->prepare("
    SELECT givenname, middlename, lastname, email, contact
    FROM users WHERE user_id = :id
");
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Store in session for JS access
foreach ($user as $key => $value) {
    $_SESSION[$key] = $value;
}
/*--------- FOR AUTO FILL END */
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Santa Krus Parish</title>
  <link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../stylesheets/public/user_footer_navbar.css" />
  <link rel="stylesheet" href="../../stylesheets/public/certicates.css" /> 
  <link rel="stylesheet" href="../../stylesheets/public/sacraments.css" />
</head>
<body>

<!-------------------- NAVBAR -------------------->
<header>
    <nav class="navbar">
        <ul class="nav-links">
            <li><a href="#"><img src="../../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" /></a></li>
            <li><a href="../homepage.php">Home</a></li>
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
<!----------------- NAVBAR END----------------->

<div class="header-banner" style="background-image: url('../../stylesheets/assets/banner.png');">
    <h1>Certificate Request</h1>
</div>

<main>
  <div class="reservation-form">
    <h3>Claiming of Certificate</h3>

    <!-- IMPORTANT: action + method + enctype -->
    <form action="handle/save_certificate_request.php" method="POST" enctype="multipart/form-data">

      <div class="form-row">
        <div>
          <label>Certificate Type</label><br>
          <select name="certificate_type" required>
            <option value="Baptism">Baptism</option>
            <option value="Wedding">Wedding</option>
          </select>
        </div>
      </div>

      <label>Name</label>
      <div class="form-row">
        <input name="request_firstname" type="text" id="first" placeholder="First Name" required />
        <input name="request_middlename" type="text" id="middle" placeholder="Middle Name" />
        <input name="request_lastname" type="text" id="last" placeholder="Last Name" required />
        <input name="request_suffix" type="text" id="suffix" placeholder="Suffix" />
      </div>

      <div class="form-row">
        <div>
          <label>Valid ID of the Person Concerned</label><br>
          <input name="id_picture" type="file" accept="image/*,.pdf" required />
        </div>
      </div>

      <div class="form-row">
        <div>
          <label>Receipt (if recent)</label><br>
          <input name="receipt_picture" type="file" accept="image/*,.pdf" />
        </div>
      </div>

      <label>Are you claiming a certificate for someone else?</label>
      <div class="form-radio">
        <label>
          <input type="radio" name="claiming_for" value="someone_else" onclick="toggleAuthorization(true)" required />
          <span>Yes</span>
        </label>
        <label>
          <input type="radio" name="claiming_for" value="self" onclick="toggleAuthorization(false)" required />
          <span>No</span>
        </label>
      </div>

      <div id="authorization-section" style="display: none;">

        <label>Owner's Name</label>
        <div class="form-row">
          <input name="owner_firstname" type="text" id="first" placeholder="Owner First Name" />
          <input name="owner_middlename" type="text" id="middle" placeholder="Owner Middle Name" />
          <input name="owner_lastname" type="text" id="last" placeholder="Owner Last Name" />
          <input name="owner_suffix" type="text" id="suffix" placeholder="Owner Suffix" />
        </div>

        <div class="form-row">
          <div>
            <label>Authorization Letter (with signature)</label><br>
            <input name="authorization_letter" type="file" accept="image/*,.pdf" />
          </div>
        </div>

        <div class="form-row">
          <div>
            <label>Valid ID of Authorized Person</label><br>
            <input name="authorized_id_picture" type="file" accept="image/*,.pdf" />
          </div>
        </div>
      </div>

      <div class="form-row">
        <div>
          <label>Contact Number</label><br>
          <input name="contact" type="number"id="contact" placeholder="Contact Number" required />
        </div>
        <div>
          <label>Email Address</label><br>
          <input name="email" type="email" id="email" placeholder="Email Address" required />
        </div>
      </div>

      <div class="form-row terms">
        <input type="checkbox" id="terms" name="terms" required />
        <label for="terms">
          I agree to the <a href="#" target="_blank">Terms & Conditions</a>.
          <span class="required">*</span>
        </label>
      </div>

      <div class="form-button-wrapper">
        <button type="submit">Send Request</button>
      </div>
    </form>
  </div>
</main>



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

<script>
document.addEventListener("DOMContentLoaded", () => {
    const first = "<?php echo $_SESSION['givenname'] ?? ''; ?>";
    const middle = "<?php echo $_SESSION['middlename'] ?? ''; ?>";
    const last = "<?php echo $_SESSION['lastname'] ?? ''; ?>";
    const suffix = "<?php echo $_SESSION['suffix'] ?? ''; ?>";
    const email = "<?php echo $_SESSION['email'] ?? ''; ?>";
    const contact = "<?php echo $_SESSION['contact'] ?? ''; ?>";

    document.getElementById('first').value = first;
    document.getElementById('middle').value = middle;
    document.getElementById('last').value = last;
    document.getElementById('suffix').value = suffix;
    document.getElementById('email').value = email;
    document.getElementById('contact').value = contact;
});
</script>

<script>
  function toggleAuthorization(show) {
    const section = document.getElementById('authorization-section');
    section.style.display = show ? 'block' : 'none';
  }
  
</script>

</body>
</html>