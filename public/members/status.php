<?php
require_once '../connection.php';

session_start();
$user_id = $_SESSION['user_id'];

/*--------- USER ACCESS RESTRICT START
- restrict access to registered users only */
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
    echo "<script>
            window.history.back();
          </script>";
    exit();
}
$user_name = $_SESSION['givenname'] ?? null; 
// retrieves logged in user's username
$user_id = $_SESSION['user_id']; 
// retrieves logged in user ID
/*--------- USER ACCESS RESTRICT END */

$user_id = (int)$_SESSION['user_id'];

/*--------- NOTIFICATION HANDLING START
- for notification icon at navbar
- retrieves all notifications belonging to logged in user */
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
/*--------- NOTIFICATION HANDLING END */
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Santa Krus Parish</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../../stylesheets/public/user_footer_navbar.css" />
  <link rel="stylesheet" href="../../stylesheets/public/status.css" />
</head>
<body>

  <!-------------------- NAVBAR -------------------->
  <header>
      <nav class="navbar">
          <ul class="nav-links">
              <li><a href="#"><img src="../../stylesheets/assets/santa krus logo.png" alt="Parish Logo" class="logo" /></a></li>
              <li><a href="../homepage.php">Home</a></li>
              <li><a href="../history.php">History</a></li>
              <li><a href="../events.php">News & Events</a></li>
              <!--<li><a href="sacraments.php">Sacraments</a></li>-->

              <li class="li-dropdown">
                  <a href="#">Parish <i class="fa-solid fa-angle-down"></i></a>
                  <ul class="dropdown">
                      <li style="border-bottom: 1px solid var(--yellow);"><a href="../sacraments.php">Sacraments</a></li>
                      <li><a href="../services.php">Services</a></li>
                  </ul>
              </li>
              
              <li><a href="../donation.php">Donate</a></li>
              <li><a href="../prayer_page.php">Prayer Request</a></li>
              <li><a href="../contact.php">Contact</a></li>

              <?php
              if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
                  // USER NOT LOGGED IN → Show Login button
                  echo '<li><a href="login.php" id="login-btn" style="color:white;"><i class="fa-regular fa-user"></i>   Log In</a></li>';
              } else {
                  // USER LOGGED IN → Show Username + Dropdown
                  ?>
              <p style="color:white; padding-left: 17px; padding-right: 17px;">|</p>
                  <li class="li-dropdown">
                      <a href="#"><?= htmlspecialchars($user_name) ?></a>
                      <ul class="dropdown">
                          <li style="border-bottom: 1px solid var(--yellow);"><a href="../members/status.php">Status</a></li>
                        <li><a href="../logout.php" a>Logout</a></li>
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
  <!----------------- NAVBAR END----------------->
  
  <div class="container py-4">

    <div class="page-header">
      <h4 class="mb-0">My Reservations & Parish Services</h4>
      <span class="badge" id="countBadge">0</span>
    </div>

    <ul class="nav mb-3" id="statusTabs">
      <?php
        $tabs = ['All','Pending','Accepted','Done','Declined','Cancelled'];
        foreach ($tabs as $t) {
          $active = ($t === 'All') ? 'active' : '';
          echo "<li class='nav-item'>
                  <button class='nav-link $active' data-status='$t' type='button'>$t</button>
                </li>";
        }
      ?>
    </ul>

    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>Type</th>
                <th>For / Details</th>
                <th>Date</th>
                <th>Time</th>
                <th>Status</th>
                <th class="text-end">Action</th>
              </tr>
            </thead>
            <tbody id="rows">
              <tr><td colspan="6" class="text-center text-muted py-4">Loading…</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>

  <!-- View Modal -->
  <div class="modal" id="viewModal" aria-hidden="true">
    <div class="modal__backdrop" data-close="true"></div>
    <div class="modal__dialog" role="dialog" aria-modal="true" aria-labelledby="viewTitle">
      <div class="modal__header">
        <div class="modal__title" id="viewTitle">Details</div>
        <button type="button" class="modal__close" data-close="true" aria-label="Close">×</button>
      </div>
      <div class="modal__body">
        <dl class="row mb-0" id="modalDetails"></dl>
      </div>
    </div>
  </div>
  
  <!-- Reschedule Request Modal (Custom) -->
  <div class="modal" id="reschedModal" aria-hidden="true">
    <div class="modal__backdrop" data-close="true"></div>

    <div class="modal__dialog" role="dialog" aria-modal="true" aria-labelledby="reschedTitle">
      <div class="modal__header">
        <div class="modal__title" id="reschedTitle">Request Reschedule</div>
        <button type="button" class="modal__close" data-close="true" aria-label="Close">×</button>
      </div>

      <div class="modal__body">
        <div class="mb-3">
          <label class="form-label">What do you want to reschedule?</label>
          <select class="form-select" id="reschedType">
            <option value="Service">Main Schedule (Date/Time)</option>
            <option value="Rehearsal">Rehearsal (Date/Time)</option>
            <option value="Canon Interview" class="wedding-only">Canon Interview (Date/Time)</option>
            <option value="Pre-Cana" class="wedding-only">Pre-Cana Seminar (Date/Time)</option>
          </select>
        </div>

        <div class="mb-2">
          <div class="text-muted small">Current</div>
          <div id="reschedCurrent" class="fw-semibold">—</div>
        </div>

        <div class="row g-2 mb-3">
          <div class="col-6">
            <label class="form-label">Requested Date</label>
            <input type="date" class="form-control" id="reschedDate">
          </div>
          <div class="col-6">
            <label class="form-label">Requested Time</label>
            <input type="time" class="form-control" id="reschedTime">
          </div>
        </div>

        <div class="mb-2">
          <label class="form-label">Reason</label>
          <textarea class="form-control" id="reschedReason" rows="3" placeholder="Write your reason..."></textarea>
        </div>
      </div>

      <div class="modal__footer">
        <button type="button" class="btn btn-outline-secondary" data-close="true">Cancel</button>
        <button type="button" class="btn btn-primary" id="submitReschedBtn">Submit Request</button>
      </div>
    </div>
  </div>

    <!-- Cancel Confirmation Modal (Custom) -->
  <div class="modal" id="cancelModal" aria-hidden="true">
    <div class="modal__backdrop" data-close="true"></div>

    <div class="modal__dialog modal__dialog--sm"
        role="dialog"
        aria-modal="true"
        aria-labelledby="cancelTitle">

      <div class="modal__header">
        <div class="modal__title text-danger" id="cancelTitle">
          Confirm Cancellation
        </div>
        <button type="button"
                class="modal__close"
                data-close="true"
                aria-label="Close">×</button>
      </div>

      <div class="modal__body">
        <p class="mb-3">
          Please select a reason for requesting cancellation:
        </p>

        <div class="mb-3">
          <select class="form-select" id="cancelReason">
            <option value="">-- Select reason --</option>
            <option value="Schedule conflict">Schedule conflict</option>
            <option value="Personal reasons">Personal reasons</option>
            <option value="Health-related concern">Health-related concern</option>
            <option value="Incomplete requirements">Incomplete requirements</option>
            <option value="Other">Other (please specify)</option>
          </select>
        </div>

        <div class="mb-2 d-none" id="otherReasonWrapper">
          <textarea
            class="form-control"
            id="otherReason"
            rows="3"
            placeholder="Please specify your reason"></textarea>
        </div>

        <small class="text-muted">
          This request will be sent to the parish administrator for review.
        </small>
      </div>

      <div class="modal__footer">
        <button type="button"
                class="btn btn-outline-secondary"
                data-close="true">
          Cancel
        </button>
        <button type="button"
                class="btn btn-danger"
                id="confirmCancelBtn">
          Submit Cancellation Request
        </button>
      </div>
    </div>
  </div>


<script>
  // ========= Modal helper =========
  function createModal(modalId){
    const el = document.getElementById(modalId);
    if (!el) throw new Error(`Modal not found: ${modalId}`);

    let lastActive = null;

    function open(){
      lastActive = document.activeElement;
      el.classList.add('is-open');
      el.setAttribute('aria-hidden', 'false');
      document.body.classList.add('modal-open');

      const closeBtn = el.querySelector('[data-close="true"]');
      closeBtn?.focus();
    }

    function close(){
      el.classList.remove('is-open');
      el.setAttribute('aria-hidden', 'true');
      document.body.classList.remove('modal-open');

      if (lastActive && typeof lastActive.focus === 'function') lastActive.focus();
      lastActive = null;
    }

    el.addEventListener('click', (e) => {
      if (e.target.matches('[data-close="true"]')) close();
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && el.classList.contains('is-open')) close();
    });

    return { open, close, el };
  }

  // ========= Elements =========
  const rowsEl = document.getElementById('rows');
  const countBadge = document.getElementById('countBadge');
  const tabs = document.getElementById('statusTabs');

  const viewModal = createModal('viewModal');
  const cancelModal = createModal('cancelModal');
  const reschedModal = createModal('reschedModal');

  const modalDetails = document.getElementById('modalDetails');

  // Cancel modal inputs
  const cancelReasonSelect = document.getElementById('cancelReason');
  const otherReasonWrapper = document.getElementById('otherReasonWrapper');
  const otherReasonInput = document.getElementById('otherReason');
  const confirmCancelBtn = document.getElementById('confirmCancelBtn');

  let pendingCancelPayload = null;

  // Resched modal inputs
  const reschedType = document.getElementById('reschedType');
  const reschedCurrent = document.getElementById('reschedCurrent');
  const reschedDate = document.getElementById('reschedDate');
  const reschedTime = document.getElementById('reschedTime');
  const reschedReason = document.getElementById('reschedReason');
  const submitReschedBtn = document.getElementById('submitReschedBtn');

  let pendingReschedPayload = null;

  // ========= Helpers =========
  function safe(v){ return (v === null || v === undefined || v === '') ? '-' : String(v); }

  function formatDate(dateStr){
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    if (isNaN(d)) return dateStr;
    return d.toLocaleDateString('en-US',{ year:'numeric', month:'long', day:'numeric' });
  }

  function formatTime(timeStr){
    if (!timeStr) return '—';
    const parts = String(timeStr).split(':');
    if (parts.length < 2) return timeStr;

    let hours = parseInt(parts[0], 10);
    const minutes = parts[1];
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12 || 12;
    return `${hours}:${minutes} ${ampm}`;
  }

  function formatDateTime(dt){
    if (!dt) return '—';
    const d = new Date(dt);
    if (isNaN(d)) return dt;
    return d.toLocaleString('en-US', { year:'numeric', month:'short', day:'numeric', hour:'numeric', minute:'2-digit', hour12:true });
  }

  function statusBadge(status){
    const s = (status || '').toLowerCase();
    if (s === 'pending') return '<span class="badge text-bg-warning">Pending</span>';
    if (s === 'accepted') return '<span class="badge text-bg-primary">Accepted</span>';
    if (s === 'done') return '<span class="badge text-bg-success">Done</span>';
    if (s === 'declined') return '<span class="badge text-bg-danger">Declined</span>';
    if (s === 'cancel requested') return '<span class="badge text-bg-secondary">Cancel Requested</span>';
    if (s === 'cancelled') return '<span class="badge text-bg-secondary">Cancelled</span>';
    if (s === 'reschedule requested') return '<span class="badge text-bg-secondary">Reschedule Requested</span>';
    return '<span class="badge text-bg-secondary">' + safe(status) + '</span>';
  }

  // ========= Cancel Reason "Other" toggle =========
  cancelReasonSelect.addEventListener('change', () => {
    if (cancelReasonSelect.value === 'Other') {
      otherReasonWrapper.classList.remove('d-none');
    } else {
      otherReasonWrapper.classList.add('d-none');
      otherReasonInput.value = '';
    }
  });

  // ========= Reschedule "Current" display =========
  function getCurrentForType(r, type){
    if (type === 'Service') return { d: r.service_date, t: r.service_time };
    if (type === 'Rehearsal') return { d: r.rehearsal_date, t: r.rehearsal_time };
    if (type === 'Canon Interview') return { d: r.canon_interview_date, t: r.canon_interview_time };
    if (type === 'Pre-Cana') return { d: r.precana_semi_date, t: r.precana_semi_time };
    return { d: null, t: null };
  }

  function refreshReschedCurrent(){
    if (!pendingReschedPayload) return;
    const { d, t } = getCurrentForType(pendingReschedPayload, reschedType.value);
    reschedCurrent.textContent = `${formatDate(d)} • ${formatTime(t)}`;
  }
  reschedType.addEventListener('change', refreshReschedCurrent);

  // ========= Render =========
  function render(data){
    countBadge.textContent = data.length;

    if (!data.length) {
      rowsEl.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">You have no reservations.</td></tr>`;
      return;
    }

    rowsEl.innerHTML = data.map(r => {
      const date = formatDate(r.service_date);
      const time = formatTime(r.service_time);

      const service = (r.service_type || '').toLowerCase();
      const stat = (r.status || '').toLowerCase();

      const canCancel = ['pending','accepted'].includes(stat);
      const canReschedule =
        stat === 'accepted' &&
        ['baptism','first communion','confirmation','wedding'].includes(service);

      return `
        <tr>
          <td>
            <div class="fw-semibold">${safe(r.service_type)}</div>
            <div class="text-muted small">#${safe(r.request_id)}${r.subtype ? ' • ' + safe(r.subtype) : ''}</div>
          </td>
          <td>${safe(r.primary_name)}</td>
          <td>${service === 'certificate request' ? '—' : date}</td>
          <td>${service === 'certificate request' ? '—' : time}</td>
          <td>${statusBadge(r.status)}</td>
          <td class="text-end">
            <button type="button" class="btn btn-sm btn-outline-secondary"
              data-view='${encodeURIComponent(JSON.stringify(r))}'>View</button>

            ${canReschedule ? `
              <button type="button" class="btn btn-sm btn-outline-primary ms-2"
                data-resched='${encodeURIComponent(JSON.stringify({
                  service_type: r.service_type,
                  request_id: r.request_id,
                  service_date: r.service_date,
                  service_time: r.service_time,
                  rehearsal_date: r.rehearsal_date,
                  rehearsal_time: r.rehearsal_time,
                  canon_interview_date: r.canon_interview_date,
                  canon_interview_time: r.canon_interview_time,
                  precana_semi_date: r.precana_semi_date,
                  precana_semi_time: r.precana_semi_time
                }))}'>Reschedule</button>
            ` : ``}

            ${canCancel ? `
              <button type="button" class="btn btn-sm btn-outline-danger ms-2"
                data-cancel='${encodeURIComponent(JSON.stringify({
                  service_type: r.service_type,
                  request_id: r.request_id
                }))}'>Cancel</button>
            ` : ``}
          </td>
        </tr>
      `;
    }).join('');
  }

  // ========= Load =========
  async function load(status='All'){
    rowsEl.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">Loading…</td></tr>`;
    const res = await fetch(`tracking_fetch.php?status=${encodeURIComponent(status)}`);
    const json = await res.json();
    if (!res.ok) {
      rowsEl.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">${safe(json.error)}</td></tr>`;
      return;
    }
    render(json.data || []);
  }

  tabs.addEventListener('click', (e) => {
    const btn = e.target.closest('button[data-status]');
    if (!btn) return;
    tabs.querySelectorAll('.nav-link').forEach(x => x.classList.remove('active'));
    btn.classList.add('active');
    load(btn.dataset.status);
  });

  // ========= Row actions (RESCHEDULE → CANCEL → VIEW) =========
  rowsEl.addEventListener('click', (e) => {

    // 1) Reschedule
    const resBtn = e.target.closest('button[data-resched]');
    if (resBtn) {
      pendingReschedPayload = JSON.parse(decodeURIComponent(resBtn.dataset.resched));

      const isWedding = (pendingReschedPayload.service_type || '').toLowerCase() === 'wedding';
      document.querySelectorAll('#reschedType .wedding-only').forEach(opt => {
        opt.hidden = !isWedding;
      });

      reschedType.value = 'Service';
      reschedDate.value = '';
      reschedTime.value = '';
      reschedReason.value = '';
      refreshReschedCurrent();

      reschedModal.open();
      return;
    }

    // 2) Cancel
    const cancelBtn = e.target.closest('button[data-cancel]');
    if (cancelBtn) {
      pendingCancelPayload = JSON.parse(decodeURIComponent(cancelBtn.dataset.cancel));
      cancelModal.open();
      return;
    }

    // 3) View
    const viewBtn = e.target.closest('button[data-view]');
    if (!viewBtn) return;

    const r = JSON.parse(decodeURIComponent(viewBtn.dataset.view));
    const items = [
      ['Type', r.service_type],
      ['Request ID', r.request_id],
      ['Subtype', r.subtype],
      ['For / Details', r.primary_name],
    ];

    if ((r.service_type || '').toLowerCase() !== 'certificate request') {
      items.push(['Date', formatDate(r.service_date)]);
      items.push(['Time', formatTime(r.service_time)]);
    } else if (r.pickup_date) {
      items.push(['Pickup Date', formatDate(r.pickup_date)]);
    }

    items.push(['Status', r.status]);

    if ((r.status || '').toLowerCase() === 'declined') {
      items.push(['Decline Reason', r.decline_reason && r.decline_reason.trim() ? r.decline_reason : '—']);
    }

    items.push(['Created At', formatDateTime(r.created_at)]);

    modalDetails.innerHTML = items.map(([k,v]) => `
      <dt class="col-sm-4 text-muted">${k}</dt>
      <dd class="col-sm-8">${safe(v)}</dd>
    `).join('');

    viewModal.open();
  });

  // ========= Submit cancel =========
  confirmCancelBtn.addEventListener('click', () => {
    if (!pendingCancelPayload) return;

    const selectedReason = cancelReasonSelect.value;
    const otherReason = otherReasonInput.value.trim();

    if (!selectedReason) { alert('Please select a reason.'); return; }
    if (selectedReason === 'Other' && !otherReason) { alert('Please specify your reason.'); return; }

    const finalReason = (selectedReason === 'Other') ? otherReason : selectedReason;

    const fd = new FormData();
    fd.append('service_type', pendingCancelPayload.service_type);
    fd.append('request_id', pendingCancelPayload.request_id);
    fd.append('cancel_reason', finalReason);

    confirmCancelBtn.disabled = true;
    confirmCancelBtn.textContent = 'Submitting...';

    fetch('cancel_request.php', { method:'POST', body: fd })
      .then(r => r.json().then(j => ({ ok:r.ok, j })))
      .then(({ ok, j }) => {
        if (!ok) { alert(j.details ? `${j.error}\n\n${j.details}` : j.error); return; }
        cancelModal.close();

        cancelReasonSelect.value = '';
        otherReasonInput.value = '';
        otherReasonWrapper.classList.add('d-none');

        const activeTab = tabs.querySelector('.nav-link.active')?.dataset.status || 'All';
        load(activeTab);
      })
      .catch(() => alert('Network error. Please try again.'))
      .finally(() => {
        confirmCancelBtn.disabled = false;
        confirmCancelBtn.textContent = 'Submit Cancellation Request';
        pendingCancelPayload = null;
      });
  });

  // ========= Submit reschedule =========
  submitReschedBtn.addEventListener('click', () => {
    if (!pendingReschedPayload) return;

    const type = reschedType.value;
    const date = reschedDate.value;
    const time = reschedTime.value;
    const reason = reschedReason.value.trim();

    if (!date || !time) { alert('Please select requested date and time.'); return; }
    if (!reason) { alert('Please provide a reason.'); return; }

    const fd = new FormData();
    fd.append('service_type', pendingReschedPayload.service_type);
    fd.append('request_id', pendingReschedPayload.request_id);
    fd.append('change_type', type);
    fd.append('requested_date', date);
    fd.append('requested_time', time);
    fd.append('reason', reason);

    submitReschedBtn.disabled = true;
    submitReschedBtn.textContent = 'Submitting...';

    fetch('reschedule_request.php', { method:'POST', body: fd })
      .then(r => r.json().then(j => ({ ok:r.ok, j })))
      .then(({ ok, j }) => {
        if (!ok) { alert(j.details ? `${j.error}\n\n${j.details}` : j.error); return; }
        reschedModal.close();

        const activeTab = tabs.querySelector('.nav-link.active')?.dataset.status || 'All';
        load(activeTab);
      })
      .catch(() => alert('Network error. Please try again.'))
      .finally(() => {
        submitReschedBtn.disabled = false;
        submitReschedBtn.textContent = 'Submit Request';
        pendingReschedPayload = null;
      });
  });

  // initial load
  load('All');
</script>


<!-- FOOTER -->
<footer class="custom-footer">

  <div class="footer-left">
    <h2>Santa Krus Parish</h2>
    <p>Serving the community of Bagumbong, Caloocan with faith and love.</p>
    <p><b>Email Address</b><br>stakrusparish@gmail.com</p>
    <p><b>Contact Number</b><br>(02) 8984-3933</p>

    <ul class="social-icons">
      <li><a href="https://www.facebook.com/DioNova.StaKrusParish"><span class="fab fa-facebook-f"></span></a></li>
    </ul>
  </div>
  <!-- footer-left was not closed before -->

  <!-- LOCATION SECTION (Center) -->
  <div class="footer-location">
    <h3>Location</h3>
    <p>205 Deparo Rd, Caloocan City, 1420 Metro Manila</p>

    <div class="map-container">
      <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3858.6890401821715!2d121.00261717591935!3d14.737954572658468!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b7bb4e6b1b65%3A0x424ba9f332da9e86!2s205%20Deparo%20Rd%2C%20Caloocan%2C%201420%20Metro%20Manila!5e0!3m2!1sen!2sph!4v1731985610001"
        width="100%"
        height="250"
        style="border:0;"
        allowfullscreen=""
        loading="lazy">
      </iframe>
    </div>
  </div>

  <!-- CONTACT SECTION -->
  <div class="footer-contact">
    <?php if (!empty($message)) : ?>
        <div class="alert alert-info text-center">
            <?= htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <h3>Contact Us</h3>
    <form  method="POST">
      <input type="text" name="name" placeholder="Name" required>
      <input type="email" name="email" placeholder="Email Address" required>
      <input type="text" name="subject" placeholder="Subject" required>
      <textarea name="message" placeholder="Message" required></textarea>
      <button type="submit">SUBMIT</button>
    </form>
  </div>

</footer>
</body>
</html>