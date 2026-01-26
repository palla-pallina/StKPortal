    <?php
    require_once 'dbconfig.php';
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "test_tree_db";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    session_start();

    $user_id = $_SESSION['user_id'];

    // Restrict access to admins only
    if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
        header("Location: ../../public/login.php");
        exit();
    }
    $admin_name = !empty($_SESSION['givenname']) ? $_SESSION['givenname'] : $_SESSION['username'];

    /*--------- NOTIFICATION HANDLING START
    - for notification icon at navbar
    - retrieves all notifications belonging to logged in user */
    $stmt = $conn->prepare("SELECT * FROM admin_notifications ORDER BY created_at DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $unreadCount = 0;
    if (isset($_SESSION['user_id'])) {
        $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM admin_notifications WHERE status = 'Unread'");
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $unreadCount = $result['cnt'] ?? 0;
        $stmt->close();
    }
    /*--------- NOTIFICATION HANDLING END */

    // Fetch all events by date
    //$events = $dbh->query("SELECT * FROM events ORDER BY date ASC")->fetchAll(PDO::FETCH_ASSOC);

    // Pagination settings
    $limit = 5; // events per page
    $page  = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $offset = ($page - 1) * $limit;

    // Get total number of events
    $totalEvents = $dbh->query("SELECT COUNT(*) FROM events")->fetchColumn();
    $totalPages = ceil($totalEvents / $limit);

    // Fetch events for current page
    $stmt = $dbh->prepare("SELECT * FROM events ORDER BY date ASC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <link rel="stylesheet" href="../../stylesheets/private/dashboard.css" />
    <link rel="stylesheet" href="../../stylesheets/private/events.css" />
    <link rel="stylesheet" href="../../stylesheets/private/event-cal.css" />

    <!-- NAVBAR, MAIN CONTENT, FAVICON -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../stylesheets/private/navbar_updated.css" />
    <link rel='stylesheet' href='../../stylesheets/private/main_content.css'>
    <link rel="icon" type="image/x-icon" href="../../stylesheets/assets/favicon.ico">
    <!--------------------->
    <title>Dashboard</title>

    </head>

    <body>


    <!-------------------- NAVBAR -------------------->
    <header class="custom-header">
    <nav class="navbar custom-navbar">

        <!-- LEFT: Logo -->
        <div class="nav-left">
            <a href="dashboard.php">
                <img src="../santa krus logo.png"
                    alt="Parish Logo"
                    class="logo">
            </a>
        </div>

        <!-- CENTER: Navigation links -->
        <ul class="nav-links nav-center">
            <li><a href="../dashboard.php">Home</a></li>
            <li><a href="members.php">Members</a></li>

            <li class="li-dropdown">
                <a href="#" class="active">
                    Publish <i class="fa-solid fa-angle-down"></i>
                </a>
                <ul class="dropdown">
                    <li><a href="announcement.php">Announcement</a></li>
                    <li><a href="event.php" class="active">Event</a></li>
                    <li><a href="../livemass/live_mass.php">Mass Livestream</a></li>
                    <li><a href="../mass/mass_schedule.php">Mass Schedule</a></li>
                    <li><a href="../prayer_admin.php">Prayer Requests</a></li>
                </ul>
            </li>

            <li><a href="../sacraments/sacrament_dashboard.php">Sacrament Reservations</a></li>
            <li><a href="../sacraments/certificate_req/certificate_request.php">Certificate Requests</a></li>
            <li><a href="../donations.php">Donations</a></li>
        </ul>

        <!-- RIGHT: Notifications + User -->
        <div class="nav-right">
            <ul class="nav-links">
                <li class="nav-notification">
                    <a href="../notification/admin_notif.php">
                        <span class="bell">&#128276;</span>

                        <?php if (!empty($unreadCount) && $unreadCount > 0): ?>
                            <span class="badge"><?= $unreadCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>

                <li class="divider">|</li>

                <li class="li-dropdown">
                    <a href="#" style="margin-right: -10px;"><?= htmlspecialchars($admin_name) ?></a>
                    <ul class="dropdown">
                        <li><a href="../logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>

    </nav>
    </header>
    <!----------------- NAVBAR END ----------------->

    <div class="page-header">
    <div class="page-header-left">
        <h1>Church Event Calendar</h1>
    </div> 
    <div class="page-header-right">
    </div>
    </div>
    <hr class="divider">

    <script>
    const eventDates = <?= json_encode(array_map(fn($e) => [
        'date' => $e['date'],
        'repeat' => $e['repeat_type'] ?? 'none',
        'text' => htmlspecialchars($e['text'])
    ], $events)); ?>;
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

    <section id="content">
    <div class="main-content">
    
    <!-- ADD NEW EVENT -->
    <div class="add-form">
        <h1>Add New Event</h1>
        <hr class="divider">
        <form method="POST" action="event-actions.php" class="add-event">
            <input type="date" name="date" required>
            <input type="text" name="text" placeholder="Event Title" required style="width:60%">
            <button class="add-btn" name="add"><i class='bx bx-plus'></i></button>
        </form>
    </div>

    
    <!-- EVENTS TABLE -->
    <table class="event-table">
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Event Title</th>
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

        <!-- PAGINATION -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination-container">
            <ul class="pagination">
                <!-- Previous -->
                <li class="<?= $page == 1 ? 'disabled' : '' ?>">
                    <a href="<?= $page > 1 ? '?page=' . ($page - 1) : '#' ?>">&laquo; Prev</a>
                </li>

                <!-- Page Numbers -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="<?= $i === $page ? 'active' : '' ?>">
                        <a href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Next -->
                <li class="<?= $page == $totalPages ? 'disabled' : '' ?>">
                    <a href="<?= $page < $totalPages ? '?page=' . ($page + 1) : '#' ?>">Next &raquo;</a>
                </li>
            </ul>
        </div>
        <?php endif; ?>


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

            // ✅ Highlight if this date has an event (including repeats)
            const hasEvent = eventDates.some(ev => {
                const evDate = new Date(ev.date);
                const current = new Date(fullDate);

                if (ev.repeat === 'none') {
                    return ev.date === fullDate;
                } else if (ev.repeat === 'week') {
                    const diffDays = Math.floor((current - evDate) / (1000*60*60*24));
                    return diffDays >= 0 && diffDays % 7 === 0;
                } else if (ev.repeat === 'month') {
                    return current.getDate() === evDate.getDate() && current >= evDate;
                } else if (ev.repeat === 'year') {
                    return current.getDate() === evDate.getDate() && current.getMonth() === evDate.getMonth() && current >= evDate;
                }
            });

            if (hasEvent) div.classList.add("has-event");

            div.onclick = () => selectDate(fullDate, div);
            calDays.appendChild(div);
        }
    }

    /* -------------------------
    Day Click Handler
    -------------------------- */
    function selectDate(date, element) {
        // Remove previous selection
        document.querySelectorAll(".cal-day").forEach(d => d.classList.remove("selected"));
        element.classList.add("selected");

        // Format date as text
        const [year, month, day] = date.split("-");
        const formattedDate = new Date(year, month - 1, day).toLocaleDateString("en-US", {
            year: "numeric",
            month: "long",
            day: "numeric"
        });

        panelTitle.innerText = `Events for ${formattedDate}`;
        panelDesc.innerText = "";

        loadEvents(date);
    }


    /* -------------------------
    Load Events
    -------------------------- */

    async function loadEvents(date) {
        const current = new Date(date);

        const filteredEvents = eventDates.filter(ev => {
            const evDate = new Date(ev.date);
            if (ev.repeat === 'none') return ev.date === date;
            else if (ev.repeat === 'week') {
                const diffDays = Math.floor((current - evDate) / (1000*60*60*24));
                return diffDays >= 0 && diffDays % 7 === 0;
            } else if (ev.repeat === 'month') {
                return current.getDate() === evDate.getDate() && current >= evDate;
            } else if (ev.repeat === 'year') {
                return current.getDate() === evDate.getDate() && current.getMonth() === evDate.getMonth() && current >= evDate;
            }
        });

        eventList.innerHTML = "";
        if (filteredEvents.length === 0) {
            eventList.innerHTML = "<p style='color:#777;'>No events for this date.</p>";
            return;
        }

        filteredEvents.forEach(ev => {
            const item = document.createElement("div");
            item.className = "event-item";
            item.innerHTML = `<span>${ev.text}</span>`;
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

    // Automatically select today's date
    const today = new Date();
    const todayStr = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,"0")}-${String(today.getDate()).padStart(2,"0")}`;
    const todayElem = document.querySelector(`.cal-day[data-date='${todayStr}']`);
    if (todayElem) {
        selectDate(todayStr, todayElem);
    }

    </script>

    </body>
    </html>