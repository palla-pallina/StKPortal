<?php
require_once __DIR__ . '/connection.php';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=weddings.csv');

$out = fopen('php://output', 'w');
fputcsv($out, ['id','groom_first','groom_last','bride_first','bride_last',
               'wedding_date','wedding_place','created_at']);

$search = trim($_GET['q'] ?? '');
$where = '';
$params = [];
$types = '';

if ($search !== '') {
  $where = "WHERE (groom_first LIKE CONCAT('%', ?, '%')
               OR groom_last LIKE CONCAT('%', ?, '%')
               OR bride_first LIKE CONCAT('%', ?, '%')
               OR bride_last LIKE CONCAT('%', ?, '%'))";
  $params = [$search,$search,$search,$search];
  $types = 'ssss';
}

$sql = "SELECT id, groom_first, groom_last, bride_first, bride_last,
               wedding_date, wedding_place, created_at
        FROM wedding_cert $where ORDER BY id DESC";

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

while ($row = $res->fetch_assoc()) {
  fputcsv($out, [$row['id'],$row['groom_first'],$row['groom_last'],$row['bride_first'],$row['bride_last'],
                 $row['wedding_date'],$row['wedding_place'],$row['created_at']]);
}
fclose($out);
$stmt->close();
$conn->close();
exit;
