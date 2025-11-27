<?php
require_once 'connection.php';
session_start();

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}

$admin_name = !empty($_SESSION['givenname']) ? $_SESSION['givenname'] : $_SESSION['username'];


// -----------------------------------------------------------
// UTIL: Function to build names
// -----------------------------------------------------------
function fullName($f, $m, $l, $s) {
    return trim("$f $m $l $s");
}


// -----------------------------------------------------------
// FILTER HANDLING
// -----------------------------------------------------------
$filterType = $_GET['type'] ?? '';
$search = $_GET['search'] ?? '';

$table = 'baptisms';
if ($filterType === 'Funeral') $table = 'funerals';
elseif ($filterType === 'Wedding') $table = 'weddings';


// -----------------------------------------------------------
// DELETE / ACCEPT / CANCEL
// -----------------------------------------------------------
if (isset($_GET['del']) || isset($_GET['accept']) || isset($_GET['cancel'])) {
    $actionId = intval($_GET['del'] ?? $_GET['accept'] ?? $_GET['cancel']);

    if (isset($_GET['del'])) {
        $sql = "DELETE FROM $table WHERE id = :id";
    } elseif (isset($_GET['accept'])) {
        $sql = "UPDATE $table SET status='Accepted' WHERE id = :id";
    } else {
        $sql = "UPDATE $table SET status='Cancelled' WHERE id = :id";
    }

    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':id', $actionId, PDO::PARAM_INT);
    $stmt->execute();

    echo "<script>alert('Action completed.');window.location.href='certificate.php?type=$filterType';</script>";
    exit();
}


// -----------------------------------------------------------
// SEARCH (REWRITTEN — now uses individual name fields)
// -----------------------------------------------------------
$where = [];
$param = [];

if (!empty($search)) {

    $searchLike = "(firstname LIKE :s OR middlename LIKE :s OR lastname LIKE :s OR suffix LIKE :s)";

    switch ($filterType) {

        case 'Funeral':
            $where[] =
            "(" .
                "(deceased_firstname LIKE :s OR deceased_middlename LIKE :s OR deceased_lastname LIKE :s OR deceased_suffix LIKE :s)" .
                " OR funeral_address LIKE :s" .
                " OR (contact_person_firstname LIKE :s OR contact_person_middlename LIKE :s OR contact_person_lastname LIKE :s OR contact_person_suffix LIKE :s)" .
            ")";
            break;

        case 'Wedding':
            $where[] =
            "(" .
                "(bride_firstname LIKE :s OR bride_middlename LIKE :s OR bride_lastname LIKE :s OR bride_suffix LIKE :s)" .
                " OR (groom_firstname LIKE :s OR groom_middlename LIKE :s OR groom_lastname LIKE :s OR groom_suffix LIKE :s)" .
                " OR wedding_type LIKE :s" .
            ")";
            break;

        default: // Baptism
            $where[] =
            "(" .
                "(child_firstname LIKE :s OR child_middlename LIKE :s OR child_lastname LIKE :s OR child_suffix LIKE :s)" .
                " OR (father_firstname LIKE :s OR father_middlename LIKE :s OR father_lastname LIKE :s OR father_suffix LIKE :s)" .
                " OR (mother_firstname LIKE :s OR mother_middlename LIKE :s OR mother_lastname LIKE :s OR mother_suffix LIKE :s)" .
            ")";
    }

    $param[':s'] = "%$search%";
}

$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";


// -----------------------------------------------------------
// FETCH RECORDS
// -----------------------------------------------------------
$stmt = $dbh->prepare("SELECT * FROM $table $whereSQL ORDER BY id DESC");
foreach ($param as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_OBJ);
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
<link href="certificate.css" rel="stylesheet">
</head>

<body>
<!-- SIDEBAR -->
<section id="sidebar" <?php if ($hideSidebar) echo 'class="hide"'; ?>>
<a href="#" class="label">
<img src="../santa krus logo.png" alt="Logo" class="logo" />
</a>
<ul class="side-menu top">
<li><a href="../dashboard.php"><span class="text">Dashboard</span></a></li>
<li><a href="../stk/filemanager.php"><span class="text">File Manager</span></a></li>
<li><a href="../stk/announcement.php"><span class="text">Announcement</span></a></li>
<li><a href="#"><span class="text">Event</span></a></li>
<li><a href="../stk/members.php"><span class="text">Members</span></a></li>
<li class="active"><a href="#"><span class="text">Certificates</span></a></li>
<li><a href="../sacraments/s_dashboard.php"><span class="text">Reservations</span></a></li>
<li><a href="../donations.php" data-section="dashboard"><span class="text">Donations</span></a></li>
<li><a href="../prayer_admin.php"><span class="text">Prayer Requests</span></a></li>
</ul>
<ul class="side-menu">
<li><a href="../logout.php" class="logout"><span class="text">Logout</span></a></li>
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
    <h3>Certificates</h3>

    <form method="get" class="form-inline">
        <label>Filter:</label>
        <select name="type" class="form-control" onchange="this.form.submit()">
            <option value="Baptism" <?= $filterType==='Baptism'?'selected':'' ?>>Baptism</option>
            <option value="Wedding" <?= $filterType==='Wedding'?'selected':'' ?>>Wedding</option>
            <option value="Funeral" <?= $filterType==='Funeral'?'selected':'' ?>>Funeral</option>
        </select>
        <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search) ?>" placeholder="Search...">
        <button class="btn btn-primary">Search</button>
    </form>

    <div class="table-responsive" style="margin-top:20px;">
    <table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th>#</th>

        <?php if ($filterType === 'Funeral'): ?>
            <th>Deceased</th><th>Birthdate</th><th>Funeral Date</th>
            <th>Time</th><th>Address</th><th>Contact Person</th><th>Relation</th>
            <th>Contact No</th><th>Email</th><th>Status</th><th>Actions</th>

        <?php elseif ($filterType === 'Wedding'): ?>
            <th>Bride</th><th>Groom</th><th>Wedding Date</th><th>Time</th>
            <th>Status</th><th>Actions</th>

        <?php else: ?>
            <th>Child</th><th>Date of Birth</th><th>Place of Birth</th>
            <th>Father</th><th>Mother</th><th>Baptism Date</th>
            <th>Status</th><th>Actions</th>
        <?php endif; ?>
    </tr>
    </thead>


<tbody>
    <?php
    $cnt = 1;
    if ($results) {
        foreach ($results as $r) {

            $statusClass =
                $r->status === 'Accepted' ? 'label-success' :
                ($r->status === 'Cancelled' ? 'label-warning' : 'label-default');

            echo "<tr><td>{$cnt}</td>";

            //------------------------------------------
            // FUNERAL ROW
            //------------------------------------------
            if ($filterType === 'Funeral') {

                $deceased = fullName(
                    $r->deceased_firstname,
                    $r->deceased_middlename,
                    $r->deceased_lastname,
                    $r->deceased_suffix
                );

                $contactPerson = fullName(
                    $r->contact_person_firstname,
                    $r->contact_person_middlename,
                    $r->contact_person_lastname,
                    $r->contact_person_suffix
                );

                echo "
                    <td>$deceased</td>
                    <td>{$r->deceased_dob}</td>
                    <td>{$r->funeral_date}</td>
                    <td>{$r->funeral_time}</td>
                    <td>{$r->funeral_address}</td>
                    <td>$contactPerson</td>
                    <td>{$r->contact_person_relation}</td>
                    <td>{$r->contact_person_contact_no}</td>
                    <td>{$r->contact_person_email}</td>
                    <td><span class='label {$statusClass}'>{$r->status}</span></td>
                    <td>
                        <a href='funeral_pdf.php?id={$r->id}' class='btn btn-success btn-sm'>PDF</a>
                        <a href='edit_funeral.php?id={$r->id}' class='btn btn-primary btn-sm'>Edit</a>
                        <a href='certificate.php?del={$r->id}&type=Funeral' class='btn btn-danger btn-sm'>Delete</a>
                        <a href='certificate.php?accept={$r->id}&type=Funeral' class='btn btn-success btn-sm'>Accept</a>
                        <a href='certificate.php?cancel={$r->id}&type=Funeral' class='btn btn-warning btn-sm'>Cancel</a>
                    </td>
                ";

            //------------------------------------------
            // WEDDING ROW
            //------------------------------------------
            } elseif ($filterType === 'Wedding') {

                $bride = fullName($r->bride_firstname, $r->bride_middlename, $r->bride_lastname, $r->bride_suffix);
                $groom = fullName($r->groom_firstname, $r->groom_middlename, $r->groom_lastname, $r->groom_suffix);

                echo "
                    <td>$bride</td>
                    <td>$groom</td>
                    <td>{$r->date}</td>
                    <td>{$r->time}</td>
                    <td><span class='label {$statusClass}'>{$r->status}</span></td>
                    <td>
                        <a href='wedding_pdf.php?id={$r->id}' class='btn btn-success btn-sm'>PDF</a>
                        <a href='edit_wedding.php?id={$r->id}' class='btn btn-primary btn-sm'>Edit</a>
                        <a href='certificate.php?del={$r->id}&type=Wedding' class='btn btn-danger btn-sm'>Delete</a>
                        <a href='certificate.php?accept={$r->id}&type=Wedding' class='btn btn-success btn-sm'>Accept</a>
                        <a href='certificate.php?cancel={$r->id}&type=Wedding' class='btn btn-warning btn-sm'>Cancel</a>
                    </td>
                ";

            //------------------------------------------
            // BAPTISM ROW
            //------------------------------------------
            } else {

                $child = fullName($r->child_firstname, $r->child_middlename, $r->child_lastname, $r->child_suffix);
                $father = fullName($r->father_firstname, $r->father_middlename, $r->father_lastname, $r->father_suffix);
                $mother = fullName($r->mother_firstname, $r->mother_middlename, $r->mother_lastname, $r->mother_suffix);

                echo "
                    <td>$child</td>
                    <td>{$r->birth_date}</td>
                    <td>{$r->birth_place}</td>
                    <td>$father</td>
                    <td>$mother</td>
                    <td>{$r->date}</td>
                    <td><span class='label {$statusClass}'>{$r->status}</span></td>
                    <td>
                    <center>
                        <a href='baptsimpdf.php?id={$r->id}' class='btn btn-generate btn-sm'>Generate PDF</a><br>
                        <a href='edit_baptism.php?id={$r->id}' class='btn btn-primary btn-sm'>Edit</a>
                        <a href='certificate.php?del={$r->id}&type=Baptism' class='btn btn-danger btn-sm'>Delete</a><br>
                        <a href='certificate.php?accept={$r->id}&type=Baptism' class='btn btn-success btn-sm'>Accept</a>
                        <a href='certificate.php?cancel={$r->id}&type=Baptism' class='btn btn-warning btn-sm'>Cancel</a><br>
                        <a href='' class='btn btn-dark-grey btn-sm'>View Details</a>
                        </center>
                    </td>
                ";
            }

            echo "</tr>";
            $cnt++;
        }
    } else {
        echo "<tr><td colspan='15' class='text-center'>No records found.</td></tr>";
    }
    ?>
  </tbody>
  </table>
  </div>

  </div>
</section>

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