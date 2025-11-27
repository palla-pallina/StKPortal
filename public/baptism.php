<?php
require_once 'connection.php';
session_start();

/*--------- USER ACCESS RESTRICT START
- restrict access to registered users only */
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
    echo "<script>
            alert('Please log in with an account.');
            window.location.href='homepage.php';
          </script>";
    exit();
}
$user_name = $_SESSION['username'] ?? null; 
// retrieves logged in user's username
$user_id = $_SESSION['user_id']; 
// retrieves logged in user ID
/*--------- USER ACCESS RESTRICT END */

/*--------- NOTIFICATION HANDLING START
- for notification icon at navbar
- retrieves all notifications belonging to logged in user */
$stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);

$unreadCount = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $unreadCount = $result['cnt'] ?? 0;
    $stmt->close();
}
/*--------- NOTIFICATION HANDLING END */

/*--------- FOR AUTO FILL
- retrieves user information */
$stmt = $dbh->prepare("
    SELECT givenname, middlename, lastname, suffix, gender, birthday, contact, email
    FROM users WHERE user_id = :id
");
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Store in session for JS access
foreach ($user as $key => $value) {
    $_SESSION[$key] = $value;
}
/*--------- FOR AUTO FILL END */
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Santa Krus Parish</title>
  <link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../stylesheets/public/baptism.css" />
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
      <img src="../stylesheets/assets/baptism_banner.jpg" alt="Church Interior" />
    </div>
  </section>
  
  <div class="breadcrumb">
      <a href="sacraments.php">Sacraments</a><span>Baptism</span>
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
            <label>Baptism Type</label>
            <select name="baptism_type" id="baptism_type" required> 
              <option value="" disabled selected></option>
              <option value="regular">Regular</option>
              <option value="solo">Solo</option>
            </select>
          </div>

          <div>
            <label>Set Date</label>
            <input type="date" id="date" name="date" required />
          </div>
          <div>
            <label>Set Time</label>
            <input type="time" name="time" id="time" required />
          </div>
          
        </div>
          
        <div class="form-row">
          <div>
            <label>Email Address</label>
            <input type="email" name="email" id="email" placeholder="Email Address" required />
          </div>
          <div>
            <label>Contact Number</label>
            <input type="text" name="contact" id="contact" placeholder="Contact Number" required>
          </div>
        </div>
        _____________________________________________________________________________________________________________________

        <h3>Child</h3>
        
        <!-- Child Info -->
        <label>Name of Child</label>
        <div class="form-row">
          <input type="text" name="child_firstname" placeholder="First Name" required />
          <input type="text" name="child_middlename" placeholder="Middle Name" />
          <input type="text" name="child_lastname" placeholder="Last Name" required />
          <input type="text" name="child_suffix" placeholder="Suffix" />
        </div>

        <div class="form-row">
          <div>
            <label>Birthdate</label>
            <input type="date" name="birth_date" required />
          </div>
          <div>
            <label>Birthplace</label>
            <input type="text" name="birth_place" required />
          </div>
        </div>

        <div class="form-row">
          <div>
            <label>Gender</label>
            <select name="child_gender" required>
              <option value="" disabled selected></option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>
          <div>
            <label>Nationality</label>
            <input type="text" name="nationality" required />
          </div>
        </div>

        _____________________________________________________________________________________________________________________

        <h3>Parents</h3>

        <label>Name of Mother</label>
        <div class="form-row">
          <input type="text" name="mother_firstname" id="mother_first" placeholder="First Name" required />
          <input type="text" name="mother_middlename" id="mother_middle" placeholder="Middle Name" />
          <input type="text" name="mother_lastname" id="mother_last" placeholder="Last Name" required />
        </div>
        
        <label>Name of Father</label>
        <div class="form-row">
          <input type="text" name="father_firstname" id="father_first" placeholder="First Name" />
          <input type="text" name="father_middlename" id="father_middle" placeholder="Middle Name" />
          <input type="text" name="father_lastname" id="father_last" placeholder="Last Name" />
          <input type="text" name="father_suffix" id="father_suffix" placeholder="Suffix" />
        </div>

        <div class="form-row">
          <div>
            <label>Married?</label>
            <select name="married" id="married" required>
              <option value="" disabled selected></option>
              <option value="Yes">Yes</option>
              <option value="No">No</option>
            </select>
          </div>

          <div id="marriage_type_container" style="display:none;">
            <label>Marriage Type</label>
            <select name="marriage_type" id="marriage_type">
                <option value="" disabled selected></option>
                <option value="Church Marriage">Church Marriage</option>
                <option value="Civil Marriage">Civil Marriage</option>
            </select>
          </div>
        </div>

        <div>
          <label>Address</label>
          <input type="text" name="address" placeholder="Home Address" required />
        </div>
        _____________________________________________________________________________________________________________________
        
        <h3>Primary Sponsors</h3>

        <label>Pangunahing Ninong</label>
        <div class="form-row">
          <input type="text" name="ninong_firstname" placeholder="First Name"/>
          <input type="text" name="ninong_middlename" placeholder="Middle Name" />
          <input type="text" name="ninong_lastname" placeholder="Last Name"/>
          <input type="text" name="ninong_suffix" placeholder="Suffix"/>
        </div>

        <label>Pangunahing Ninang</label>
        <div class="form-row">
          <input type="text" name="ninang_firstname" placeholder="First Name"/>
          <input type="text" name="ninang_middlename" placeholder="Middle Name" />
          <input type="text" name="ninang_lastname" placeholder="Last Name" />
        </div>

        _____________________________________________________________________________________________________________________
        
        <h3>Additional Sponsors</h3>

        <label>Ninong <!--<i class="fas fa-plus icon-plus"></i>--></label>
        <div class="form-row">
          <input type="text" name="as_ninong_firstname" placeholder="First Name" />
          <input type="text" name="as_ninong_middlename" placeholder="Middle Name" />
          <input type="text" name="as_ninong_lastname" placeholder="Last Name" />
          <input type="text" name="as_ninong_lastname" placeholder="Suffix" />
        </div>

        <label class="label-with-icon">Ninang <!--<i class="fas fa-plus icon-plus"></i>--></label>
        <div class="form-row">
          <input type="text" name="as_ninang_firstname" placeholder="First Name" />
          <input type="text" name="as_ninang_middlename" placeholder="Middle Name" />
          <input type="text" name="as_ninang_lastname" placeholder="Last Name" />
        </div>

        _____________________________________________________________________________________________________________________

        <div class="form-row">
          <div>
            <label>Birth Certificate</label>
            <input type="file" name="birth_certificate" accept="image/*" />
          </div>
          
          <div id="marriage_cert_container" style="display:none;">
            <label>Marriage Certificate</label>
            <input type="file" name="marriage_certificate" id="marriage_certificate" accept="image/*" />
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
  
</body>
</html>


<script>
document.addEventListener("DOMContentLoaded", () => {
    const first = "<?php echo $_SESSION['givenname'] ?? ''; ?>";
    const middle = "<?php echo $_SESSION['middlename'] ?? ''; ?>";
    const last = "<?php echo $_SESSION['lastname'] ?? ''; ?>";
    const suffix = "<?php echo $_SESSION['suffix'] ?? ''; ?>";
    //const birthday = 
    const contact = "<?php echo $_SESSION['contact'] ?? ''; ?>";
    const email = "<?php echo $_SESSION['email'] ?? ''; ?>";
    const gender = "<?php echo $_SESSION['gender'] ?? ''; ?>";

    if (gender === "Female") {
        document.getElementById('mother_first').value = first;
        document.getElementById('mother_middle').value = middle;
        document.getElementById('mother_last').value = last;
        //document.getElementById('mother_birthday').value = birthday;
        //document.getElementById('mother_contact').value = contact;
        document.getElementById('contact').value = contact;
        document.getElementById('email').value = email;
        
    }

    if (gender === "Male") {
        document.getElementById('father_first').value = first;
        document.getElementById('father_middle').value = middle;
        document.getElementById('father_last').value = last;
        document.getElementById('father_suffix').value = suffix;
        //document.getElementById('father_birthday').value = birthday;
        //document.getElementById('father_contact').value = contact;
        document.getElementById('contact').value = contact;
        document.getElementById('email').value = email;

    }
});

document.getElementById('married').addEventListener('change', function() {
    let marriageTypeDiv = document.getElementById('marriage_type_container');
    let marriageTypeSelect = document.getElementById('marriage_type');
    let marriageCertDiv = document.getElementById('marriage_cert_container');
    let marriageCertSelect = document.getElementById('marriage_certificate');

    if (this.value === "Yes") {
        marriageTypeDiv.style.display = "block";
        marriageTypeSelect.setAttribute("required", "required");
        
        marriageCertDiv.style.display = "block";
        marriageCertSelect.setAttribute("required", "required");
    } else {
        marriageTypeDiv.style.display = "none";
        marriageTypeSelect.removeAttribute("required");
        marriageTypeSelect.value = "";

        marriageCertDiv.style.display = "none";
        marriageCertSelect.removeAttribute("required");
        marriageCertSelect.value = "";
    }
});

const dateInput = document.getElementById("date");
const timeInput = document.getElementById("time");
const baptismTypeSelect = document.getElementById("baptism_type");

// Helper: get all Sundays in next 6 months
function getNextSundays(months = 6) {
    const sundays = [];
    const today = new Date();
    const endDate = new Date();
    endDate.setMonth(today.getMonth() + months);

    for (let d = new Date(today); d <= endDate; d.setDate(d.getDate() + 1)) {
        if (d.getDay() === 0) sundays.push(new Date(d));
    }
    return sundays;
}

let allowedSundays = getNextSundays();

// check baptism type
baptismTypeSelect.addEventListener("change", function() {
    if (this.value === "regular") {
        dateInput.value = ""; // clear input
        timeInput.value = "11:30"; // Lock time to 11:30 AM
        timeInput.readOnly = true;

        // Restrict date to Sundays
        if (allowedSundays.length > 0) {
            dateInput.min = allowedSundays[0].toISOString().split("T")[0];
            dateInput.max = allowedSundays[allowedSundays.length - 1].toISOString().split("T")[0];
        }
    } else {
        dateInput.value = ""; // clear input
        timeInput.readOnly = false; // Unlock time and date
        timeInput.value = "";
        dateInput.min = "";
        dateInput.max = "";
    }
});

// FOR REGULAR BAPTISM: validate that selected date is Sunday
dateInput.addEventListener("input", function() {
    if (baptismTypeSelect.value === "regular") {
        const selected = new Date(this.value);
        if (selected.getDay() !== 0) {
            alert("For Regular Baptism, only Sundays can be selected!");
            this.value = "";
        }
    }
});

</script>