<?php
require_once __DIR__.'/../../core/auth.php'; require_login();
$pdo = db(); $config = require __DIR__.'/../../config.php'; $prefix = $config['db']['prefix'];
$id = (int)($_GET['id'] ?? 0);
$pdo->prepare("DELETE FROM `{$prefix}posts` WHERE id=:id")->execute([':id'=>$id]);
header('Location: '.admin_url('posts/index.php')); exit;
