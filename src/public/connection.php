<?php
    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "test_tree_db";

    $conn = new mysqli($host, $user, $pass, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>
