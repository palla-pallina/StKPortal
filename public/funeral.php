<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Santa Krus Parish</title>
  <link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../stylesheets/funeral.css" />
</head>
<body>

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
            <li><a href="wedding.php">Wedding</a></li>
            <li><a href="funeral.php">Funeral</a></li>
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
        </a>
        <div class="card">
          <img src="../stylesheets/assets/funeral.png" alt="Funeral" />
          <div class="card-label">Funeral</div>
        </div>
      </div>
    </section>

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
