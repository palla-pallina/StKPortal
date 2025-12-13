<?php
require_once 'connection.php';
session_start();

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['givenname'] ?? null; 

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
    SELECT givenname, lastname, email
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
    <link rel="stylesheet" href="../stylesheets/public/user_footer_navbar.css" />    
    <link rel="stylesheet" href="../stylesheets/public/prayer-page.css" />
</head>
<body>

<!-------------------- NAVBAR -------------------->
<header>
    <nav class="navbar">
        <ul class="nav-links">
            <li><a href="#"><img src="../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" /></a></li>
            <li><a href="homepage.php">Home</a></li>
            <li><a href="history.php">History</a></li>
            <li><a href="events.php">News & Events</a></li>
            <li><a href="sacraments.php">Sacraments</a></li>
            <li><a href="donation.php">Donate</a></li>
            <li><a href="prayer_page.php" class="active">Prayer Request</a></li>
            <li><a href="contact.php">Contact</a></li>

            <?php
            session_start();

            if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
                // USER NOT LOGGED IN → Show Login button
                echo '<li><a href="login.php" id="login-btn" style="color:white;"><i class="fa-regular fa-user"></i>   Log In</a></li>';
            } else {
                // USER LOGGED IN → Show Username + Dropdown
                ?>
            <p style="color:white; padding-left: 17px; padding-right: 17px;">|</p>
                <li class="li-dropdown">
                    <a href="#"><?= htmlspecialchars($user_name) ?></a>
                    <ul class="dropdown">
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </li>

                <li class="nav-notification">
                    <a href="members/notifications.php">
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


<div class="hero-image" role="img" aria-label="Interior of church">
  <img src="../stylesheets/assets/bannernight.jpg" alt="Church Exterior" />
</div>

<section class="prayer-section">
    <h1 class="prayer-title">Prayer Request</h1>
    <p class="prayer-quote">
        "Do not be anxious about anything, but in every situation, by prayer and petition, with thanksgiving, present your requests to God."<br>
        <span class="verse">– Philippians 4:6 (NIV)</span>
    </p>

        <div class="requests-wrapper">
        <div id="requestsGrid" class="requests-grid">
        </div>

        <div id="pageControls" class="page-controls" aria-hidden="true">
            <button id="prevBtn" class="page-btn" aria-label="Previous">Previous</button>
            <button id="nextBtn" class="page-btn" aria-label="Next">Next</button>
        </div>
    </div>
</section>

<section class="prayer-header">
    <h2>Prayer Request</h2>
</section>
<section class="prayer-form-section">
    <div class="form-header-bar">
    <h2 class="prayer-form-header">Submit a Prayer Request</h2>
</div>
    <div class="form-subtext">
        Send your prayer request.
        <span class="tooltip-icon">ⓘ
        <span class="tooltip-text" id="dateTooltip">
            Your prayer request will be displayed publicly unless specified.
        </span>
        </span>
    </div>
    <form class="prayer-form" method="post" action="handle/prayer_handle.php">
        <label for="name">Your name:</label>
        <input type="text" id="name" name="name"/>

        <label for="email">Email address:</label>
        <input type="email" id="email" name="email"/>

        <label for="intention">Intention:</label>
        <select id="intention" name="intention">
            <option value="">Select an intention</option>
            <option value="thanksgiving">Thanksgiving</option>
            <option value="special">Special Intention</option>
            <option value="soul">Soul</option>
        </select>

        <label for="request">Prayer request:</label>
        <textarea id="request" name="request" rows="5" placeholder=""></textarea>

        <div class="privacy-options">
            <div class="share-control">
                <label class="switch">
                    <input type="checkbox" id="sharePublic" name="sharePublic" />
                    <span class="slider"></span>
                </label>
                <label for="sharePublic" class="switch-label">Share your publicly</label>
            </div>

            <label class="anonymous">
                <input type="checkbox" id="anonymous" name="anonymous" />
                <span class="anon-box" aria-hidden="true"></span>
                <span class="anon-label">Anonymous</span>
            </label>
        </div>

        <button type="submit" class="submit-btn">SUBMIT</button>
    </form>
</section>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const nameInput = document.getElementById("name");
    const emailInput = document.getElementById("email");
    const anonCheckbox = document.getElementById("anonymous");

    // Store original name so it can be restored
    let originalName = nameInput.value;

    function toggleAnonymous() {
        if (anonCheckbox.checked) {
            originalName = nameInput.value; // save current value
            nameInput.value = "Anonymous";
            nameInput.disabled = true;
            emailInput.value = " ";
        } else {
            nameInput.disabled = false;
            nameInput.value = originalName;
        }
    }

    // Run when checkbox changes
    anonCheckbox.addEventListener("change", toggleAnonymous);

    // Run once on page load (in case checkbox is pre-checked)
    toggleAnonymous();
});
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const name = "<?php echo ($_SESSION['givenname'] ?? '') . ' ' . ($_SESSION['lastname'] ?? ''); ?>";
    const email = "<?php echo $_SESSION['email'] ?? ''; ?>";

    document.getElementById('name').value = name;
    document.getElementById('email').value = email;
});
</script>

<script>
(function(){
    const params = new URLSearchParams(window.location.search);
    const status = params.get('status');
    const msg = params.get('msg');
    if(!status) return;
    const container = document.querySelector('.prayer-form-section');
    if(!container) return;
    const div = document.createElement('div');
    div.id = 'formMessage';
    div.style.margin = '1rem auto';
    div.style.maxWidth = '700px';
    div.style.padding = '12px 20px';
    div.style.borderRadius = '8px';
    div.style.fontWeight = '600';
    if(status === 'success'){
        div.style.background = '#d4edda';
        div.style.color = '#155724';
        div.textContent = 'Prayer request submitted. Thank you.';
    } else {
        div.style.background = '#f8d7da';
        div.style.color = '#721c24';
        div.textContent = 'There was an error: ' + (msg ? decodeURIComponent(msg) : 'Please try again.');
    }
    container.parentNode.insertBefore(div, container);
    if(window.history && window.history.replaceState){
        const url = window.location.pathname;
        window.history.replaceState({}, document.title, url);
    }
})();
</script>

<script>
(function(){
    const grid = document.getElementById('requestsGrid');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const pageControls = document.getElementById('pageControls');
    const pageSize = 4;
    let requests = [];
    let page = 0;

    function formatDate(dt){
        try{
            const d = new Date(dt);
            const opts = { year: 'numeric', month: 'short', day: 'numeric' };
            return d.toLocaleDateString(undefined, opts);
        } catch(e){ return dt; }
    }

    function renderCard(r){
        const card = document.createElement('div');
        card.className = 'request-card';

        const header = document.createElement('div');
        header.className = 'card-header';

        const avatar = document.createElement('div');
        avatar.className = 'avatar';

        const isAnon = (r.anonymous && parseInt(r.anonymous) === 1) || !r.name;
        const displayName = isAnon ? 'Anonymous' : (r.name || 'Anonymous');

        if(isAnon){
            const img = document.createElement('img');
            img.src = '../stylesheets/assets/stklogo.png';
            avatar.appendChild(img);
        } else {
            avatar.textContent = displayName ? displayName.charAt(0).toUpperCase() : 'A';
        }

        const nameEl = document.createElement('div');
        nameEl.className = 'card-name';
        nameEl.textContent = displayName;

        // ✅ CREATED DATE (RIGHT SIDE)
        const dateEl = document.createElement('div');
        dateEl.className = 'card-date';
        dateEl.textContent = r.created_at ? formatDate(r.created_at) : '';

        header.appendChild(avatar);
        
        const nameWrap = document.createElement('div');
        nameWrap.className = 'card-name-wrap';
        nameWrap.appendChild(nameEl);

        header.appendChild(nameWrap);
        header.appendChild(dateEl);

        const body = document.createElement('p');
        body.className = 'card-body';
        body.textContent = r.request_text || '';

        const footer = document.createElement('div');
        footer.className = 'card-footer';

        const intent = document.createElement('div');
        intent.className = 'card-intent';

        const intentMap = {
            'thanksgiving': 'Thanksgiving',
            'special': 'Special Intention',
            'soul': 'Soul'
        };
            
        const intentKey = r.intention ? String(r.intention).toLowerCase() : '';
        const mappedIntent = intentKey && intentMap[intentKey] ? intentMap[intentKey] : '';

        if (mappedIntent) {
            intent.textContent = mappedIntent;
            footer.appendChild(intent);
        }

        card.appendChild(header);
        card.appendChild(body);
        card.appendChild(footer);

        return card;
    }


    function renderPage(){
        grid.innerHTML = '';
        const start = page * pageSize;
        const slice = requests.slice(start, start + pageSize);
        if(slice.length === 0){
            grid.innerHTML = '<p style="grid-column:1/-1;text-align:center;color:#666;">No public prayer requests available.</p>';
        } else {
            slice.forEach(r => grid.appendChild(renderCard(r)));
        }

        const pageCount = Math.ceil(requests.length / pageSize);
        if(pageCount > 1){
            pageControls.setAttribute('aria-hidden','false');
            prevBtn.disabled = page === 0;
            nextBtn.disabled = page >= pageCount - 1;
        } else {
            pageControls.setAttribute('aria-hidden','true');
        }
    }

    prevBtn.addEventListener('click', function(){
        if(page > 0){ page--; renderPage(); }
    });
    nextBtn.addEventListener('click', function(){
        const pageCount = Math.ceil(requests.length / pageSize);
        if(page < pageCount - 1){ page++; renderPage(); }
    });

    fetch('handle/get_public_requests.php')
        .then(r => r.json())
        .then(data => {
            if(Array.isArray(data)){
                requests = data;
                page = 0;
                renderPage();
            }
        }).catch(err => {
            console.error('Failed to fetch requests', err);
            grid.innerHTML = '<p style="grid-column:1/-1;text-align:center;color:#666;">Failed to load public requests.</p>';
        });

})();
</script>

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
