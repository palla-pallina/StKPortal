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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../stylesheets/public/funeral.css" />
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
    
    <div class="breadcrumb">
      <a href="sacraments.php">Sacraments</a> &gt; <span>Funeral</span>
    </div>

    <!-- Reservation Form -->
    <div class="reservation-form">
      <h3>Reservation Information</h3>
      <form action="save_funeral.php" method="post">
        <div class="form-row">
          <label>Reservation Type</label>
          <select name="reservation_type" required>
            <option value="baptism" disabled>Baptism</option>
            <option value="wedding" disabled>Wedding</option>
            <option value="funeral" selected>Funeral</option>
          </select>
        </div>

        <label>Name of Deceased</label>
        <div class="form-row">
          <input type="text" name="deceased_firstname" placeholder="First Name" required />
          <input type="text" name="deceased_middlename" placeholder="Middle Name" />
          <input type="text" name="deceased_lastname" placeholder="Last Name" required />
          <input type="text" name="deceased_suffix" placeholder="Suffix" />
        </div>

        <div class="form-row">
          <div>
            <label>Cause of Death</label>
            <input type="text" name="cause_of_death" required />
          </div>
          <div>
            <label>Birthdate</label>
            <input type="date" name="deceased_dob" required />
          </div>
        </div>

        <div class="form-row">
          <div>
            <label>Date</label>
            <input type="date" name="funeral_date" required />
          </div>
          <div>
            <label>Time</label>
            <input type="time" name="funeral_time" required />
          </div>
          <div>
            <label>Address</label>
            <input type="text" name="funeral_address" placeholder="Address" required />
          </div>
        </div>

        <div class="form-row">
          <div>
            <label>Firstname</label>
            <input type="text" name="contact_person_firstname" placeholder="Contact Person" required />
          </div>
          <div>
            <label>Lastname</label>
            <input type="text" name="contact_person_lastname" placeholder="Contact Person" required />
          </div>
          <div>
            <label>Relation to Deceased</label>
            <input type="text" name="contact_person_relation" required />
          </div>
        </div>

        <div class="form-row">
          <div>
            <label>Contact Number</label>
            <input type="tel" name="contact_person_contact_no" placeholder="Contact Number" required />
          </div>
          <div>
            <label>Email Address</label>
            <input type="email" name="contact_person_email" placeholder="Email Address" />
          </div>
        </div>

        <button type="submit">RESERVE</button>
      </form>
    </div>
  </main>

  <footer class="site-footer">
    <p>© 2025 Santa Krus Parish, Bagumbong Caloocan. All Rights Reserved.</p>
  </footer>
</body>
</html>
