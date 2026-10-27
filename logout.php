<?php
require 'db.php';
if (!empty($_SESSION['user_id'])) { $pdo->prepare('INSERT INTO audit_logs(user_id,action,details) VALUES(?,?,?)')->execute([$_SESSION['user_id'],'logout','User signed out']); }
$_SESSION=[]; if (ini_get('session.use_cookies')) { $p=session_get_cookie_params(); setcookie(session_name(),'',time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']); }
session_destroy(); header('Location: login.php'); exit;
?>
