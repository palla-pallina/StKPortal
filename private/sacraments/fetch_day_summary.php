<?php
require_once '../connection.php';

$date = $_GET['date'];
$summary = [];

/* Helper */
function fullName($f,$m,$l,$s){
  return trim("$f $m $l $s");
}

/* ===============================
   ANOINTING OF THE SICK
================================ */
$q = $conn->prepare("
  SELECT * FROM anointing_of_sick_requests WHERE date=?
");
$q->bind_param("s", $date);
$q->execute();
$r = $q->get_result();
while ($row = $r->fetch_assoc()) {
  $summary[] =
    "Anointing of the Sick: ".
    fullName($row['sick_firstname'],$row['sick_middlename'],$row['sick_lastname'],$row['sick_suffix']).
    "'s Anointing of the Sick at ".$row['time'];
}

/* ===============================
   BAPTISM
================================ */
$q = $conn->prepare("SELECT * FROM baptisms WHERE date=?");
$q->bind_param("s", $date);
$q->execute();
$r = $q->get_result();
while ($row = $r->fetch_assoc()) {
  $summary[] =
    "Baptism: ".
    fullName($row['child_firstname'],$row['child_middlename'],$row['child_lastname'],$row['child_suffix']).
    "'s Baptism at ".$row['time'];
}

/* ===============================
   FIRST COMMUNION
================================ */
$q = $conn->prepare("SELECT * FROM first_communion WHERE date=?");
$q->bind_param("s", $date);
$q->execute();
$r = $q->get_result();
while ($row = $r->fetch_assoc()) {
  $summary[] =
    "First Communion: ".
    fullName($row['child_firstname'],$row['child_middlename'],$row['child_lastname'],$row['child_suffix']).
    "'s First Communion at ".$row['time'];
}

/* ===============================
   CONFIRMATION
================================ */
$q = $conn->prepare("SELECT * FROM confirmations WHERE date=?");
$q->bind_param("s", $date);
$q->execute();
$r = $q->get_result();
while ($row = $r->fetch_assoc()) {
  $summary[] =
    "Confirmation: ".
    fullName($row['confirm_firstname'],$row['confirm_middlename'],$row['confirm_lastname'],$row['confirm_suffix']).
    "'s Confirmation at ".$row['time'];
}

/* ===============================
   WEDDING
================================ */
$q = $conn->prepare("SELECT * FROM weddings WHERE date=?");
$q->bind_param("s", $date);
$q->execute();
$r = $q->get_result();
while ($row = $r->fetch_assoc()) {
  $summary[] =
    "Wedding: {$row['groom_firstname']} {$row['groom_lastname']} and {$row['bride_firstname']} {$row['bride_lastname']}'s Wedding at {$row['time']}";
}

/* ===============================
   FUNERAL
================================ */
$q = $conn->prepare("SELECT * FROM funerals WHERE funeral_date=?");
$q->bind_param("s", $date);
$q->execute();
$r = $q->get_result();
while ($row = $r->fetch_assoc()) {
  $summary[] =
    "Funeral Mass: ".
    fullName($row['deceased_firstname'],$row['deceased_middlename'],$row['deceased_lastname'],$row['deceased_suffix']).
    "'s funeral mass at ".$row['funeral_time'];
}

/* ===============================
   CELEBRATORY MASS
================================ */
$q = $conn->prepare("SELECT * FROM celebratory WHERE date=?");
$q->bind_param("s", $date);
$q->execute();
$r = $q->get_result();
while ($row = $r->fetch_assoc()) {
  $summary[] =
    "Celebratory Mass: {$row['celebration_type']} Mass at {$row['time']}";
}

/* ===============================
   HOUSE BLESSING
================================ */
$q = $conn->prepare("SELECT * FROM house_blessings WHERE date=?");
$q->bind_param("s", $date);
$q->execute();
$r = $q->get_result();
while ($row = $r->fetch_assoc()) {
  $summary[] =
    "House Blessing: House blessing at {$row['time']}";
}

echo json_encode($summary);
