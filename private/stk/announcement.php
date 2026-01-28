<?php
require_once 'db.php';
session_start();

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

<!-- NAVBAR + FAVICON -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="../../stylesheets/private/navbar_updated.css" />
<link rel="icon" type="image/x-icon" href="../../stylesheets/assets/favicon.ico">
<!--------------------->

<link rel='stylesheet' href='../../stylesheets/private/main_content.css' >
<link rel="stylesheet" href="../../stylesheets/private/announcement.css" />
<title>StKPortal | Post Announcement</title>

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
                <li><a href="announcement.php" class="active">Announcement</a></li>
                <li><a href="event.php">Event</a></li>
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

<?php if (isset($_SESSION['success_message'])): ?>
<div class="popup-overlay" id="successPopup">
    <div class="popup-box">
        <div class="popup-icon">✓</div>
        <h3>Success</h3>
        <p><?= $_SESSION['success_message']; ?></p>
        <button class="popup-btn" onclick="closePopup()">OK</button>
    </div>
</div>
<?php unset($_SESSION['success_message']); ?>
<?php endif; ?>
</header>
<!----------------- NAVBAR END ----------------->

<section id="content">

<div class="main-content">
<div class="page-header">
<h1>Post Announcement</h1>

</div>

<form action="save_announcement.php" method="POST" enctype="multipart/form-data">
    <div class="form-group">
    <label>Category: </label>
        <select name="ann_category" id="ann_category" required>
            <option value="" disabled selected>Select</option>
            <option value="General">General</option>
            <option value="Sunday Services">Sunday Services</option>
            <option value="Events & Activities">Events & Activities</option>
            <option value="Ministries">Ministries</option>
            <option value="Church Life">Church Life</option>
            <option value="Wedding Banns">Wedding Banns</option>
        </select>
    </div>

    <!-- Wedding Banns Fields -->
 <!-- Wedding Banns Fields -->
<div id="wedding-fields" style="display:none; border:1px solid #ccc; padding:15px; margin-top:15px; border-radius:5px;">
    <h3>Wedding Banns Details</h3>

    <div class="form-group">
        <label>Groom's Full Name:</label>
        <input type="text" name="groom_name" id="groom_name" placeholder="Groom's Name">
    </div>

    <div class="form-group">
        <label>Photo of Groom:</label>
        <input type="file" name="groom_photo" accept="image/*">
    </div>

    <div class="form-group">
        <label>Bride's Full Name:</label>
        <input type="text" name="bride_name" id="bride_name" placeholder="Bride's Name">
    </div>

    <div class="form-group">
        <label>Photo of Bride:</label>
        <input type="file" name="bride_photo" accept="image/*">
    </div>

    <div class="form-group">
        <label>Place of Marriage:</label>
        <input type="text" name="marriage_place" id="marriage_place" placeholder="Place of Marriage">
    </div>

    <div class="form-group">
        <label>Date of Marriage:</label>
        <input type="date" name="marriage_date">
    </div>
</div>

<div class="form-group">
    <label>Title:</label>
    <input type="text" name="title" placeholder="Title" required>
</div>  

<div class="form-group">
    <label>Set Date (Optional):</label>
    <input type="date" name="date">
</div>

<div class="form-group">
    <label>Announcement:</label>
    <textarea name="content" id="announcement" rows="5" placeholder="Announcement" required></textarea>
</div>

<div class="form-group">
    <label>Upload Image (Optional):</label>
    <input type="file" name="image" accept="image/*" id="image-input">
</div>

<div class="actions">
    <button type="submit" class="btn btn-primary">Save Announcement</button> 
    <a href="all_announcements.php" class="btn btn-secondary">View All Announcements</a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const categorySelect = document.getElementById('ann_category');
    const contentTextarea = document.getElementById('announcement');
    const bannsFields = document.getElementById('wedding-fields');

    const weddingBannsTemplate = `Everyone,

We are pleased to announce the upcoming marriage of [Name] and [Name].

Those who know of any canonical impediment to this marriage are kindly requested
to inform the parish office.

Please keep them in your prayers.

Blessings,
Sta. Krus Parish`;

    let templateAutoFilled = false;

    categorySelect.addEventListener('change', function() {

        if (this.value === 'Wedding Banns') {
            bannsFields.style.display = 'block';

            if (contentTextarea.value.trim() === '') {
                contentTextarea.value = weddingBannsTemplate;
                templateAutoFilled = true;
            }

        } else {
            bannsFields.style.display = 'none';

            if (templateAutoFilled) {
                contentTextarea.value = '';
                templateAutoFilled = false;
            }
        }
    });

});
</script>
<!-- AUTO TEMPLATE SCRIPT -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    const category = document.getElementById("ann_category");
    const weddingFields = document.getElementById("wedding-fields");

    const groom = document.getElementById("groom_name");
    const bride = document.getElementById("bride_name");
    const place = document.getElementById("marriage_place");
    const announcement = document.getElementById("announcement");

    let isWedding = false;

    function updateTemplate() {
        if (!isWedding) return;

        const g = groom.value || "__________";
        const b = bride.value || "__________";
        const p = place.value || "__________";

        announcement.value =
`We joyfully announce the forthcoming marriage of ${g} and ${b},
to be celebrated at ${p}.

Those who know of any canonical impediment to this marriage are kindly requested
to inform the parish office.

Let us keep the couple in our prayers.

— Sta. Krus Parish`;
    }

    category.addEventListener("change", function () {
        if (this.value === "Wedding Banns") {
            weddingFields.style.display = "block";
            isWedding = true;
            updateTemplate();
        } else {
            weddingFields.style.display = "none";
            isWedding = false;
        }
    });

    [groom, bride, place].forEach(el => {
        el.addEventListener("input", updateTemplate);
    });

});
</script>
<script>
const categorySelect = document.getElementById('ann_category');
const weddingFields = document.getElementById('wedding-fields');

categorySelect.addEventListener('change', function() {
    if (this.value === 'Wedding Banns') {
        weddingFields.style.display = 'block';

        // Make Wedding Banns fields required
        weddingFields.querySelectorAll('input').forEach(input => {
            input.required = true;
        });

        // Optional: remove required from general date if you want
        document.getElementById('general-date').required = false;

    } else {
        weddingFields.style.display = 'none';
        // Remove required from Wedding Banns fields
        weddingFields.querySelectorAll('input').forEach(input => {
            input.required = false;
        });

        // Restore general date required if needed
        document.getElementById('general-date').required = false;
    }
});
</script>


<script>
/* IMAGE PREVIEW + REMOVE BUTTON + ORIENTATION */
const imageInput = document.getElementById('image-input');
const previewContainer = document.getElementById('image-preview');
const removeBtn = document.getElementById('remove-image-btn');

imageInput.addEventListener('change', function () {
    previewContainer.classList.remove("portrait");
    previewContainer.innerHTML = "";
    previewContainer.appendChild(removeBtn);

    const file = this.files[0];
    if (!file) return;

    const img = document.createElement('img');
    img.src = URL.createObjectURL(file);

    img.onload = function () {
        if (img.naturalHeight > img.naturalWidth) {
            previewContainer.classList.add("portrait");
        }
        previewContainer.appendChild(img);
        removeBtn.style.display = "block";
    };
});

// Remove image
removeBtn.addEventListener("click", function () {
    imageInput.value = "";
    previewContainer.innerHTML = "";
    previewContainer.appendChild(removeBtn);
    removeBtn.style.display = "none";
});
</script>
<script>
function closePopup() {
    const popup = document.getElementById('successPopup');
    if (popup) popup.style.display = 'none';
}

// auto close after 3 seconds
setTimeout(closePopup, 3000);
</script>

</body>
</html>
