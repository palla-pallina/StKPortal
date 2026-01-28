<?php
require_once 'connection.php';
session_start();

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../../public/login.php");
    exit();
}
$admin_name = !empty($_SESSION['givenname']) ? $_SESSION['givenname'] : $_SESSION['username'];


if (!isset($_GET['user_id'])) {
    header('Location: members.php'); // no user_id, go back
    exit();
}

$user_id = intval($_GET['user_id']);
$msg = '';

// Fetch user
try {
    $stmt = $dbh->prepare("SELECT * FROM users WHERE user_id = :user_id LIMIT 1");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$user) {
        // user not found
        header('Location: members.php?error=notfound');
        exit();
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // Collect and sanitize input (adjust required fields as needed)
    $givenname  = trim($_POST['givenname'] ?? '');
    $middlename = trim($_POST['middlename'] ?? '');
    $lastname   = trim($_POST['lastname'] ?? '');
    $suffix     = trim($_POST['suffix'] ?? '');
    $birthday   = trim($_POST['birthday'] ?? '');
    $username   = trim($_POST['username'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $contact    = trim($_POST['contact'] ?? '');
    $user_type  = trim($_POST['user_type'] ?? '');

    // Simple validation (expand as needed)
    if ($givenname === '' || $lastname === '' ) {
        $msg = "Please fill required fields.";
    } else {
        try {
            $sql = "UPDATE users SET
                        givenname = :givenname,
                        middlename = :middlename,
                        lastname = :lastname,
                        suffix = :suffix,
                        birthday = :birthday,
                        username = :username,
                        email = :email,
                        contact = :contact,
                        user_type = :user_type
                    WHERE user_id = :user_id";
            $upd = $dbh->prepare($sql);
            $upd->bindParam(':givenname', $givenname, PDO::PARAM_STR);
            $upd->bindParam(':middlename', $middlename, PDO::PARAM_STR);
            $upd->bindParam(':lastname', $lastname, PDO::PARAM_STR);
            $upd->bindParam(':suffix', $suffix, PDO::PARAM_STR);
            $upd->bindParam(':birthday', $birthday, PDO::PARAM_STR);
            $upd->bindParam(':username', $username, PDO::PARAM_STR);
            $upd->bindParam(':email', $email, PDO::PARAM_STR);
            $upd->bindParam(':contact', $contact, PDO::PARAM_STR);
            $upd->bindParam(':user_type', $user_type, PDO::PARAM_STR);
            $upd->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            $upd->execute();

            // Redirect back with success
            header('Location: members.php?msg=updated');
            exit();
        } catch (PDOException $e) {
            $msg = "Update failed: " . $e->getMessage();
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Update Member</title>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet"/>
  <link href='../../stylesheets/private/sacrament_insert.css' rel='stylesheet'>
</head>
<body>
<div class="record-container" style="max-width:700px;margin-top:30px;">
    <h2>Update Member</h2>

    <?php if ($msg): ?>
        <div class="alert alert-warning"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form method="post" action="update.php?user_id=<?= $user_id ?>">
        <input type="hidden" name="user_id" value="<?= $user_id ?>">

        <!-- ================= PERSONAL DETAILS ================= -->
        <div class="record-section">
            <h3>Personal Details</h3>
            <div class="row record-inline">
                <div class="col-md-3 record-row">
                    <span class="record-label">First Name</span>
                    <input type="text" name="givenname" class="form-control" value="<?= htmlspecialchars($user->givenname ?? '') ?>" required>
                </div>

                <div class="col-md-2 record-row">
                    <span class="record-label">Middle Name</span>
                    <input type="text" name="middlename" class="form-control" value="<?= htmlspecialchars($user->middlename ?? '') ?>">
                </div>

                <div class="col-md-3 record-row">
                    <span class="record-label">Last Name</span>
                    <input type="text" name="lastname" class="form-control" value="<?= htmlspecialchars($user->lastname ?? '') ?>" required>
                </div>

                <div class="col-md-2 record-row">
                    <span class="record-label">Suffix</span>
                    <input type="text" name="suffix" class="form-control" value="<?= htmlspecialchars($user->suffix ?? '') ?>">
                </div>
            </div>

            <div class="row record-inline" style="margin-top:10px;">
                <div class="col-md-3 record-row">
                    <span class="record-label">Birthday</span>
                    <input type="date" name="birthday" class="form-control" value="<?= htmlspecialchars($user->birthday ?? '') ?>">
                </div>
            </div>
        </div>

        <!-- ================= ACCOUNT INFO ================= -->
        <div class="record-section">
            <h3>Account Info</h3>
            <div class="row record-inline">
                <div class="col-md-4 record-row">
                    <span class="record-label">Email Address</span>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user->email ?? '') ?>">
                </div>

                <div class="col-md-3 record-row">
                    <span class="record-label">Contact</span>
                    <input type="text" name="contact" class="form-control" value="<?= htmlspecialchars($user->contact ?? '') ?>">
                </div>

                <div class="col-md-4 record-row">
                    <span class="record-label">User Type</span>
                    <select name="user_type" class="form-control" required>
                        <option value="" disabled <?= empty($user->user_type) ? 'selected' : '' ?>>Select User Type</option>
                        <option value="User" <?= ($user->user_type ?? '') === 'User' ? 'selected' : '' ?>>User</option>
                        <option value="Admin" <?= ($user->user_type ?? '') === 'Admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="Priest" <?= ($user->user_type ?? '') === 'Priest' ? 'selected' : '' ?>>Priest</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- ================= BUTTONS ================= -->
        <div class="record-buttons" style="margin-top:20px;">
            <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
            <a href="members.php" class="btn btn-default">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>

