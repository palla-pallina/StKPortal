<?php
$conn = new mysqli('localhost', 'root', '', 'test_tree_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_GET['delete']) && isset($_GET['type']) && isset($_GET['id'])) {
        $type = $_GET['type'];
        $id = intval($_GET['id']);
        if ($type === 'wedding') {
            $conn->query("DELETE FROM weddings WHERE id=$id");
        } elseif ($type === 'funeral') {
            $conn->query("DELETE FROM funerals WHERE id=$id");
        } elseif ($type === 'baptism') {
            $conn->query("DELETE FROM baptisms WHERE id=$id");
        }
        exit;
    }
    if (isset($_GET['edit']) && isset($_GET['type']) && isset($_GET['id'])) {
        $type = $_GET['type'];
        $id = intval($_GET['id']);
        if ($type === 'wedding') {
            $stmt = $conn->prepare("UPDATE weddings SET bride=?, groom=?, phone=?, date=?, time=?, best_man=?, maid_of_honor=? WHERE id=?");
            $stmt->bind_param("sssssssi",
                $_POST['Bride'], $_POST['Groom'], $_POST['Phone_Number'], $_POST['Date'],
                $_POST['Time'], $_POST['Best_Man'], $_POST['Maid_of_Honor'], $id
            );
            $stmt->execute();
            $stmt->close();
        } elseif ($type === 'funeral') {
            $stmt = $conn->prepare("UPDATE funerals SET name=?, cause_of_death=?, contact_person=?, phone=?, address=?, date=?, time=?, presider=? WHERE id=?");
            $stmt->bind_param("ssssssssi",
                $_POST['Name'], $_POST['Cause_of_Death'], $_POST['Contact_Person'], $_POST['Phone_Number'],
                $_POST['Address'], $_POST['Date'], $_POST['Time'], $_POST['Presider'], $id
            );
            $stmt->execute();
            $stmt->close();
        } elseif ($type === 'baptism') {
            $stmt = $conn->prepare("UPDATE baptisms SET child=?, mother=?, father=?, phone=?, date=?, time=? WHERE id=?");
            $stmt->bind_param("ssssssi",
                $_POST['Child'], $_POST['Mother'], $_POST['Father'], $_POST['Phone_Number'],
                $_POST['Date'], $_POST['Time'], $id
            );
            $stmt->execute();
            $stmt->close();
        }
        exit;
    }
    $type = $_POST['type'];

    if ($type === 'wedding') {
        $bride = $_POST['Bride'];
        $groom = $_POST['Groom'];
        $phone = $_POST['Phone_Number'];
        $date = $_POST['Date'];
        $time = $_POST['Time'];
        $best_man = $_POST['Best_Man'];
        $maid_of_honor = $_POST['Maid_of_Honor'];

        $stmt = $conn->prepare("INSERT INTO weddings (bride, groom, phone, date, time, best_man, maid_of_honor) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $bride, $groom, $phone, $date, $time, $best_man, $maid_of_honor);
        $stmt->execute();
        $stmt->close();
        $message = 'Wedding reservation saved.';
    } elseif ($type === 'funeral') {
        $name = $_POST['Name'];
        $cause = $_POST['Cause_of_Death'];
        $contact = $_POST['Contact_Person'];
        $phone = $_POST['Phone_Number'];
        $address = $_POST['Address'];
        $date = $_POST['Date'];
        $time = $_POST['Time'];
        $presider = $_POST['Presider'];

        $stmt = $conn->prepare("INSERT INTO funerals (name, cause_of_death, contact_person, phone, address, date, time, presider) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $name, $cause, $contact, $phone, $address, $date, $time, $presider);
        $stmt->execute();
        $stmt->close();
        $message = 'Funeral reservation saved.';
    } elseif ($type === 'baptism') {
        $child = $_POST['Child'];
        $mother = $_POST['Mother'];
        $father = $_POST['Father'];
        $phone = $_POST['Phone_Number'];
        $date = $_POST['Date'];
        $time = $_POST['Time'];

        $stmt = $conn->prepare("INSERT INTO baptisms (child, mother, father, phone, date, time) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $child, $mother, $father, $phone, $date, $time);
        $stmt->execute();
        $stmt->close();
        $message = 'Baptism reservation saved.';
    }
} elseif (isset($_GET['fetch']) && isset($_GET['type'])) {
    $type = $_GET['type'];
    $data = [];
    if ($type === 'wedding') {
        $result = $conn->query("SELECT * FROM weddings ORDER BY date DESC");
        while ($row = $result->fetch_assoc()) $data[] = $row;
    } elseif ($type === 'funeral') {
        $result = $conn->query("SELECT * FROM funerals ORDER BY date DESC");
        while ($row = $result->fetch_assoc()) $data[] = $row;
    } elseif ($type === 'baptism') {
        $result = $conn->query("SELECT * FROM baptisms ORDER BY date DESC");
        while ($row = $result->fetch_assoc()) $data[] = $row;
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
  <title>Reservations</title>
  <style>
    :root {
      --poppins: 'Poppins', sans-serif;
      --lato: 'Lato', sans-serif;
      --light: #F9F9F9;
      --blue: #3C91E6;
      --grey: #eee;
      --dark-grey: #AAAAAA;
      --dark: #342E37;
      --red: #DB504A;
      --green: #418033;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: var(--lato);
      background: var(--grey);
      overflow-x: hidden;
    }

    #sidebar {
      position: fixed; top: 0; left: 0;
      width: 280px; height: 100%;
      background: var(--light);
      display: flex; flex-direction: column; justify-content: space-between;
      z-index: 2000; transition: .3s ease;
    }

    #sidebar .side-menu li.active {
        background: var(--grey);
    }

    #sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 280px;
        height: 100%;
        background: var(--light);
        z-index: 2000;
        transition: .3s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 0;
    }

    #sidebar.hide {
        width: 0 !important;
        overflow: hidden;
        pointer-events: none;
    }

    #sidebar .label {
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding-top: 20px;
    }

    #sidebar .label .logo {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    #sidebar .side-menu { width: 100%; }
    #sidebar .side-menu li { height: 48px; }
    #sidebar .side-menu li a {
      display: flex; align-items: center; justify-content: center;
      height: 100%; font-size: 16px; color: var(--dark); text-decoration: none;
    }
    #sidebar .side-menu li.active {
        background: var(--grey);
    }

    #content {
        position: relative;
        width: calc(100% - 280px);
        left: 280px;
        transition: .3s ease;
        min-width: 0;
    }

    #sidebar.hide ~ #content {
        width: 100%;
        left: 0;
    }

    #content nav {
        height: 56px;
        background-color: #418033;
        padding: 0 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        grid-gap: 16px;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    #content nav .bx {
        font-size: 24px;
        color: var(--dark);
        cursor: pointer;
    }

      #content nav a {
        color: var(--dark);
    }

    #content nav .nav-link {
        font-size: 16px;
        transition: .3s ease;
    }

    #content nav #datetime {
        color: white;
        font-size: 14px;
        font-weight: 500;
        letter-spacing: 1px;
        margin-left: 16px;
        flex-grow: 1;
    }
    .content-section { padding: 20px; min-height: calc(100vh - 56px); }
    .main-content { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); }

    .header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .header-row h1 { font-family: var(--poppins); font-size: 28px; font-weight: 700; color: var(--dark); margin: 0; }

    .controls { display: flex; align-items: center; gap: 10px; margin-bottom: 0; }
    .divider { border: none; border-top: 2px solid black; margin: 12px 0 20px 0; width: 100%; }

    .table-controls { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
    .table-controls input, .table-controls select { padding: 8px; border-radius: 6px; border: 1px solid var(--dark-grey); }
    .table-controls input { width: 200px; }
    .table-controls button {
      background: var(--green); color: white; border: none; padding: 8px 12px;
      border-radius: 6px; cursor: pointer; font-size: 16px;
    }
    .table-controls button:hover { background: #2f5e22; }

    .reservation-layout { display: flex; gap: 24px; flex-wrap: wrap; }
    .table-container { flex: 2; min-width: 600px; overflow-x: auto; }

    .calendar-container {
      flex: 1; background: var(--light); border-radius: 12px;
      padding: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      text-align: center; font-family: var(--poppins);
    }

    #reservationTable { width: 100%; border-collapse: collapse; margin-top: 12px; border: 1px solid var(--grey); }
    #reservationTable th, #reservationTable td {
      padding: 10px 8px;
      border: 1px solid var(--grey);
      text-align: left;
      font-size: 15px;
      white-space: normal;
      max-width: 300px;
      overflow-wrap: break-word;
      word-break: break-word;
      transition: background 0.2s;
    }
    #reservationTable tr {
      transition: background 0.2s;
    }
    #reservationTable tr:hover {
      background: #f3f8e6;
    }
    #reservationTable th { background-color: #f1d3a8; color: black; position: sticky; top: 0; z-index: 1; }

    .empty-row { text-align: center; font-style: italic; color: var(--dark-grey); background: #f5f5f5; padding: 20px; }

    .calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-weight: 600; font-size: 18px; color: var(--dark); }
    .calendar-header button { background: none; border: none; font-size: 20px; color: var(--green); cursor: pointer; }

    .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; font-size: 14px; }
    .calendar-day { font-weight: 600; color: #c69c6d; }
    .calendar-date { padding: 6px; border-radius: 6px; cursor: pointer; }
    .calendar-date:hover { background: var(--green); color: white; }
    .calendar-date.active { background: var(--green); color: white; font-weight: bold; }
    .calendar-date.has-event { background: #e1e5d6; border: 1px solid var(--green); border-radius: 50%; }

    .calendar-total { text-align: center; font-weight: 600; color: var(--dark); margin-top: 12px; font-size: 15px; }

    .modal {
      display: none;
      position: fixed;
      z-index: 3000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.4);
    }

    .modal-content {
      background-color: #fff;
      margin: 8% auto;
      padding: 20px;
      border-radius: 12px;
      width: 400px;
      max-width: 95%;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
    }

    .modal-header h2 {
      font-size: 18px;
      margin: 0;
      font-family: var(--poppins);
    }

    .close {
      font-size: 22px;
      font-weight: bold;
      cursor: pointer;
    }

    #formFields input {
      display: block;
      margin: 6px 0;
      padding: 8px;
      width: 100%;
      border: 1px solid var(--dark-grey);
      border-radius: 6px;
    }

    #formFields button {
      margin-top: 8px;
      background: var(--green);
      color: white;
      border: none;
      padding: 8px 14px;
      border-radius: 6px;
      cursor: pointer;
    }

    #content nav .profile {
    display: flex;
    align-items: center;
    gap: 10px;
    }

    #content nav .profile .greeting {
        font-size: 14px;
        color: var(--light);
        font-weight: 500;
    }

    #content nav .profile-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #ffffff;
        font-size: 24px;
        transition: background-color 0.3s ease, transform 0.2s ease;
    }

    #content nav .profile-link:hover {
        background-color: #9fecd3;
        transform: scale(1.05);
    }
  </style>
</head>
<body>

<!-- SIDEBAR -->
<section id="sidebar" <?php if ($hideSidebar) echo 'class="hide"'; ?>>
<a href="#" class="label">
<img src="santa krus logo.png" alt="Logo" class="logo" />
</a>
<ul class="side-menu top">
<li><a href="dashboard.php"><span class="text">Dashboard</span></a></li>
<li><a href="stk/filemanager.php"><span class="text">File Manager</span></a></li>
<li><a href="stk/announcement.php"><span class="text">Announcement</span></a></li>
<li><a href="#"><span class="text">Event</span></a></li> <!-- N/A -->
<li><a href="stk/members.php"><span class="text">Members</span></a></li>
<li><a href="certificate/admin.php?page=certificate"><span class="text">Certificates</span></a></li> <!-- N/A -->
<li class="active"><a href="booking_sacraments.php"><span class="text">Reservations</span></a></li>
<li><a href="donations.php" data-section="dashboard"><span class="text">Donations</span></a></li>
<li><a href="prayer_admin.php"><span class="text">Prayer Requests</span></a></li>
</ul>
<ul class="side-menu">
<li><a href="../public/homepage.html" class="logout"><span class="text">Logout</span></a></li>
</ul>
</section>
<!-- SIDEBAR END -->

  <section id="content">
    <nav>
      <i class='bx bx-menu'></i>
      <div id="datetime"></div>
      <div class="profile">
        <span class="greeting">Admin</span>
        <a href="#" class="profile-link"><i class='bx bx-user'></i></a>
      </div>
    </nav>

    <main>
      <section id="reservations" class="content-section active">
        <div class="main-content">
          <div class="header-row">
            <h1>Reservations</h1>
            <div class="controls">
              <label for="eventType">Type:</label>
              <select id="eventType">
                <option value="wedding">Wedding</option>
                <option value="funeral">Funeral Mass</option>
                <option value="baptism">Baptism</option>
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
              <p id="eventSummary" class="calendar-total">Total weddings this month: 0</p>
            </div>
          </div>
        </div>
      </section>
    </main>
  </section>

  <div id="reservationModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Add Reservation</h2>
        <span class="close">&times;</span>
      </div>
      <form id="formFields" method="POST" action="">
      </form>
    </div>
  </div>

<script>

/*
  const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');

  allSideMenu.forEach(item => {
    const li = item.parentElement;

    item.addEventListener('click', function (e) {
      e.preventDefault();
      
      // Remove active class from all menu items
      allSideMenu.forEach(i => {
        i.parentElement.classList.remove('active');
      });
      
      // Add active class to clicked item
      li.classList.add('active');
    });
  });
*/
  
  const menuIcon = document.querySelector('.bx-menu');
  const sidebar = document.querySelector('#sidebar');
  menuIcon.addEventListener('click', () => sidebar.classList.toggle('hide'));

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
        <th>Date</th><th>Time</th><th>Action</th>
      </tr>`;
    }
    reservationTable.innerHTML = `<thead>${headers}</thead><tbody><tr><td colspan="10" class="empty-row">Loading...</td></tr>`;

    fetch(`?fetch=1&type=${type}`)
      .then(res => res.json())
      .then(data => {
        let rows = '';
        if (data.length === 0) {
          rows = `<tr><td colspan="10" class="empty-row">No reservations found</td></tr>`;
        } else {
          if (type === 'wedding') {
            data.forEach(r => {
              rows += `<tr>
                <td>${r.bride}</td><td>${r.groom}</td><td>${r.phone}</td><td>${r.date}</td>
                <td>${formatTime(r.time)}</td><td>${r.best_man}</td><td>${r.maid_of_honor}</td>
                <td>
                  <button class="delete-btn" data-id="${r.id}" data-type="wedding" title="Delete"><i class='bx bx-trash'></i></button>
                  <button class="edit-btn" data-id="${r.id}" data-type="wedding" title="Edit"><i class='bx bx-edit'></i></button>
                </td>
              </tr>`;
            });
          } else if (type === 'funeral') {
            data.forEach(r => {
              rows += `<tr>
                <td>${r.name}</td><td>${r.cause_of_death}</td><td>${r.contact_person}</td><td>${r.phone}</td>
                <td>${r.address}</td><td>${r.date}</td><td>${formatTime(r.time)}</td><td>${r.presider}</td>
                <td>
                  <button class="delete-btn" data-id="${r.id}" data-type="funeral" title="Delete"><i class='bx bx-trash'></i></button>
                  <button class="edit-btn" data-id="${r.id}" data-type="funeral" title="Edit"><i class='bx bx-edit'></i></button>
                </td>
              </tr>`;
            });
          } else {
            data.forEach(r => {
              rows += `<tr>
                <td>${r.child}</td><td>${r.mother}</td><td>${r.father}</td><td>${r.phone}</td>
                <td>${r.date}</td><td>${formatTime(r.time)}</td>
                <td>
                  <button class="delete-btn" data-id="${r.id}" data-type="baptism" title="Delete"><i class='bx bx-trash'></i></button>
                  <button class="edit-btn" data-id="${r.id}" data-type="baptism" title="Edit"><i class='bx bx-edit'></i></button>
                </td>
              </tr>`;
            });
          }
        }
        reservationTable.querySelector('tbody').innerHTML = rows;

        document.querySelectorAll('.delete-btn').forEach(btn => {
          btn.onclick = function() {
            if (confirm('Are you sure you want to delete this reservation?')) {
              fetch(`?delete=1&type=${btn.dataset.type}&id=${btn.dataset.id}`, { method: 'POST' })
                .then(res => res.text())
                .then(() => renderTable(eventTypeSelect.value));
            }
          };
        });

        document.querySelectorAll('.edit-btn').forEach(btn => {
          btn.onclick = function() {
            fetch(`?fetch=1&type=${btn.dataset.type}&id=${btn.dataset.id}`)
              .then(res => res.json())
              .then(data => {
                buildForm(btn.dataset.type, data[0]);
                modal.style.display = "block";
                formFields.dataset.editId = btn.dataset.id;
                formFields.dataset.editType = btn.dataset.type;
              });
          };
        });
      });
    updateCalendar(type);
  }

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

    for (let i = 0; i < firstDay; i++) {
      calendarDays.appendChild(document.createElement('div'));
    }

    fetch(`?fetch=1&type=${type}`)
      .then(res => res.json())
      .then(data => {
        const events = {};
        let monthlyCount = 0;
        data.forEach(r => {
          const dateStr = r.date;
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

          if (events[dateKey]) {
            div.classList.add('has-event');
          }

          div.addEventListener('click', () => {
            document.querySelectorAll('.calendar-date').forEach(d => d.classList.remove('active'));
            div.classList.add('active');
            eventSummary.textContent = `Total ${type}s on ${currentDate.toLocaleString('default', { month: 'long' })} ${date}, ${year}: ${events[dateKey] || 0}`;
          });

          calendarDays.appendChild(div);
        }

        eventSummary.textContent = `Total ${type}s on ${currentDate.toLocaleString('default', { month: 'long' })}: ${monthlyCount}`;
      });
  }

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

  
  eventTypeSelect.addEventListener('change', () => {
    renderTable(eventTypeSelect.value);
    searchInput.value = '';
  });

  addReservationBtn.addEventListener("click", () => {
    buildForm(eventTypeSelect.value);
    modal.style.display = "block";
  });

  closeBtn.onclick = function() {
    modal.style.display = "none";
  }

  window.onclick = function(event) {
    if (event.target === modal) {
      modal.style.display = "none";
    }
  }

  function buildForm(type, data = {}) {
    formFields.innerHTML = "";

    const hiddenType = document.createElement('input');
    hiddenType.type = 'hidden';
    hiddenType.name = 'type';
    hiddenType.value = type;
    formFields.appendChild(hiddenType);

    let fields = [];
    if (type === "wedding") {
      fields = ["Bride","Groom","Phone Number","Date","Time","Best Man","Maid of Honor"];
    } else if (type === "funeral") {
      fields = ["Name","Cause of Death","Contact Person","Phone Number","Address","Date","Time","Presider"];
    } else {
      fields = ["Child","Mother","Father","Phone Number","Date","Time"];
    }

    fields.forEach(field => {
      const input = document.createElement("input");
      input.type = field.includes("Date") ? "date" : field.includes("Time") ? "time" : "text";
      input.name = field;
      input.placeholder = field;
      input.required = true;
      if (data) {
        let key = field.replace(/ /g, '_').toLowerCase();
        if (type === "wedding") {
          if (field === "Bride") key = "bride";
          if (field === "Groom") key = "groom";
          if (field === "Phone Number") key = "phone";
          if (field === "Date") key = "date";
          if (field === "Time") key = "time";
          if (field === "Best Man") key = "best_man";
          if (field === "Maid of Honor") key = "maid_of_honor";
        } else if (type === "funeral") {
          if (field === "Name") key = "name";
          if (field === "Cause of Death") key = "cause_of_death";
          if (field === "Contact Person") key = "contact_person";
          if (field === "Phone Number") key = "phone";
          if (field === "Address") key = "address";
          if (field === "Date") key = "date";
          if (field === "Time") key = "time";
          if (field === "Presider") key = "presider";
        } else {
          if (field === "Child") key = "child";
          if (field === "Mother") key = "mother";
          if (field === "Father") key = "father";
          if (field === "Phone Number") key = "phone";
          if (field === "Date") key = "date";
          if (field === "Time") key = "time";
        }
        input.value = data[key] || '';
      }
      formFields.appendChild(input);
    });

    const submitBtn = document.createElement("button");
    submitBtn.type = "submit";
    submitBtn.textContent = "Save";
    formFields.appendChild(submitBtn);
  }

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
    .then(response => response.text())
    .then(data => {
      alert('Reservation saved successfully!');
      modal.style.display = 'none';
      renderTable(eventTypeSelect.value);
      formFields.dataset.editId = '';
      formFields.dataset.editType = '';
    })
    .catch(error => {
      alert('Error saving reservation.');
    });
  });

  renderTable(eventTypeSelect.value);
</script>
</body>
</html>