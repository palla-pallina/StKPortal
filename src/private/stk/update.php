<?php
// update.php
require_once 'dbconfig.php';

if (!isset($_GET['id'])) {
    header('Location: members.php'); // no id, go back
    exit();
}

$id = intval($_GET['id']);
$msg = '';

// Fetch user
try {
    $stmt = $dbh->prepare("SELECT * FROM users WHERE user_id = :id LIMIT 1");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
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
    $photo      = trim($_POST['photo'] ?? $user->Photo); // keep existing if not provided

    // Simple validation (expand as needed)
    if ($givenname === '' || $lastname === '' || $username === '') {
        $msg = "Please fill required fields (First name, Last name, Username).";
    } else {
        try {
            $sql = "UPDATE users SET
                        Photo = :photo,
                        givenname = :givenname,
                        middlename = :middlename,
                        lastname = :lastname,
                        suffix = :suffix,
                        birthday = :birthday,
                        username = :username,
                        email = :email,
                        contact = :contact,
                        user_type = :user_type
                    WHERE user_id = :id";
            $upd = $dbh->prepare($sql);
            $upd->bindParam(':photo', $photo, PDO::PARAM_STR);
            $upd->bindParam(':givenname', $givenname, PDO::PARAM_STR);
            $upd->bindParam(':middlename', $middlename, PDO::PARAM_STR);
            $upd->bindParam(':lastname', $lastname, PDO::PARAM_STR);
            $upd->bindParam(':suffix', $suffix, PDO::PARAM_STR);
            $upd->bindParam(':birthday', $birthday, PDO::PARAM_STR);
            $upd->bindParam(':username', $username, PDO::PARAM_STR);
            $upd->bindParam(':email', $email, PDO::PARAM_STR);
            $upd->bindParam(':contact', $contact, PDO::PARAM_STR);
            $upd->bindParam(':user_type', $user_type, PDO::PARAM_STR);
            $upd->bindParam(':id', $id, PDO::PARAM_INT);

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

  <form method="post" action="update.php?id=<?php echo $id; ?>">
    <div class="form-group">
      <label>Photo (path or URL)</label>
      <input type="text" name="photo" class="form-control" value="<?php echo htmlspecialchars($user->Photo ?? ''); ?>">
    </div>

    <div class="form-group">
      <label>First Name *</label>
      <input type="text" name="givenname" class="form-control" value="<?php echo htmlspecialchars($user->givenname ?? ''); ?>" required>
    </div>

    <div class="form-group">
      <label>Middle Name</label>
      <input type="text" name="middlename" class="form-control" value="<?php echo htmlspecialchars($user->middlename ?? ''); ?>">
    </div>

    <div class="form-group">
      <label>Last Name *</label>
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
      <label>Username *</label>
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
      <input type="text" name="user_type" class="form-control" value="<?php echo htmlspecialchars($user->user_type ?? ''); ?>">
    </div>

    <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
    <a href="members.php" class="btn btn-default">Cancel</a>
  </form>
</div>
</body>
</html>
