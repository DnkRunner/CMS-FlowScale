<?php
require_once __DIR__.'/../../core/auth.php'; require_login();
require_once __DIR__.'/../../core/helpers.php';
$pdo = db(); $config = require __DIR__.'/../../config.php'; $prefix = $config['db']['prefix'];
$id = (int)($_GET['id'] ?? 0);
if ($id>0) {
  $stmt = $pdo->prepare("DELETE FROM `{$prefix}pages` WHERE id=:id");
  $stmt->execute([':id'=>$id]);
}
header('Location: '.admin_url('pages/index.php')); exit;
