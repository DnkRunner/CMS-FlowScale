<?php
// core/helpers.php — v6.3.2 (konfiguracja steruje bazą; bez zgadywania)
function cfg(): array {
    static $c = null;
    if ($c === null) {
        $c = require __DIR__.'/config.php';
        // Backward-compat: dopisz base_path gdy brak
        if (!isset($c['app']['base_path']) || $c['app']['base_path']==='') {
            $bp = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'])), '/');
            // jeśli jesteśmy w /admin, obetnij /admin z końca
            $bp = preg_replace('#/admin(/.*)?$#', '', $bp);
            if ($bp === '/') $bp = '';
            $c['app']['base_path'] = $bp;
        }
        if (!isset($c['app']['base_url']) || $c['app']['base_url']==='') {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $c['app']['base_url'] = $scheme.'://'.$host;
        }
    }
    return $c;
}
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function site_url(string $path = ''): string {
    $c = cfg()['app']; $base = rtrim($c['base_url'], '/').($c['base_path']??'');
    return $base . ($path ? '/'.ltrim($path,'/') : '');
}
function admin_url(string $path = ''): string { return site_url('admin'.($path?'/'.ltrim($path,'/'):'')); }
function app_path(string $path = ''): string {
    $base = rtrim(cfg()['app']['base_path'] ?? '', '/');
    return $base . ($path ? '/'.ltrim($path,'/') : '');
}
function slugify($text) {
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('~[^\pL0-9]+~u', '-', $text);
    $text = trim($text, '-');
    $text = preg_replace('~[^-a-z0-9]+~', '', $text);
    return $text ?: bin2hex(random_bytes(4));
}
function get_setting(PDO $pdo, string $prefix, string $key, $default = '') {
    try { $st = $pdo->prepare("SELECT `value` FROM `{$prefix}settings` WHERE `key`=:k"); $st->execute([':k'=>$key]); $v = $st->fetchColumn(); return $v!==false ? $v : $default; } catch (Throwable $e) { return $default; }
}
function set_setting(PDO $pdo, string $prefix, string $key, string $value): void {
    $st = $pdo->prepare("INSERT INTO `{$prefix}settings` (`key`,`value`) VALUES (:k,:v) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)");
    $st->execute([':k'=>$key, ':v'=>$value]);
}
function debug_enabled(PDO $pdo, string $prefix): bool {
    return (bool) (int) get_setting($pdo, $prefix, 'debug', '0');
}
function debug_log(string $msg): void {
    $file = __DIR__.'/../storage/logs/app.log';
    @file_put_contents($file, '['.date('Y-m-d H:i:s').'] '.$msg.PHP_EOL, FILE_APPEND);
}

function get_theme_setting($key, $default = null) {
    static $settings = null;
    
    if ($settings === null) {
        require_once __DIR__.'/db.php';
        $pdo = db();
        $config = require __DIR__.'/config.php';
        $prefix = $config['db']['prefix'];
        
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM `{$prefix}theme_settings`");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    return $settings[$key] ?? $default;
}

function get_menu_items() {
    require_once __DIR__.'/db.php';
    $pdo = db();
    $config = require __DIR__.'/config.php';
    $prefix = $config['db']['prefix'];
    
    $stmt = $pdo->query("SELECT * FROM `{$prefix}menu_items` WHERE is_active = 1 ORDER BY position");
    return $stmt->fetchAll();
}
