<?php
require_once 'connection.php';
session_start();

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
    exit();
}

$admin_name = !empty($_SESSION['givenname']) ? $_SESSION['givenname'] : $_SESSION['username'];

// Fetch all baptism records
$stmt = $dbh->prepare("SELECT * FROM baptisms ORDER BY id DESC");
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_OBJ);

// Helper function to build full names
function fullName($f, $m, $l, $s) {
    return trim("$f $m $l $s");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Baptism Certificates</title>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container" style="margin-top:30px;">
    <h2>Baptism Certificates</h2>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Child</th>
                <th>Father</th>
                <th>Mother</th>
                <th>Baptism Date</th>
                <th>Generate PDF</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($results) {
                $cnt = 1;
                foreach ($results as $r) {
                    $child = fullName($r->child_firstname, $r->child_middlename, $r->child_lastname, $r->child_suffix);
                    $father = fullName($r->father_firstname, $r->father_middlename, $r->father_lastname, $r->father_suffix);
                    $mother = fullName($r->mother_firstname, $r->mother_middlename, $r->mother_lastname, $r->mother_suffix);

                    echo "<tr>
                            <td>{$cnt}</td>
                            <td>{$child}</td>
                            <td>{$father}</td>
                            <td>{$mother}</td>
                            <td>{$r->date}</td>
                            <td>
                                <a href='baptsimpdf.php?id={$r->id}' class='btn btn-success btn-sm'>Generate PDF</a>
                            </td>
                          </tr>";
                    $cnt++;
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>No records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
