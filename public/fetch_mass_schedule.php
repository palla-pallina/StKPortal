<?php
require_once 'connection.php';
date_default_timezone_set('Asia/Manila');

$days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
$todayDay = date('l');
$currentTimeHM = date('H:i');

$schedules = [];
$maxRows = 0;

/* Fetch schedules INCLUDING time_in & time_out */
$query = "
    SELECT day, time_in, time_out
    FROM mass_schedule
    ORDER BY time_in ASC
";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $day = $row['day'];

    $schedules[$day][] = [
        'time_in'  => substr($row['time_in'], 0, 5),
        'time_out' => substr($row['time_out'], 0, 5)
    ];

    $count = count($schedules[$day]);
    if ($count > $maxRows) $maxRows = $count;
}

/* Ensure all days exist */
foreach ($days as $d) {
    if (!isset($schedules[$d])) {
        $schedules[$d] = [];
    }
}

echo json_encode([
    'days'          => $days,
    'schedules'     => $schedules,
    'maxRows'       => $maxRows,
    'todayDay'      => $todayDay,
    'currentTimeHM' => $currentTimeHM
]);
