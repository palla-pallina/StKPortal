<?php
// Database connection
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "test_tree_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// Restrict access to registered users only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
    echo "<script>
            alert('Please log in with an account.');
            window.location.href='homepage.php';
          </script>";
    exit();
}
$user_name = $_SESSION['username'] ?? null;


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Santa Krus Parish</title>
  <link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../stylesheets/public/certicates.css" /> 
</head>
<body>


  <!-- NAVBAR -->
  <header>
    <nav class="navbar">
      <img src="../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" />
      <ul class="nav-links">
        <li><a href="homepage.php">Home</a></li>
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
    </nav>
  </header>
  <!-- NAVBAR END-->
  
  <main>
    <section class="hero">
      <h1>SANTA KRUS PARISH</h1>
      <h2>DIOCESE OF NOVALICHES</h2>
      <div class="hero-image">
        <img src="../stylesheets/assets/banner.png" alt="Church Interior" />
      </div>
    </section>
    <section class="sacraments">
      <h3>Sacraments</h3>
    </section>
    <section class="sacrament-reservation-wrapper">
      <div class="sacrament-cards">
        <a href="baptism.php" class="card-link">
          <div class="card">
            <img src="../stylesheets/assets/baptism.png" alt="Baptism" />
            <div class="card-label">Baptism</div>
          </div>
        </a>
        <a href="wedding.php" class="card-link">
        <div class="card">
          <img src="../stylesheets/assets/wedding.png" alt="Wedding" />
          <div class="card-label">Wedding</div>
        </div>
        <a href="funeral.php" class="card-link">
        <div class="card">
          <img src="../stylesheets/assets/funeral.png" alt="Funeral" />
          <div class="card-label">Funeral</div>
        </div>
      </a>
      </div>
      <div class="reservation-form">
  <h3>Claiming of Certificate</h3>
  <form>
    <div class="form-row">
      <div>
        <label>Certificate Type*</label><br>
        <select required>
          <option value="baptism">Baptism</option>
          <option value="wedding">Wedding</option>
          <option value="funeral">Funeral</option>
        </select>
      </div>
      <div>
        <label>Pick-up Date*</label><br>
        <input type="date" required />
      </div>
    </div>
    <label>Name*</label>
    <div class="form-row">
      <input type="text" placeholder="First Name" required />
      <input type="text" placeholder="Middle Name" />
      <input type="text" placeholder="Last Name" required />
      <input type="text" placeholder="Suffix" />
    </div>
<div class="form-row">
  <div>
    <label>Valid ID of the Person Concerned*</label><br>
    <input type="file" accept="image/*" required />
  </div>
</div>
<div class="form-row">
  <div>
    <label>Receipt (if recent)</label><br>
    <input type="file" accept="image/*" />
  </div>
</div>
<label>Are you claiming a certificate for someone else?*</label>
<div class="form-radio">
  <label>
    <input type="radio" name="claiming_for_someone_else" value="yes" onclick="toggleAuthorization(true)" required />
    <span>Yes</span>
  </label>
  <label>
    <input type="radio" name="claiming_for_someone_else" value="no" onclick="toggleAuthorization(false)" required />
    <span>No</span>
  </label>
</div>
<div id="authorization-section" style="display: none;">
  <div class="form-row">
    <div>
      <label>Authorization Letter (with signature)*</label><br>
      <input type="file" accept="image/*" />
    </div>
  </div>
  <div class="instructions">
    <p><strong>Kindly indicate on the letter:</strong></p>
    <ul>
      <li>1. The date of the letter</li>
      <li>2. The reason why he/she is not able to pick up the certificate personally</li>
      <li>3. The purpose of the certificate requested</li>
      <li>4. The full details of the authorized person (relationship to the person concerned)</li>
    </ul>
  </div>
  <div class="form-row">
    <div>
      <label>Valid ID of Authorized Person*</label><br>
      <input type="file" accept="image/*" />
    </div>
  </div>
</div>
    <div class="form-row">
      <div>
        <label>Contact Number*</label><br>
        <input type="tel" placeholder="Contact Number" required />
      </div>
      <div>
        <label>Email Address*</label><br>
        <input type="email" placeholder="Email Address" required />
      </div>
    </div>

    <div class="form-row terms">
      <input type="checkbox" id="terms" name="terms" required />
        <label for="terms">
              I agree to the <a href="#" target="_blank">Terms & Conditions</a> and understand that my valid ID will only be used for certificate verification.
        <span class="required">*</span>
      </label>
    </div>
<div class="form-button-wrapper">
  <button type="submit">RESERVE</button>
</div>
  </form>
</div>
      </main>
        <footer class="site-footer-simple">
    <p>&copy; 2025 Santa Krus Parish, Bagumbong Caloocan. All Rights Reserved.</p>
  </footer>
  <script>
  function toggleAuthorization(show) {
    const section = document.getElementById('authorization-section');
    section.style.display = show ? 'block' : 'none';
  }
</script>
</body>
</html>