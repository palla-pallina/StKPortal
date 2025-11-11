

<?php
require_once 'dbconfig.php';

// =============================================================
// 🔹 FILTER HANDLING
// =============================================================
$filterType = $_GET['type'] ?? '';
$search = $_GET['search'] ?? '';

$table = 'baptism_reservations';
if ($filterType === 'Funeral') $table = 'funeral_reservation';
elseif ($filterType === 'Wedding') $table = 'wedding_reservations';

// =============================================================
// 🔹 DELETE / ACCEPT / CANCEL
// =============================================================
$actions = ['del' => 'DELETE FROM', 'accept' => 'UPDATE', 'cancel' => 'UPDATE'];
foreach ($actions as $param => $sqlStart) {
    if (isset($_GET[$param])) {
        $id = intval($_GET[$param]);
        if ($param === 'del') $sql = "$sqlStart $table WHERE id = :id";
        elseif ($param === 'accept') $sql = "$sqlStart $table SET status = 'Accepted' WHERE id = :id";
        else $sql = "$sqlStart $table SET status = 'Cancelled' WHERE id = :id";

        $query = $dbh->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        echo "<script>alert('Action completed successfully!');window.location.href='certificate.php?type=$filterType';</script>";
        exit();
    }
}

// =============================================================
// 🔹 SEARCH QUERY
// =============================================================
$whereClause = [];
$params = [];

if ($filterType === 'Funeral' && !empty($search)) {
    $whereClause[] = "(deceased_fullname LIKE :search OR cause_of_death LIKE :search OR funeral_address LIKE :search OR contact_person_firstname LIKE :search OR contact_person_lastname LIKE :search)";
    $params[':search'] = "%$search%";
} elseif (!empty($search)) {
    $whereClause[] = "(child_name LIKE :search OR father_name LIKE :search OR mother_name LIKE :search OR reservation_type LIKE :search)";
    $params[':search'] = "%$search%";
}

$whereSQL = !empty($whereClause) ? "WHERE " . implode(' AND ', $whereClause) : "";

// =============================================================
// 🔹 FETCH RECORDS
// =============================================================
try {
    $sql = "SELECT * FROM $table $whereSQL ORDER BY id DESC";
    $query = $dbh->prepare($sql);
    foreach ($params as $key => $val) $query->bindValue($key, $val, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Certificates</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
a { text-decoration: none; }
li { list-style: none; }
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
}
html, body { height: 100%; overflow-x: hidden; background: var(--grey); font-family: var(--lato); }

/* SIDEBAR */
#sidebar { position: fixed; top: 0; left: 0; width: 280px; height: 100%; background: var(--light); z-index: 2000; transition: all 0.3s ease; display: flex; flex-direction: column; justify-content: space-between; }
#sidebar.hide { width: 0 !important; overflow: hidden; pointer-events: none; }
#sidebar .label { height: 150px; display: flex; align-items: center; justify-content: center; padding-top: 20px; }
#sidebar .label .logo { width: 100%; height: 100%; object-fit: contain; }
#sidebar .side-menu { width: 100%; }
#sidebar .side-menu li { height: 48px; }
#sidebar .side-menu li a { display: flex; align-items: center; justify-content: center; height: 100%; font-size: 16px; color: var(--dark); }
#sidebar .side-menu li.active { background: var(--grey); }

/* CONTENT */
#content { position: relative; width: calc(100% - 280px); left: 280px; transition: all 0.3s ease; min-height: 100vh; }
#sidebar.hide ~ #content { width: 100%; left: 0; }

/* NAVBAR */
#content nav { height: 56px; background-color: var(--green); padding: 0 15px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 1000; }
#menu-bar { color: white; font-size: 26px; cursor: pointer; transition: transform 0.3s ease; }
#menu-bar:hover { transform: scale(1.1); }
#content nav #datetime { color: white; font-size: 14px; font-weight: 500; letter-spacing: 1px; flex-grow: 1; margin-left: 16px; }

/* MAIN CONTENT */
.main-content { background: white; padding: 40px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); width: 100%; }
.main-content h3 { font-family: var(--poppins); font-size: 26px; font-weight: 700; color: var(--dark); margin-bottom: 16px; }
.table td, .table th { vertical-align: middle !important; }

/* Status labels */
.label { padding: 5px 10px; border-radius: 4px; color: #fff; font-weight: 500; display: inline-block; }
.label-secondary { background-color: #777; }
.label-success { background-color: var(--green); }
.label-warning { background-color: var(--red); }

/* Filter + Search */
.form-inline { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px; }
.form-inline .form-control { min-width: 180px; }

/* Full-width table inside main content */
.table-responsive { width: 100%; overflow-x: auto; }

/* Responsive */
@media screen and (max-width: 768px) { #sidebar { width: 200px; } #content { width: calc(100% - 200px); left: 200px; } }
@media screen and (max-width: 576px) { .form-inline { flex-direction: column; } }
</style>
</head>
<body>

<!-- Sidebar -->
<section id="sidebar" class="hide">
  <div class="label"><img src="stklogo.png" alt="Logo" class="logo"></div>
  <ul class="side-menu top">
    <li><a href="../dashboard.php">Dashboard</a></li>
    <li><a href="filemanager.php">File Manager</a></li>
    <li><a href="announcement.php">Announcement</a></li>
    <li><a href="#">Event</a></li>
    <li><a href="members.php">Members</a></li>
    <li class="active"><a href="certificate.php">Certificates</a></li>
    <li><a href="../booking_sacraments.php">Reservations</a></li>
    <li><a href="../donations.php">Donations</a></li>
    <li><a href="../prayer_admin.php">Prayer Requests</a></li>
  </ul>
  <ul class="side-menu">
    <li><a href="../../public/homepage.html" class="logout">Logout</a></li>
  </ul>
</section>

<!-- Content -->
<section id="content">
  <nav>
    <i class='bx bx-menu' id="menu-bar"></i>
    <div id="datetime"></div>
  </nav>

  <div class="main-content">
    <h3>Certificates</h3>

    <form method="get" class="form-inline">
      <label for="type">Filter:</label>
      <select name="type" id="type" class="form-control" onchange="this.form.submit()">
        <option value="">All</option>
        <option value="Baptism" <?= $filterType=='Baptism'?'selected':'' ?>>Baptism</option>
        <option value="Wedding" <?= $filterType=='Wedding'?'selected':'' ?>>Wedding</option>
        <option value="Funeral" <?= $filterType=='Funeral'?'selected':'' ?>>Funeral</option>
      </select>
      <input type="text" name="search" class="form-control" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
      <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <div class="table-responsive" style="margin-top:20px;">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>#</th>
            <?php if($filterType==='Funeral'): ?>
              <th>Deceased Name</th><th>Cause of Death</th><th>Birthdate</th>
              <th>Funeral Date</th><th>Time</th><th>Address</th>
              <th>Contact Person</th><th>Relation</th><th>Contact No</th><th>Email</th>
              <th>Status</th><th>Actions</th>
            <?php else: ?>
              <th>Child Name</th><th>Date of Birth</th><th>Place of Birth</th>
              <th>Father's Name</th><th>Mother's Name</th><th>Preferred Date</th>
              <th>Reservation Type</th><th>Status</th><th>Actions</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php
          $cnt = 1;
          if ($results) {
            foreach ($results as $result) {
              $status = $result->status ?? 'Pending';
              $statusClass = $status === 'Accepted' ? 'label-success' : ($status === 'Cancelled' ? 'label-warning' : 'label-secondary');
              $acceptDisabled = $status === 'Accepted' ? 'disabled' : '';
              $cancelDisabled = $status === 'Cancelled' ? 'disabled' : '';

              echo "<tr><td>{$cnt}</td>";
              if($filterType==='Funeral'){
                $contact = $result->contact_person_firstname.' '.$result->contact_person_lastname;
                echo "<td>{$result->deceased_fullname}</td><td>{$result->cause_of_death}</td>
                      <td>{$result->deceased_dob}</td><td>{$result->funeral_date}</td><td>{$result->funeral_time}</td>
                      <td>{$result->funeral_address}</td><td>{$contact}</td><td>{$result->contact_person_relation}</td>
                      <td>{$result->contact_person_contact_no}</td><td>{$result->contact_person_email}</td>
                      <td><span class='label {$statusClass}'>{$status}</span></td>
                      <td>
                       <a href='funeral_pdf.php?id={$result->id}' class='btn btn-success btn-sm'>PDF</a>
                        <a href='edit_funeral.php?id={$result->id}' class='btn btn-primary btn-sm'>Edit</a>
                        <a href='certificate.php?del={$result->id}&type=Funeral' class='btn btn-danger btn-sm'>Delete</a>
                        <a href='certificate.php?accept={$result->id}&type=Funeral' class='btn btn-success btn-sm' {$acceptDisabled}>Accept</a>
                        <a href='certificate.php?cancel={$result->id}&type=Funeral' class='btn btn-warning btn-sm' {$cancelDisabled}>Cancel</a>
                      </td>";
              } else {
                echo "<td>{$result->child_name}</td><td>{$result->birth_date}</td><td>{$result->birth_place}</td>
                      <td>{$result->father_name}</td><td>{$result->mother_name}</td><td>{$result->preferred_date}</td>
                      <td>{$result->reservation_type}</td><td><span class='label {$statusClass}'>{$status}</span></td>
                      <td>
                        <a href='baptsimpdf.php?id={$result->id}' class='btn btn-success btn-sm'>PDF</a>
                        <a href='edit_baptism.php?id={$result->id}' class='btn btn-primary btn-sm'>Edit</a>
                        <a href='certificate.php?del={$result->id}&type=Baptism' class='btn btn-danger btn-sm'>Delete</a>
                        <a href='certificate.php?accept={$result->id}&type=Baptism' class='btn btn-success btn-sm' {$acceptDisabled}>Accept</a>
                        <a href='certificate.php?cancel={$result->id}&type=Baptism' class='btn btn-warning btn-sm' {$cancelDisabled}>Cancel</a>
                      </td>";
              }
              echo "</tr>";
              $cnt++;
            }
          } else echo "<tr><td colspan='20' class='text-center'>No records found.</td></tr>";
          ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<script>
const menuBar = document.getElementById('menu-bar');
const sidebar = document.getElementById('sidebar');

menuBar.addEventListener('click', ()=> sidebar.classList.toggle('hide'));

function updateDateTime(){
  const now = new Date();
  const options = { year:'numeric', month:'short', day:'numeric', hour:'2-digit', minute:'2-digit' };
  document.getElementById('datetime').textContent = now.toLocaleString('en-US', options);
}
setInterval(updateDateTime, 1000);
updateDateTime();
</script>
</body>
</html>
