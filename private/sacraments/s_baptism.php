<?php
$conn = new mysqli('localhost', 'root', '', 'test_tree_db');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
require_once 'connection.php';
$message = "";
session_start();

// ===============================
// UPDATE STATUS + NOTIFICATION
// ===============================
if (isset($_GET['ajax']) && isset($_GET['status']) && isset($_GET['id'])) {

    $id     = intval($_GET['id']);
    $status = $_GET['status']; // Accepted | Cancelled
    $table  = "baptisms";

    // 1. Get reservation_user_id
    $stmt = $conn->prepare("SELECT reservation_user_id FROM baptisms WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result  = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();

    if (!$booking) {
        http_response_code(400);
        echo "Booking not found";
        exit;
    }

    $user_id = $booking['reservation_user_id'];

    // 2. Update status
    $stmt = $conn->prepare("UPDATE baptisms SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();

    // 3. Insert notification ONLY if Accepted
    if ($status === "Accepted") {
        $title   = "Baptism Request Accepted";
        $message = "Your baptism request has been accepted. Please proceed with your donation.";
        $type    = "donation_reminder";

        $stmt = $conn->prepare("
            INSERT INTO notifications (user_id, title, message, type, is_read, created_at)
            VALUES (?, ?, ?, ?, 0, NOW())
        ");
        $stmt->bind_param("isss", $user_id, $title, $message, $type);
        $stmt->execute();
        $stmt->close();
    }

    echo "success";
    exit;
}



// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../../public/login.php");
    exit();
}
$admin_name = !empty($_SESSION['givenname']) ? $_SESSION['givenname'] : $_SESSION['username'];

// Handle delete if del param is set
if (isset($_GET['del'])) {
    $uid = intval($_GET['del']);
    $sql = "DELETE FROM users WHERE user_id = :user_id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':user_id', $uid, PDO::PARAM_INT);
    $query->execute();

    echo "<script>alert('Deleted!');</script>";
    echo "<script>window.location.href='s_baptism.php';</script>";
    exit();
}

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
<link href='../../stylesheets/private/sacraments.css' rel='stylesheet'>
<title>StKPortal | Baptism Reservations</title>

</head>

<body>

<!--------------------------------------- SIDEBAR ------------------------------------------->
<section id="sidebar" <?php if ($hideSidebar) echo 'class="hide"'; ?>>
    <a href="#" class="label">
        <img src="../santa krus logo.png" alt="Logo" class="logo" />
    </a>
    <ul class="side-menu top">
        <li><a href="../dashboard.php"><span class="text">Dashboard</span></a></li>
        <li><a href="filemanager.php"><span class="text">File Manager</span></a></li>
        <li><a href="announcement.php"><span class="text">Announcement</span></a></li>
        <li><a href="#"><span class="text">Event</span></a></li> <!-- N/A -->
        <li ><a href="#"><span class="text">Members</span></a></li>
        <li><a href="../certificate/certificate.php"><span class="text">Certificates</span></a></li>
        <li class="active"><a href="#"><span class="text">Reservations</span></a></li>
        <li><a href="../donations.php" data-section="dashboard"><span class="text">Donations</span></a></li>
        <li><a href="../prayer_admin.php"><span class="text">Prayer Requests</span></a></li>
    </ul>
    <ul class="side-menu">
        <li><a href="../logout.php" class="logout"><span class="text">Logout</span></a></li>
    </ul>
</section>
<!------------------------------------- SIDEBAR END ----------------------------------------->

<section id="content">

<!--------------------------------------- NAVBAR -------------------------------------------->
<nav>
  <i class='bx bx-menu'></i>
  <div id="datetime"></div>
  <div class="profile">
    <span class="greeting"><?php echo htmlspecialchars($admin_name); ?></span>
  </div>
</nav>
<!------------------------------------ NAVBAR END ------------------------------------------>

<!--------------------------------------- MAIN --------------------------------------------->
<div class="main-content">

<div class="breadcrumb">
    <a href="s_dashboard.php">Reservations Dashboard</a><span>Baptism</span>
</div>
<div class="reservations-header">
<h1>Baptism Reservations</h1>
<div class="reservations-controls">
<div class="search-box">
<form method="POST" action="search.php" class="form-inline">
  <input type="text" name="query" placeholder="Search by ID, First or Last Name" class="form-control" />
  <button type="submit" class="btn btn-default">Search</button>
</form>
</div>
<a href="s_baptism_insert.php">
    <button class="add-btn" id="addReservationBtn">
    <i class='bx bx-plus'></i>
    </button>
</a>
</div>
</div>

<hr class="divider">
<div class="sacraments-table-container">
  <table class="sacraments-table">

    <thead>
    <tr>
        <th>#</th>
        <th>Type</th>
        <th>Date</th>
        <th>Time</th>
        <th>Child</th>
        <th>Parents</th>
        <th>Contact</th>
        <th>Status</th>
        <th class="confirm-column">Confirm</th>
        <th class="action-column">Action</th>
    </tr>
    </thead>

    <tbody>
      <?php
      try {
          $sql = "SELECT * FROM baptisms";
          $query = $dbh->prepare($sql);
          $query->execute();
          $results = $query->fetchAll(PDO::FETCH_OBJ);
          $cnt = 1;

          if ($query->rowCount() > 0) {
              foreach ($results as $result) {
                  ?>
                    <tr>
                    <td><?php echo htmlentities($result->id); ?></td>
                    <td><?php echo htmlentities($result->baptism_type); ?></td>
                    <td><?php echo htmlentities($result->date); ?></td>
                    <td><?php echo htmlentities($result->time); ?></td>

                    <td>
                        <?php echo htmlentities($result->child_firstname); ?>
                        <?php echo htmlentities($result->child_middlename); ?>
                        <?php echo htmlentities($result->child_lastname); ?>
                        <?php echo htmlentities($result->child_suffix); ?>
                    </td>

                    <td>
                        <?php echo htmlentities($result->mother_firstname); ?>
                        <?php echo htmlentities($result->mother_middlename); ?>
                        <?php echo htmlentities($result->mother_lastname); ?>
                        &
                        <?php echo htmlentities($result->father_firstname); ?>
                        <?php echo htmlentities($result->father_middlename); ?>
                        <?php echo htmlentities($result->father_lastname); ?>
                        <?php echo htmlentities($result->father_suffix); ?>
                    </td>

                    <td><?php echo htmlentities($result->contact); ?></td>

                    <!-- ✅ STATUS -->
                    <td class="
                        <?php
                        if ($result->status === 'Accepted') echo 'status-verified';
                        elseif ($result->status === 'Cancelled') echo 'status-unverified';
                        else echo 'status-pending';
                        ?>
                    ">
                        <?php echo htmlentities($result->status); ?>
                    </td>

                    <!-- ✅ CONFIRM BUTTONS -->
                    <td class="confirm-column">
                        <button class="btn-approve"
                                data-id="<?php echo $result->id; ?>"
                                title="Accept">
                        <i class="glyphicon glyphicon-ok"></i>
                        </button>

                        <button class="btn-reject"
                                data-id="<?php echo $result->id; ?>"
                                title="Reject">
                        <i class="glyphicon glyphicon-remove"></i>
                        </button>
                    </td>

                    <!-- ✅ ACTION BUTTON -->
                    <td class="action-column">
                        <div class="action-buttons">
                        <a href="s_baptism_view.php?id=<?php echo $result->id; ?>" class="btn-view">
                            <i class="glyphicon glyphicon-file"></i>
                        </a>

                        <a href="schedule.php?id=<?php echo $result->id; ?>" class="btn-schedule">
                            <i class="glyphicon glyphicon-calendar"></i>
                        </a>

                        <a href="s_baptism_update.php?id=<?php echo $result->id; ?>" class="btn-edit">
                            <i class="glyphicon glyphicon-pencil"></i>
                        </a>

                        <a href="s_baptism.php?del=<?php echo $result->id; ?>"
                            onclick="return confirm('Are you sure?')"
                            class="btn-delete">
                            <i class="glyphicon glyphicon-trash"></i>
                        </a>
                        </div>
                    </td>
                    </tr>

                  <?php
                  $cnt++;
              }
          } else {
              echo '<tr><td colspan="12">No records found.</td></tr>';
          }
      } catch (PDOException $e) {
          echo "<tr><td colspan='12'>Error: " . $e->getMessage() . "</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>
    </section>
<!----------------------------------- MAIN END ------------------------------------------>

<script>
document.addEventListener("click", function(e) {

  if (e.target.closest(".btn-approve")) {
      const btn = e.target.closest(".btn-approve");
      updateStatus(btn.dataset.id, "Accepted");
  }

  if (e.target.closest(".btn-reject")) {
      const btn = e.target.closest(".btn-reject");
      updateStatus(btn.dataset.id, "Cancelled");
  }

});


function updateStatus(id, status) {

    if (!confirm("Are you sure you want to " + status + " this request?")) return;

    const url = `s_baptism.php?ajax=1&id=${id}&status=${status}`;

    fetch(url)
        .then(res => res.text())
        .then(data => {
            if (data.trim() === "success") {
                alert("Status updated successfully!");
                location.reload();
            } else {
                alert("Update failed: " + data);
            }
        })
        .catch(err => {
            console.error(err);
            alert("Server error.");
        });
}
</script>


<script>
const allSideMenu = document.querySelectorAll('#sidebar .side-menu.top li a'); // sidebar effect

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