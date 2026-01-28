<?php
require_once '../connection.php';

$events = [];

/* ===============================
   SCHEDULED MASS
================================ */
$start = $_GET['start'] ?? null;
$end   = $_GET['end'] ?? null;

if ($start && $end) {

  $weekStart = date('Y-m-d', strtotime($start));

  $daysMap = [
    'Monday'    => 'monday',
    'Tuesday'   => 'tuesday',
    'Wednesday' => 'wednesday',
    'Thursday'  => 'thursday',
    'Friday'    => 'friday',
    'Saturday'  => 'saturday',
    'Sunday'    => 'sunday'
  ];

  $q = $conn->query("SELECT * FROM mass_schedule");

  while ($row = $q->fetch_assoc()) {
    $massDate = date(
      'Y-m-d',
      strtotime($daysMap[$row['day']], strtotime($weekStart))
    );

    $events[] = [
      'title' => 'Mass Schedule',
      'start' => $massDate . 'T' . $row['mass_time'],
      'display' => 'block'
    ];
  }
}


function dateForWeekday($weekday, $weekStart) {
  return date('Y-m-d', strtotime($weekday, strtotime($weekStart)));
}

/* ===============================
   ANOINTING OF THE SICK
================================ */
$q = $conn->query("
  SELECT date, time,
         CONCAT(sick_firstname,' ',sick_lastname) AS title
  FROM anointing_of_sick_requests
");
while ($r = $q->fetch_assoc()) {
  $events[] = [
    'title' => 'Anointing of the Sick',
    'start' => $r['date'].'T'.$r['time']
  ];
}

/* ===============================
   BAPTISM
================================ */
$q = $conn->query("
  SELECT date, time,
         CONCAT(child_firstname,' ',child_lastname) AS title
  FROM baptisms
");
while ($r = $q->fetch_assoc()) {
  $events[] = [
    'title' => 'Baptism',
    'start' => $r['date'].'T'.$r['time']
  ];
}

/* ===============================
   FIRST COMMUNION
================================ */
$q = $conn->query("
  SELECT date, time FROM first_communion
");
while ($r = $q->fetch_assoc()) {
  $events[] = [
    'title' => 'First Communion',
    'start' => $r['date'].'T'.$r['time']
  ];
}

/* ===============================
   CONFIRMATION
================================ */
$q = $conn->query("
  SELECT date, time FROM confirmations
");
while ($r = $q->fetch_assoc()) {
  $events[] = [
    'title' => 'Confirmation',
    'start' => $r['date'].'T'.$r['time']
  ];
}

/* ===============================
   WEDDINGS
================================ */
$q = $conn->query("
  SELECT date, time FROM weddings
");
while ($r = $q->fetch_assoc()) {
  $events[] = [
    'title' => 'Wedding',
    'start' => $r['date'].'T'.$r['time']
  ];
}

/* ===============================
   FUNERALS
================================ */
$q = $conn->query("
  SELECT funeral_date, funeral_time FROM funerals
");
while ($r = $q->fetch_assoc()) {
  $events[] = [
    'title' => 'Funeral Mass',
    'start' => $r['funeral_date'].'T'.$r['funeral_time']
  ];
}

/* ===============================
   CELEBRATORY MASS
================================ */
$q = $conn->query("
  SELECT date, time, celebration_type FROM celebratory
");
while ($r = $q->fetch_assoc()) {
  $events[] = [
    'title' => $r['celebration_type'].' Mass',
    'start' => $r['date'].'T'.$r['time']
  ];
}

/* ===============================
   HOUSE BLESSING
================================ */
$q = $conn->query("
  SELECT date, time FROM house_blessings
");
while ($r = $q->fetch_assoc()) {
  $events[] = [
    'title' => 'House Blessing',
    'start' => $r['date'].'T'.$r['time']
  ];
}

echo json_encode($events);
