<?php
require 'db.php';
$count=(int)$pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
?><!doctype html><html lang="en"><meta charset="utf-8"><title>Database test</title><body style="font:18px system-ui;padding:30px"><h1>Database connection successful</h1><p>Connected to the StudentHub database. Student records: <?=h($count)?></p><p><a href="home.html">Open portal</a></p></body></html>
