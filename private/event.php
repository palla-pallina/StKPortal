    <?php
    require_once "dbconfig.php";
    session_start();

    // Restrict access to admins only
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
        header("Location: ../public/login.php");
        exit();
    }
    $admin_name = !empty($_SESSION['givenname']) ? $_SESSION['givenname'] : $_SESSION['username'];

    // Fetch all events by date
    $events = $dbh->query("SELECT * FROM events ORDER BY date ASC")->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href='../stylesheets/private/navbar.css' rel='stylesheet'>
    <link rel="stylesheet" href="../stylesheets/private/dashboard.css" />
    <link rel="stylesheet" href="../stylesheets/private/events.css" />
    <link rel="stylesheet" href="../stylesheets/private/event-cal.css" />
    <title>Dashboard</title>

    </head>

    <body>

    <!-------------------------- NAVBAR -------------------------->
    <section id="sidebar" <?php if ($hideSidebar) echo 'class="hide"'; ?>>
        <a href="#" class="label">
            <img src="santa krus logo.png" alt="Logo" class="logo" />
        </a>
        <ul class="side-menu top">
                <li><a href="dashboard.php"><span class="text">Dashboard</span></a></li>
                <li><a href="stk/filemanager.php"><span class="text">File Manager</span></a></li>
                <li><a href="stk/announcement.php"><span class="text">Announcement</span></a></li>
                <li class="active"><a href="event.php"><span class="text">Event</span></a></li>
                <li><a href="stk/members.php"><span class="text">Members</span></a></li>
                <li><a href="sacraments/certificate_req/certificate_request.php"><span class="text">Certificate Requests</span></a></li>
                <li><a href="sacraments/sacrament_dashboard.php"><span class="text">Reservations</span></a></li>
                <li><a href="donations.php" data-section="dashboard"><span class="text">Donations</span></a></li>
                <li><a href="prayer_admin.php"><span class="text">Prayer Requests</span></a></li>
            </ul>
            <ul class="side-menu">
                <li><a href="logout.php" class="logout"><span class="text">LOGOUT</span></a></li>
            </ul>
    </section>
    <!------------------------- NAVABAR END ------------------------>

    <section id="content">

    <!-------------------------- SIDEBAR -------------------------->
    <nav>
    <i class='bx bx-menu'></i>
    <div id="datetime"></div>
    <ul>
        <li class="li-dropdown">
        <a href="#"><span class="greeting"><?php echo htmlspecialchars($admin_name); ?></span></a>
        <ul class="dropdown">
            <li><a href="logout.php" class="logout">Logout</a></li>
        </ul>
    </li>
    </ul>
    </nav>
    <!------------------------- SIDEBAR END ------------------------>

    <div class="main-content">

    <h1>Manage Events</h1>
    <hr class="divider">


    <script>
    const eventDates = <?= json_encode(array_column($events, 'date')); ?>;
    </script>

    <!-- ======================= INTERACTIVE CALENDAR ======================= -->
    <div class="events-wrapper container">
        <div class="calendar-card">
        <div class="cal-header">
            <button class="cal-btn" onclick="prevMonth()">&#8249;</button>
            <h2 id="cal-title"></h2>
            <button class="cal-btn" onclick="nextMonth()">&#8250;</button>
        </div>
        <div class="cal-grid">

            <!-- WEEKDAY LABELS -->
            <div class="cal-weekday">Su</div>
            <div class="cal-weekday">Mo</div>
            <div class="cal-weekday">Tu</div>
            <div class="cal-weekday">We</div>
            <div class="cal-weekday">Th</div>
            <div class="cal-weekday">Fr</div>
            <div class="cal-weekday">Sa</div>

            <!-- DAYS GRID (JS will fill this) -->
            <div id="cal-days"></div>

        </div>
    </div>
        <div class="event-details-card">
            <h3 id="event-panel-title">Select a date</h3>
            <p id="event-panel-desc">Click any highlighted day to see details here.</p>

            <div id="event-list"></div>
        </div>
    </div>


    <!-- ADD NEW EVENT -->
    <div class="add-form">
        <h2>Add New Event</h2>
        <form method="POST" action="event-actions.php">
            <input type="date" name="date" required>
            <input type="text" name="text" placeholder="Event text..." required style="width:60%">
            <button class="add-btn" name="add"><i class='bx bx-plus'></i></button>
        </form>
    </div>
    <!-- EVENTS TABLE -->
    <table class="event-table">
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Event</th>
            <th>Actions</th>
        </tr>

        <?php foreach ($events as $e): ?>
        <tr>
            <td><?= $e['id'] ?></td>
            <td><?= $e['date'] ?></td>
            <td><?= htmlspecialchars($e['text']) ?></td>
            <td class="actions">
                <button class="btn-edit" onclick="openEdit(<?= $e['id'] ?>, '<?= $e['date'] ?>', '<?= htmlspecialchars($e['text']) ?>')"> <i class="glyphicon glyphicon-pencil"></i> </button>
                <button class="btn-delete" onclick="deleteEvent(<?= $e['id'] ?>)"> <i class="glyphicon glyphicon-trash"></i> </button>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- EDIT MODAL -->
    <div class="modal-bg" id="modal">
        <div class="modal-box">
            <h2>Edit Event</h2>
            <form method="POST" action="event-actions.php">
                <input type="hidden" id="edit-id" name="id">
                <input type="date" id="edit-date" name="date" required><br><br>
                <input type="text" id="edit-text" name="text" required style="width:100%"><br><br>
                <button class="save-btn" name="edit">Save Changes</button>
            </form><br>
            <button onclick="closeModal()">Cancel</button>
        </div>
    </div>

    <script>
    function openEdit(id, date, text) {
        document.getElementById("modal").style.display = "flex";
        document.getElementById("edit-id").value = id;
        document.getElementById("edit-date").value = date;
        document.getElementById("edit-text").value = text;
    }

    function closeModal() {
        document.getElementById("modal").style.display = "none";
    }

    function deleteEvent(id) {
        if (!confirm("Delete this event?")) return;

        window.location.href = "event-actions.php?delete=" + id;
    }
    </script>




    <script>
    let currentDate = new Date();

    // DOM elements
    const calTitle = document.getElementById("cal-title");
    const calDays = document.getElementById("cal-days");
    const panelTitle = document.getElementById("event-panel-title");
    const panelDesc = document.getElementById("event-panel-desc");
    const eventList = document.getElementById("event-list");
    const eventText = document.getElementById("event-text");

    /* -------------------------
    Render Real Calendar
    -------------------------- */
    function renderCalendar() {
        calDays.innerHTML = "";

        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        // Month Name + Year
        calTitle.innerText = currentDate.toLocaleString("default", {
            month: "long",
            year: "numeric"
        });

        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const firstWeekDay = firstDay.getDay();

        // Empty placeholders
        for (let i = 0; i < firstWeekDay; i++) {
            const blank = document.createElement("div");
            blank.classList.add("cal-day", "empty");
            calDays.appendChild(blank);
        }
        
        // Actual days
        for (let day = 1; day <= lastDay.getDate(); day++) {
            const div = document.createElement("div");
            div.classList.add("cal-day");
            div.innerText = day;

            const fullDate = `${year}-${String(month+1).padStart(2,"0")}-${String(day).padStart(2,"0")}`;
            div.dataset.date = fullDate;

            // ✅ HIGHLIGHT IF THIS DATE HAS AN EVENT
            if (eventDates.includes(fullDate)) {
                div.classList.add("has-event");
            }

            div.onclick = () => selectDate(fullDate, div);
            calDays.appendChild(div);
        }

    }

    /* -------------------------
    Day Click Handler
    -------------------------- */
    function selectDate(date, element) {
        document.querySelectorAll(".cal-day").forEach(d => d.classList.remove("selected"));
        element.classList.add("selected");

        panelTitle.innerText = `Events for ${date}`;
        panelDesc.innerText = "";

        loadEvents(date);
    }

    /* -------------------------
    Load Events
    -------------------------- */
    async function loadEvents(date) {
        const res = await fetch(`events.php?date=${date}`);
        const data = await res.json();

        eventList.innerHTML = "";

        if (data.length === 0) {
            eventList.innerHTML = "<p style='color:#777;'>No events for this date.</p>";
            return;
        }

        data.forEach(ev => {
            const item = document.createElement("div");
            item.className = "event-item";
            item.innerHTML = `
                <span>${ev.text}</span>
            `
            eventList.appendChild(item);
        });
    }
    /* -------------------------
    Month Navigation
    -------------------------- */
    function prevMonth() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    }

    function nextMonth() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    }

    // Initialize Calendar
    renderCalendar();
    </script>





    <script>
    const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a');

    const menuBar = document.querySelector('#content nav .bx.bx-menu');
    const sidebar = document.getElementById('sidebar');

    menuBar.addEventListener('click', function () {
        sidebar.classList.toggle('hide');
    });

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
    </script>
    </body>
    </html>