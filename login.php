<?php
require 'db.php';
if (!empty($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 $email=strtolower(trim($_POST['email']??'')); $password=$_POST['password']??'';
 $stmt=$pdo->prepare('SELECT id,full_name,email,password_hash,role FROM users WHERE email=?'); $stmt->execute([$email]); $user=$stmt->fetch();
 if ($user && password_verify($password,$user['password_hash'])) {
  session_regenerate_id(true); $_SESSION['user_id']=$user['id']; $_SESSION['name']=$user['full_name']; $_SESSION['email']=$user['email']; $_SESSION['role']=$user['role']; $_SESSION['last_activity']=time();
  $pdo->prepare('UPDATE users SET last_login=NOW() WHERE id=?')->execute([$user['id']]);
  $pdo->prepare('INSERT INTO audit_logs(user_id,action,details) VALUES(?,?,?)')->execute([$user['id'],'login','Successful login']);
  header('Location: dashboard.php'); exit;
 } else { $error='Email or password is incorrect.'; }
}
?>
<!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>StudentHub Login</title><style>body{font:16px system-ui;background:#eef2ff;margin:0;padding:30px;color:#172033}.card{max-width:430px;margin:8vh auto;background:white;padding:28px;border-radius:18px;box-shadow:0 12px 35px #17203312}label{display:block;margin-top:14px}input{width:100%;box-sizing:border-box;padding:12px;margin-top:5px;border:1px solid #ccd3df;border-radius:8px}button{margin-top:20px;padding:12px 18px;border:0;border-radius:8px;background:#3157d5;color:white;font-weight:700}.error{color:#a21b1b;background:#fff0f0;padding:10px;border-radius:8px}</style><div class="card"><h1>Welcome back</h1><p>Sign in to StudentHub.</p><?php if(isset($_GET['registered'])):?><p style="color:green">Account created. You can sign in now.</p><?php endif;?><?php if($error):?><p class="error"><?=h($error)?></p><?php endif;?><form method="post"><label>Email<input type="email" name="email" required autocomplete="username"></label><label>Password<input type="password" name="password" required autocomplete="current-password"></label><button>Sign in</button></form><p>New here? <a href="register_db.php">Create account</a></p><p><a href="home.html">Back to portal</a></p></div></html>
