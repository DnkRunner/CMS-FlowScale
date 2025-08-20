<?php
require_once __DIR__.'/../auth.php';
require_once __DIR__.'/../helpers.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? ''); $pass = (string)($_POST['password'] ?? '');
    if ($login === '' || $pass === '') { $errors[] = 'Podaj login/e-mail i hasło.'; }
    else if (login_user($login, $pass)) { header('Location: index.php'); exit; }
    else { $errors[] = 'Nieprawidłowe dane logowania.'; }
}
?><!doctype html><html lang="pl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Logowanie</title>
<style>:root{--bg:#0f1115;--fg:#e5e7eb;--muted:#9ca3af;--acc:#3b82f6;--err:#ef4444;}*{box-sizing:border-box;font-family:system-ui,Segoe UI,Roboto,Inter,Arial}body{background:var(--bg);color:var(--fg);margin:0;min-height:100vh;display:grid;place-items:center}.card{background:#12151b;border:1px solid #1f2733;border-radius:12px;padding:24px;max-width:420px;width:100%}label{display:block;font-size:14px;color:var(--muted);margin-bottom:6px}input{width:100%;padding:12px;border-radius:10px;border:1px solid #252b36;background:#0e1218;color:var(--fg);margin-bottom:12px}.btn{background:var(--acc);border:0;color:white;padding:12px 16px;border-radius:10px;cursor:pointer;font-weight:600;width:100%}.errors{background:#1a1113;border:1px solid #3b1520;color:#fecaca;padding:12px;border-radius:8px;margin-bottom:12px}h1{margin-top:0}a{color:#93c5fd;text-decoration:none}</style></head>
<body><div class="card"><h1>Logowanie do panelu</h1><?php if ($errors): ?><div class="errors"><ul><?php foreach ($errors as $e): ?><li><?php echo e($e); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post"><label>Login lub e-mail</label><input name="login" placeholder="admin lub admin@domena.pl" required><label>Hasło</label><input type="password" name="password" placeholder="••••••••" required><button class="btn" type="submit">Zaloguj</button></form>
<p style="margin-top:10px"><a href="<?php echo site_url('index.php'); ?>">← Wróć do strony</a></p></div></body></html>
