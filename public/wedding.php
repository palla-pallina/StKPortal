<?php
require_once 'connection.php';
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


$user_id = $_SESSION['user_id'];
$gender = $_SESSION['gender'];
$birthday = $_SESSION['birthday'];

$stmt = $dbh->prepare("SELECT givenname, middlename, lastname, gender, birthday FROM users WHERE user_id = :id");
$stmt->bindParam(':id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$givenname = $user['givenname'] ?? '';
$middlename = $user['middlename'] ?? '';
$lastname = $user['lastname'] ?? '';
$suffix = $user['suffix'] ?? '';
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Santa Krus Parish</title>
  <link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../stylesheets/public/wedding.css" />


</head>
<body>
  
  <!-- Toast Notification -->
  <div id="toast" class="toast">
    Reservation Saved Successfully!
  </div>

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

    <!-- HERO SECTION -->
    <section class="hero">
      <h1>SANTA KRUS PARISH</h1>
      <h2>DIOCESE OF NOVALICHES</h2>
      <div class="hero-image">
        <img src="../stylesheets/assets/banner.png" alt="Church Interior" />
      </div>
    </section>

      <div class="breadcrumb">
          <a href="sacraments.php">Sacraments</a> &gt; <span>Wedding</span>
      </div>

      <!-- RESERVATION FORM -->
      <div class="reservation-form">
        <h3>Reservation Information</h3>
        <form action="save_wedding.php" method="post">

        <input type="hidden" id="db_first" value="<?php echo htmlspecialchars($givenname); ?>">
        <input type="hidden" id="db_middle" value="<?php echo htmlspecialchars($middlename); ?>">
        <input type="hidden" id="db_last" value="<?php echo htmlspecialchars($lastname); ?>">
        <input type="hidden" id="db_suffix" value="<?php echo htmlspecialchars($suffix); ?>">
        <input type="hidden" id="db_birth" value="<?php echo htmlspecialchars($birthday); ?>">
        <input type="hidden" id="db_gender" value="<?php echo htmlspecialchars($_SESSION['gender']); ?>">

          <div class="form-row">
            <div>
              <label>Reservation Type</label>
              <select name="reservation_type" required>
                <option value="baptism" disabled>Baptism</option>
                <option value="wedding" selected>Wedding</option>
                <option value="funeral" disabled>Funeral</option>
              </select>
            </div>

            <div>
              <label>Set Date</label>
              <input type="date" name="date" required />
            </div>
            <div>
              <label>Set Time</label>
              <input type="time" name="time" required />
            </div>

            <div>
              <label>Contact Number</label>
              <input type="tel" name="phone" id="phone" placeholder="Contact Number" required />
            </div>
          </div>


          <!-- Bride Info -->
          <label>Bride's Name</label>
          <div class="form-row">
            <input type="text" name="bride_firstname" id="bride_first" placeholder="First Name" required />
            <input type="text" name="bride_middlename" id="bride_middle" placeholder="Middle Name" />
            <input type="text" name="bride_lastname" id="bride_last" placeholder="Last Name" required />
            <!--<input type="text" name="bride_suffix" id="bride_suffix" placeholder="Suffix" required />-->
            <input type="date" name="bride_birthdate" id="bride_birth" required />
            
            <?php if (isset($_SESSION['gender']) && $_SESSION['gender'] === "Female"): ?>
            <label style="">
                <input type="checkbox" id="use_my_info" style="height:50%; margin-left: -67px;"> <label style="font-size:10px; color:grey;";>autofill</label>
            </label>
            <?php endif; ?>
          </div>
          
          <div class="form-row">
            <div>
              <label>Address</label>
              <input type="text" name="bride_address" placeholder="Home Address" required />
            </div>
            <div>
              <label>Contact Number</label>
              <input type="tel" name="bride_contact" placeholder="Contact Number" required />
            </div>
          </div>

          <!-- Groom Info -->
          <label>Groom's Name</label>
          <div class="form-row">
            <input type="text" name="groom_firstname" id="groom_first" placeholder="First Name" />
            <input type="text" name="groom_middlename" id="groom_middle" placeholder="Middle Name" />
            <input type="text" name="groom_lastname" id="groom_last" placeholder="Last Name" />
            <input type="text" name="groom_suffix" id="groom_suffix" placeholder="Suffix" />
            <input type="date" name="groom_birthdate" id="groom_birth" required />

            <?php if (isset($_SESSION['gender']) && $_SESSION['gender'] === "Male"): ?>
            <label style="margin-top:15px; display:block;">
                <input type="checkbox" id="use_my_info" style="height:50%; margin-left: -67px;"> <label style="font-size:10px";>autofill</label>
            </label>
            <?php endif; ?>
              
          </div>
          <div class="form-row">
            <div>
              <label>Address*</label>
              <input type="text" name="groom_address" placeholder="Home Address" required />
            </div>
            <div>
              <label>Contact Number</label>
              <input type="tel" name="groom_contact" placeholder="Contact Number" required />
            </div>
          </div>

          <!-- Best Man -->
          <label>Best Man</label>
          <div class="form-row">
            <input type="text" name="best_man_firstname" placeholder="First Name" required />
            <input type="text" name="best_man_middlename" placeholder="Middle Name" />
            <input type="text" name="best_man_lastname" placeholder="Last Name" required />
            <input type="text" name="best_man_suffix" placeholder="Suffix" />
          </div>

          <!-- Maid of Honor -->
          <label>Maid of Honor</label>
          <div class="form-row">
            <input type="text" name="maid_of_honor_firstname" placeholder="First Name" required />
            <input type="text" name="maid_of_honor_middlename" placeholder="Middle Name" />
            <input type="text" name="maid_of_honor_lastname" placeholder="Last Name" required />
            <!--<input type="text" name="maid_of_honor_suffix" placeholder="Suffix" />-->
          </div>

          <button type="submit">RESERVE</button>
        </form>
      </div>
    </section>
  </main>

  <footer class="site-footer-simple">
    <p>&copy; 2025 Santa Krus Parish, Bagumbong Caloocan. All Rights Reserved.</p>
  </footer>

  <!-- Toast Notification JS -->
  <?php if(isset($_GET['success']) && $_GET['success'] == 1): ?>
  <script>
    window.addEventListener('DOMContentLoaded', () => {
      const toast = document.getElementById('toast');
      toast.classList.add('show');
      setTimeout(() => {
        toast.classList.remove('show');
      }, 3000); // 3 seconds
    });
  </script>
  <?php endif; ?>

</body>
</html>

<script>
document.getElementById('use_my_info')?.addEventListener('change', function () {
    const gender = document.getElementById('db_gender').value;

    // Stop if gender is missing
    if (!gender) return;

    const first  = document.getElementById('db_first').value;
    const middle = document.getElementById('db_middle').value;
    const last   = document.getElementById('db_last').value;
    const suffix   = document.getElementById('db_suffix').value;
    const birth   = document.getElementById('db_birth').value;

    if (this.checked) {
        if (gender === "Male") {
            document.getElementById('groom_first').value = first;
            document.getElementById('groom_middle').value = middle;
            document.getElementById('groom_last').value = last;
            document.getElementById('groom_suffix').value = suffix;
            document.getElementById('groom_birth').value = birth;
        } 
        else if (gender === "Female") {
            document.getElementById('bride_first').value = first;
            document.getElementById('bride_middle').value = middle;
            document.getElementById('bride_last').value = last;
            document.getElementById('bride_suffix').value = suffix;
            document.getElementById('bride_birth').value = birth;
        }
    } else {
        // Clear all
        document.getElementById('groom_first').value = "";
        document.getElementById('groom_middle').value = "";
        document.getElementById('groom_last').value = "";
        document.getElementById('groom_suffix').value = "";
        document.getElementById('groom_birth').value = "";

        document.getElementById('bride_first').value = "";
        document.getElementById('bride_middle').value = "";
        document.getElementById('bride_last').value = "";
        document.getElementById('bride_suffix').value = "";
        document.getElementById('bride_birth').value = "";
    }

    
});

</script>