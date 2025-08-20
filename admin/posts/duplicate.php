<?php
require_once __DIR__.'/../../core/auth.php'; require_login();
require_once __DIR__.'/../../core/helpers.php';
$pdo = db(); $config = require __DIR__.'/../../config.php'; $prefix = $config['db']['prefix'];
$id = (int)($_GET['id'] ?? 0);
$p = $pdo->prepare("SELECT * FROM `{$prefix}posts` WHERE id=:id"); $p->execute([':id'=>$id]); $post = $p->fetch();
if ($post) {
  $newSlug = slugify($post['slug'].'-kopiuj');
  $stmt = $pdo->prepare("INSERT INTO `{$prefix}posts` (title,slug,content,status,template,author_id,published_at) VALUES (:t,:s,:c,:st,:tp,:a,:p)");
  $stmt->execute([
    ':t'=>$post['title'] . ' (kopiuj)',
    ':s'=>$newSlug,
    ':c'=>$post['content'],
    ':st'=>'draft',
    ':tp'=>$post['template'],
    ':a'=>$_SESSION['user_id'],
    ':p'=>null
  ]);
}
header('Location: '.admin_url('posts/index.php')); exit;
