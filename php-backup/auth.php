<?php
require_once __DIR__.'/db.php';
require_once __DIR__.'/helpers.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }
function current_user_id(): ?int { return $_SESSION['user_id'] ?? null; }
function is_logged_in(): bool { return current_user_id() !== null; }
function require_login(): void { if (!is_logged_in()) { header('Location: '.admin_url('login.php')); exit; } }
function logout(): void {
    $_SESSION = []; if (ini_get('session.use_cookies')) { $p = session_get_cookie_params(); setcookie(session_name(), '', time()-42000, $p['path'],$p['domain'],$p['secure'],$p['httponly']); }
    session_destroy();
}
function login_user(string $loginOrEmail, string $password): bool {
    $pdo = db(); $config = require __DIR__.'/config.php'; $prefix = $config['db']['prefix'];
    $stmt = $pdo->prepare("SELECT id,email,username,password,role FROM `{$prefix}users` WHERE username=:u OR email=:u LIMIT 1");
    $stmt->execute([':u'=>$loginOrEmail]); $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password'])) return false;
    $_SESSION['user_id'] = (int)$user['id']; $_SESSION['user_role'] = $user['role']; $_SESSION['user_name'] = $user['username']; return true;
}

function get_current_user(): ?array {
    if (!is_logged_in()) return null;
    $pdo = db(); $config = require __DIR__.'/config.php'; $prefix = $config['db']['prefix'];
    $stmt = $pdo->prepare("SELECT * FROM `{$prefix}users` WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => current_user_id()]);
    return $stmt->fetch();
}
