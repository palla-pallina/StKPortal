<?php
// tracking_fetch.php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['error' => 'Unauthorized']);
  exit;
}

$userId = (int)$_SESSION['user_id'];
$status = $_GET['status'] ?? 'All';

$allowed = ['All','Pending','Accepted','Done','Declined','Cancelled','Reschedule Requested'];

if (!in_array($status, $allowed, true)) {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid status']);
  exit;
}

// DB connection (EDIT THESE)
$dsn = "mysql:host=localhost;dbname=test_tree_db;charset=utf8mb4";
$dbUser = "root";
$dbPass = "";

try {
  $pdo = new PDO($dsn, $dbUser, $dbPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);

  // Build WHERE dynamically 
  $whereStatus = "";
  if ($status !== 'All') {
    if ($status === 'Pending') {
      $whereStatus = " AND (x.status = 'Pending' OR x.status = 'Cancel Requested') ";
    } elseif ($status === 'Accepted') {
      $whereStatus = " AND (x.status = 'Accepted' OR x.status = 'Reschedule Requested') ";
    } elseif ($status === 'Cancelled') {
      $whereStatus = " AND x.status = 'Cancelled' ";
    } else {
      $whereStatus = " AND x.status = :status ";
    }
  }

  $sql = "
    SELECT *
    FROM (
      SELECT
        'Baptism' AS service_type,
        b.id AS request_id,
        b.reservation_user_id AS user_id,
        b.status AS status,
        b.decline_reason AS decline_reason,
        b.date AS service_date,
        b.time AS service_time,
        b.created_at AS created_at,
        CONCAT_WS(' ', b.child_firstname, b.child_middlename, b.child_lastname, b.child_suffix) AS primary_name,
        b.baptism_type AS subtype,

        b.rehearsal_date AS rehearsal_date,
        b.rehearsal_time AS rehearsal_time,
        NULL AS canon_interview_date,
        NULL AS canon_interview_time,
        NULL AS precana_semi_date,
        NULL AS precana_semi_time,
        
        NULL AS pickup_date
      FROM baptisms b

      UNION ALL

      SELECT
        'First Communion' AS service_type,
        fc.id AS request_id,
        fc.reservation_user_id AS user_id,
        fc.status AS status,
        fc.decline_reason AS decline_reason,
        fc.date AS service_date,
        fc.time AS service_time,
        fc.created_at AS created_at,
        CONCAT_WS(' ', fc.child_firstname, fc.child_middlename, fc.child_lastname, fc.child_suffix) AS primary_name,
        fc.first_comm_type AS subtype,

        fc.rehearsal_date AS rehearsal_date,
        fc.rehearsal_time AS rehearsal_time,
        NULL AS canon_interview_date,
        NULL AS canon_interview_time,
        NULL AS precana_semi_date,
        NULL AS precana_semi_time,
        
        NULL AS pickup_date
      FROM first_communion fc

      UNION ALL

      SELECT
        'Confirmation' AS service_type,
        c.id AS request_id,
        c.reservation_user_id AS user_id,
        c.status AS status,
        c.decline_reason AS decline_reason,
        c.date AS service_date,
        c.time AS service_time,
        c.created_at AS created_at,
        CONCAT_WS(' ', c.confirm_firstname, c.confirm_middlename, c.confirm_lastname, c.confirm_suffix) AS primary_name,
        c.confirmation_type AS subtype,

        c.rehearsal_date AS rehearsal_date,
        c.rehearsal_time AS rehearsal_time,
        NULL AS canon_interview_date,
        NULL AS canon_interview_time,
        NULL AS precana_semi_date,
        NULL AS precana_semi_time,
        
        NULL AS pickup_date
      FROM confirmations c

      UNION ALL

      SELECT
        'Wedding' AS service_type,
        w.id AS request_id,
        w.reservation_user_id AS user_id,
        w.status AS status,
        w.decline_reason AS decline_reason,
        w.date AS service_date,
        w.time AS service_time,
        w.created_at AS created_at,
        CONCAT_WS(' & ',
          CONCAT_WS(' ', w.bride_firstname, w.bride_middlename, w.bride_lastname),
          CONCAT_WS(' ', w.groom_firstname, w.groom_middlename, w.groom_lastname)
        ) AS primary_name,
        w.wedding_type AS subtype,

        w.rehearsal_date AS rehearsal_date,
        w.rehearsal_time AS rehearsal_time,
        w.canon_interview_date AS canon_interview_date,
        w.canon_interview_time AS canon_interview_time,
        w.precana_semi_date AS precana_semi_date,
        w.precana_semi_time AS precana_semi_time,
        
        NULL AS pickup_date
      FROM weddings w


      UNION ALL

      SELECT
        'Funeral' AS service_type,
        f.id AS request_id,
        f.reservation_user_id AS user_id,
        f.status AS status,
        f.decline_reason AS decline_reason,
        f.funeral_date AS service_date,
        f.funeral_time AS service_time,
        NULL AS created_at,
        CONCAT_WS(' ', f.deceased_firstname, f.deceased_middlename, f.deceased_lastname, f.deceased_suffix) AS primary_name,
        NULL AS subtype,

        NULL AS rehearsal_date,
        NULL AS rehearsal_time,
        NULL AS canon_interview_date,
        NULL AS canon_interview_time,
        NULL AS precana_semi_date,
        NULL AS precana_semi_time,
        
        NULL AS pickup_date
      FROM funerals f

      UNION ALL

      SELECT
        'Anointing of the Sick' AS service_type,
        a.id AS request_id,
        a.reservation_user_id AS user_id,
        a.status AS status,
        a.decline_reason AS decline_reason,
        a.date AS service_date,
        a.time AS service_time,
        a.created_at AS created_at,
        CONCAT_WS(' ', a.sick_firstname, a.sick_middlename, a.sick_lastname) AS primary_name,
        NULL AS subtype,

        NULL AS rehearsal_date,
        NULL AS rehearsal_time,
        NULL AS canon_interview_date,
        NULL AS canon_interview_time,
        NULL AS precana_semi_date,
        NULL AS precana_semi_time,
        
        NULL AS pickup_date
      FROM anointing_of_sick_requests a

      UNION ALL

      SELECT
        'House Blessing' AS service_type,
        hb.id AS request_id,
        hb.reservation_user_id AS user_id,
        hb.status AS status,
        hb.decline_reason AS decline_reason,
        hb.date AS service_date,
        hb.time AS service_time,
        NULL AS created_at,
        CONCAT(hb.street_no, ', ', hb.barangay, ', ', hb.city, ', ', hb.province) AS primary_name,
        NULL AS subtype,

        NULL AS rehearsal_date,
        NULL AS rehearsal_time,
        NULL AS canon_interview_date,
        NULL AS canon_interview_time,
        NULL AS precana_semi_date,
        NULL AS precana_semi_time,
        
        NULL AS pickup_date
      FROM house_blessings hb

      UNION ALL

      SELECT
        'Celebratory' AS service_type,
        ce.id AS request_id,
        ce.reservation_user_id AS user_id,
        ce.status AS status,
        ce.decline_reason AS decline_reason,
        ce.date AS service_date,
        ce.time AS service_time,
        ce.created_at AS created_at,
        ce.celebration_type AS primary_name,
        NULL AS subtype,

        NULL AS rehearsal_date,
        NULL AS rehearsal_time,
        NULL AS canon_interview_date,
        NULL AS canon_interview_time,
        NULL AS precana_semi_date,
        NULL AS precana_semi_time,

        NULL AS pickup_date
      FROM celebratory ce

      UNION ALL

      SELECT
        'Certificate Request' AS service_type,
        cr.cert_req_id AS request_id,
        cr.user_id AS user_id,
        cr.certificate_status AS status,
        cr.decline_reason AS decline_reason,
        NULL AS service_date,
        NULL AS service_time,
        cr.created_at AS created_at,
        CONCAT_WS(' ', cr.owner_firstname, cr.owner_middlename, cr.owner_lastname, cr.owner_suffix) AS primary_name,
        cr.certificate_type AS subtype,

        NULL AS rehearsal_date,
        NULL AS rehearsal_time,
        NULL AS canon_interview_date,
        NULL AS canon_interview_time,
        NULL AS precana_semi_date,
        NULL AS precana_semi_time,

        cr.pickup_date AS pickup_date
      FROM certificate_request cr
    ) x
    WHERE x.user_id = :user_id
    $whereStatus
    ORDER BY
      COALESCE(x.service_date, DATE(x.created_at)) DESC,
      COALESCE(x.service_time, TIME(x.created_at)) DESC
  ";

  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

  // bind :status ONLY when $whereStatus = " AND x.status = :status "
  if ($status !== 'All' && !in_array($status, ['Pending','Accepted','Cancelled'], true)) {
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);
  }

  $stmt->execute();
  $rows = $stmt->fetchAll();
  echo json_encode(['data' => $rows]);

} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode([
    'error' => 'Server error',
    // show actual error while debugging:
    'details' => $e->getMessage()
  ]);
}
?>