<?php
// Fetch public prayer requests to display in the live requests area
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'prayer_db';
$requests = [];
try {
    $conn = new mysqli($host, $user, $pass, $db);
    if (! $conn->connect_error) {
        $q = "SELECT id, name, request_text, shared_public, anonymous, created_at, intention FROM prayer_requests WHERE shared_public=1 ORDER BY created_at DESC LIMIT 50";
        $res = $conn->query($q);
        if ($res) {
            $requests = $res->fetch_all(MYSQLI_ASSOC);
        }
    }
} catch (Exception $e) {
    $requests = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Santa Krus Parish</title>
    <link rel="stylesheet" href="prayer-page.css" />
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@700&display=swap" rel="stylesheet" />
    <style>
      /* Small adjustments for cards if CSS missing specifics */
      .requests-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; }
      .request-card { background: #fff; border-radius: 8px; padding: 14px; box-shadow: 0 2px 6px rgba(0,0,0,0.06); min-height:120px; display:flex; flex-direction:column; }
      .request-card .card-header { font-weight:700; margin-bottom:6px; display:flex; justify-content:space-between; align-items:center; gap:8px; }
      .request-card .card-body { flex:1; color:#333; margin-bottom:8px; white-space:pre-wrap; }
      .request-card .card-footer { font-size:12px; color:#666; text-align:right; }
      .no-requests { padding:20px; text-align:center; color:#666; }
    </style>
</head>
<body>

<header>
    <nav class="navbar">
        <img src="santaKogo.png" alt="Parish Logo" class="logo" />
        <ul class="nav-links">
            <li><a href="homepage.html" class="active">Home</a></li>
            <li><a href="history.html">History</a></li>
            <li><a href="events.html">Events</a></li>
            <li><a href="donation.html">Give</a></li>
            <li><a href="baptism.php">Sacraments</a></li>
            <li><a href="pray.html">Pray</a></li>
            <li><a href="contact.html">Contact</a></li>
            <li><a href="login.php" class="login-btn">Login</a></li>
        </ul>
    </nav>
</header>

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
        <div class="carousel">
            <div class="carousel-track">
                <?php if (!empty($requests)):
                    // split into pages of 4
                    $pages = array_chunk($requests, 4);
                    foreach ($pages as $pIndex => $page): ?>
                    <div class="page" data-page="<?php echo $pIndex; ?>">
                        <div class="page-grid">
                            <?php foreach ($page as $r):
                                $isAnon = isset($r['anonymous']) && intval($r['anonymous']) === 1;
                                $name = $isAnon ? 'Anonymous' : (isset($r['name']) && $r['name'] !== '' ? $r['name'] : '');
                                $text = isset($r['request_text']) ? $r['request_text'] : '';
                                $created = isset($r['created_at']) ? $r['created_at'] : '';
                                $intention = isset($r['intention']) && $r['intention'] !== '' ? $r['intention'] : '';
                                // Prefer new keys 'special' and 'soul'; keep legacy keys for existing rows
                                $intentMap = [
                                    'thanksgiving' => 'Thanksgiving',
                                    'special' => 'Special Intention',
                                    'guidance' => 'Special Intention', // legacy
                                    'soul' => 'Soul',
                                    'peace' => 'Soul' // legacy
                                ];
                                $intentLabel = $intention && isset($intentMap[$intention]) ? $intentMap[$intention] : $intention;
                            ?>
                            <div class="request-card" aria-live="polite">
                                <div class="card-header">
                                    <span><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="intent"><?php echo htmlspecialchars($intentLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <div class="card-body"><?php echo nl2br(htmlspecialchars($text, ENT_QUOTES, 'UTF-8')); ?></div>
                                <div class="card-footer"><?php echo $created ? htmlspecialchars(date('Y-m-d \@ h:i A', strtotime($created)), ENT_QUOTES, 'UTF-8') : ''; ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; else: ?>
                    <div class="no-requests">No public prayer requests available right now.</div>
                <?php endif; ?>
            </div>
        </div>

        <button id="prevBtn" class="arrow arrow-left" aria-label="Previous" disabled>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15 6L9 12L15 18" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>

        <button id="nextBtn" class="arrow arrow-right" aria-label="Next">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 6L15 12L9 18" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
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
    <form class="prayer-form" method="post" action="connect.php">
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

// Carousel controls for pages of 4 cards
(function(){
    const track = document.querySelector('.carousel-track');
    if(!track) return;
    const pages = Array.from(track.querySelectorAll('.page'));
    if(pages.length <= 1) {
        // nothing to paginate
        document.getElementById('prevBtn').disabled = true;
        if(document.getElementById('nextBtn')) document.getElementById('nextBtn').disabled = true;
        return;
    }
    let current = 0;
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    function update() {
        const percent = -current * 100;
        track.style.transform = `translateX(${percent}%)`;
        prevBtn.disabled = current === 0;
        nextBtn.disabled = current === pages.length - 1;
    }

    prevBtn.addEventListener('click', () => {
        if(current > 0) current--;
        update();
    });
    nextBtn.addEventListener('click', () => {
        if(current < pages.length - 1) current++;
        update();
    });

    // make track width depend on number of pages and ensure each page fills container
    track.style.width = (pages.length * 100) + '%';
    pages.forEach(p => {
        p.style.flex = '0 0 100%';
        p.style.width = '100%';
    });
    update();
})();
</script>

</body>
</html>
