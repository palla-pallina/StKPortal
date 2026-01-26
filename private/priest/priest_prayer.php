<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />

	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
	<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />
	<link href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">
	<link href="../../stylesheets/private/navbar.css" rel="stylesheet">
	<link rel="stylesheet" href="../../stylesheets/private/prayer_admin.css" />
	<link rel="stylesheet" href="../../stylesheets/private/priest_prayer.css" />

	<title>Priest - Prayer</title>
</head>

<body>

<!-- SIDEBAR -->
<section id="sidebar">
	<a href="#" class="label">
		<img src="santa krus logo.png" alt="Logo" class="logo" />
	</a>

	<ul class="side-menu top">
		<li><a href="../dashboard.php"><span class="text">Dashboard</span></a></li>
		<li><a href="../stk/filemanager.php"><span class="text">File Manager</span></a></li>
		<li><a href="../stk/announcement.php"><span class="text">Announcement</span></a></li>
		<li><a href="../event.php"><span class="text">Event</span></a></li>
		<li><a href="../stk/members.php"><span class="text">Members</span></a></li>
		<li><a href="../sacraments/certificate_req/certificate_request.php"><span class="text">Certificate Requests</span></a></li>
		<li><a href="../sacraments/sacrament_dashboard.php"><span class="text">Reservations</span></a></li>
		<li><a href="../donations.php"><span class="text">Donations</span></a></li>

		<!-- Prayer Section -->
		<li>
			<a href="prayer_admin.php">
				<span class="text">Prayer Requests</span>
			</a>
		</li>
		<li class="active">
			<a href="priest_prayer.php">
				<span class="text">Priest Prayer</span>
			</a>
		</li>
	</ul>
</section>
<!-- SIDEBAR END -->

<section id="content">
	<nav>
		<i class='bx bx-menu'></i>
		<div id="datetime"></div>
	</nav>

	<!-- PAGE BODY -->
	<section class="priest-prayer-section">
		<div class="prayer-controls">
			<div class="prayer-header-search">
				<h1>Prayers</h1>
				<div class="search-box">
					<input type="text" id="searchPrayers" placeholder="Search..." class="search-input" />
					<i class='bx bx-search'></i>
				</div>
				<div class="filter-dropdown-wrapper">
					<select id="prayerFilter" class="filter-dropdown">
						<option value="">All</option>
						<option value="published">Public</option>
						<option value="private">Private</option>
					</select>
					<i class='bx bx-chevron-down'></i>
				</div>
			</div>
			<div class="prayer-count">
				<span class="count-label">Total of prayers</span>
				<span class="count-value" id="prayerCount">0</span>
			</div>
		</div>

		<hr class="divider">

		<div class="prayer-requests-container">
			<div id="requestsGrid" class="requests-grid">
			</div>
		</div>
	</section>
</section>

<!-- MODAL POPUP -->
<div id="prayerModal" class="prayer-modal">
	<div class="modal-content">
		<button class="modal-close" onclick="closePrayerModal()">&times;</button>
		<div id="modalBody" class="modal-body">
		</div>
	</div>
</div>

<script>
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

// Load Prayers
(function(){
	const grid = document.getElementById('requestsGrid');
	const countElement = document.getElementById('prayerCount');
	const searchInput = document.getElementById('searchPrayers');
	const filterSelect = document.getElementById('prayerFilter');
	let allPrayers = [];

	function formatDate(dt){
		try{
			const d = new Date(dt);
			const opts = { year: 'numeric', month: 'short', day: 'numeric' };
			return d.toLocaleDateString(undefined, opts);
		} catch(e){ return dt; }
	}

	function openPrayerModal(prayer) {
		const modal = document.getElementById('prayerModal');
		const modalBody = document.getElementById('modalBody');

		const isAnon = (prayer.anonymous && parseInt(prayer.anonymous) === 1) || !prayer.name;
		const displayName = isAnon ? 'Anonymous' : (prayer.name || 'Anonymous');

		const intentMap = {
			'thanksgiving': 'Thanksgiving',
			'special': 'Special Intention',
			'soul': 'Soul'
		};
		
		const intentKey = prayer.intention ? String(prayer.intention).toLowerCase() : '';
		const mappedIntent = intentKey && intentMap[intentKey] ? intentMap[intentKey] : 'Not Specified';

		const dateStr = prayer.created_at ? formatDate(prayer.created_at) : 'N/A';

		const statusBadge = prayer.status ? `<span class="prayer-status-badge ${prayer.status}">${prayer.status === 'published' ? 'Published' : 'Private'}</span>` : '';

		modalBody.innerHTML = `
			<div class="prayer-details">
				<h2 class="prayer-detail-name">${displayName}</h2>
				<div class="prayer-detail-meta">
					<span class="prayer-detail-date">${dateStr}</span>
					<span class="prayer-detail-intent">${mappedIntent}</span>
					${statusBadge}
				</div>
				<div class="prayer-detail-text">
					${prayer.request_text ? prayer.request_text.replace(/\n/g, '<br>') : 'No prayer text provided.'}
				</div>
			</div>
		`;

		modal.style.display = 'flex';
	}

	function renderCard(prayer){
		const card = document.createElement('div');
		card.className = 'request-card';
		card.style.cursor = 'pointer';
		card.onclick = function() { openPrayerModal(prayer); };

		const header = document.createElement('div');
		header.className = 'card-header';

		const avatar = document.createElement('div');
		avatar.className = 'avatar';

		const isAnon = (prayer.anonymous && parseInt(prayer.anonymous) === 1) || !prayer.name;
		const displayName = isAnon ? 'Anonymous' : (prayer.name || 'Anonymous');

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

		const dateEl = document.createElement('div');
		dateEl.className = 'card-date';
		dateEl.textContent = prayer.created_at ? formatDate(prayer.created_at) : '';

		header.appendChild(avatar);
		
		const nameWrap = document.createElement('div');
		nameWrap.className = 'card-name-wrap';
		nameWrap.appendChild(nameEl);

		header.appendChild(nameWrap);
		header.appendChild(dateEl);

		const body = document.createElement('p');
		body.className = 'card-body';
		body.textContent = prayer.request_text || '';

		const footer = document.createElement('div');
		footer.className = 'card-footer';

		const intent = document.createElement('div');
		intent.className = 'card-intent';

		const intentMap = {
			'thanksgiving': 'Thanksgiving',
			'special': 'Special Intention',
			'soul': 'Soul'
		};
			
		const intentKey = prayer.intention ? String(prayer.intention).toLowerCase() : '';
		const mappedIntent = intentKey && intentMap[intentKey] ? intentMap[intentKey] : '';

		if (mappedIntent) {
			intent.textContent = mappedIntent;
			footer.appendChild(intent);
		}

		// Add status badge if private
		if (prayer.status === 'private') {
			const statusBadge = document.createElement('span');
			statusBadge.className = 'card-status-badge';
			statusBadge.textContent = 'Private';
			footer.appendChild(statusBadge);
		}

		card.appendChild(header);
		card.appendChild(body);
		card.appendChild(footer);

		return card;
	}

	function filterPrayers(searchTerm) {
		const statusFilter = filterSelect.value;
		const filtered = allPrayers.filter(prayer => {
			const name = (prayer.name || '').toLowerCase();
			const text = (prayer.request_text || '').toLowerCase();
			const term = searchTerm.toLowerCase();
			const matchesSearch = name.includes(term) || text.includes(term);
			const matchesStatus = !statusFilter || prayer.status === statusFilter;
			return matchesSearch && matchesStatus;
		});
		
		grid.innerHTML = '';
		if(filtered.length === 0) {
			grid.innerHTML = '<p style="grid-column:1/-1;text-align:center;color:#666;padding:2rem;">No prayer requests match your search or filter.</p>';
		} else {
			filtered.forEach(prayer => grid.appendChild(renderCard(prayer)));
		}
	}

	function loadPrayers(){
		fetch('../../Public/handle/get_all_prayers.php')
			.then(r => r.json())
			.then(data => {
				if(Array.isArray(data)){
					allPrayers = data;
					countElement.textContent = data.length;
					grid.innerHTML = '';
					if(data.length === 0){
						grid.innerHTML = '<p style="grid-column:1/-1;text-align:center;color:#666;padding:2rem;">No prayer requests available.</p>';
					} else {
						data.forEach(prayer => grid.appendChild(renderCard(prayer)));
					}
				}
			}).catch(err => {
				console.error('Failed to fetch requests', err);
				grid.innerHTML = '<p style="grid-column:1/-1;text-align:center;color:#666;">Failed to load prayer requests.</p>';
			});
	}

	// Search functionality
	searchInput.addEventListener('input', function() {
		filterPrayers(this.value);
	});

	// Filter functionality
	filterSelect.addEventListener('change', function() {
		filterPrayers(searchInput.value);
	});

	loadPrayers();
})();

// Close modal when clicking outside
window.closePrayerModal = function() {
	document.getElementById('prayerModal').style.display = 'none';
};

document.getElementById('prayerModal').addEventListener('click', function(event) {
	if (event.target === this) {
		this.style.display = 'none';
	}
});
</script>

</body>
</html>