<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - Santa Krus Parish</title>
  <link rel="stylesheet" href="../stylesheets/register.css">
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
          <ul class="dropdown">
            <li><a href="login.php">Log In</a></li>
          </ul>
        </li>
      </ul>
    </nav>
  </header>
  <!-- NAVBAR END-->

  <!-- ✅ Register Page Content -->
  <div class="container">
    <!-- Left Section -->
    <div class="left-section">

      <img src="../stylesheets/assets/stklogo.png" alt="Santa Krus Parish Logo">


      <h1 class="site-name">Santa<br>Krus<br>Parish</h1>
    </div>

    <!-- Right Section -->
    <div class="right-section">
      <h2>Create an account</h2>
      <form class="register-form">
        <div class="name-fields">
          <input type="text" placeholder="Given Name" required>
          <input type="text" placeholder="Middle Name">
          <input type="text" placeholder="Last Name" required>
          <input type="text" placeholder="Suffix">
        </div>
        <input type="date" placeholder="Birthday" required>
        <input type="text" placeholder="Username" required>
        <input type="email" placeholder="Email Address" required>
        <input type="text" placeholder="Contact Number" required>
        <input type="password" placeholder="Password" required>
        <input type="password" placeholder="Confirm Password" required>
        <button type="submit" class="signup-btn">SIGN UP</button>
      </form>
      <p class="login-link">Already have an account? <a href="login.php">Log In</a></p>
    </div>
  </div>

  <!-- ✅ Footer -->
  <footer>
    <p>© 2025 Santa Krus Parish, Bagumbong Caloocan. All Rights Reserved.</p>
  </footer>
</body>
</html>
