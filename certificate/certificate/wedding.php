<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Santa Krus Parish</title>
  <link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="wedding.css" />

  <style>
    /* Toast Notification CSS */
    .toast {
      visibility: hidden; /* Hidden by default */
      min-width: 250px;
      margin-left: -125px;
      background-color: #4CAF50;
      color: white;
      text-align: center;
      border-radius: 5px;
      padding: 15px;
      position: fixed;
      z-index: 1000;
      left: 50%;
      top: 30px;
      font-size: 16px;
      font-weight: bold;
      box-shadow: 0 2px 10px rgba(0,0,0,0.2);
      opacity: 0;
      transition: opacity 0.5s, top 0.5s;
    }

    .toast.show {
      visibility: visible;
      opacity: 1;
      top: 50px;
    }
  </style>
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
        <li><a href="homepage.html">Home</a></li>
        <li><a href="history.html">History</a></li>
        <li><a href="events.html">Events</a></li>
        <li><a href="donation.html">Give</a></li>
        <li class="services-dropdown">
          <a href="#" class="active">Sacraments</a>
          <ul class="dropdown">
            <li><a href="baptism.php">Baptism</a></li>
            <li><a href="wedding_service.html">Wedding</a></li>
            <li><a href="funeral.html">Funeral</a></li>
            <li><a href="certicates.html">Certificates</a></li>
          </ul>
        </li>
        <li><a href="pray.html">Pray</a></li>
        <li><a href="contact.html">Contact</a></li>
        <li class="hamburg-dropdown">
          <a href="#" class="active" style="font-size: 20px;">☰</a>
          <ul class="dropdown">
            <li><a href="login.php">Log In</a></li>
          </ul>
        </li>
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

    <!-- SACRAMENTS SECTION -->
    <section class="sacraments">
      <h3>Sacraments</h3>
    </section>

    <!-- SACRAMENT CARDS -->
    <section class="sacrament-reservation-wrapper">
      <div class="sacrament-cards">
        <a href="baptism.php" class="card-link">
          <div class="card">
            <img src="../stylesheets/assets/baptism.png" alt="Baptism" />
            <div class="card-label">Baptism</div>
          </div>
        </a>
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

      <!-- RESERVATION FORM -->
      <div class="reservation-form">
        <h3>Reservation Information</h3>
        <form action="save_wedding.php" method="post">

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
              <label>Preferred Date</label>
              <input type="date" name="date" required />
            </div>
            <div>
              <label>Preferred Time</label>
              <input type="time" name="time" required />
            </div>
            <div>
              <label>Contact Number</label>
              <input type="tel" name="phone" placeholder="Contact Number" required />
            </div>
          </div>

          <!-- Bride Info -->
          <label>Bride's Name *</label>
          <div class="form-row">
            <input type="text" name="bride_firstname" placeholder="First Name" required />
            <input type="text" name="bride_middlename" placeholder="Middle Name" />
            <input type="text" name="bride_lastname" placeholder="Last Name" required />
            <input type="text" name="bride_suffix" placeholder="Suffix" />
          </div>
          <div class="form-row">
            <div>
              <label>Birthdate*</label>
              <input type="date" name="bride_birthdate" required />
            </div>
            <div>
              <label>Age</label>
              <input type="number" name="bride_age" required />
            </div>
            <div>
              <label>Birthplace</label>
              <input type="text" name="bride_birthplace" required />
            </div>
            <div>
              <label>Nationality*</label>
              <input type="text" name="bride_nationality" required />
            </div>
          </div>
          <div class="form-row">
            <div>
              <label>Address*</label>
              <input type="text" name="bride_address" placeholder="Home Address" required />
            </div>
            <div>
              <label>Contact Number*</label>
              <input type="tel" name="bride_contact" placeholder="Contact Number" required />
            </div>
          </div>

          <!-- Groom Info -->
          <label>Groom's Name *</label>
          <div class="form-row">
            <input type="text" name="groom_firstname" placeholder="First Name" required />
            <input type="text" name="groom_middlename" placeholder="Middle Name" />
            <input type="text" name="groom_lastname" placeholder="Last Name" required />
            <input type="text" name="groom_suffix" placeholder="Suffix" />
          </div>
          <div class="form-row">
            <div>
              <label>Birthdate*</label>
              <input type="date" name="groom_birthdate" required />
            </div>
            <div>
              <label>Age</label>
              <input type="number" name="groom_age" required />
            </div>
            <div>
              <label>Birthplace</label>
              <input type="text" name="groom_birthplace" required />
            </div>
            <div>
              <label>Nationality*</label>
              <input type="text" name="groom_nationality" required />
            </div>
          </div>
          <div class="form-row">
            <div>
              <label>Address*</label>
              <input type="text" name="groom_address" placeholder="Home Address" required />
            </div>
            <div>
              <label>Contact Number*</label>
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
            <input type="text" name="maid_of_honor_suffix" placeholder="Suffix" />
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
