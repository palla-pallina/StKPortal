<?php
require_once __DIR__ . '/connection.php';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=baptisms.csv');

$out = fopen('php://output', 'w');
fputcsv($out, ['id','child_first','child_last','birth_date','baptism_date',
               'father_first','father_last','mother_first','mother_last','created_at']);

$search = trim($_GET['q'] ?? '');
$where = '';
$params = [];
$types = '';

if ($search !== '') {
  $where = "WHERE (child_first LIKE CONCAT('%', ?, '%')
               OR child_last LIKE CONCAT('%', ?, '%')
               OR father_first LIKE CONCAT('%', ?, '%')
               OR father_last LIKE CONCAT('%', ?, '%')
               OR mother_first LIKE CONCAT('%', ?, '%')
               OR mother_last LIKE CONCAT('%', ?, '%'))";
  $params = [$search,$search,$search,$search,$search,$search];
  $types = 'ssssss';
}

$sql = "SELECT id, child_first, child_last, birth_date, baptism_date,
               father_first, father_last, mother_first, mother_last, created_at
        FROM baptism_cert $where ORDER BY id DESC";

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
  fputcsv($out, [$row['id'],$row['child_first'],$row['child_last'],$row['birth_date'],$row['baptism_date'],
                 $row['father_first'],$row['father_last'],$row['mother_first'],$row['mother_last'],$row['created_at']]);
}
fclose($out);
$stmt->close();
$conn->close();
exit;
