<?php
// backend/db.php
// Update these values to match your existing database credentials.
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_NAME = getenv('DB_NAME') ?: 'test_tree_db';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';

function db(): PDO {
  static $pdo = null;
  if ($pdo) return $pdo;

  $dsn = "mysql:host={$GLOBALS['DB_HOST']};dbname={$GLOBALS['DB_NAME']};charset=utf8mb4";
  $pdo = new PDO($dsn, $GLOBALS['DB_USER'], $GLOBALS['DB_PASS'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
  return $pdo;
}
