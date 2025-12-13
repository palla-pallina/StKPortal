<?php
$pdo = new PDO(
  "mysql:host=localhost;dbname=test_tree_db;charset=utf8mb4",
  "root",   // XAMPP default username
  "",       // XAMPP default password is blank
  [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]
);