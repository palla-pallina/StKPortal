<?php
require_once 'connection.php';
session_start();

// Restrict access to admins only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Admin') {
    header("Location: ../public/login.php");
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
    if ($givenname === '' || $lastname === '' || $username === '') {
        $msg = "Please fill required fields (First name, Last name, Username).";
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
</head>
<body>
<div class="container" style="max-width:700px;margin-top:30px;">
  <h2>Update Member</h2>
  <?php if ($msg): ?>
    <div class="alert alert-warning"><?php echo htmlspecialchars($msg); ?></div>
  <?php endif; ?>

  <form method="post" action="update.php?user_id=<?php echo $user_id; ?>">
    <div class="form-group">
      <label>First Name</label>
      <input type="text" name="givenname" class="form-control" value="<?php echo htmlspecialchars($user->givenname ?? ''); ?>" required>
    </div>

    <div class="form-group">
      <label>Middle Name</label>
      <input type="text" name="middlename" class="form-control" value="<?php echo htmlspecialchars($user->middlename ?? ''); ?>">
    </div>

    <div class="form-group">
      <label>Last Name</label>
      <input type="text" name="lastname" class="form-control" value="<?php echo htmlspecialchars($user->lastname ?? ''); ?>" required>
    </div>

    <div class="form-group">
      <label>Suffix</label>
      <input type="text" name="suffix" class="form-control" value="<?php echo htmlspecialchars($user->suffix ?? ''); ?>">
    </div>

    <div class="form-group">
      <label>Birthday</label>
      <input type="date" name="birthday" class="form-control" value="<?php echo htmlspecialchars($user->birthday ?? ''); ?>">
    </div>

    <div class="form-group">
      <label>Username</label>
      <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user->username ?? ''); ?>" required>
    </div>

    <div class="form-group">
      <label>Email</label>
      <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user->email ?? ''); ?>">
    </div>

    <div class="form-group">
      <label>Contact</label>
      <input type="text" name="contact" class="form-control" value="<?php echo htmlspecialchars($user->contact ?? ''); ?>">
    </div>

    <div class="form-group">
      <label>User Type</label>

      <select name="user_type" class="form-control" required>
          <option value="" disabled <?= empty($user->user_type) ? 'selected' : '' ?>>User Type</option>
          <option value="User" <?= ($user->user_type ?? '') === 'User' ? 'selected' : '' ?>>User</option>
          <option value="Admin" <?= ($user->user_type ?? '') === 'Admin' ? 'selected' : '' ?>>Admin</option>
      </select>
    </div>

    <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
    <a href="members.php" class="btn btn-default">Cancel</a>
  </form>
  <br><br><br>
</div>
</body>
</html>

