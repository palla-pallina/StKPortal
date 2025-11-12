<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Santa Krus Parish</title>
  <link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style.css" />
</head>
<body>

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
      <div class="card">
        <img src="../stylesheets/assets/baptism.png" alt="Baptism" />
        <div class="card-label">Baptism</div>
      </div>
      <a href="wedding_service.html" class="card-link">
        <div class="card">
          <img src="../stylesheets/assets/wedding.png" alt="Wedding" />
          <div class="card-label">Wedding</div>
        </div>
      </a>
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
      <form action="save_baptism.php" method="post">
        <div class="form-row">
          <div>
            <label>Reservation Type</label>
            <select name="reservation_type" required>
              <option value="Baptism" autofocus>Baptism</option>
              <option value="Wedding" disabled>Wedding</option>
              <option value="Funeral" disabled>Funeral</option>
            </select>
          </div>
          <div>
            <label>Preferred Date</label>
            <input type="date" name="preferred_date" required />
          </div>
        </div>

        <label>Name of Child*</label>
        <div class="form-row">
          <input type="text" name="child_first" placeholder="First Name" required />
          <input type="text" name="child_middle" placeholder="Middle Name" />
          <input type="text" name="child_last" placeholder="Last Name" required />
          <input type="text" name="child_suffix" placeholder="Suffix" />
        </div>

        <div class="form-row">
          <div>
            <label>Birthdate*</label>
            <input type="date" name="birthdate" required />
          </div>
          <div>
            <label>Age</label>
            <input type="number" name="age" required />
          </div>
          <div>
            <label>Birthplace</label>
            <input type="text" name="birthplace" required />
          </div>
        </div>

        <div class="form-row">
          <div>
            <label>Gender*</label>
            <select name="gender" required>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>
          <div>
            <label>Nationality*</label>
            <input type="text" name="nationality" required />
          </div>
        </div>

        <div>
          <label>Address*</label>
          <input type="text" name="address" placeholder="Home Address" required />
        </div>

        <label>Name of Mother*</label>
        <div class="form-row">
          <input type="text" name="mother_first" placeholder="First Name" required />
          <input type="text" name="mother_middle" placeholder="Middle Name" />
          <input type="text" name="mother_last" placeholder="Last Name" required />
          <input type="text" name="mother_suffix" placeholder="Suffix" />
        </div>

        <label>Name of Father*</label>
        <div class="form-row">
          <input type="text" name="father_first" placeholder="First Name" required />
          <input type="text" name="father_middle" placeholder="Middle Name" />
          <input type="text" name="father_last" placeholder="Last Name" required />
          <input type="text" name="father_suffix" placeholder="Suffix" />
        </div>

        <label>Married?*</label>
        <div class="form-row">
          <select name="married" required>
            <option value="Yes">Yes</option>
            <option value="No">No</option>
          </select>
          <input type="text" name="marital_type" placeholder="Type (Single, Married)" />
        </div>

        <label>Pangunahing Ninong*</label>
        <div class="form-row">
          <input type="text" name="main_ninong_first" placeholder="First Name" required />
          <input type="text" name="main_ninong_middle" placeholder="Middle Name" />
          <input type="text" name="main_ninong_last" placeholder="Last Name" required />
          <input type="text" name="main_ninong_suffix" placeholder="Suffix" />
        </div>
        <div>
          <label>Address*</label>
          <input type="text" name="main_ninong_address" placeholder="Home Address" required />
        </div>

        <label>Pangunahing Ninang*</label>
        <div class="form-row">
          <input type="text" name="main_ninang_first" placeholder="First Name" required />
          <input type="text" name="main_ninang_middle" placeholder="Middle Name" />
          <input type="text" name="main_ninang_last" placeholder="Last Name" required />
          <input type="text" name="main_ninang_suffix" placeholder="Suffix" />
        </div>
        <div>
          <label>Address*</label>
          <input type="text" name="main_ninang_address" placeholder="Home Address" required />
        </div>

        <!-- ✅ Added Additional Ninang/Ninong Fields -->
        <label class="label-with-icon">
          Ninang <i class="fas fa-plus icon-plus"></i>
        </label>
        <div class="form-row">
          <input type="text" name="ninang_first" placeholder="First Name" required />
          <input type="text" name="ninang_middle" placeholder="Middle Name" />
          <input type="text" name="ninang_last" placeholder="Last Name" required />
          <input type="text" name="ninang_suffix" placeholder="Suffix" />
        </div>

        <label class="label-with-icon">
          Ninong <i class="fas fa-plus icon-plus"></i>
        </label>
        <div class="form-row">
          <input type="text" name="ninong_first" placeholder="First Name" required />
          <input type="text" name="ninong_middle" placeholder="Middle Name" />
          <input type="text" name="ninong_last" placeholder="Last Name" required />
          <input type="text" name="ninong_suffix" placeholder="Suffix" />
        </div>

        <div class="form-row">
          <div>
            <label>Email Address*</label>
            <input type="email" name="email" placeholder="Email Address" required />
          </div>
          <div>
            <label>Contact Number*</label>
            <input type="tel" name="contact" placeholder="Contact Number" required />
          </div>
        </div>

        <div class="text-center">
          <button type="submit" name="submit">RESERVE</button>
          
        </div>
      </form>
    </div>
  </section>
</main>

<footer class="site-footer-simple">
  <p>&copy; 2025 Santa Krus Parish, Bagumbong Caloocan. All Rights Reserved.</p>
</footer>
<!-- POP-UP MODAL -->


</body>
</html>
