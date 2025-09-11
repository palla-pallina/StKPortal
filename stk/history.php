<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Santa Krus Parish</title>
  <link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="wedding.css" />
</head>
<body>
  <header>
    <nav class="navbar">
      <img src="santa krus logo.png" alt="Parish Logo" class="logo" />
    <nav class="navbar">
      <img src="santa krus logo.png" alt="Parish Logo" class="logo" />
      <ul class="nav-links">
  <li><a href="#">Home</a></li>
  <li><a href="#">History</a></li>
  <li><a href="#">Articles</a></li>
  <li><a href="#">Give</a></li>

  <li class="services-dropdown">
    <a href="#" class="active">Services</a>
    <ul class="dropdown">
      <li><a href="services.html">Baptism</a></li>
      <li><a href="wedding_service.html">Wedding</a></li>
      <li><a href="funeral.html">Funeral</a></li>
      <li><a href="certicates.html">Certificates</a></li>
    </ul>
  </li>

  <li><a href="#">Pray</a></li>
  <li><a href="contact.html">Contact</a></li>
</ul>

      <div class="nav-profile">
        <img src="profile-icon.png" alt="Profile Icon" />
      </div>
      <button class="menu-toggle">☰</button>
    </nav>
  </header>

  <main>
    <section class="hero">
      <h1>SANTA KRUS PARISH</h1>
      <h2>DIOCESE OF NOVALICHES</h2>
      <div class="hero-image">
        <img src="banner.png" alt="Church Interior" />
      </div>
    </section>

    <section class="sacraments">
      <h3>Sacraments</h3>
    </section>

    <section class="sacrament-reservation-wrapper">
      <div class="sacrament-cards">
        <a href="services.html" class="card-link">
          <div class="card">
            <img src="baptism.png" alt="Baptism" />
            <div class="card-label">Baptism</div>
          </div>
        </a>
        <div class="card">
          <img src="wedding.png" alt="Wedding" />
          <div class="card-label">Wedding</div>
        </div>
        <a href="funeral.html" class="card-link">
        <div class="card">
          <img src="funeral.png" alt="Funeral" />
          <div class="card-label">Funeral</div>
        </div>
      </a>
      </div>

      <div class="reservation-form">
        <h3>Reservation Information</h3>
        <form>
          <div class="form-row">
            <div>
              <label>Reservation Type</label>
              <select required>
                <option value="baptism">Wedding</option>
                <option value="wedding">Funeral</option>
                <option value="funeral">Baptism</option>
              </select>
            </div>
            <div>
              <label>Preferred Date</label>
              <input type="date" required />
            </div>
          </div>

          <label>Bride's Name *</label>
          <div class="form-row">
            <input type="text" placeholder="First Name" required />
            <input type="text" placeholder="Middle Name" />
            <input type="text" placeholder="Last Name" required />
            <input type="text" placeholder="Suffix" />
          </div>

          <div class="form-row">
            <div>
              <label>Birthdate*</label>
              <input type="date" required />
            </div>
            <div>
              <label>Age</label>
              <input type="number" required />
            </div>
            <div>
              <label>Birthplace</label>
              <input type="text" required />
            </div>
            <div>
              <label>Nationality*</label>
              <input type="text" required />
            </div>
          </div>

          <div class="form-row">
            <div>
              <label>Address*</label>
              <input type="text" placeholder="Home Address" required />
            </div>
            <div>
              <label>Contact Number*</label>
              <input type="tel" placeholder="Contact Number" required />
            </div>
          </div>

          <label>Groom's Name *</label>
          <div class="form-row">
            <input type="text" placeholder="First Name" required />
            <input type="text" placeholder="Middle Name" />
            <input type="text" placeholder="Last Name" required />
            <input type="text" placeholder="Suffix" />
          </div>

          <div class="form-row">
            <div>
              <label>Birthdate*</label>
              <input type="date" required />
            </div>
            <div>
              <label>Age</label>
              <input type="number" required />
            </div>
            <div>
              <label>Birthplace</label>
              <input type="text" required />
            </div>
            <div>
              <label>Nationality*</label>
              <input type="text" required />
            </div>
          </div>

          <div class="form-row">
            <div>
              <label>Address*</label>
              <input type="text" placeholder="Home Address" required />
            </div>
            <div>
              <label>Contact Number*</label>
              <input type="tel" placeholder="Contact Number" required />
            </div>
          </div>

          <label>Best Man</label>
          <div class="form-row">
            <input type="text" placeholder="First Name" required />
            <input type="text" placeholder="Middle Name" />
            <input type="text" placeholder="Last Name" required />
            <input type="text" placeholder="Suffix" />
          </div>

          <label>Maid of Honor</label>
          <div class="form-row">
            <input type="text" placeholder="First Name" required />
            <input type="text" placeholder="Middle Name" />
            <input type="text" placeholder="Last Name" required />
            <input type="text" placeholder="Suffix" />
          </div>

          <button type="submit">RESERVE</button>
        </form>
      </div>
    </section>
  </main>

  <footer class="site-footer-simple">
    <p>&copy; 2025 Santa Krus Parish, Bagumbong Caloocan. All Rights Reserved.</p>
  </footer>
</body>
</html>