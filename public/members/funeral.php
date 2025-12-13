<?php
require_once '../connection.php';
session_start();

/*--------- USER ACCESS RESTRICT START
- restrict access to registered users only */
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
    echo "<script>
            alert('Please log in with an account.');
            window.history.back();
          </script>";
    exit();
}
$user_name = $_SESSION['givenname'] ?? null; 
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
    SELECT givenname, middlename, lastname, email, contact
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../stylesheets/public/user_footer_navbar.css" />
    <link rel="stylesheet" href="../../stylesheets/public/reservation_form.css" />
</head>
<body>
  
<!-------------------- NAVBAR -------------------->
<header>
    <nav class="navbar">
        <ul class="nav-links">
            <li><a href="#"><img src="../../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" /></a></li>
            <li><a href="../homepage.php">Home</a></li>
            <li><a href="../history.php">History</a></li>
            <li><a href="../events.php">News & Events</a></li>
            <li><a href="../sacraments.php">Sacraments</a></li>
            <li><a href="../donation.php">Donate</a></li>
            <li><a href="../prayer_page.php">Prayer Request</a></li>
            <li><a href="../contact.php">Contact</a></li>

            <?php
            session_start();

            if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
                // USER NOT LOGGED IN → Show Login button
                echo '<li><a href="../login.php" id="login-btn" style="color:white;"><i class="fa-regular fa-user"></i>   Log In</a></li>';
            } else {
                // USER LOGGED IN → Show Username + Dropdown
                ?>
            <p style="color:white; padding-left: 17px; padding-right: 17px;">|</p>
                <li class="li-dropdown">
                    <a href="#"><?= htmlspecialchars($user_name) ?></a>
                    <ul class="dropdown">
                        <li><a href="../logout.php">Logout</a></li>
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
<!----------------- NAVBAR END----------------->

<div class="header-banner" style="background-image: url('../../stylesheets/assets/banner/funeral.png');">
    <h1>Funeral Mass</h1>
</div>

<main>

    <!-- RESERVATION FORM -->
    <div class="reservation-form">
      <h3>Funeral Mass Form</h3>
      <form action="handle/save_funeral.php" method="post" enctype="multipart/form-data">

        <div class="form-row">
          <div>
            <div style="padding-top: 9px;">
            <label>Set Time</label>
              <div id="timeButtons" class="time-buttons"></div>
                <input type="hidden" name="funeral_time" id="time_hidden">
              </div>
          </div>
          <div>
            
            <label class="tooltip-label">
              Set Date
              <span class="tooltip-icon">ⓘ
                <span class="tooltip-text" id="dateTooltip">
                  Please book at least 1 day ahead the intended date.
                </span>
              </span>
            </label>

            <div class="mini-calendar">
              <div class="calendar-header">
                <button type="button" id="prevMonth">❮</button>
                <span id="calendarMonth"></span>
                <button type="button" id="nextMonth">❯</button>
              </div>
              <div class="calendar-grid" id="calendarGrid"></div>
            </div>
            <input type="date" id="date" name="funeral_date" required hidden />
          </div>
        </div>
        <div>
          <label>Address</label>
          <div class="form-row">
            <input type="text" name="province" placeholder="Province" required />
            <input type="text" name="city" placeholder="City" required />
            <input type="text" name="street_no" placeholder="Street Number" required />
            <input type="text" name="barangay" placeholder="Barangay" required />
          </div>
        </div>

        <hr class="divider">

        <h3>Deceased</h3>
        
        <!-- Child Info -->
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
        </div>

        <hr class="divider">

        <h3>Contact Person</h3>
        
        <label>Name of Contact Person</label>
        <div class="form-row">
          <input type="text" name="contact_person_firstname" id="first" placeholder="First Name" />
          <input type="text" name="contact_person_middlename" id="middle" placeholder="Middle Name" />
          <input type="text" name="contact_person_lastname" id="last" placeholder="Last Name" />
          <input type="text" name="contact_person_suffix" id="suffix" placeholder="Suffix" />
        </div>

        <div class="form-row">
          <div>
            <label>Relation to the deceased</label>
            <select name="contact_person_relation" id="relation">
              <option value="" disabled selected></option>
              <option value="Spouse">Spouse</option>
              <option value="Parent">Parent</option>
              <option value="Sibling">Sibling</option>
              <option value="Relative">Relative</option>
            </select>
          </div>
          <div>
            <label>Contact Number</label>
            <input type="text" name="contact_person_contact_no" id="contact" placeholder="Contact Number" required>
          </div>
          <div>
            <label>Email Address</label>
            <input type="email" name="contact_person_email" id="email" placeholder="Email Address" required />   
          </div>
        </div>


        <div class="text-center" id="reserve-btn">
          <button type="submit" name="submit">RESERVE</button>
        </div>
        
      </form>
    </div>
  </section>
</main>


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

<script>
document.addEventListener("DOMContentLoaded", () => {
    const first = "<?php echo $_SESSION['givenname'] ?? ''; ?>";
    const middle = "<?php echo $_SESSION['middlename'] ?? ''; ?>";
    const last = "<?php echo $_SESSION['lastname'] ?? ''; ?>";
    const suffix = "<?php echo $_SESSION['suffix'] ?? ''; ?>";
    const contact = "<?php echo $_SESSION['contact'] ?? ''; ?>";
    const email = "<?php echo $_SESSION['email'] ?? ''; ?>";

    document.getElementById('first').value = first;
    document.getElementById('middle').value = middle;
    document.getElementById('last').value = last;
    document.getElementById('suffix').value = suffix;
    document.getElementById('contact').value = contact;
    document.getElementById('email').value = email;
    }
);
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {

  const calendarGrid  = document.getElementById("calendarGrid");
  const calendarMonth = document.getElementById("calendarMonth");
  const dateInput     = document.getElementById("date");
  const timeButtons   = document.getElementById("timeButtons");
  const timeHidden    = document.getElementById("time_hidden");

  let currentDate = new Date();

  /* ================= TIME (ALL 24 HOURS) ================= */

  function resetTime() {
    timeButtons.innerHTML = "";
    timeHidden.value = "";
  }

  function createButton(label, value) {
    const btn = document.createElement("button");
    btn.type = "button";
    btn.textContent = label;
    btn.className = "time-btn";
    btn.dataset.value = value;

    btn.addEventListener("click", () => {
      document.querySelectorAll(".time-btn")
        .forEach(b => b.classList.remove("active"));

      btn.classList.add("active");
      timeHidden.value = value;
    });

    timeButtons.appendChild(btn);
  }

  function convertToAMPM(hour) {
    const ampm = hour >= 12 ? "PM" : "AM";
    const h = hour % 12 || 12;
    return `${h}:00 ${ampm}`;
  }

  function populateTimes() {
    resetTime();

    for (let h = 0; h < 24; h++) {
      const value = String(h).padStart(2, "0") + ":00";
      const label = convertToAMPM(h);
      createButton(label, value);
    }
  }

  /* ================= CALENDAR ================= */

  function buildISO(y, m, d) {
    return `${y}-${String(m + 1).padStart(2, "0")}-${String(d).padStart(2, "0")}`;
  }

  function loadCalendar() {
    calendarGrid.innerHTML = "";

    const year  = currentDate.getFullYear();
    const month = currentDate.getMonth();

    const firstDay    = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    calendarMonth.textContent = currentDate.toLocaleString("default", {
      month: "long",
      year: "numeric"
    });

    const days = ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];
    days.forEach(d => {
      const div = document.createElement("div");
      div.className = "calendar-weekday";
      div.textContent = d;
      calendarGrid.appendChild(div);
    });

    for (let i = 0; i < firstDay; i++) {
      calendarGrid.appendChild(document.createElement("div"));
    }

    /* ---- RULE: MUST BE AT LEAST 1 DAY AHEAD ---- */
    const minDate = new Date();
    minDate.setDate(minDate.getDate() + 1);
    minDate.setHours(0,0,0,0);

    for (let day = 1; day <= daysInMonth; day++) {
      const dateObj = new Date(year, month, day);
      dateObj.setHours(0,0,0,0);

      const isoDate = buildISO(year, month, day);

      const cell = document.createElement("div");
      cell.textContent = day;
      cell.className = "calendar-day";

      if (dateObj < minDate) {
        cell.classList.add("blocked");
      } else {
        cell.classList.add("available");
      }

      cell.addEventListener("click", () => {
        if (!cell.classList.contains("available")) return;

        document.querySelectorAll(".calendar-day")
          .forEach(d => d.classList.remove("selected"));

        cell.classList.add("selected");
        dateInput.value = isoDate;
      });

      calendarGrid.appendChild(cell);
    }
  }

  /* ================= NAV ================= */

  document.getElementById("prevMonth").onclick = () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    loadCalendar();
  };

  document.getElementById("nextMonth").onclick = () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    loadCalendar();
  };

  /* ================= INIT ================= */

  populateTimes();
  loadCalendar();

});
</script>
