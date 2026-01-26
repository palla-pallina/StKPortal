<?php
// backend/church_appointments_api.php
header('Content-Type: application/json; charset=utf-8');

// If you need CORS (different domain/port), uncomment:
// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: GET, OPTIONS');
// header('Access-Control-Allow-Headers: Content-Type, Accept');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(204);
  exit;
}

require_once __DIR__ . '/db.php';

function clean_status(?string $s): string {
  $s = $s ?? '';
  $s = str_replace("'", "", $s);
  $s = trim($s);
  return $s === '' ? 'Pending' : $s;
}

function status_key(string $clean): string {
  $c = strtolower($clean);
  if (in_array($c, ['pending', 'in process', 'processing'])) return 'pending';
  if (in_array($c, ['accepted', 'approved', 'ready', 'completed', 'done'])) return 'accepted';
  if (in_array($c, ['declined', 'rejected', 'cancelled', 'canceled', 'cancel'])) return 'declined';
  // fallback
  return 'pending';
}

function full_name($first, $middle, $last, $suffix): string {
  $parts = [];
  if ($first) $parts[] = $first;
  if ($middle) $parts[] = $middle;
  if ($last) $parts[] = $last;
  $name = trim(implode(' ', $parts));
  if ($suffix) $name = trim($name . ' ' . $suffix);
  return $name;
}

try {
  $pdo = db();

  // You can optionally filter by status via query param:
  // status=all|pending|accepted|declined  (front-end uses all)
  $requested = $_GET['status'] ?? 'all';
  $requested = strtolower(trim($requested));

  $sql = "
    SELECT
      'baptisms' AS source,
      b.id AS id,
      'Baptism' AS service,
      b.baptism_type AS subtype,
      b.status AS raw_status,
      b.date AS d,
      b.time AS t,
      b.child_firstname AS f1,
      b.child_middlename AS m1,
      b.child_lastname AS l1,
      b.child_suffix AS s1,
      NULL AS f2, NULL AS m2, NULL AS l2, NULL AS s2,
      NULL AS cert_type,
      b.created_at AS created_at
    FROM baptisms b

    UNION ALL

    SELECT
      'confirmations' AS source,
      c.id AS id,
      'Confirmation' AS service,
      c.confirmation_type AS subtype,
      c.status AS raw_status,
      c.date AS d,
      c.time AS t,
      c.confirm_firstname AS f1,
      c.confirm_middlename AS m1,
      c.confirm_lastname AS l1,
      c.confirm_suffix AS s1,
      NULL AS f2, NULL AS m2, NULL AS l2, NULL AS s2,
      NULL AS cert_type,
      c.date AS created_at
    FROM confirmations c

    UNION ALL

    SELECT
      'first_communion' AS source,
      fc.id AS id,
      'First Communion' AS service,
      fc.first_comm_type AS subtype,
      fc.status AS raw_status,
      fc.date AS d,
      fc.time AS t,
      fc.child_firstname AS f1,
      fc.child_middlename AS m1,
      fc.child_lastname AS l1,
      fc.child_suffix AS s1,
      NULL AS f2, NULL AS m2, NULL AS l2, NULL AS s2,
      NULL AS cert_type,
      fc.date AS created_at
    FROM first_communion fc

    UNION ALL

    SELECT
      'weddings' AS source,
      w.id AS id,
      'Wedding' AS service,
      w.wedding_type AS subtype,
      w.status AS raw_status,
      w.date AS d,
      w.time AS t,
      w.bride_firstname AS f1,
      w.bride_middlename AS m1,
      w.bride_lastname AS l1,
      NULL AS s1,
      w.groom_firstname AS f2,
      w.groom_middlename AS m2,
      w.groom_lastname AS l2,
      NULL AS s2,
      NULL AS cert_type,
      w.created_at AS created_at
    FROM weddings w

    UNION ALL

    SELECT
      'certificate_request' AS source,
      cr.cert_req_id AS id,
      'Certificate Request' AS service,
      NULL AS subtype,
      cr.certificate_status AS raw_status,
      cr.pickup_date AS d,
      NULL AS t,
      cr.request_firstname AS f1,
      cr.request_middlename AS m1,
      cr.request_lastname AS l1,
      cr.request_suffix AS s1,
      cr.owner_firstname AS f2,
      cr.owner_middlename AS m2,
      cr.owner_lastname AS l2,
      cr.owner_suffix AS s2,
      cr.certificate_type AS cert_type,
      cr.created_at AS created_at
    FROM certificate_request cr
  ";

  $rows = $pdo->query($sql)->fetchAll();

  $lists = [
    'all' => [],
    'pending' => [],
    'accepted' => [],
    'declined' => [],
  ];

  foreach ($rows as $r) {
    $clean = clean_status($r['raw_status'] ?? '');
    $key = status_key($clean);

    // For certificate requests: "Ready" should show under accepted
    if (strtolower($clean) === 'ready') $key = 'accepted';

    // Make a title + subtitle for the UI
    $title = $r['service'];
    if (!empty($r['subtype'])) $title .= " ({$r['subtype']})";
    if ($r['source'] === 'certificate_request' && !empty($r['cert_type'])) {
      $title .= " ({$r['cert_type']})";
    }

    $subtitle = '';
    if ($r['source'] === 'weddings') {
      $bride = full_name($r['f1'], $r['m1'], $r['l1'], $r['s1']);
      $groom = full_name($r['f2'], $r['m2'], $r['l2'], $r['s2']);
      $names = trim($bride) && trim($groom) ? ($bride . " & " . $groom) : (trim($bride.$groom));
      if ($names) $subtitle = $names;
    } else if ($r['source'] === 'certificate_request') {
      $req = full_name($r['f1'], $r['m1'], $r['l1'], $r['s1']);
      $owner = full_name($r['f2'], $r['m2'], $r['l2'], $r['s2']);
      if ($req && $owner) $subtitle = "Requested by: {$req} • Owner: {$owner}";
      else if ($req) $subtitle = "Requested by: {$req}";
      else if ($owner) $subtitle = "Owner: {$owner}";
    } else {
      $name = full_name($r['f1'], $r['m1'], $r['l1'], $r['s1']);
      if ($name) $subtitle = $name;
    }

    // Combine date+time into scheduled_at
    $scheduled_at = null;
    if (!empty($r['d'])) {
      $scheduled_at = $r['d'];
      if (!empty($r['t'])) $scheduled_at .= " " . $r['t'];
      else $scheduled_at .= " 00:00:00";
    }

    $item = [
      'source' => $r['source'],
      'id' => (int)$r['id'],
      'service' => $r['service'],
      'title' => $title,
      'subtitle' => $subtitle ?: null,
      'scheduled_at' => $scheduled_at,
      'status_label' => $clean,
      'status_key' => $key,
    ];

    $lists['all'][] = $item;
    $lists[$key][] = $item;
  }

  // Sort newest first by scheduled_at (fallback to id)
  $sortFn = function($a, $b){
    $ta = strtotime($a['scheduled_at'] ?? '') ?: 0;
    $tb = strtotime($b['scheduled_at'] ?? '') ?: 0;
    if ($ta === $tb) return ($b['id'] <=> $a['id']);
    return $tb <=> $ta;
  };
  foreach ($lists as $k => $arr) {
    usort($lists[$k], $sortFn);
  }

  $counts = [
    'all' => count($lists['all']),
    'pending' => count($lists['pending']),
    'accepted' => count($lists['accepted']),
    'declined' => count($lists['declined']),
  ];

  // If caller requests a single list, still return all lists (UI needs them),
  // but you can slim this later if desired.
  echo json_encode([
    'ok' => true,
    'counts' => $counts,
    'lists' => $lists
  ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode([
    'ok' => false,
    'error' => 'Server error',
    'details' => $e->getMessage()
  ]);
}
