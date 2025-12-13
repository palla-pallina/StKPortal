<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "test_tree_db";
    
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'test_tree_db');

$conn = new mysqli($host, $user, $pass, $dbname);
    
// Check connection
if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

try {
  $conn = new PDO("mysql:host=localhost;dbname=test_tree_db;charset=utf8", "root", "");
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} 
catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Collect and sanitize input
    $givenname   = trim($_POST['givenname']);
    $middlename  = trim($_POST['middlename']);
    $lastname    = trim($_POST['lastname']);
    $suffix    = trim($_POST['suffix']);
    $gender      = $_POST['gender'];
    $birthday    = $_POST['birthday'];
    $username    = trim($_POST['username']);
    $email       = trim($_POST['email']);
    $contact     = trim($_POST['contact']);
    $password    = $_POST['password'];
    $confirmPass = $_POST['confirm_password'];

    // Validate passwords match
    if ($password !== $confirmPass) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit;
    }

    // Check if username or email exists
    $checkQuery = "SELECT user_id FROM users WHERE username = :username OR email = :email";
    $stmt = $conn->prepare($checkQuery);

    if (!$stmt) {
        echo "<pre>";
        print_r($conn->errorInfo());
        echo "</pre>";
        exit;
    }

    $stmt->execute([
        ':username' => $username,
        ':email' => $email
    ]);


    if ($stmt->rowCount() > 0) {
        echo "<script>alert('Username or Email already exists!'); window.history.back();</script>";
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user
    $insertQuery = "INSERT INTO users 
    (user_type, username, password, givenname, middlename, lastname, suffix, gender, email, contact, birthday)
    VALUES 
    (:user_type, :username, :password, :givenname, :middlename, :lastname, :suffix, :gender, :email, :contact, :birthday)";

    $stmt = $conn->prepare($insertQuery);
    $success = $stmt->execute([
        ':user_type' => 'User',
        ':username' => $username,
        ':password' => $hashedPassword,
        ':givenname' => $givenname,
        ':middlename' => $middlename,
        ':lastname' => $lastname,
        ':suffix' => $suffix,
        ':gender' => $gender,
        ':email' => $email,
        ':contact' => $contact,
        ':birthday' => $birthday
    ]);

    if ($success) {
        echo "<script>alert('Registration successful! Redirecting to login...'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Error: Unable to register. Please try again later.'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Santa Krus Parish</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../stylesheets/public/register.css" />
</head>
<body>

<!-------------------- NAVBAR -------------------->
<header>
    <nav class="navbar">
        <ul class="nav-links">
            <li><a href="#"><img src="../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" /></a></li>
            <li><a href="homepage.php" class="active">Home</a></li>
            <li><a href="history.php">History</a></li>
            <li><a href="events.php">News & Events</a></li>
            <li><a href="sacraments.php">Sacraments</a></li>
            <li><a href="donation.php">Donate</a></li>
            <li><a href="prayer_page.php">Prayer Request</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </nav>
</header>
<!----------------- NAVBAR END----------------->

  <!-- Register Page Content -->
  <div class="container">
    <!-- Left Section -->
    <div class="left-section">

      <img src="../stylesheets/assets/stklogo.png" alt="Santa Krus Parish Logo">


      <h1 class="site-name">Santa<br>Krus<br>Parish</h1>
    </div>

    <!-- Right Section -->
    <div class="right-section">
      <h2>Create an account</h2>
      
        <form class="register-form" method="POST" action="register.php">
          <div class="name-fields">
            <input type="text" name="givenname" placeholder="Given Name" required>
            <input type="text" name="middlename" placeholder="Middle Name">
            <input type="text" name="lastname" placeholder="Last Name" required>
            <input type="text" name="suffix" placeholder="Suffix">
          </div>

          
          <div class="pii-fields">
          <input type="date" name="birthday" placeholder="Birthday" required>
          <select name="gender" required>
            <option value="" disabled selected>Sex</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
          </div>

          <input type="text" name="username" placeholder="Username" required>
          <input type="email" name="email" placeholder="Email Address" required>
          <input type="text" name="contact" placeholder="Contact Number" required>
          <input type="password" name="password" placeholder="Password" required>
          <input type="password" name="confirm_password" placeholder="Confirm Password" required>
          <button type="submit" class="signup-btn">SIGN UP</button>
        </form>

      <p class="login-link">Already have an account? <a href="login.php">Log In</a></p>
    </div>
  </div>


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
  

</body>
</html>