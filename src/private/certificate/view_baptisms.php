<?php
require_once __DIR__ . '/db.php';

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
        FROM baptism_cert
        $where
        ORDER BY id DESC LIMIT 200";

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html><html><head>
<meta charset="utf-8"><title>Baptisms</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" />
<style>body{padding:20px}</style>
</head><body>
<h3>Baptisms</h3>
<form class="form-inline" method="get" style="margin:10px 0">
  <input class="form-control" name="q" placeholder="Search names..." value="<?= htmlspecialchars($search) ?>">
  <button class="btn btn-primary">Search</button>
  <a class="btn btn-default" href="export_baptisms_csv.php<?= $search ? '?q='.urlencode($search) : '' ?>">Export CSV</a>
  <a class="btn btn-default" href="admin.php">Back to Admin</a>
</form>
<table class="table table-bordered table-striped">
<thead><tr>
  <th>ID</th><th>Child</th><th>Parents</th><th>Birth</th><th>Baptism</th><th>Created</th>
</tr></thead>
<tbody>
<?php while($r=$res->fetch_assoc()): ?>
<tr>
  <td><?= (int)$r['id'] ?></td>
  <td><?= htmlspecialchars($r['child_first'].' '.$r['child_last']) ?></td>
  <td><?= htmlspecialchars(trim(($r['father_first'].' '.$r['father_last']))) ?>
      <?= ($r['mother_first']||$r['mother_last']) ? ' &amp; '.htmlspecialchars(trim($r['mother_first'].' '.$r['mother_last'])) : '' ?>
  </td>
  <td><?= htmlspecialchars($r['birth_date'] ?: '—') ?></td>
  <td><?= htmlspecialchars($r['baptism_date'] ?: '—') ?></td>
  <td><?= htmlspecialchars($r['created_at']) ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</body></html>
<?php $stmt->close(); $conn->close(); ?>
