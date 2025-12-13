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

<div class="header-banner" style="background-image: url('../../stylesheets/assets/banner/confirmation.jpg');">
    <h1>Confirmation</h1>
</div>

<main>
  <div class="breadcrumb">
      <a href="../sacraments.php">Sacraments</a>
      <span></span>
      <a href="../sacraments/confirmation_info.php">Confirmation</a>
      <span>Reserve Confirmation</span>
  </div>

    <!-- RESERVATION FORM -->
    <div class="reservation-form">
      <h3>Confirmation Form</h3>
      <form action="handle/save_confirmation.php" method="post" enctype="multipart/form-data">
          
        <div class="form-row">
              <!--<label>Baptism Type</label>-->
                <select name="confirmation_type" id="confirmation_type" required hidden> 
                  <option value="regular" hidden>Regular</option>
                  <option value="solo" selected>Solo</option>
                </select>
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
                  Please select a type.
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

        <div class="form-row">
          <div>
            <label>Contact Number</label>
            <input type="text" name="contact" id="contact" placeholder="Contact Number" required>
          </div>
          <div>
            <label>Email Address</label>
            <input type="email" name="email" id="email" placeholder="Email Address" required />   
          </div>
        </div>
      
        <hr class="divider">

        <h3>Confirmand</h3>
        
        <!-- Confirmand Info -->
        <label>Name of Confirmand</label> 
        <div class="form-row">
          <input type="text" name="confirm_firstname" placeholder="First Name" required />
          <input type="text" name="confirm_middlename" placeholder="Middle Name" />
          <input type="text" name="confirm_lastname" placeholder="Last Name" required />
          <input type="text" name="confirm_suffix" placeholder="Suffix" />
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
            <select name="confirm_gender" required>
              <option value="" disabled selected></option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>
          <div>
            <label>Nationality</label>
            <input type="text" name="confirm_nat" required />
          </div>
        </div>

        <hr class="divider">

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
        
        <hr class="divider">
        
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

        <div id="extra-ninang-wrapper">
          <label>Ninang</label>
          <div class="form-row extra-ninang">
            <input type="text" name="as_ninang_firstname[]" placeholder="First Name">
            <input type="text" name="as_ninang_middlename[]" placeholder="Middle Name">
            <input type="text" name="as_ninang_lastname[]" placeholder="Last Name">
            <button type="button" class="add-btn" onclick="addNinang()">+</button>
          </div>
        </div>


        <hr class="divider">

        <div class="form-row">
          <div>
            <label>Birth Certificate</label>
            <input type="file" name="birth_certificate" accept=".jpg,.jpeg,.png,.pdf"/>
          </div>

          <div>
            <label>Baptismal Certificate</label>
            <input type="file" name="baptismal_certificate" accept=".jpg,.jpeg,.png,.pdf"/>
          </div>
        </div>

        <div class="text-center" id="reserve-btn">
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

<!--
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
</script>
-->

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
  const confirmationType   = document.getElementById("confirmation_type");
  const dateInput     = document.getElementById("date");
  const timeButtons   = document.getElementById("timeButtons");
  const timeHidden    = document.getElementById("time_hidden");

  let currentDate = new Date();

  const SOLO_TIMES = [
    "10:00","15:00"
  ];
  const REGULAR_TIME = "11:30";

  /* ================= TIME ================= */

  function resetTime() {
  timeButtons.innerHTML = "";
  timeHidden.value = "";
}

function createButton(label, value, disabled = false) {
  const btn = document.createElement("button");
  btn.type = "button";
  btn.textContent = label;
  btn.className = "time-btn";
  btn.dataset.value = value;

  if (disabled) {
    btn.classList.add("disabled");
  } else {
    btn.addEventListener("click", () => {
      document.querySelectorAll(".time-btn")
        .forEach(b => b.classList.remove("active"));

      btn.classList.add("active");
      timeHidden.value = value;
    });
  }

  timeButtons.appendChild(btn);
}

function populateTimes() {
  resetTime();

  if (confirmationType.value === "regular") {
    createButton("11:30 AM", "11:30");
    document.querySelector(".time-btn").classList.add("active");
    timeHidden.value = "11:30";
  } 
  else if (confirmationType.value === "solo") {
    SOLO_TIMES.forEach(t => {
      const label = convertToAMPM(t);
      createButton(label, t);
    });
  }
}

function convertToAMPM(time) {
  let [h, m] = time.split(":");
  h = parseInt(h);
  const ampm = h >= 12 ? "PM" : "AM";
  h = h % 12 || 12;
  return `${h}:${m} ${ampm}`;
}


  /* ================= CALENDAR ================= */

  function buildISO(y, m, d) {
    return `${y}-${String(m+1).padStart(2,"0")}-${String(d).padStart(2,"0")}`;
  }

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

    const minDate = new Date();
    minDate.setDate(minDate.getDate() + 14);
    minDate.setHours(0,0,0,0);

    for (let day = 1; day <= daysInMonth; day++) {

      const dateObj = new Date(year, month, day);
      dateObj.setHours(0,0,0,0);

      const isoDate = buildISO(year, month, day);
      const dow     = dateObj.getDay();

      const cell = document.createElement("div");
      cell.textContent = day;
      cell.className = "calendar-day available";

      /* ---- 2 WEEK MINIMUM ---- */
      if (dateObj < minDate) {
        cell.classList.add("blocked");
        cell.classList.remove("available");
      }

      /* ---- SUNDAY RULES (UI BLOCKING) ---- */
      if (confirmationType.value === "solo" && dow === 0) {
        cell.classList.add("blocked");
        cell.classList.remove("available");
      }

      if (confirmationType.value === "regular" && dow !== 0) {
        cell.classList.add("blocked");
        cell.classList.remove("available");
      }

      /* ---- SLOT CHECK (ONLY IF STILL AVAILABLE) ---- */
      if (cell.classList.contains("available") && confirmationType.value) {
        try {
          const res  = await fetch(`handle/get_baptism_slot_counts.php?date=${isoDate}`);
          const data = await res.json();

          if (
            (confirmationType.value === "regular" && data.regular >= 15) ||
            (confirmationType.value === "solo"    && data.solo    >= 20)
          ) {
            cell.classList.add("full");
            cell.classList.remove("available");
          }

        } catch (e) {
          console.error("Slot check failed:", e);
        }
      }

      /* ---- CLICK HANDLER ---- */
      cell.addEventListener("click", () => {

        if (!confirmationType.value) {
          alert("Please select a Type first.");
          return;
        }

        if (!cell.classList.contains("available")) return;

        document.querySelectorAll(".calendar-day")
          .forEach(d => d.classList.remove("selected"));

        cell.classList.add("selected");
        dateInput.value = isoDate;

        if (confirmationType.value === "regular") {
          timeHidden.value = REGULAR_TIME;
        }
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

  /* ================= TYPE CHANGE ================= */

  confirmationType.addEventListener("change", () => {
    dateInput.value = "";
    populateTimes();
    loadCalendar();
  });

  /* ================= INIT ================= */

  populateTimes();
  loadCalendar();

});
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {

  const confirmationType = document.getElementById("confirmation_type"); // your select
  const tooltipText = document.getElementById("dateTooltip");

  function updateWeddingTooltip() {

    if (!confirmationType.value) {
      tooltipText.innerHTML = "Please select a type first.";
      return;
    }

    if (confirmationType.value === "solo") {
      tooltipText.innerHTML = `
        Confirmation Rules:
        <br>• You can only book at least <b>3 weeks ahead</b>
        <br>• Any day except Sundays
      `;
    }
  }

  confirmationType.addEventListener("change", updateWeddingTooltip);

  // Run once on load if already selected
  updateWeddingTooltip();

});
</script>

