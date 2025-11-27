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

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
<title>Admin Dashboard</title>

<style>
    
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

a {
    text-decoration: none;
}

li {
    list-style: none;
}

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
    --success: #16a34a;
    --warning: #f59e0b;
    --danger: #dc2626;
}

html, body {
    height: 100%;
    overflow-x: hidden;
}

body.dark {
    --light: #0C0C1E;
    --grey: #060714;
    --dark: #FBFBFB;
}

body {
    background: var(--grey);
    font-family: var(--lato);
}

/* SIDEBAR */
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

.content-section {
    display: none;
    padding: 20px;
    min-height: calc(100vh - 56px);
}

.content-section.active {
    display: block;
}
/* SIDEBAR END */

.main-content {
    background: white;
    /*border-radius: 31px;*/
    padding: 51px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 100%;
}

.main-content h1 {
    font-family: var(--poppins);
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 16px;
}

.main-content h2 {
    font-family: var(--poppins);
    font-size: 18px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 12px;
}

@media screen and (max-width: 768px) {
    #sidebar {
        width: 200px;
    }
    #content {
        width: calc(100% - 200px);
        left: 200px;
    }
    #content nav .nav-link {
        display: none;
    }
}

@media screen and (max-width: 576px) {
    #content nav .nav-link {
        display: none;
    }
}


.card{                   
          border:1px solid #cfcfcf;
          border-radius:8px;
          padding:16px 18px;
          margin-bottom:16px;
        }

        .card h3{
          margin:0 0 10px;
          font-size:22px;
          font-weight:700;
        }
        .card h4{
          margin:14px 0 8px;
          font-size:16px;
          font-weight:700;
        }

        label{ 
          display:block;
          font-weight:700;
          margin:0 0 6px;                        
          color:#2a2a2a;
        }
        .req{ color:#b10000; font-size:.85em; vertical-align:super; }

        input, select, textarea{
          width:100%;
          box-sizing:border-box;
          background:#fff;
          color:#222;
          border:1px solid #bdbdbd;
          border-radius:6px;
          height:34px;                           
          padding:6px 8px;
          outline:0;
        }

        textarea{ 
          min-height:76px; height:auto; 
        }

        input::placeholder, textarea::placeholder{ 
          color:#8a8a8a; 
        }

        input:focus, select:focus, textarea:focus{
          border-color:#7a7a7a;
          box-shadow:0 0 0 2px rgba(0,0,0,.06);
        }

        .grid{ 
          display:grid; 
          gap:8px; 
        }

        .grid.auto{ 
          grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); 
        }

        :root{ 
          --name-cols: 1fr 1fr 1fr 1fr; 
        } 

        .grid.names{ 
          grid-template-columns:var(--name-cols); 
        }

        .fit2-1{ 
          grid-template-columns:2fr 1fr; 
        }

        .span-2{ 
          grid-column:span 2; 
        }

        .full{ 
          grid-column:1 / -1; 
        }
        .w-xxs{ 
          max-width:120px; 
        }       

        @media (max-width:900px){ 
          .grid.names{ grid-template-columns:repeat(2,1fr);
          } 
        }
        @media (max-width:560px){
          .grid.names{ 
            grid-template-columns:1fr; 
          }
          .span-2,.full{ 
            grid-column:auto; 
          }
        }

        .inline-controls{ 
          display:flex; 
          align-items:center; 
          gap:14px; 
        }
        .inline-controls label{ 
          font-weight:600; 
          margin:0; 
        }
        .inline-controls input[type="radio"],
        .inline-controls input[type="checkbox"]{
          width:18px; 
          height:18px; 
          margin-right:6px;
        }

        .divider{
          height:1px;
          background:#c9c9c9;
          margin:12px 0 4px;
        }

        .btn{ 
          padding:8px 12px; 
          border-radius:6px; 
          text-decoration:none; 
        }
        .btn.primary{ 
          background:#8f6a3a; 
          color:#fff; 
        }
        .btn.secondary{ 
          background:#e6e6e6; 
          color:#222; 
        }



</style>
</head>

<body>

<!-- SIDEBAR -->
<section id="sidebar" <?php if ($hideSidebar) echo 'class="hide"'; ?>>
<a href="#" class="label">
<img src="../santa krus logo.png" alt="Logo" class="logo" />
</a>
<ul class="side-menu top">
<li class="active"><a href="dashboard.php"><span class="text">Dashboard</span></a></li>
<li><a href="#"><span class="text">File Manager</span></a></li>
<li><a href="#"><span class="text">Announcement</span></a></li>
<li><a href="#"><span class="text">Event</span></a></li>
<li><a href="#"><span class="text">Members</span></a></li>
<li><a href="#"><span class="text">Certificates</span></a></li>
<li><a href="booking_sacraments.php"><span class="text">Reservations</span></a></li>
<li><a href="donations.php" data-section="dashboard"><span class="text">Donations</span></a></li>
<li><a href="prayer_admin.php"><span class="text">Prayer Requests</span></a></li>
</ul>
<ul class="side-menu">
<li><a href="logout.php" class="logout"><span class="text">Logout</span></a></li>
</ul>
</section>
<!-- SIDEBAR END -->

<section id="content">
<nav>
<i class='bx bx-menu'></i>
<div id="datetime"></div>
<div class="profile">
<span class="greeting"><?php echo htmlspecialchars($admin_name); ?></span>
<a href="#" class="profile-link"><i class='bx bx-user'></i></a>
</div>
</nav>


<div class="main-content">


<div class="cert-wrap">
        <h2>Baptism Certificate</h2>
        <form action="submit_baptism.php" method="POST" id="baptismForm">
        
    <div class="card">
      <h3>Name of Child <span class="req">*</span></h3>
        <div class="grid names">
          <div><label>First <span class="req">*</span></label><input name="child_first" required></div>
          <div><label>Middle <span class="req">*</span></label><input name="child_middle"required></div>
          <div><label>Last <span class="req">*</span></label><input name="child_last" required></div>
          <div><label>Suffix</label><input name="child_suffix"></div>
        </div>

      <div class="grid auto">
        <div class="grid fit2-1 span-2">
          <div><label>Birthdate <span class="req">*</span></label>
            <input type="date" id="birthdate" name="birthdate" required oninput="calcChildAge()">
          </div>
          <div><label>Age</label>
            <input name="age" id="age" class="w-xxs" readonly placeholder="auto">
          </div>
        </div>
        <div><label>Birthplace <span class="req">*</span></label><input name="birthplace" required></div>
        <div><label>Gender <span class="req">*</span></label>
          <select name="gender" required>
          <option value="" disabled selected>Select --</option>
          <option>Male</option><option>Female</option>
          </select>
        </div>
        <div><label>Nationality <span class="req">*</span></label><input name="nationality" required></div>
      </div>

      <div class="full">
        <label>Address <span class="req">*</span></label>
        <input name="child_address" required placeholder="House / Street / Barangay / City">
      </div>
    </div>

      
    <div class="card">
      <h3>Name of Mother <span class="req">*</span></h3>
        <div class="grid names">
          <div><label>First <span class="req">*</span></label><input name="mother_first" required></div>
          <div><label>Middle <span class="req">*</span></label><input name="mother_middle"required></div>
          <div><label>Last <span class="req">*</span></label><input name="mother_last" required></div>
          <div><label>Suffix</label><input name="mother_suffix"></div>
        </div>
     </div>
          
    <div class="card">
      <h3>Name of Father <span class="req">*</span></h3>
        <div class="grid names">
          <div><label>First <span class="req">*</span></label><input name="father_first" required></div>
          <div><label>Middle <span class="req">*</span></label><input name="father_middle"required></div>
          <div><label>Last <span class="req">*</span></label><input name="father_last" required></div>
          <div><label>Suffix</label><input name="father_suffix"></div>
        </div>
    </div>

    <div class="card">
      <label>Married? <span class="req">*</span></label>
        <div class="inline-controls">
          <label><input type="radio" name="parents_married" value="Yes" required> YES</label>
          <label><input type="radio" name="parents_married" value="No" required> NO</label>
          <select name="marriage_type">
            <option value="" selected>Type</option>
            <option>Church</option><option>Civil</option>
          </select>
        </div>
      <div>
        <label>Parents' Address</label>
        <input name="parents_address" placeholder="(optional if same as child)">
      </div>
    </div>

    <div class="card">
      <<h4>Pangunahing Ninong<span class="req">*</span></h4>
        <div class="grid names">
          <div><label>First <span class="req">*</span></label><input name="primary_ninong_first" required></div>
          <div><label>Middle <span class="req">*</span></label><input name="primary_ninong_middle"required></div>
          <div><label>Last <span class="req">*</span></label><input name="primary_ninong_last" required></div>
          <div><label>Suffix</label><input name="primary_ninong_suffix"></div>
        </div>
      <div class="full"><label>Address <span class="req">*</span></label><input name="primary_ninong_address"required></div>

      <h4>Pangunahing Ninang <span class="req">*</span></h4>
        <div class="grid names">
          <div><label>First <span class="req">*</span></label><input name="primary_ninang_first" required></div>
          <div><label>Middle <span class="req">*</span></label><input name="primary_ninang_middle"required></div>
          <div><label>Last <span class="req">*</span></label><input name="primary_ninang_last" required></div>
          <div><label>Suffix</label><input name="primary_ninang_suffix"></div>
        </div>
          <div class="full"><label>Address</label> <span class="req">*</span><input name="primary_ninang_address"required></div>

          <div style="display:flex; gap:8px; margin-top:8px;">
            <button type="button" class="btn secondary" id="addNinong">+ Ninong</button>
            <button type="button" class="btn secondary" id="addNinang">+ Ninang</button>
          </div>
          <div id="extraSponsors" style="margin-top:8px;"></div>
          </div>
      <div class="card">
      <div class="grid auto">
        <div>
          <label>Pastor <span class="req">*</span></label>
          <select name="pastor" required>
            <option value="" disabled selected>Select Pastor</option>
            <option>Fr. Juan Dela Cruz</option>
            <option>Fr. Pedro Santos</option>
            <option>Fr. Jose Ramos</option>
          </select>
        </div>
          <div>
            <label>Date of Baptism <span class="req">*</span></label>
            <input type="date" name="baptism_date" id="baptism_date" required>
          </div>
          <div style="align-self:end;color:#666"><span class="req">*</span> Required fields</div>
      </div>
      </div>

          <div class="actions">
            <a class="btn secondary" href="?page=certificate">← Back</a>
            <button type="button" class="btn secondary" onclick="window.print()">PRINT</button>
            <button type="submit" class="btn primary">SAVE</button>
          </div>
        </form>
      </div>




</div>



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