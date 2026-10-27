<?php
require 'db.php';
$errors=[]; $name=''; $email='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 $name=trim($_POST['name']??''); $email=strtolower(trim($_POST['email']??''));
 $password=$_POST['password']??''; $confirm=$_POST['confirm_password']??'';
 if (mb_strlen($name)<2 || mb_strlen($name)>100) $errors[]='Name must contain 2 to 100 characters.';
 if (!filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[]='Enter a valid email address.';
 if (strlen($password)<8 || !preg_match('/[A-Z]/',$password) || !preg_match('/[0-9]/',$password)) $errors[]='Password must be at least 8 characters and include an uppercase letter and a number.';
 if ($password!==$confirm) $errors[]='Passwords do not match.';
 if (!$errors) {
  $check=$pdo->prepare('SELECT id FROM users WHERE email=?'); $check->execute([$email]);
  if ($check->fetch()) $errors[]='This email is already registered.';
 }
 if (!$errors) {
  $stmt=$pdo->prepare('INSERT INTO users(full_name,email,password_hash) VALUES(?,?,?)');
  $stmt->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT)]);
  header('Location: login.php?registered=1'); exit;
 }
}
?>
<!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Create StudentHub account</title>
<style>body{font:16px system-ui;background:#f3f6fb;margin:0;padding:30px;color:#172033}.card{max-width:480px;margin:4vh auto;background:white;padding:28px;border-radius:18px;box-shadow:0 12px 35px #17203312}label{display:block;margin-top:14px}input{width:100%;box-sizing:border-box;padding:11px;margin-top:5px;border:1px solid #ccd3df;border-radius:8px}button{margin-top:20px;padding:12px 18px;border:0;border-radius:8px;background:#3157d5;color:white;font-weight:700;cursor:pointer}.errors{background:#fff0f0;padding:12px;color:#9a2020;border-radius:8px}</style>
<div class="card"><h1>Create account</h1><p>Join the StudentHub campus portal.</p><?php if($errors):?><div class="errors"><ul><?php foreach($errors as $e):?><li><?=h($e)?></li><?php endforeach;?></ul></div><?php endif;?>
<form method="post" novalidate><label>Full name<input name="name" value="<?=h($name)?>" required maxlength="100" autocomplete="name"></label><label>Email<input type="email" name="email" value="<?=h($email)?>" required autocomplete="email"></label><label>Password<input type="password" name="password" required minlength="8" autocomplete="new-password"></label><label>Confirm password<input type="password" name="confirm_password" required autocomplete="new-password"></label><button>Create account</button></form><p>Already registered? <a href="login.php">Sign in</a></p></div></html>
