<?php
$conn = new mysqli('localhost', 'root', '', 'test_tree_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}
$admin_name = !empty($_SESSION['givenname']) ? $_SESSION['givenname'] : $_SESSION['username'];

header('X-Content-Type-Options: nosniff');

$message = '';

// ---------- REQUEST HANDLING (POST create/edit/delete) ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // DELETE (POST with ?delete=1&type=...&id=...)
    if (isset($_GET['delete']) && isset($_GET['type']) && isset($_GET['id'])) {
        $type = $_GET['type'];
        $id = intval($_GET['id']);
        if ($type === 'wedding') {
            $stmt = $conn->prepare("DELETE FROM weddings WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        } elseif ($type === 'funeral') {
            $stmt = $conn->prepare("DELETE FROM funerals WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        } elseif ($type === 'baptism') {
            $stmt = $conn->prepare("DELETE FROM baptisms WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
        // Return OK (empty response)
        http_response_code(200);
        exit;
    }

    // EDIT (POST with ?edit=1&type=...&id=...)
    if (isset($_GET['edit']) && isset($_GET['type']) && isset($_GET['id'])) {
        $type = $_GET['type'];
        $id = intval($_GET['id']);

        if ($type === 'wedding') {
            $stmt = $conn->prepare("
                UPDATE weddings SET
                    bride_firstname=?, bride_middlename=?, bride_lastname=?, bride_suffix=?,
                    groom_firstname=?, groom_middlename=?, groom_lastname=?, groom_suffix=?,
                    best_man__firstname=?, best_man__middlename=?, best_man__lastname=?, best_man__suffix=?,
                    maid_of_honor_firstname=?, maid_of_honor_middlename=?, maid_of_honor_lastname=?, maid_of_honor_suffix=?,
                    phone=?, date=?, time=?
                WHERE id=?
            ");
            $stmt->bind_param("sssssssssssssssssssi",
                $_POST['BrideFN'], $_POST['BrideMN'], $_POST['BrideLN'], $_POST['BrideSuffix'],
                $_POST['GroomFN'], $_POST['GroomMN'], $_POST['GroomLN'], $_POST['GroomSuffix'],
                $_POST['Best_Man_FN'], $_POST['Best_Man_MN'], $_POST['Best_Man_LN'], $_POST['Best_Man_Suffix'],
                $_POST['Maid_Of_HonorFN'], $_POST['Maid_Of_HonorMN'], $_POST['Maid_Of_HonorLN'], $_POST['Maid_Of_HonorSuffix'],
                $_POST['Phone_Number'], $_POST['Date'], $_POST['Time'], $id
            );
            $stmt->execute();
            $stmt->close();
        } elseif ($type === 'funeral') {
            // Ensure cause_of_death column exists in DB
            $stmt = $conn->prepare("
                UPDATE funerals SET
                    deceased_firstname=?, deceased_middlename=?, deceased_lastname=?, deceased_suffix=?,
                    contact_person_firstname=?, contact_person_middlename=?, contact_person_lastname=?, contact_person_suffix=?,
                    contact_person_contact_no=?, cause_of_death=?, funeral_address=?, funeral_date=?, funeral_time=?, presider=?
                WHERE id=?
            ");
            $stmt->bind_param("ssssssssssssssi",
                $_POST['DeceasedFN'], $_POST['DeceasedMN'], $_POST['DeceasedLN'], $_POST['DeceasedSuffix'],
                $_POST['ContactFN'], $_POST['ContactMN'], $_POST['ContactLN'], $_POST['ContactSuffix'],
                $_POST['Phone_Number'], $_POST['Cause_of_Death'], $_POST['Address'], $_POST['Date'], $_POST['Time'],
                $_POST['Presider'], $id
            );
            $stmt->execute();
            $stmt->close();
        } elseif ($type === 'baptism') {
            $stmt = $conn->prepare("
                UPDATE baptisms SET
                    child_firstname=?, child_middlename=?, child_lastname=?, child_suffix=?,
                    mother_firstname=?, mother_middlename=?, mother_lastname=?, mother_suffix=?,
                    father_firstname=?, father_middlename=?, father_lastname=?, father_suffix=?,
                    contact=?, date=?, time=?
                WHERE id=?
            ");
            $stmt->bind_param("ssssssssssssssi",
                $_POST['ChildFN'], $_POST['ChildMN'], $_POST['ChildLN'], $_POST['ChildSuffix'],
                $_POST['MotherFN'], $_POST['MotherMN'], $_POST['MotherLN'], $_POST['MotherSuffix'],
                $_POST['FatherFN'], $_POST['FatherMN'], $_POST['FatherLN'], $_POST['FatherSuffix'],
                $_POST['Contact'], $_POST['Date'], $_POST['Time'], $id
            );
            $stmt->execute();
            $stmt->close();
        }

        // Return OK (empty response)
        http_response_code(200);
        exit;
    }

// UPDATE STATUS (accept/cancel)
if (isset($_GET['status']) && isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $id = intval($_GET['id']);
    $status = $_GET['status']; // "Accepted" or "Cancelled"

    $table = ($type === 'wedding') ? 'weddings' : (($type === 'funeral') ? 'funerals' : 'baptisms');

    // Get reservation_user_id
    $stmt = $conn->prepare("SELECT reservation_user_id FROM $table WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();

    $user_id = $booking['reservation_user_id'] ?? null;

    // Update status
    $stmt = $conn->prepare("UPDATE $table SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();

    // Insert notification if accepted
    if ($status === "Accepted" && !empty($user_id)) {
        $title = ucfirst($type) . " Request Accepted";
        $message = "Your request has been accepted. Please proceed with your donation.";
        $typeNotif = "donation_reminder";

        $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type, is_read, created_at) 
                                VALUES (?, ?, ?, ?, 0, NOW())");
        $stmt->bind_param("isss", $user_id, $title, $message, $typeNotif);
        if(!$stmt->execute()) {
            error_log("Notification insert error: " . $stmt->error);
        }
        $stmt->close();
    }

    http_response_code(200);
    exit;
}


  


    // CREATE (regular POST from modal form, type in form hidden input)
    $type = $_POST['type'] ?? '';

    if ($type === 'wedding') {
        // collect and default optional fields
        $bride_firstname = $_POST['BrideFN'] ?? '';
        $bride_middlename = $_POST['BrideMN'] ?? '';
        $bride_lastname = $_POST['BrideLN'] ?? '';
        $bride_suffix = $_POST['BrideSuffix'] ?? '';
        $groom_firstname = $_POST['GroomFN'] ?? '';
        $groom_middlename = $_POST['GroomMN'] ?? '';
        $groom_lastname = $_POST['GroomLN'] ?? '';
        $groom_suffix = $_POST['GroomSuffix'] ?? '';
        $best_man_firstname = $_POST['Best_Man_FN'] ?? '';
        $best_man_middlename = $_POST['Best_Man_MN'] ?? '';
        $best_man_lastname = $_POST['Best_Man_LN'] ?? '';
        $best_man_suffix = $_POST['Best_Man_Suffix'] ?? '';
        $maid_of_honor_firstname = $_POST['Maid_Of_HonorFN'] ?? '';
        $maid_of_honor_middlename = $_POST['Maid_Of_HonorMN'] ?? '';
        $maid_of_honor_lastname = $_POST['Maid_Of_HonorLN'] ?? '';
        $maid_of_honor_suffix = $_POST['Maid_Of_HonorSuffix'] ?? '';
        $phone = $_POST['Phone_Number'] ?? '';
        $date = $_POST['Date'] ?? null;
        $time = $_POST['Time'] ?? null;

        $stmt = $conn->prepare("
            INSERT INTO weddings (
                bride_firstname, bride_middlename, bride_lastname, bride_suffix,
                groom_firstname, groom_middlename, groom_lastname, groom_suffix,
                best_man_firstname, best_man_middlename, best_man_lastname, best_man_suffix,        
                maid_of_honor_firstname, maid_of_honor_middlename, maid_of_honor_lastname, maid_of_honor_suffix,
                phone, date, time
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssssssssssssssss",
            $bride_firstname, $bride_middlename, $bride_lastname, $bride_suffix,
            $groom_firstname, $groom_middlename, $groom_lastname, $groom_suffix,
            $best_man_firstname, $best_man_middlename, $best_man_lastname, $best_man_suffix,        
            $maid_of_honor_firstname, $maid_of_honor_middlename, $maid_of_honor_lastname, $maid_of_honor_suffix,
            $phone, $date, $time
        );
        $stmt->execute();
        $stmt->close();
        $message = 'Wedding reservation saved.';
    } elseif ($type === 'funeral') {
        $deceased_firstname = $_POST['DeceasedFN'] ?? '';
        $deceased_middlename = $_POST['DeceasedMN'] ?? '';
        $deceased_lastname = $_POST['DeceasedLN'] ?? '';
        $deceased_suffix = $_POST['DeceasedSuffix'] ?? '';

        $contact_person_firstname = $_POST['ContactFN'] ?? '';
        $contact_person_middlename = $_POST['ContactMN'] ?? '';
        $contact_person_lastname = $_POST['ContactLN'] ?? '';
        $contact_person_suffix = $_POST['ContactSuffix'] ?? '';
        $contact_person_contact_no = $_POST['Phone_Number'] ?? '';

        $cause = $_POST['Cause_of_Death'] ?? '';
        $funeral_address = $_POST['Address'] ?? '';
        $funeral_date = $_POST['Date'] ?? null;
        $funeral_time = $_POST['Time'] ?? null;
        $presider = $_POST['Presider'] ?? '';

        $stmt = $conn->prepare("
            INSERT INTO funerals (
                deceased_firstname, deceased_middlename, deceased_lastname, deceased_suffix,
                contact_person_firstname, contact_person_middlename, contact_person_lastname, contact_person_suffix, contact_person_contact_no,
                cause_of_death, funeral_address, funeral_date, funeral_time, presider
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssssssssssss",
            $deceased_firstname, $deceased_middlename, $deceased_lastname, $deceased_suffix,
            $contact_person_firstname, $contact_person_middlename, $contact_person_lastname, $contact_person_suffix, $contact_person_contact_no,
            $cause, $funeral_address, $funeral_date, $funeral_time, $presider
        );
        $stmt->execute();
        $stmt->close();
        $message = 'Funeral reservation saved.';
    } elseif ($type === 'baptism') {
        $child_firstname = $_POST['ChildFN'] ?? '';
        $child_middlename = $_POST['ChildMN'] ?? '';
        $child_lastname = $_POST['ChildLN'] ?? '';
        $child_suffix = $_POST['ChildSuffix'] ?? '';

        $mother_firstname = $_POST['MotherFN'] ?? '';
        $mother_middlename = $_POST['MotherMN'] ?? '';
        $mother_lastname = $_POST['MotherLN'] ?? '';
        $mother_suffix = $_POST['MotherSuffix'] ?? '';

        $father_firstname = $_POST['FatherFN'] ?? '';
        $father_middlename = $_POST['FatherMN'] ?? '';
        $father_lastname = $_POST['FatherLN'] ?? '';
        $father_suffix = $_POST['FatherSuffix'] ?? '';

        $contact = $_POST['Contact'] ?? '';
        $date = $_POST['Date'] ?? null;
        $time = $_POST['Time'] ?? null;

        $stmt = $conn->prepare("
            INSERT INTO baptisms (
                child_firstname, child_middlename, child_lastname, child_suffix,
                mother_firstname, mother_middlename, mother_lastname, mother_suffix,
                father_firstname, father_middlename, father_lastname, father_suffix,
                contact, date, time
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssssssssssss",
            $child_firstname, $child_middlename, $child_lastname, $child_suffix,
            $mother_firstname, $mother_middlename, $mother_lastname, $mother_suffix,
            $father_firstname, $father_middlename, $father_lastname, $father_suffix,
            $contact, $date, $time
        );
        $stmt->execute();
        $stmt->close();
        $message = 'Baptism reservation saved.';
    }

    // Done handling POST create/edit/delete — return JSON or redirect as desired.
    // For the AJAX form we use fetch and expect a plain response — we'll just exit.
    exit;
}

// ---------- FETCH (AJAX) ----------
if (isset($_GET['fetch']) && isset($_GET['type'])) {
    $type = $_GET['type'];
    $data = [];

    // optional single record fetch
    $singleId = isset($_GET['id']) ? intval($_GET['id']) : null;

    if ($type === 'wedding') {
        if ($singleId) {
            $stmt = $conn->prepare("SELECT * FROM weddings WHERE id=? LIMIT 1");
            $stmt->bind_param("i", $singleId);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $data = $row ? [$row] : [];
            $stmt->close();
        } else {
            $result = $conn->query("SELECT * FROM weddings ORDER BY date DESC");
            while ($row = $result->fetch_assoc()) $data[] = $row;
        }
    } elseif ($type === 'funeral') {
        if ($singleId) {
            $stmt = $conn->prepare("SELECT * FROM funerals WHERE id=? LIMIT 1");
            $stmt->bind_param("i", $singleId);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $data = $row ? [$row] : [];
            $stmt->close();
        } else {
            // note: order by funeral_date
            $result = $conn->query("SELECT * FROM funerals ORDER BY funeral_date DESC");
            while ($row = $result->fetch_assoc()) $data[] = $row;
        }
    } elseif ($type === 'baptism') {
        if ($singleId) {
            $stmt = $conn->prepare("SELECT * FROM baptisms WHERE id=? LIMIT 1");
            $stmt->bind_param("i", $singleId);
            $stmt->execute();
            $res = $stmt->get_result();
            $row = $res->fetch_assoc();
            $data = $row ? [$row] : [];
            $stmt->close();
        } else {
            $result = $conn->query("SELECT * FROM baptisms ORDER BY date DESC");
            while ($row = $result->fetch_assoc()) $data[] = $row;
        }
    }

    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
  <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="../stylesheets/private/booking_sacraments.css" />
  <title>Reservations</title>
</head>
<body>

<!-- SIDEBAR (unchanged) -->
<section id="sidebar" <?php if (isset($hideSidebar) && $hideSidebar) echo 'class="hide"'; ?>>
<a href="#" class="label">
<img src="santa krus logo.png" alt="Logo" class="logo" />
</a>
<ul class="side-menu top">
<li><a href="dashboard.php"><span class="text">Dashboard</span></a></li>
<li><a href="stk/filemanager.php"><span class="text">File Manager</span></a></li>
<li><a href="stk/announcement.php"><span class="text">Announcement</span></a></li>
<li><a href="#"><span class="text">Event</span></a></li>
<li><a href="stk/members.php"><span class="text">Members</span></a></li>
<li><a href="certificate/certificate.php"><span class="text">Certificates</span></a></li>
<li class="active"><a href="booking_sacraments.php"><span class="text">Reservations</span></a></li>
<li><a href="donations.php" data-section="dashboard"><span class="text">Donations</span></a></li>
<li><a href="prayer_admin.php"><span class="text">Prayer Requests</span></a></li>
</ul>
<ul class="side-menu">
<li><a href="logout.php" class="logout"><span class="text">Logout</span></a></li>
</ul>
</section>

<section id="content">
  <nav>
    <i class='bx bx-menu'></i>
    <div id="datetime"></div>
    <div class="profile">
      <span class="greeting"><?php echo htmlspecialchars($admin_name, ENT_QUOTES, 'UTF-8'); ?></span>
    </div>
  </nav>

  <main>
    <section id="reservations" class="content-section active">
      <div class="main-content">
        <div class="header-row">
          <h1>Reservations</h1>
          <div class="controls">
            <label for="eventType">Type:</label>
            <select id="eventType" style ="height: 39px; font-size: 18px;">
              <option value="baptism">Baptism</option>
              <option value="wedding">Wedding</option>
              <option value="funeral">Funeral Mass</option>
            </select>
          </div>
        </div>

        <hr class="divider" />

        <div class="table-controls">
          <input type="text" id="searchInput" placeholder="Search..." />
          <select id="sortBy">
            <option value="date">Sort by Date</option>
            <option value="name">Sort by Name</option>
          </select>
          <button id="addReservation"><i class='bx bx-plus'></i></button>
        </div>

        <div class="reservation-layout">
          <div class="table-container">
            <table id="reservationTable"></table>
          </div>
          <div>
            <div class="calendar-container">
              <div class="calendar-header">
                <button id="prevMonth">&lt;</button>
                <span id="monthYear"></span>
                <button id="nextMonth">&gt;</button>
              </div>
              <div class="calendar-grid" id="calendarDays"></div>
            </div>
            <p id="eventSummary" class="calendar-total">Total events this month: 0</p>
          </div>
        </div>
      </div>
    </section>
  </main>
</section>

<!-- Modal + form container -->
<div id="reservationModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2>Add Reservation</h2>
      <span class="close">&times;</span>
    </div>
    <form id="formFields" method="POST" action="">
      <!-- generated by JS -->
    </form>
  </div>
</div>

<script>
/* ---------- UI helpers & data flow ---------- */
const menuIcon = document.querySelector('.bx-menu');
const sidebar = document.querySelector('#sidebar');
menuIcon && menuIcon.addEventListener('click', () => sidebar.classList.toggle('hide'));

function updateDateTime() {
  const now = new Date();
  const optionsDate = { year: 'numeric', month: 'short', day: 'numeric' };
  const date = now.toLocaleDateString(undefined, optionsDate);
  const optionsTime = { hour: '2-digit', minute: '2-digit' };
  const time = now.toLocaleTimeString(undefined, optionsTime);
  document.getElementById('datetime').textContent = `${date} | ${time}`;
}
setInterval(updateDateTime, 1000);
updateDateTime();

/* elements */
const eventTypeSelect = document.getElementById('eventType');
const reservationTable = document.getElementById('reservationTable');
const searchInput = document.getElementById('searchInput');
const calendarDays = document.getElementById('calendarDays');
const monthYear = document.getElementById('monthYear');
const prevMonthBtn = document.getElementById('prevMonth');
const nextMonthBtn = document.getElementById('nextMonth');
const eventSummary = document.getElementById('eventSummary');

const addReservationBtn = document.getElementById('addReservation');
const modal = document.getElementById("reservationModal");
const closeBtn = document.querySelector(".close");
const formFields = document.getElementById("formFields");

let currentDate = new Date();

function formatTime(timeStr) {
  if (!timeStr) return '';
  const [hourStr, minuteStr] = timeStr.split(':');
  let hour = parseInt(hourStr, 10);
  const minute = minuteStr ? minuteStr.padStart(2, '0') : '00';
  const ampm = hour >= 12 ? 'PM' : 'AM';
  hour = hour % 12 || 12;
  return `${hour}:${minute} ${ampm}`;
}

/* ---------- Render table and wire buttons ---------- */
function renderTable(type) {
  let headers = '';
  if (type === 'wedding') {
    headers = `<tr>
      <th>Bride</th><th>Groom</th><th>Phone Number</th><th>Date</th>
      <th>Time</th><th>Best Man</th><th>Maid of Honor</th><th>Action</th>
    </tr>`;
  } else if (type === 'funeral') {
    headers = `<tr>
      <th>Name</th><th>Cause of Death</th><th>Contact Person</th><th>Phone Number</th>
      <th>Address</th><th>Date</th><th>Time</th><th>Presider</th><th>Action</th>
    </tr>`;
  } else {
    headers = `<tr>
      <th>Child</th><th>Mother</th><th>Father</th><th>Phone Number</th>
      <th>Date</th><th>Time</th><th>Status</th><th>Action</th>
    </tr>`;
  }

  reservationTable.innerHTML = `<thead>${headers}</thead><tbody><tr><td colspan="10" class="empty-row">Loading...</td></tr>`;

  fetch(`?fetch=1&type=${type}`)
    .then(res => res.json())
    .then(data => {
      let rows = '';
      if (!data || data.length === 0) {
        rows = `<tr><td colspan="10" class="empty-row">No reservations found</td></tr>`;
      } else {
        if (type === 'wedding') {
          data.forEach(r => {
            const brideFull = [r.bride_firstname, r.bride_middlename, r.bride_lastname, r.bride_suffix].filter(Boolean).join(' ');
            const groomFull = [r.groom_firstname, r.groom_middlename, r.groom_lastname, r.groom_suffix].filter(Boolean).join(' ');
            const bestmanFull = [r.best_man_firstname, r.best_man_middlename, r.best_man_lastname, r.best_man_suffix].filter(Boolean).join(' ');
            const maidofhonorFull = [r.maid_of_honor_firstname, r.maid_of_honor_middlename, r.maid_of_honor_lastname, r.maid_of_honor_suffix].filter(Boolean).join(' ');
            rows += `<tr>
              <td>${brideFull}</td>
              <td>${groomFull}</td>
              <td>${r.phone}</td>
              <td>${r.date}</td>
              <td>${formatTime(r.time)}</td>
              <td>${bestmanFull}</td>
              <td>${maidofhonorFull}</td>
              <td>
                <button class="delete-btn" data-id="${r.id}" data-type="wedding" title="Delete"><i class='bx bx-trash'></i></button>
                <button class="edit-btn" data-id="${r.id}" data-type="wedding" title="Edit"><i class='bx bx-edit'></i></button>
              </td>
            </tr>`;
          });
        } else if (type === 'funeral') {
          data.forEach(r => {
            const deceasedFull = [r.deceased_firstname, r.deceased_middlename, r.deceased_lastname, r.deceased_suffix].filter(Boolean).join(' ');
            const contactFull = [r.contact_person_firstname, r.contact_person_middlename, r.contact_person_lastname, r.contact_person_suffix].filter(Boolean).join(' ');
            rows += `<tr>
              <td>${deceasedFull}</td>
              <td>${r.cause_of_death}</td>
              <td>${contactFull}</td>
              <td>${r.contact_person_contact_no}</td>
              <td>${r.funeral_address}</td>
              <td>${r.funeral_date}</td>
              <td>${formatTime(r.funeral_time)}</td>
              <td>${r.presider}</td>
              <td>
                <button class="delete-btn" data-id="${r.id}" data-type="funeral" title="Delete"><i class='bx bx-trash'></i></button>
                <button class="edit-btn" data-id="${r.id}" data-type="funeral" title="Edit"><i class='bx bx-edit'></i></button>
              </td>
            </tr>`;
          });
        } else { // baptism
          data.forEach(r => {
            const childFull = [r.child_firstname, r.child_middlename, r.child_lastname, r.child_suffix].filter(Boolean).join(' ');
            const motherFull = [r.mother_firstname, r.mother_middlename, r.mother_lastname, r.mother_suffix].filter(Boolean).join(' ');
            const fatherFull = [r.father_firstname, r.father_middlename, r.father_lastname, r.father_suffix].filter(Boolean).join(' ');
            rows += `<tr>
              <td>${childFull}</td>
              <td>${motherFull}</td>
              <td>${fatherFull}</td>
              <td>${r.contact}</td>
              <td>${r.date}</td>
              <td>${formatTime(r.time)}</td>
              <td>${r.status}</td>
              <td>
                <button class="accept-btn" data-id="${r.id}" data-type="baptism" title="Accept"><i class='bx bx-check'></i></button>
                <button class="cancel-btn" data-id="${r.id}" data-type="baptism" title="Cancel"><i class='bx bx-x'></i></button>
                <button class="edit-btn" data-id="${r.id}" data-type="baptism" title="Edit"><i class='bx bx-edit'></i></button>
                <button class="delete-btn" data-id="${r.id}" data-type="baptism" title="Delete"><i class='bx bx-trash'></i></button>
              </td>
            </tr>`;
          });
        }
      }

      reservationTable.querySelector('tbody').innerHTML = rows;

      // delete handlers
      document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.onclick = function() {
          if (!confirm('Are you sure you want to delete this reservation?')) return;
          fetch(`?delete=1&type=${btn.dataset.type}&id=${btn.dataset.id}`, { method: 'POST' })
            .then(res => res.text())
            .then(() => renderTable(eventTypeSelect.value));
        };
      });

      // edit handlers
      document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.onclick = function() {
          fetch(`?fetch=1&type=${btn.dataset.type}&id=${btn.dataset.id}`)
            .then(res => res.json())
            .then(data => {
              buildForm(btn.dataset.type, data[0] || {});
              modal.style.display = "block";
              formFields.dataset.editId = btn.dataset.id;
              formFields.dataset.editType = btn.dataset.type;
            });
        };
      });

      // ACCEPT
      document.querySelectorAll('.accept-btn').forEach(btn => {
        btn.onclick = function() {
          fetch(`?status=Accepted&type=${btn.dataset.type}&id=${btn.dataset.id}`, { method: 'POST' })
            .then(() => renderTable(eventTypeSelect.value));
        };
      });

      // CANCEL
      document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.onclick = function() {
          fetch(`?status=Cancelled&type=${btn.dataset.type}&id=${btn.dataset.id}`, { method: 'POST' })
            .then(() => renderTable(eventTypeSelect.value));
        };
      });

    })
    .catch(err => {
      console.error('Error fetching reservations:', err);
      reservationTable.querySelector('tbody').innerHTML = `<tr><td colspan="10" class="empty-row">Error loading</td></tr>`;
    });

  updateCalendar(type);
}

/* ---------- Calendar ---------- */
function updateCalendar(type) {
  const month = currentDate.getMonth();
  const year = currentDate.getFullYear();
  monthYear.textContent = `${currentDate.toLocaleString('default', { month: 'long' })} ${year}`;
  calendarDays.innerHTML = '';

  const daysOfWeek = ['Su','Mo','Tu','We','Th','Fr','Sa'];
  daysOfWeek.forEach(day => {
    const div = document.createElement('div');
    div.textContent = day;
    div.classList.add('calendar-day');
    calendarDays.appendChild(div);
  });

  const firstDay = new Date(year, month, 1).getDay();
  const daysInMonth = new Date(year, month + 1, 0).getDate();

  for (let i = 0; i < firstDay; i++) calendarDays.appendChild(document.createElement('div'));

  fetch(`?fetch=1&type=${type}`)
    .then(res => res.json())
    .then(data => {
      const events = {};
      let monthlyCount = 0;
      data.forEach(r => {
        // normalize date field name depending on type
        const dateStr = r.date || r.funeral_date || r.preferred_date || r.funeral_date;
        if (!dateStr) return;
        const d = new Date(dateStr);
        if (d.getMonth() === month && d.getFullYear() === year) {
          events[dateStr] = (events[dateStr] || 0) + 1;
          monthlyCount++;
        }
      });

      for (let date = 1; date <= daysInMonth; date++) {
        const div = document.createElement('div');
        div.textContent = date;
        div.classList.add('calendar-date');
        const dateKey = `${year}-${String(month+1).padStart(2,'0')}-${String(date).padStart(2,'0')}`;
        if (events[dateKey]) div.classList.add('has-event');
        div.addEventListener('click', () => {
          document.querySelectorAll('.calendar-date').forEach(d => d.classList.remove('active'));
          div.classList.add('active');
          eventSummary.textContent = `Total ${type}s on ${currentDate.toLocaleString('default', { month: 'long' })} ${date}, ${year}: ${events[dateKey] || 0}`;
        });
        calendarDays.appendChild(div);
      }
      eventSummary.textContent = `Total ${type}s on ${currentDate.toLocaleString('default', { month: 'long' })}: ${monthlyCount}`;
    })
    .catch(err => {
      console.error('Calendar fetch error:', err);
    });
}

/* ---------- Form builder (creates full name fields including cause_of_death for funeral) ---------- */
function buildForm(type,data={}){
  formFields.innerHTML="";
  const hiddenType=document.createElement('input');
  hiddenType.type="hidden"; hiddenType.name="type"; hiddenType.value=type;
  formFields.appendChild(hiddenType);

  const fields = type==='wedding'?[
    {label:"Bride First Name",name:"BrideFN"},{label:"Bride Middle Name",name:"BrideMN"},{label:"Bride Last Name",name:"BrideLN"},{label:"Bride Suffix",name:"BrideSuffix"},
    {label:"Groom First Name",name:"GroomFN"},{label:"Groom Middle Name",name:"GroomMN"},{label:"Groom Last Name",name:"GroomLN"},{label:"Groom Suffix",name:"GroomSuffix"},
    {label:"Best Man First Name",name:"Best_Man_FN"},{label:"Best Man Middle Name",name:"Best_Man_MN"},{label:"Best Man Last Name",name:"Best_Man_LN"},{label:"Best Man Suffix",name:"Best_Man_Suffix"},
    {label:"Phone Number",name:"Phone_Number"},{label:"Date",name:"Date",type:"date"},{label:"Time",name:"Time",type:"time"},{label:"Maid of Honor",name:"Maid_of_Honor"}
  ]:type==='funeral'?[
    {label:"Deceased First Name",name:"DeceasedFN"},{label:"Deceased Middle Name",name:"DeceasedMN"},{label:"Deceased Last Name",name:"DeceasedLN"},{label:"Deceased Suffix",name:"DeceasedSuffix"},
    {label:"Contact First Name",name:"ContactFN"},{label:"Contact Middle Name",name:"ContactMN"},{label:"Contact Last Name",name:"ContactLN"},{label:"Contact Suffix",name:"ContactSuffix"},
    {label:"Phone Number",name:"Phone_Number"},{label:"Cause of Death",name:"Cause_of_Death"},{label:"Address",name:"Address"},{label:"Date",name:"Date",type:"date"},{label:"Time",name:"Time",type:"time"},{label:"Presider",name:"Presider"}
  ]:[
    {label:"Child First Name",name:"ChildFN"},{label:"Child Middle Name",name:"ChildMN"},{label:"Child Last Name",name:"ChildLN"},{label:"Child Suffix",name:"ChildSuffix"},
    {label:"Mother First Name",name:"MotherFN"},{label:"Mother Middle Name",name:"MotherMN"},{label:"Mother Last Name",name:"MotherLN"},{label:"Mother Suffix",name:"MotherSuffix"},
    {label:"Father First Name",name:"FatherFN"},{label:"Father Middle Name",name:"FatherMN"},{label:"Father Last Name",name:"FatherLN"},{label:"Father Suffix",name:"FatherSuffix"},
    {label:"Contact",name:"Contact"},{label:"Date",name:"Date",type:"date"},{label:"Time",name:"Time",type:"time"}
  ];

  const mapping = type==='wedding'?{
    "BrideFN":"bride_firstname","BrideMN":"bride_middlename","BrideLN":"bride_lastname","BrideSuffix":"bride_suffix",
    "GroomFN":"groom_firstname","GroomMN":"groom_middlename","GroomLN":"groom_lastname","GroomSuffix":"groom_suffix",
    "Best_Man_FN":"best_man_firstname","Best_Man_MN":"best_man_middlename","Best_Man_LN":"best_man_lastname","Best_Man_Suffix":"best_man_suffix",            
    "Maid_Of_HonorFN":"maid_of_honor_firstname","Maid_Of_HonorMN":"maid_of_honor_middlename","Maid_Of_HonorLN":"maid_of_honor_lastname","Maid_Of_HonorSuffix":"maid_of_honor_suffix",
    "Phone_Number":"phone","Date":"date","Time":"time"
  }:type==='funeral'?{
    "DeceasedFN":"deceased_firstname","DeceasedMN":"deceased_middlename","DeceasedLN":"deceased_lastname","DeceasedSuffix":"deceased_suffix",
    "ContactFN":"contact_person_firstname","ContactMN":"contact_person_middlename","ContactLN":"contact_person_lastname","ContactSuffix":"contact_person_suffix",
    "Phone_Number":"contact_person_contact_no","Cause_of_Death":"cause_of_death","Address":"funeral_address",
    "Date":"funeral_date","Time":"funeral_time","Presider":"presider"
  }:{
    "ChildFN":"child_firstname","ChildMN":"child_middlename","ChildLN":"child_lastname","ChildSuffix":"child_suffix",
    "MotherFN":"mother_firstname","MotherMN":"mother_middlename","MotherLN":"mother_lastname","MotherSuffix":"mother_suffix",
    "FatherFN":"father_firstname","FatherMN":"father_middlename","FatherLN":"father_lastname","FatherSuffix":"father_suffix",
    "Phone_Number":"phone","Date":"date","Time":"time"
  };

  fields.forEach(field=>{
    const wrapper=document.createElement('div');
    wrapper.className="form-field";
    const label=document.createElement('label');
    label.textContent=field.label;
    wrapper.appendChild(label);

    const input=document.createElement('input');
    input.type=field.type||"text";
    input.name=field.name;
    input.placeholder=field.label;
    input.required=false;

    const dbKey = mapping[field.name];
    if(data && dbKey && data[dbKey]) input.value=data[dbKey];

    wrapper.appendChild(input);
    formFields.appendChild(wrapper);
  });

  const submitBtn=document.createElement('button');
  submitBtn.type="submit";
  submitBtn.textContent="Submit";
  formFields.appendChild(submitBtn);
}

/* ---------- Form submit via fetch ---------- */
formFields.addEventListener('submit', function(e) {
  e.preventDefault();
  const formData = new FormData(formFields);
  let url = '';
  if (formFields.dataset.editId) {
    url = `?edit=1&type=${formFields.dataset.editType}&id=${formFields.dataset.editId}`;
    formData.append('edit', '1');
  }
  fetch(url, {
    method: 'POST',
    body: formData
  })
  .then(response => {
    // response may be empty; re-render table and close modal
    alert('Reservation saved successfully!');
    modal.style.display = 'none';
    renderTable(eventTypeSelect.value);
    formFields.dataset.editId = '';
    formFields.dataset.editType = '';
  })
  .catch(error => {
    console.error('Save error:', error);
    alert('Error saving reservation.');
  });
});

/* ---------- UI events ---------- */
eventTypeSelect.addEventListener('change', () => {
  renderTable(eventTypeSelect.value);
  searchInput.value = '';
});

addReservationBtn.addEventListener("click", () => {
  buildForm(eventTypeSelect.value);
  modal.style.display = "block";
});

closeBtn.onclick = () => modal.style.display = "none";
window.onclick = function(event) { if (event.target === modal) modal.style.display = "none"; };

prevMonthBtn.addEventListener('click', () => {
  currentDate.setMonth(currentDate.getMonth() - 1);
  updateCalendar(eventTypeSelect.value);
});
nextMonthBtn.addEventListener('click', () => {
  currentDate.setMonth(currentDate.getMonth() + 1);
  updateCalendar(eventTypeSelect.value);
});

searchInput.addEventListener('input', () => {
  const filter = searchInput.value.toLowerCase();
  const rows = reservationTable.querySelectorAll('tbody tr');
  rows.forEach(row => {
    if (!row.classList.contains('empty-row')) {
      row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
    }
  });
});

/* initial render */
renderTable(eventTypeSelect.value);
</script>

</body>
</html>