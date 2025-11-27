<?php
// Database connection
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

$user_name = $_SESSION['username'] ?? null; 
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Santa Krus Parish</title>
    <link href="https://fonts.googleapis.com/css2?family=Lora&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../stylesheets/public/prayer-page.css" />
</head>
<body>

  <!-- NAVBAR -->
  <header>
    <nav class="navbar">
      <img src="../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" />
      <ul class="nav-links">
        <li><a href="homepage.php">Home</a></li>
        <li><a href="history.php">History</a></li>
        <li><a href="events.php">Events</a></li> 
        <li><a href="donation.php">Donate</a></li>
        <li><a href="sacraments.php">Sacraments</a></li>
        <li><a href="prayer-page.php" class="active">Prayer Request</a></li>
        <li><a href="contact.php">Contact</a></li>
          <?php
          session_start();

          if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
              // USER NOT LOGGED IN → Show Login button
              echo '<li><a href="login.php" class="login-btn">Login</a></li>';
          } else {
              // USER LOGGED IN → Show Username + Dropdown
              ?>
            <p style="color:white;">|</p>
              <li class="logout-dropdown">
                  <a href="#"><?= htmlspecialchars($user_name) ?></a>
                  <ul class="dropdown">
                      <li><a href="logout.php">Logout</a></li>
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
  <!-- NAVBAR END-->

<section class="title-section">
    <div class="title-text">
        <h1>SANTA KRUS PARISH</h1>
        <h2>DIOCESE OF NOVALICHES</h2>
    </div>
</section>

<div class="banner-image">
    <img src="banner.png" alt="Church Interior" />
</div>

<section class="prayer-section">
    <h1 class="prayer-title">Live Prayer Request</h1>
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

<div class="subnav-bar"> <h2>Prayer request</h2> </div>
<section class="prayer-form-section">
    <div class="form-header-bar">
    <h2>Submit a Prayer Request</h2>
</div>
    <div class="form-subtext">
        <p>Send your prayer or petition on the church</p>
    </div>
    <form class="prayer-form" method="post" action="prayer_handle.php">
        <label for="name">Your name:</label>
        <input type="text" id="name" name="name" placeholder="Enter your name" />

        <label for="email">Email address:</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" />

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
            img.src = 'santaKogo.png';
            img.alt = 'Site logo';
            avatar.appendChild(img);
        } else {
            avatar.textContent = displayName ? displayName.charAt(0).toUpperCase() : 'A';
        }

        const nameEl = document.createElement('div');
        nameEl.className = 'card-name';
        nameEl.textContent = displayName;
        header.appendChild(avatar);
        header.appendChild(nameEl);

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

    fetch('get_public_requests.php')
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

  </main>
   <footer class="site-footer-simple">
    <p>&copy; 2025 Santa Krus Parish, Bagumbong Caloocan. All Rights Reserved.</p>
  </footer>

</body>
</html>
