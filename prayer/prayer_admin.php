<?php include 'db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="prayer.css">
	<title>Prayer Requests</title>
</head>
<body>

	<section id="sidebar">
		<a href="#" class="label">
			<img src="santa krus logo.png" alt="Logo" class="logo">
		</a>
		<ul class="side-menu top">
			<li><a href="admin.php"><span class="text">Dashboard</span></a></li>
			<li><a href="#"><span class="text">File Manager</span></a></li>
			<li><a href="#"><span class="text">Announcement</span></a></li>
			<li><a href="#"><span class="text">Event</span></a></li>
			<li><a href="#"><span class="text">Members</span></a></li>
			<li><a href="#"><span class="text">Certificates</span></a></li>
			<li><a href="#"><span class="text">Bookings</span></a></li>
			<li><a href="#"><span class="text">Donations</span></a></li>
			<li class="active"><a href="prayer_admin.php"><span class="text">Prayer Request</span></a></li>
		</ul>
		<ul class="side-menu">
			<li><a href="#" class="logout"><span class="text">Logout</span></a></li>
		</ul>
	</section>

	<section id="content">
		<nav>
			<i class='bx bx-menu'></i>
			<div id="datetime"></div>
			<a href="#" class="nav-link" id="toggle-sidebar"></a>
			<div class="profile">
				<span class="greeting">Admin</span>
				<a href="#" class="profile-link"><i class='bx bx-user'></i></a>
			</div>
		</nav>

		<main>
			<section id="prayer-request" class="content-section active">
				<div class="main-content">
					<div class="prayer-controls">
						<h1>PRAYER REQUESTS</h1>
						<div class="search-sort-group">
							<div class="search-box">
								<input type="text" placeholder="Search...">
								<i class='bx bx-search'></i>
							</div>
							<button class="sort-button">
								Sort by <i class='bx bx-chevron-down'></i>
							</button>
						</div>
					</div>

					<hr class="divider">

					<div class="date-filter">
						<label for="filter-date"><strong>DATE</strong></label>
						<input type="date" id="filter-date" name="filter-date">
					</div>

					<?php
					if (isset($_GET['delete'])) {
						$id = intval($_GET['delete']);
						$conn->query("DELETE FROM prayer_requests WHERE prayreq_id = $id");
						header("Location: prayer_admin.php");
						exit;
					}

					$result = $conn->query("SELECT * FROM prayer_requests ORDER BY created_at DESC");
					?>

					<div class="prayer-table-container">
						<table class="prayer-table">
							<thead>
								<tr>
									<th>ID</th>
									<th>Name</th>
									<th>Username</th>
									<th>Email</th>
									<th>Intention</th>
									<th>Visibility</th>
									<th>Time</th> <!-- New column -->
									<th>Date</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
							<?php if ($result->num_rows > 0): ?>
								<?php while($row = $result->fetch_assoc()): ?>
								<tr>
									<td><?= $row['prayreq_id'] ?></td>
									<td><?= htmlspecialchars($row['prayer_name']) ?></td>
									<td><?= htmlspecialchars($row['prayer_username']) ?></td>
									<td><?= htmlspecialchars($row['email']) ?></td>
									<td><?= htmlspecialchars($row['prayer_intention']) ?></td>
									<td><?= $row['visibility_name'] ?></td>
									<td><?= date("h:i A", strtotime($row['created_at'])) ?></td> <!-- Time only -->
									<td><?= date("Y-m-d", strtotime($row['created_at'])) ?></td> <!-- Date only -->
									<td class="action-column">
										<button class="btn-delete"
											onclick="if(confirm('Delete this prayer request?')) { window.location='prayer_admin.php?delete=<?= $row['prayreq_id'] ?>'; }">
											<i class='bx bx-trash'></i>
										</button>
									</td>
								</tr>
								<?php endwhile; ?>
							<?php else: ?>
								<tr><td colspan="9">No prayer requests found.</td></tr>
							<?php endif; ?>
							</tbody>
						</table>
					</div>

					<div class="pagination-container">
					  <div class="pagination-bar">
					    <div class="entry-info">0 – 0 of 0 Entries</div>
					    <div class="pagination-nav">
					      <button class="page-btn">← Previous</button>
					      <button class="page-btn">Next →</button>
					    </div>
					  </div>
					</div>
				</div>
			</section>
		</main>
	</section>

	<script>
		const menuIcon = document.querySelector('.bx-menu');
		const sidebar = document.querySelector('#sidebar');
		menuIcon.addEventListener('click', () => sidebar.classList.toggle('hide'));

		function updateDateTime() {
			const now = new Date();
			const options = {
			  hour: '2-digit',
			  minute: '2-digit',
			  hour12: true
			};
			const formattedDate = now.toLocaleDateString();
			const formattedTime = now.toLocaleTimeString([], options);
			document.getElementById('datetime').textContent = `${formattedDate} | ${formattedTime}`;
		}
		setInterval(updateDateTime, 1000);
		updateDateTime();
	</script>
</body>
</html>
