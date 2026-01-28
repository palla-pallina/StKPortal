<?php
require_once __DIR__ . '/../../snippets/connection.php';
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
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

$current_embed = null;
$current_src = '';
$result = $conn->query("SELECT embed FROM live_streams ORDER BY created_at DESC LIMIT 1");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $current_embed = $row['embed'];
    if (preg_match('/src="([^"]+)"/i', $current_embed, $matches)) {
        $current_src = $matches[1];
    } else {
        $current_src = $current_embed;
    }
}

$conn->close();
?>

<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />
	<link href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">

	<link href="../../stylesheets/private/navbar.css" rel="stylesheet">
	<link rel="stylesheet" href="live_mass.css" />

	<!-- NAVBAR, MAIN CONTENT, FAVICON -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link rel="stylesheet" href="../../stylesheets/private/navbar_updated.css" />
	<link rel='stylesheet' href='../../stylesheets/private/main_content.css'>
	<link rel="icon" type="image/x-icon" href="../../stylesheets/assets/favicon.ico">
	<!--------------------->
	<title>StKPortal | Update Live Mass</title>
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
        <li><a href="../stk/members.php">Members</a></li>

        <li class="li-dropdown">
            <a href="#" class="active">
                Publish <i class="fa-solid fa-angle-down"></i>
            </a>
            <ul class="dropdown">
                <li><a href="../stk/announcement.php">Announcement</a></li>
                <li><a href="../stk/event.php">Event</a></li>
                <li><a href="#" class="active">Mass Livestream</a></li>
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

<section id="content">
	<div class="main-content">
		<div class="live-mass-container">
			<h1>Live Mass</h1>

			<hr class="divider">

			<div class="recent-header">
				<h3>Recent live stream</h3>
				<span class="tutorial-icon">?</span>
			</div>

			<div id="tutorialModal" class="modal">
				<span class="close">&times;</span>
				<div class="modal-content">
					<img class="slide" src="tut1.png" alt="Slide 1">
					<img class="slide" src="tut2.png" alt="Slide 2">
					<img class="slide" src="tut3.png" alt="Slide 3">
					<img class="slide" src="tut4.png" alt="Slide 4">

					<a class="prev">&#10094;</a>
					<a class="next">&#10095;</a>
				</div>
			</div>

			<div class="preview-box">
				<?php if ($current_src): ?>
					<iframe
						src="<?php echo htmlspecialchars($current_src); ?>"
						width="100%"
						height="100%"
						style="border-radius: 25px;">
					</iframe>

					<div class="actions">
						<button
							type="button"
							onclick="editStream('<?php echo addslashes($current_src); ?>')">
							Edit
						</button>
						<button
							type="button"
							onclick="deleteStream()">
							Delete
						</button>
					</div>
				<?php else: ?>
					<div
						style="display: flex; align-items: center; justify-content: center; height: 100%; color: #666;">
						No live stream available.
					</div>
				<?php endif; ?>
			</div>

			<form method="POST" action="live_stream.php">
				<div class="link-wrapper">
					<?php if (isset($_GET['success'])): ?>
						<div class="success-notification">
							Link uploaded successfully!
						</div>
					<?php endif; ?>

					<label for="live_embed">Paste a link:</label>

					<textarea name="live_embed" id="live_embed"></textarea>

					<div class="button-row">
						<button type="submit" id="uploadBtn">Upload</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</section>


<script>
const modal = document.getElementById("tutorialModal");
const slides = modal.getElementsByClassName("slide");
const tutorialIcon = document.querySelector(".tutorial-icon");
const closeBtn = modal.querySelector(".close");
let slideIndex = 0;

tutorialIcon.addEventListener("click", () => {
  modal.style.display = "block";
  showSlide(slideIndex);
});

closeBtn.addEventListener("click", () => {
  modal.style.display = "none";
});

modal.querySelector(".prev").addEventListener("click", () => showSlide(slideIndex - 1));
modal.querySelector(".next").addEventListener("click", () => showSlide(slideIndex + 1));

function showSlide(n) {
  if (n >= slides.length) slideIndex = 0;
  else if (n < 0) slideIndex = slides.length - 1;
  else slideIndex = n;

  for (let i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
  }
  slides[slideIndex].style.display = "block";
}

window.addEventListener("click", (e) => {
  if (e.target === modal) modal.style.display = "none";
});

const input = document.getElementById('live_embed');
const uploadBtn = document.getElementById('uploadBtn');

input.addEventListener('input', () => {
	if (input.value.trim() !== '') {
		uploadBtn.style.display = 'block';
	} else {
		uploadBtn.style.display = 'none';
	}
});

function editStream(embed) {
	document.getElementById('live_embed').value = embed;
}

function deleteStream() {
	if (confirm('Are you sure you want to delete the current live stream?')) {
		window.location.href = 'live_stream.php?delete=1';
	}
}

const successNotif = document.querySelector('.success-notification');
if (successNotif) {
	setTimeout(() => {
		successNotif.style.display = 'none';
		const url = new URL(window.location);
		url.searchParams.delete('success');
		window.history.replaceState(null, null, url);
	}, 3000);
}
</script>

</body>
</html>
