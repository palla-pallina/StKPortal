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




        .cert-wrap{
          max-width:980px;margin:10px auto
        }

        .card{
          background:#fff;
          border:1px solid #e6e6e6;
          border-radius:12px;
          padding:14px;
          margin-bottom:12px
        }

        label{
          display:block;
          font-weight:600;
          margin:4px 0
        }

        input,select{
          width:100%;
          padding:9px;
          border:1px solid #ccc;
          border-radius:8px
        }

        .actions{
          display:flex;
          gap:10px;
          justify-content:flex-end;
          margin:14px 0
        }
        .btn{
          display:inline-block;
          padding:10px 14px;
          border-radius:8px;
          text-decoration:none
        }

        .btn.primary{
          background:#4b8a3f;
          color:#fff
        }
        .btn.secondary{
          background:#eee;
          color:#333
        }

        .cert-wrap { 
          max-width: 1100px; 
          margin: 0 auto; 
        }

        .grid { 
          display: grid; 
          gap: 2px;              
          align-items: start;
        }
        .grid.g2 { 
          grid-template-columns: 1fr 1fr; 
        }                  
        .grid.g3 { 
          grid-template-columns: repeat(3, 1fr); 
        }           
        .grid.fit2-1 { 
          grid-template-columns: 2fr 1fr; 
        }              
        .grid.fit1-2 { 
          grid-template-columns: 1fr 2fr; 
        }

        .span-2 { 
          grid-column: span 2; 
        }
        .span-3 { 
          grid-column: span 3; 
        }

        .grid > div { 
          display: flex; 
          flex-direction: column; 
        }
        .grid label { 
          font-weight: 600; 
          margin-bottom: 6px; 
        }
        .grid input, .grid select, .grid textarea {
          width: 100%;
          box-sizing: border-box;
          padding: 10px 12px;
          border: 1px solid #dcdcdc;
          border-radius: 8px;
          height: 42px;
        }
        .grid textarea { 
          height: auto; 
          min-height: 90px; 
        }

        .w-xxs { 
          max-width: 160px; 
        }

        @media (max-width: 900px) {
          .grid.g3 { 
            grid-template-columns: 1fr 1fr; 
          }
          .grid.fit2-1, .grid.fit1-2 { 
            grid-template-columns: 1fr 1fr; 
          }
        }
        @media (max-width: 640px) {
          .grid.g2, .grid.g3,
          .grid.fit2-1, .grid.fit1-2 { 
            grid-template-columns: 1fr; 
          }
          .span-2, .span-3 { 
            grid-column: auto; 
          } 
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
        <h2>Wedding Certificate</h2>
        <form action="submit_wedding.php" method="POST" id="weddingForm">

          <div class="card">
            <h3>Bride</h3>
            <div class="grid g2">
              <div>
                <label>First Name<span class="req">*</span>
                </label>
                <input name="bride_first" required>
              </div>

              <div>
                <label>Middle Name<span class="req">*</span></label>
                <input name="bride_middle" required>
              </div>

              <div>
                <label>Last Name<span class="req">*</span></label>
                <input name="bride_last" required>
              </div>

              <div>
                <label>Suffix</label>
                <input name="bride_suffix">
              </div>

              <div class="grid fit2-1 span-2">
              <div>
                <label>Birthdate<span class="req">*</span></label>
                <input type="date" name="bride_birthdate" id="bride_birthdate" required oninput="calcBrideAge()">
              </div>

              <div>
                <label>Age</label>
                <input type="number" name="bride_age" id="bride_age" placeholder="auto" min="0" max="120" readonly>
              </div>
            </div>

              <div><label>Birthplace</label>
                <input name="bride_birthplace">
              </div>

              <div><label>Nationality<span class="req">*</span></label>
                <input name="bride_nationality" required>
              </div>

              <div class="span-2"><label>Address*</label>
                <input name="bride_address" required>
              </div>

              <div class="span-2">
                <label>Contact Number<span class="req">*</span></label>
                <input type="tel" 
                        name="bride_contactnumber" 
                        id="bride_contactnumber"
                        placeholder="09171234567" 
                        maxlength="11" 
                        pattern="^\d{11}$"
                        inputmode="numeric" 
                        oninput="this.value=this.value.replace(/[^0-9]/g,'')" required>
              </div>
            </div>
          </div>

          <div class="card">
            <h3>Groom</h3>
            <div class="grid g2">
              <div>
                <label>First Name<span class="req">*</span></label>
                <input name="groom_first" required>
              </div>

              <div>
                <label>Middle Name<span class="req">*</span></label>
                <input name="groom_middle" required>
              </div>

              <div>
                <label>Last Name<span class="req">*</span></label>
                <input name="groom_last" required>
              </div>

              <div>
                <label>Suffix</label>
                <input name="groom_suffix">
              </div>

              <div class="grid fit2-1 span-2">
                <div>
                  <label>Birthdate<span class="req">*</span></label>
                  <input type="date" name="groom_birthdate" id="groom_birthdate" required oninput="calcGroomAge()">
                </div>
                <div>
                  <label>Age</label>
                  <input type="number" name="groom_age" id="groom_age" placeholder="auto" min="0" max="120" inputmode="numeric" class="w-xxs">
                </div>
              </div>

              <div>
                <label>Birthplace</label>
                <input name="groom_birthplace">
              </div>

              <div>
                <label>Nationality<span class="req">*</span></label>
                <input name="groom_nationality" required>
              </div>

              <div class="span-2">
                <label>Address<span class="req">*</span></label>
                <input name="groom_address" required>
              </div>

              <div class="span-2">
                <label>Contact Number<span class="req">*</span></label>
                <input
                  type="tel"
                  name="groom_contactnumber"
                  id="groom_contactnumber"
                  placeholder="e.g. 09171234567"
                  maxlength="11"
                  pattern="^\d{11}$"
                  inputmode="numeric"
                  oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                  title="Please enter exactly 11 digits (e.g. 09171234567)"
                  required>
              </div>

            </div>
          </div>

          <div class="card">
            <div class="grid g3">
              <div>
                <label>Wedding Date<span class="req">*</span></label>
                <input type="date" name="wedding_date" id="wedding_date" required>
              </div>
              <div>
                <label>Pastor<span class="req">*</span></label>
                <select name="pastor" required>
                  <option value="" disabled selected>Select Pastor</option>
                  <option>Fr. Juan Dela Cruz</option>
                  <option>Fr. Pedro Santos</option>
                </select>
              </div>
              <div>
                <label>Location</label>
                <input name="location" placeholder="(optional)">
              </div>
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

        function calcAge(birthInputId, ageInputId) {
          const b = document.getElementById(birthInputId)?.value;
          const out = document.getElementById(ageInputId);
          if (!b || !out) return;
          const dob = new Date(b);
          if (isNaN(+dob)) return;
          const today = new Date();
          let age = today.getFullYear() - dob.getFullYear();
          const m = today.getMonth() - dob.getMonth();
          if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) {
            age--;
          }
          out.value = age >= 0 ? age : '';
        }

        function calcBrideAge() { calcAge('bride_birthdate','bride_age'); }
        function calcGroomAge() { calcAge('groom_birthdate','groom_age'); }
        
        (function(){
          const pad=n=>String(n).padStart(2,'0');
          const d=new Date(); const today=`${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
          const wd=document.getElementById('wedding_date'); if(wd) wd.min=today;
        })();
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