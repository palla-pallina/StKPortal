<?php

$page = $_GET['page'] ?? 'dashboard';

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
<title>Dashboard</title>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      overflow-x: hidden;
    }
    
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 250px;
      height: 100vh;
      background-color: #FBFBFB;
      color: white;
      padding-top: 60px;
      overflow-y: auto;
    }
    .sidebar-logo {
      text-align: center;
      margin-bottom: 20px;
      padding: 0 20px;
    }
    .sidebar-logo img {
      max-width: 100px;
      height: auto;
      display: block;
      margin: 0 auto 8px auto;
      border-radius: 10px;
    }
    .sidebar a {
      display: block;
      padding: 15px 20px;
      text-decoration: none;
      color: black;
      font-size: 16px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .sidebar a:hover {
      background-color: #999999;
    }
    
    .topbar {
      position: fixed;
      top: 0;
      left: 250px;
      right: 0;
      height: 60px;
      background-color: #4b8a3f;
      color: white;
      padding: 10px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      z-index: 5;
    }
    .hamburger {
      font-size: 24px;
      cursor: pointer;
      background: none;
      border: none;
      color: white;
    }
    
    main {
      margin-left: 250px;
      margin-top: 60px;
      padding: 20px;

    
}  </style>
</head>

<body>

  
  <div class="sidebar" id="sidebar">
    <div class="sidebar-logo">
      <img src="santa krus logo.png" alt="Logo" class="logo" />
      <div class="logo-text">My Logo Text</div>
    </div>
    <a href="../dashboard.php">Dashboard</a>
    <a href="file.php?page=filemanager">File Manager</a>
    <a href="announcement.php?page=announcement">Announcement</a>
    <a href="index.php?page=event">Event</a>
    <a href="index.php?page=member">Member</a>
    <a href="?page=certificate">Certificate</a>
    <a class="btn btn-default" href="view_baptisms.php">View Baptisms</a>
    <a class="btn btn-default" href="view_weddings.php">View Weddings</a>
    <a href="index.php?page=booking">Booking</a>
    <a href="index.php?page=donation">Donation</a>
    <a href="index.php?page=prayer">Prayer</a>
    <a href="index.php?page=logout">Log Out</a>
  </div>

  
  <div class="topbar">
    <button class="hamburger" onclick="toggleSidebar()">☰</button>
  </div>

  
  <main>
    <?php 
    
    switch ($page) {
  case 'dashboard':
    echo "<h2>Welcome to Dashboard</h2>";
    break;

    case 'certificate':   
    if (!isset($_GET['type'])) {
      ?>
      <div class="container">
        <h2>Choose Certificate Type</h2>
        <div style="display:flex; gap:16px; margin-top:14px; flex-wrap:wrap;">
          <a href="?page=certificate&type=baptism" class="btn btn-success">Baptism Certificate</a>
          <a href="?page=certificate&type=wedding" class="btn btn-primary">Wedding Certificate</a>
        </div>
      </div>
      <?php
      break;
    }

    if ($_GET['type'] === 'baptism') {
      ?>
      <style>
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

      <script>
        
        (function(){
          const pad=n=>String(n).padStart(2,'0');
          const d=new Date(); const today=`${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
          const bd=document.getElementById('baptism_date'); if(bd) bd.min=today;
        })();

    
        const birth=document.getElementById('birthdate');
        if(birth){ birth.addEventListener('change',function(){
          const b=new Date(this.value), n=new Date();
          let a=n.getFullYear()-b.getFullYear(); const m=n.getMonth()-b.getMonth();
          if(m<0 || (m===0 && n.getDate()<b.getDate())) a--;
          const age=document.getElementById('age'); if(age) age.value=isNaN(a)?'':a;
        });}

        (function(){
          const addNinongBtn = document.getElementById('addNinong');
          const addNinangBtn = document.getElementById('addNinang');
          const holder       = document.getElementById('extraSponsors');

          if (!addNinongBtn || !addNinangBtn || !holder) return;

          let ninongCount = 0, ninangCount = 0;

          function createSponsorBlock(role, index){
            const lower = role.toLowerCase();
            const wrapper = document.createElement('div');
            wrapper.className = 'card';
            wrapper.innerHTML = `
              <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
                <h4>Additional ${role}</h4>
                <button type="button" class="btn secondary remove-sponsor">Remove</button>
              </div>

              <div class="grid names">
                <div>
                  <label>First</label>
                  <input name="extra_${lower}_first[]" autocomplete="off">
                </div>
                <div>
                  <label>Middle</label>
                  <input name="extra_${lower}_middle[]" autocomplete="off">
                </div>
                <div>
                  <label>Last</label>
                  <input name="extra_${lower}_last[]" autocomplete="off">
                </div>
                <div>
                  <label>Suffix</label>
                  <input name="extra_${lower}_suffix[]" autocomplete="off">
                </div>
              </div>

              <div class="full" style="margin-top:6px">
                <label>Address</label>
                <input name="extra_${lower}_address[]" autocomplete="off">
              </div>
            `;
            wrapper.querySelector('.remove-sponsor').addEventListener('click', () => {
              wrapper.remove();
            });
            return wrapper;
          }

          addNinongBtn.addEventListener('click', () => {
            holder.appendChild(createSponsorBlock('Ninong', ++ninongCount));
          });

          addNinangBtn.addEventListener('click', () => {
            holder.appendChild(createSponsorBlock('Ninang', ++ninangCount));
          });
        })();
      </script>

      <?php
      break;
    }

    if ($_GET['type'] === 'wedding') {
      ?>
      <style>
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

      <?php
      break;
    }

    echo "<p>Unknown certificate type.</p>";
    break;


  default:
    echo "<h2>Page not found</h2>";
    break;
}
    ?>
  </main>

      <script>
        function toggleSidebar() {
          const sidebar = document.getElementById('sidebar');
          if(sidebar.style.left === '0px'){
            sidebar.style.left = '-250px';
            document.querySelector('main').style.marginLeft = '0';
            document.querySelector('.topbar').style.left = '0';
          } else {
            sidebar.style.left = '0';
            document.querySelector('main').style.marginLeft = '250px';
            document.querySelector('.topbar').style.left = '250px';
          }
        }
      </script>
</body>
</html>
