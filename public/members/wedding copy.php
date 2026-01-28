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

<div class="header-banner" style="background-image: url('../../stylesheets/assets/wedding.png');">
    <h1>Marriage</h1>
</div>

<main>
  <div class="breadcrumb">
      <a href="../sacraments.php">Sacraments</a>
      <span></span>
      <a href="../sacraments/wedding_info.php">Marriage</a>
      <span>Reserve Wedding</span>
  </div>  

  <!-- HERO CRUMB END -->

    <!-- RESERVATION FORM -->
    <div class="reservation-form">
      <h3>Wedding Form</h3><br>
      <form action="handle/save_wedding.php" method="post" enctype="multipart/form-data">

        <div class="form-row">
          <div>
          <label>Wedding Type</label>
            <select name="wedding_type" id="wedding_type" required > 
              <option value="" disabled selected></option>
              <option value="batch">Batch</option>
              <option value="solo">Solo</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div>
            <div style="padding-top: 9px;">
            <label>Set Time</label>
              <div id="timeButtons" class="time-buttons"></div>
                <input type="hidden" name="time" id="time_hidden">
              </div>
          </div>
          <div>
            
            <label class="tooltip-label">
              Set Date
              <span class="tooltip-icon">ⓘ
                <span class="tooltip-text" id="dateTooltip">
                  Please select a wedding type.
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
            <input type="date" id="date" name="date" required hidden />
          </div>
        </div>


          
        <hr class="divider">
        <h3>Bride</h3>
        
        <!-- Bride Info -->
        <label>Name of Bride</label>
        <div class="form-row">
          <input type="text" name="bride_firstname" id="bride_first" placeholder="First Name" required />
          <input type="text" name="bride_middlename" id="bride_middle" placeholder="Middle Name" />
          <input type="text" name="bride_lastname" id="bride_last" placeholder="Last Name" required />
        </div>

        <div class="form-row">
          <div>
            <label>Birthdate</label>
              <input type="date" name="bride_birthdate" id="bride_birthdate" required max="<?= date('Y-m-d', strtotime('-18 years')) ?>">
          </div>
          <div>
            <label>Nationality</label>
            <input type="text" name="bride_nationality" id="bride_nationality" placeholder="Nationality" value="Filipino" required />
          </div>
        </div>

        <div class="form-row">
          <div>
            <label>Contact Number</label>
            <input type="text" name="bride_contact" id="bride_contact" placeholder="Contact Number" required>
          </div>
          <div>
            <label>Email</label>
            <input type="email" name="bride_email" id="bride_email" placeholder="Email Address" required>
          </div>
        </div>

      <hr class="divider-dot" style="color:white;">
      <h4 style="font-size:20px; font-weight:700;">Bride Required Documents</h4>

        <div class="form-row">
          <div>
            <label>Birth Certificate</label>
            <input type="file" name="b_birth_certificate" accept=".jpg,.jpeg,.png,.pdf"/>
          </div>
          <div>
            <label>Baptismal Certificate</label>
            <input type="file" name="b_baptismal_certificate" accept=".jpg,.jpeg,.png,.pdf"/>
          </div>
          <div>
            <label>Confirmation Certificate</label>
            <input type="file" name="b_confirmation_certificate" accept=".jpg,.jpeg,.png,.pdf"/>
          </div>
        </div>
          
        <div class="form-row">
          <div>
            <label>Government-issued Marriage License</label>
            <input type="file" name="b_marriage_license" accept=".jpg,.jpeg,.png,.pdf"/>
          </div>
          <div>
            <label>Certificate of No Marriage (CENOMAR)</label>
            <input type="file" name="b_cenomar" accept=".jpg,.jpeg,.png,.pdf"/>
          </div>
        </div>

        
        <hr class="divider">

        <!-- Groom Info -->
         <h3>Groom</h3>
        <label>Name of Groom</label>
        <div class="form-row">
          <input type="text" name="groom_firstname" id="groom_first" placeholder="First Name" required />
          <input type="text" name="groom_middlename" id="groom_middle" placeholder="Middle Name" />
          <input type="text" name="groom_lastname" id="groom_last" placeholder="Last Name" required />
          <input type="text" name="groom_suffix" id="groom_suffix" placeholder="Suffix" />
        </div>

        <div class="form-row">
          <div>
            <label>Birthdate</label>
              <input type="date" name="groom_birthdate" id="groom_birthdate" required max="<?= date('Y-m-d', strtotime('-18 years')) ?>">
          </div>
          <div>
            <label>Nationality</label>
            <input type="text" name="groom_nationality" id="groom_nationality" placeholder="Nationality" value="Filipino" required />
          </div>
        </div>

        <div class="form-row">
          <div>
            <label>Contact Number</label>
            <input type="text" name="groom_contact" id="groom_contact" placeholder="Contact Number" required>
          </div>
          <div>
            <label>Email</label>
            <input type="email" name="groom_email" id="groom_email" placeholder="Email Address" required>
          </div>
        </div>

      <hr class="divider-dot" style="color:white;">
      <h4 style="font-size:20px; font-weight:700;">Groom Required Documents</h4>

        <div class="form-row">
          <div>
            <label>Birth Certificate</label>
            <input type="file" name="g_birth_certificate" accept=".jpg,.jpeg,.png,.pdf"/>
          </div>
          <div>
            <label>Baptismal Certificate</label>
            <input type="file" name="g_baptismal_certificate" accept=".jpg,.jpeg,.png,.pdf"/>
          </div>
          <div>
            <label>Confirmation Certificate</label>
            <input type="file" name="g_confirmation_certificate" accept=".jpg,.jpeg,.png,.pdf"/>
          </div>
        </div>
          
        <div class="form-row">
          <div>
            <label>Government-issued Marriage License</label>
            <input type="file" name="g_marriage_license" accept=".jpg,.jpeg,.png,.pdf"/>
          </div>
          <div>
            <label>Certificate of No Marriage (CENOMAR)</label>
            <input type="file" name="g_cenomar" accept=".jpg,.jpeg,.png,.pdf"/>
          </div>
        </div>

        <hr class="divider">
        
        <h3>Primary Sponsors</h3>

        <label>Pangunahing Ninong</label>
        <div class="form-row">
          <input type="text" name="ninong_firstname" placeholder="First Name"/>
          <input type="text" name="ninong_middlename" placeholder="Middle Name" />
          <input type="text" name="ninong_lastname" placeholder="Last Name"/>
          <input type="text" name="ninong_suffix" placeholder="Suffix"/>
        </div>

        <hr class="divider-dot">

        <label>Pangunahing Ninang</label>
        <div class="form-row">
          <input type="text" name="ninang_firstname" placeholder="First Name"/>
          <input type="text" name="ninang_middlename" placeholder="Middle Name" />
          <input type="text" name="ninang_lastname" placeholder="Last Name" />
        </div>

        <hr class="divider">
        <h3>Additional Sponsors</h3>

        <div id="extra-ninong-wrapper">
          <label>Ninong</label>
          <div class="form-row extra-ninong">
            <input type="text" name="as_ninong_firstname[]" placeholder="First Name">
            <input type="text" name="as_ninong_middlename[]" placeholder="Middle Name">
            <input type="text" name="as_ninong_lastname[]" placeholder="Last Name">
            <input type="text" name="as_ninong_suffix[]" placeholder="Suffix">
            <button type="button" class="add-btn" onclick="addNinong()">+</button>
          </div>
        </div>

        <hr class="divider-dot">

        <div id="extra-ninang-wrapper">
          <label>Ninang</label>
          <div class="form-row extra-ninang">
            <input type="text" name="as_ninang_firstname[]" placeholder="First Name">
            <input type="text" name="as_ninang_middlename[]" placeholder="Middle Name">
            <input type="text" name="as_ninang_lastname[]" placeholder="Last Name">
            <button type="button" class="add-btn" onclick="addNinang()">+</button>
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
document.querySelector("form").addEventListener("submit", function (e) {

  const brideDate = document.getElementById("bride_birthdate").value;
  const groomDate = document.getElementById("groom_birthdate").value;

  function getAge(birthDate) {
    const today = new Date();
    const bday  = new Date(birthDate);
    let age = today.getFullYear() - bday.getFullYear();
    const m = today.getMonth() - bday.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < bday.getDate())) age--;
    return age;
  }

  if (brideDate && getAge(brideDate) < 18) {
    alert("Bride must be 18 years old and above.");
    e.preventDefault();
    return;
  }

  if (groomDate && getAge(groomDate) < 18) {
    alert("Groom must be 18 years old and above.");
    e.preventDefault();
    return;
  }

});
</script>

</body>
</html>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const first = "<?php echo $_SESSION['givenname'] ?? ''; ?>";
    const middle = "<?php echo $_SESSION['middlename'] ?? ''; ?>";
    const last = "<?php echo $_SESSION['lastname'] ?? ''; ?>";
    const suffix = "<?php echo $_SESSION['suffix'] ?? ''; ?>";
    const birthday = "<?php echo $_SESSION['birthday'] ?? ''; ?>";
    const contact = "<?php echo $_SESSION['contact'] ?? ''; ?>";
    const email = "<?php echo $_SESSION['email'] ?? ''; ?>";
    const gender = "<?php echo $_SESSION['gender'] ?? ''; ?>";

    if (gender === "Female") {
        document.getElementById('bride_first').value = first;
        document.getElementById('bride_middle').value = middle;
        document.getElementById('bride_last').value = last;
        document.getElementById('bride_birthdate').value = birthday;
        document.getElementById('bride_contact').value = contact;
        document.getElementById('bride_email').value = email;
    }

    if (gender === "Male") {
        document.getElementById('groom_first').value = first;
        document.getElementById('groom_middle').value = middle;
        document.getElementById('groom_last').value = last;
        document.getElementById('groom_suffix').value = suffix;
        document.getElementById('groom_birthdate').value = birthday;
        document.getElementById('groom_contact').value = contact;
        document.getElementById('groom_email').value = email;
    }
});
</script>

<script>
function addNinong() {
  const wrapper = document.getElementById("extra-ninong-wrapper");
  const div = document.createElement("div");
  div.className = "form-row extra-ninong";

  div.innerHTML = `
    <input type="text" name="as_ninong_firstname[]" placeholder="First Name">
    <input type="text" name="as_ninong_middlename[]" placeholder="Middle Name">
    <input type="text" name="as_ninong_lastname[]" placeholder="Last Name">
    <input type="text" name="as_ninong_suffix[]" placeholder="Suffix">
    <button type="button" class="remove-btn" onclick="this.parentElement.remove()">−</button>
  `;

  wrapper.appendChild(div);
}

function addNinang() {
  const wrapper = document.getElementById("extra-ninang-wrapper");
  const div = document.createElement("div");
  div.className = "form-row extra-ninang";

  div.innerHTML = `
    <input type="text" name="as_ninang_firstname[]" placeholder="First Name">
    <input type="text" name="as_ninang_middlename[]" placeholder="Middle Name">
    <input type="text" name="as_ninang_lastname[]" placeholder="Last Name">
    <button type="button" class="remove-btn" onclick="this.parentElement.remove()">−</button>
  `;

  wrapper.appendChild(div);
}
</script>

<script>
document.addEventListener("DOMContentLoaded", async () => {

const calendarGrid  = document.getElementById("calendarGrid");
const calendarMonth = document.getElementById("calendarMonth");
const weddingType   = document.getElementById("wedding_type");
const dateInput     = document.getElementById("date");
const timeButtons   = document.getElementById("timeButtons");
const timeHidden    = document.getElementById("time_hidden");

let currentDate = new Date();

/* ================= TIME CONFIG ================= */

const SOLO_TIMES = [
  "07:00","08:00","09:00","10:00","11:00",
  "12:00","13:00","14:00","15:00","16:00"
];

const BATCH_TIME = "10:00";

/* ================= TIME UI ================= */

function resetTime() {
  timeButtons.innerHTML = "";
  timeHidden.value = "";
}

function createButton(label, value, disabled=false) {
  const btn = document.createElement("button");
  btn.type = "button";
  btn.textContent = label;
  btn.className = "time-btn";

  if (disabled) btn.classList.add("disabled");

  btn.onclick = () => {
    if (btn.classList.contains("disabled")) return;
    document.querySelectorAll(".time-btn").forEach(b => b.classList.remove("active"));
    btn.classList.add("active");
    timeHidden.value = value;
  };

  timeButtons.appendChild(btn);
}

function populateTimes() {
  resetTime();

  if (weddingType.value === "batch") {
    createButton("10:00 AM", BATCH_TIME);
    document.querySelector(".time-btn").classList.add("active");
    timeHidden.value = BATCH_TIME;
  }

  if (weddingType.value === "solo") {
    SOLO_TIMES.forEach(t => {
      createButton(convertToAMPM(t), t);
    });
  }
}

function convertToAMPM(t) {
  let [h,m] = t.split(":");
  h = parseInt(h);
  const ampm = h >= 12 ? "PM" : "AM";
  h = h % 12 || 12;
  return `${h}:${m} ${ampm}`;
}

/* ================= DATE HELPERS ================= */

function buildISO(y,m,d) {
  return `${y}-${String(m+1).padStart(2,"0")}-${String(d).padStart(2,"0")}`;
}

function isLastSaturday(dateObj) {
  const test = new Date(dateObj);
  test.setDate(test.getDate() + 7);
  return test.getMonth() !== dateObj.getMonth() && dateObj.getDay() === 6;
}

/* ================= CALENDAR LOAD ================= */

async function loadCalendar() {
  calendarGrid.innerHTML = "";

  const year  = currentDate.getFullYear();
  const month = currentDate.getMonth();

  const firstDay    = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();

  calendarMonth.textContent = currentDate.toLocaleString("default", {
    month: "long",
    year: "numeric"
  });

  ["Sun","Mon","Tue","Wed","Thu","Fri","Sat"].forEach(d => {
    const div = document.createElement("div");
    div.className = "calendar-weekday";
    div.textContent = d;
    calendarGrid.appendChild(div);
  });

  for (let i=0;i<firstDay;i++) calendarGrid.appendChild(document.createElement("div"));

  const minDate = new Date();
  minDate.setDate(minDate.getDate() + 90);
  minDate.setHours(0,0,0,0);

  for (let day=1; day<=daysInMonth; day++) {

    const dateObj = new Date(year, month, day);
    dateObj.setHours(0,0,0,0);

    const isoDate = buildISO(year, month, day);
    const dow = dateObj.getDay();

    const cell = document.createElement("div");
    cell.textContent = day;
    cell.className = "calendar-day available";

    /* --- 2 WEEK RULE --- */
    if (dateObj < minDate) {
      cell.classList.add("blocked"); 
      cell.classList.remove("available");
    }

    /* --- BATCH WEDDING RULES --- */
    const allowedBatchMonths = [1,5,9]; // Feb, Jun, Oct
    if (weddingType.value === "batch") {
      if (!allowedBatchMonths.includes(month) || !isLastSaturday(dateObj)) {
        cell.classList.add("blocked");
        cell.classList.remove("available");
      }
    }

    /* --- SOLO WEDDING RULES --- */
    if (weddingType.value === "solo" && dow === 0) {
      cell.classList.add("blocked");
      cell.classList.remove("available");
    }

    /* --- SLOT CHECK --- */
    if (cell.classList.contains("available") && weddingType.value) {
      try {
        const res = await fetch(`handle/get_wedding_slot_counts.php?date=${isoDate}`);
        const data = await res.json();

        if (
          (weddingType.value === "batch" && data.batch >= 20) ||
          (weddingType.value === "solo"  && data.solo  >= 1)
        ) {
          cell.classList.add("full");
          cell.classList.remove("available");
        }
      } catch(e) {
        console.error("Slot check failed", e);
      }
    }

    /* --- CLICK DATE --- */
    cell.onclick = () => {

      if (!weddingType.value) {
        alert("Select Wedding Type first.");
        return;
      }

      if (!cell.classList.contains("available")) return;

      document.querySelectorAll(".calendar-day").forEach(d=>d.classList.remove("selected"));
      cell.classList.add("selected");
      dateInput.value = isoDate;

      if (weddingType.value === "batch") {
        timeHidden.value = BATCH_TIME;
      }
    };

    calendarGrid.appendChild(cell);
  }
}

/* ================= NAV ================= */

document.getElementById("prevMonth").onclick = () => {
  currentDate.setMonth(currentDate.getMonth()-1);
  loadCalendar();
};

document.getElementById("nextMonth").onclick = () => {
  currentDate.setMonth(currentDate.getMonth()+1);
  loadCalendar();
};

/* ================= TYPE CHANGE ================= */

weddingType.onchange = () => {
  dateInput.value = "";
  populateTimes();
  loadCalendar();
};

/* ================= INIT ================= */

populateTimes();
loadCalendar();

});
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {

  const weddingType = document.getElementById("wedding_type"); // your select
  const tooltipText = document.getElementById("dateTooltip");

  function updateWeddingTooltip() {

    if (!weddingType.value) {
      tooltipText.innerHTML = "Please select a wedding type first.";
      return;
    }

    if (weddingType.value === "solo") {
      tooltipText.innerHTML = `
        Solo Wedding Rules:
        <br>• You can only book at least <b>3 months ahead</b>
        <br>• Any day except Sundays
      `;
    }

    if (weddingType.value === "batch") {
      tooltipText.innerHTML = `
        Batch Wedding Rules:
        <br>• Only on the <b>last Saturday</b>
        <br>• Only during <b>February, June, and October</b>
        <br>• 20 couples only per batch
        <br>• Time is fixed at <b>10:00 AM</b>
      `;
    }
  }

  weddingType.addEventListener("change", updateWeddingTooltip);

  // Run once on load if already selected
  updateWeddingTooltip();

});
</script>
