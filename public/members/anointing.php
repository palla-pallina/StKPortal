<?php
require_once '../connection.php';
session_start();

// ---------------------------------------------------------
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ------------------------------
// LOAD PHPMailer
// ------------------------------
require '../vendor/PHPMailer/PHPMailer/src/Exception.php';
require '../vendor/PHPMailer/PHPMailer/src/PHPMailer.php';
require '../vendor/PHPMailer/PHPMailer/src/SMTP.php';



$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // ------------------------------
    // COLLECT & SANITIZE INPUT
    // ------------------------------
    $name    = trim($_POST["name"]);
    $email   = trim($_POST["email"]);
    $subject = trim($_POST["subject"]);
    $msg     = trim($_POST["message"]);

    if ($name && $email && $subject && $msg) {

        try {
            $mail = new PHPMailer(true);

            // ------------------------------
            // SMTP SETTINGS (GMAIL EXAMPLE)
            // ------------------------------
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'atsaratom@gmail.com';     // ✅ YOUR EMAIL
            $mail->Password   = 'gcrp redk guor brwo';   // ✅ APP PASSWORD
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // ------------------------------
            // EMAIL HEADERS
            // ------------------------------
            $mail->setFrom('atsaratom@gmail.com', 'StKPortal');
            $mail->addAddress('atsaratom@gmail.com');        // ✅ RECEIVER
            $mail->addReplyTo($email, $name);

            // ------------------------------
            // EMAIL CONTENT
            // ------------------------------
            $mail->isHTML(true);
            $mail->Subject = "Inquiry: " . $subject;

            $mail->Body = "
                <p><strong>Name:</strong> {$name}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Subject:</strong> {$subject}</p>
                <p><strong>Message:</strong><br>{$msg}</p>
            ";

            $mail->AltBody = "
                Name: $name
                Email: $email
                Subject: $subject
                Message: $msg
            ";

            // ------------------------------
            // SEND EMAIL
            // ------------------------------
            $mail->send();
            $message = "✅ Your message has been sent successfully!";

        } catch (Exception $e) {
            $message = "❌ Mail Error: {$mail->ErrorInfo}";
        }

    } else {
        $message = "❌ All fields are required.";
    }
}
// ---------------------------------------------------------

/*--------- USER ACCESS RESTRICT START
- restrict access to registered users only */
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
    echo "<script>
            
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
    <link rel="stylesheet" href="../../stylesheets/public/TOS_modal.css" />
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
            <!--<li><a href="sacraments.php">Sacraments</a></li>-->

            <li class="li-dropdown">
                <a href="#">Parish <i class="fa-solid fa-angle-down"></i></a>
                <ul class="dropdown">
                    <li style="border-bottom: 1px solid var(--yellow);"><a href="../sacraments.php">Sacraments</a></li>
                    <li><a href="../services.php">Services</a></li>
                </ul>
            </li>
            
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
                        <li style="border-bottom: 1px solid var(--yellow);"><a href="../members/status.php">Status</a></li>
                        <li><a href="../logout.php" a>Logout</a></li>
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

<div class="header-banner" style="background-image: url('../../stylesheets/assets/banner/celebrate.png');">
    <h1>Anointing of the Sick</h1>
</div>

<main>
  
  <?php if (!empty($_SESSION['toast_success'])): ?>
    <div class="success-alert">
      <i class="fa-solid fa-circle-check"></i>
      <?= htmlspecialchars($_SESSION['toast_success']) ?>
    </div>
    <?php unset($_SESSION['toast_success']); ?>
  <?php endif; ?>

  <div class="reservation-form">

    <!-- INFO SECTION -->
    <div class="info-section">
      <h4>Important Information</h4>
      <ul>
        <li>
          Anointing of the Sick requests can be submitted online for faster processing.
        </li>
        <li>
          Anointing of the Sick are reviewed immediately by the parish office. If approved, you will receive a
          notification confirming the details.
        </li>
      </ul>
    </div>

    <form action="handle/save_anointing.php" method="POST" enctype="multipart/form-data">

      <!-- ================= SCHEDULE ================= -->
      <h4 class="section-title">Anointing of the Sick Date</h4>

      <div class="form-row">
        <div>
          <label>
            Set Time <span class="required">*</span>
          </label>
          <div id="timeButtons" class="time-buttons"></div>
          <input type="hidden" name="time" id="time_hidden" required>
        </div>

        <div>
          <label class="tooltip-label">
            Set Date <span class="required">*</span>
          </label>

          <div class="mini-calendar">
            <div class="calendar-header">
              <button type="button" id="prevMonth">❮</button>
              <span id="calendarMonth"></span>
              <button type="button" id="nextMonth">❯</button>
            </div>
            <div class="calendar-grid" id="calendarGrid"></div>
          </div>
          <input type="date" name="date" id="date" hidden required>
        </div>
      </div>

      <div class="form-group centered-section">
        <div class="form-group">
          <h4 class="form-section-title" style="margin-bottom:-17px;">Where is the sick person located?</h4>

          <div class="form-venue">
            <button type="button" class="venue-btn" name="mass_venue" value="At Home"
                    onclick="toggleClaim('home', this)">
              At Home
            </button>

            <button type="button" class="venue-btn" name="mass_venue" value="At the Hospital"
                    onclick="toggleClaim('hospital', this)">
              At the Hospital
            </button>
          </div>
        </div>
      </div>

      <input type="hidden" name="mass_venue" id="mass_venue" required>

      <!-- HOME VENUE SECTION -->
      <div id="venue-home-section" style="display:none">
        <div class="form-row">
          <div>
            <label>Province <span class="required">*</span></label>
            <input type="text" name="home_province" required data-required="true">
          </div>
          <div>
            <label>City <span class="required">*</span></label>
            <input type="text" name="home_city" required data-required="true">
          </div>
        </div>

        <div class="form-row">
          <div>
            <label>Street Number <span class="required">*</span></label>
            <input type="text" name="home_street_no" required data-required="true">
          </div>
          <div>
            <label>Barangay <span class="required">*</span></label>
            <input type="text" name="home_barangay" required data-required="true">
          </div>
        </div>
      </div>

      <!-- HOSPITAL SECTION -->
      <div id="venue-hospital-section" style="display:none">
        <div class="form-row">
          <div>
            <label>Province <span class="required">*</span></label>
            <input type="text" name="hospital_province" required data-required="true">
          </div>
          <div>
            <label>City <span class="required">*</span></label>
            <input type="text" name="hospital_city" required data-required="true">
          </div>
        </div>

        <div class="form-row">
          <div>
            <label>Street Number <span class="required">*</span></label>
            <input type="text" name="hospital_street_no" required data-required="true">
          </div>
          <div>
            <label>Barangay <span class="required">*</span></label>
            <input type="text" name="hospital_barangay" required data-required="true">
          </div>
        </div>

        <div class="form-row">
          <div>
            <label>Name of Hospital <span class="required">*</span></label>
            <input type="text" name="hospital_name" required data-required="true">
          </div>
        </div>
      </div>

      <hr class="divider">


      <!-- ================= Person to be Anointed ================= -->
      <h4 class="section-title">Person to be Anointed</h4>

      <label class="group-label">
        Name <span class="required">*</span>
      </label>

      <div class="form-row">
        <div>
          <label class="sub-label">Given Name<span class="required">*</span></label>
          <input type="text" name="sick_firstname" required>
        </div>
        <div>
          <label class="sub-label"">Middle Name</label>
          <input type="text" name="sick_middlename">
        </div>
        <div>
          <label class="sub-label">Last Name<span class="required">*</span></label>
          <input type="text" name="sick_lastname" required>
        </div>
        <div>
          <label class="sub-label">Suffix<span class="required">*</span></label>
          <input type="text" name="sick_suffix">
        </div>
      </div>

      <div class="form-row">
        <div>
          <label>
            Age <span class="required">*</span>
          </label>
          <input type="number" name="age" required>
        </div>
        <div>
          <label>
            Condition <span class="required">*</span>
          </label>
          <input type="text" name="sick_condition" required>
        </div>
      </div>

      <hr class="divider">

      <!-- ================= CONTACT PERSON ================= -->
      <h4 class="section-title">Contact Person</h4>

      <label class="group-label">
        Name of Contact Person <span class="required">*</span>
      </label>

      <div class="form-row">
        <div>
          <label class="sub-label">Given Name<span class="required">*</span></label>
          <input type="text" id="first" name="contact_person_firstname" required>
        </div>
        <div>
          <label class="sub-label"">Middle Name</label>
          <input type="text" id="middle" name="contact_person_middlename">
        </div>
        <div>
          <label class="sub-label">Last Name<span class="required">*</span></label>
          <input type="text" id="last" name="contact_person_lastname" required>
        </div>
        <div>
          <label class="sub-label">Suffix</label>
          <input type="text" id="suffix" name="contact_person_suffix">
        </div>
      </div>

      <div class="form-row">
        <div>
          <label>
            Relation to the sick person <span class="required">*</span>
          </label>
          <select name="contact_person_relation" required>
            <option value="" disabled selected>Select</option>
            <option value="Child">Child</option>
            <option value="Parent">Parent</option>
            <option value="Spouse">Spouse</option>
            <option value="Sibling">Sibling</option>
            <option value="Relative">Relative</option>
          </select>
        </div>

        <div>
          <label>
            Contact Number <span class="required">*</span>
          </label>
          <input type="text" id="contact" name="contact_person_contact_no" required>
        </div>

        <div>
          <label>
            Email Address <span class="required">*</span>
          </label>
          <input type="email" id="email" name="contact_person_email" required>
        </div>
      </div>

        <!-- TERMS MODAL -->
        <div class="modal-overlay" id="termsModal">
          <div class="modal">
            <div class="modal-header">
              <h3>Terms & Conditions</h3>
              <button type="button" class="close-modal" id="closeTerms">&times;</button>
            </div>

            <div class="modal-body">
              <p>
                By requesting a certificate from Santa Krus Parish, you agree to comply
                with parish policies and guidelines.
              </p>
              <br>
              <p>
                All personal information submitted will be used solely for verification,
                documentation, and parish-related services. Your data will not be shared
                with third parties without consent.
              </p>
              <br>
              <p>
                Submitting false or misleading information may result in request denial.
              </p>
            </div>

            <div class="modal-footer">
              <button type="button" class="modal-btn" id="acceptTerms">
                I Understand
              </button>
            </div>
          </div>
        </div>

        <!-- TERMS -->
        <div class="terms-row">
          <input type="checkbox" id="terms" required style="margin-top: 0px;">
          <label for="terms">
            I agree to the
            <a href="javascript:void(0)" id="openTerms">Terms & Conditions</a>
            <span class="required">*</span>
          </label>
        </div>

      <div class="text-center" id="reserve-btn">
        <button type="submit" name="submit">SUBMIT RESERVATION</button>
      </div>

    </form>
  </div>
</main>


<!-- FOOTER -->
<footer class="custom-footer">

  <div class="footer-left">
    <h2>Santa Krus Parish</h2>
    <p>Serving the community of Bagumbong, Caloocan with faith and love.</p>
    <p><b>Email Address</b><br>stakrusparish@gmail.com</p>
    <p><b>Contact Number</b><br>(02) 8984-3933</p>

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
  

<script>
document.addEventListener("DOMContentLoaded", () => {

  const openTerms = document.getElementById("openTerms");
  const closeTerms = document.getElementById("closeTerms");
  const acceptTerms = document.getElementById("acceptTerms");
  const modal = document.getElementById("termsModal");
  const checkbox = document.getElementById("terms");

  // Open modal
  openTerms.addEventListener("click", () => {
    modal.style.display = "flex";
  });

  // Close modal (X button)
  closeTerms.addEventListener("click", () => {
    modal.style.display = "none";
  });

  // Accept terms
  acceptTerms.addEventListener("click", () => {
    checkbox.checked = true;
    modal.style.display = "none";
  });

  // Close when clicking outside modal
  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      modal.style.display = "none";
    }
  });

});
</script>


<script>
function toggleClaim(venue, btn) {
  const homeSection = document.getElementById('venue-home-section');
  const hospitalSection = document.getElementById('venue-hospital-section');
  const venueInput = document.getElementById('mass_venue');

  homeSection.style.display = 'none';
  hospitalSection.style.display = 'none';

  // remove required
  document.querySelectorAll('#venue-home-section input, #venue-hospital-section input')
    .forEach(i => i.removeAttribute('required'));

  if (venue === 'home') {
    venueInput.value = 'At Home';
    homeSection.style.display = 'block';
    homeSection.querySelectorAll('input[data-required="true"]')
      .forEach(i => i.setAttribute('required', 'required'));
  }

  if (venue === 'hospital') {
    venueInput.value = 'At the Hospital';
    hospitalSection.style.display = 'block';
    hospitalSection.querySelectorAll('input[data-required="true"]')
      .forEach(i => i.setAttribute('required', 'required'));
  }

  document.querySelectorAll('.venue-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}

</script>


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

document.addEventListener("DOMContentLoaded", () => {
    const userGender = "<?php echo $_SESSION['gender'] ?? ''; ?>".toLowerCase();

    const suffixInput = document.getElementById("suffix");
    const suffixWrapper = suffixInput.closest("div");

    function toggleSuffixByGender(gender) {
        if (gender === "female") {
            suffixWrapper.style.display = "none";
            suffixInput.value = "";      // clear value
            suffixInput.removeAttribute("required");
        } else {
            suffixWrapper.style.display = "";
        }
    }

    toggleSuffixByGender(userGender);
});
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

