<?php
require_once __DIR__ . '/db.php';

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

$sql = "SELECT id, groom_first, groom_last, bride_first, bride_last, wedding_date, wedding_place, created_at
        FROM wedding_cert
        $where
        ORDER BY id DESC LIMIT 200";

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html><html><head>
<meta charset="utf-8"><title>Weddings</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />
<style>body{padding:20px}</style>
</head><body>
<h3>Weddings</h3>
<form class="form-inline" method="get" style="margin:10px 0">
  <input class="form-control" name="q" placeholder="Search names..." value="<?= htmlspecialchars($search) ?>">
  <button class="btn btn-primary">Search</button>
  <a class="btn btn-default" href="export_weddings_csv.php<?= $search ? '?q='.urlencode($search) : '' ?>">Export CSV</a>
  <a class="btn btn-default" href="admin.php">Back to Admin</a>
</form>
<table class="table table-bordered table-striped">
<thead><tr>
  <th>ID</th><th>Couple</th><th>Date</th><th>Place</th><th>Created</th>
</tr></thead>
<tbody>
<?php while($r=$res->fetch_assoc()): ?>
<tr>
  <td><?= (int)$r['id'] ?></td>
  <td><?= htmlspecialchars($r['groom_first'].' '.$r['groom_last'].' & '.$r['bride_first'].' '.$r['bride_last']) ?></td>
  <td><?= htmlspecialchars($r['wedding_date'] ?: '—') ?></td>
  <td><?= htmlspecialchars($r['wedding_place'] ?: '—') ?></td>
  <td><?= htmlspecialchars($r['created_at']) ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</body></html>
<?php $stmt->close(); $conn->close(); ?>
