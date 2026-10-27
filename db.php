<?php
// Practical 8: shared PDO connection. Update credentials for your local XAMPP setup.
$host = '127.0.0.1'; $dbname = 'studenthub'; $username = 'root'; $password = '';
try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]);
} catch (PDOException $e) {
  http_response_code(500);
  exit('Database connection failed. Import setup.sql in phpMyAdmin and check db.php settings.');
}
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
function h($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function require_login() { if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; } }
function require_admin() { require_login(); if (($_SESSION['role'] ?? 'student') !== 'admin') { http_response_code(403); exit('403 - Admin access required.'); } }
?>
