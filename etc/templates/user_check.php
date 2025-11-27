<?php

// Restrict access to registered users only
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {
    echo "<script>
            alert('Please log in with an account.');
            window.location.href='homepage.html';
          </script>";
    exit();
}

?>

<!-------------------------------------->

        <?php
        session_start(); // required

        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'User') {

            // USER NOT LOGGED IN → Show Login button
            echo '<li><a href="login.php" class="login-btn">Login</a></li>';

        } else {

            // USER LOGGED IN → Show Username + Dropdown
            echo '<li class="logout-dropdown">';
            echo '<a href="#">Logged in as ' . htmlspecialchars($user_name) . '</a>';
            echo '<ul class="dropdown">';
            echo '<li><a href="logout.php">Logout</a></li>';
            echo '</ul>';
            echo '</li>';
        }
        ?>