<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Santa Krus Parish</title>
  <link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../stylesheets/login.css" />
</head>
<body>
 
  <!-- NAVBAR -->
  <header>
    <nav class="navbar">
      <img src="../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" />
      <ul class="nav-links">
        <li><a href="homepage.html">Home</a></li>
        <li><a href="history.html">History</a></li>
        <li><a href="events.html">Events</a></li> <!-- N/A -->
        <li><a href="donation.html">Give</a></li>
        <li class="services-dropdown">
          <a href="#" class="active">Sacraments</a>
          <ul class="dropdown">
            <li><a href="baptism.html">Baptism</a></li>
            <li><a href="wedding_service.html">Wedding</a></li>
            <li><a href="funeral.html">Funeral</a></li>
            <li><a href="certicates.html">Certificates</a></li>
          </ul>
        </li>
        <li><a href="#">Pray</a></li>
        <li><a href="contact.html">Contact</a></li>
        <li class="hamburg-dropdown">
          <a href="#" class="active" style="font-size: 20px;">☰</a>
        </li>
      </ul>
    </nav>
  </header>
  <!-- NAVBAR END-->

  <main>
    <section class="login-section">

     <div class="logo-container">
  <img src="../stylesheets/assets/stklogo.png" alt="Parish Logo" class="login-logo">
  <h1 class="site-name">Santa Krus Parish</h1>
</div>

        <!-- Login Form -->
        <form class="login-form">
          <div class="form-group">
            <input type="email" placeholder="Email Address" required>
          </div>
          <div class="form-group">
            <input type="password" placeholder="Password" required>
          </div>
          <button type="submit" class="login-btn">LOG IN</button>
        </form>

        <!-- Links -->
        <div class="form-links">
          <a href="#" class="forgot-password">Forgot Password?</a>
        </div>
        <div class="signup-text">
          Don’t have an account? <a href="register.php">Sign Up</a>
        </div>
      </div>
    </section>
  </main>

  <footer class="site-footer-simple">
    <p>&copy; 2025 Santa Krus Parish, Bagumbong Caloocan. All Rights Reserved.</p>
  </footer>
</body>
</html>
