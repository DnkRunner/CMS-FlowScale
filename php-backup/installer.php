<?php
session_start(); 
$errors = [];
$success_messages = [];

$configPath = __DIR__.'/config.php'; 
$sampleConfigPath = __DIR__.'/wp-config-sample.php';
$hasConfig = file_exists($configPath);
$hasSampleConfig = file_exists($sampleConfigPath);

function e(string $s): string { 
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); 
}

function write_config(string $host, string $db, string $user, string $pass, string $prefix) : bool {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $hostHdr = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base_path = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    if ($base_path === '/') $base_path = '';
    $base_url = $scheme.'://'.$hostHdr;
    $app_key = bin2hex(random_bytes(32));
    
    $content = <<<PHP
<?php
return [
    'db' => [
        'host' => '{$host}',
        'database' => '{$db}',
        'user' => '{$user}',
        'pass' => '{$pass}',
        'prefix' => '{$prefix}',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci'
    ],
    'app' => [
        'base_url' => '{$base_url}',
        'base_path' => '{$base_path}',
        'app_key' => '{$app_key}',
        'env' => 'production'
    ]
];
PHP;
    return (bool)file_put_contents(__DIR__.'/config.php', $content);
}

function test_database_connection($host, $db, $user, $pass) {
    try {
        $pdo = new PDO(
            "mysql:host={$host};dbname={$db};charset=utf8mb4",
            $user,
            $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        return ['success' => true, 'pdo' => $pdo];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$hasConfig && $hasSampleConfig) {
    // Poprawione pobieranie danych z formularza
    $db_host = trim($_POST['db_host'] ?? '');
    $db_name = trim($_POST['db_name'] ?? '');
    $db_user = trim($_POST['db_user'] ?? '');
    $db_pass = (string)($_POST['db_pass'] ?? '');
    $db_prefix = trim($_POST['db_prefix'] ?? 'cms_');
    $admin_email = trim($_POST['admin_email'] ?? '');
    $admin_user = trim($_POST['admin_user'] ?? '');
    $admin_pass = (string)($_POST['admin_pass'] ?? '');
    
    // Debug - pokaż co zostało przesłane
    $success_messages[] = 'Debug - dane z formularza:';
    $success_messages[] = 'Host: ' . $db_host;
    $success_messages[] = 'Baza: ' . $db_name;
    $success_messages[] = 'Użytkownik: ' . $db_user;
    
    // Walidacja
    if ($db_host === '' || $db_name === '' || $db_user === '') {
        $errors[] = 'Uzupełnij dane bazy danych.';
    }
    if (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Podaj poprawny e-mail administratora.';
    }
    if ($admin_user === '' || strlen($admin_user) < 3) {
        $errors[] = 'Login min. 3 znaki.';
    }
    if ($admin_pass === '' || strlen($admin_pass) < 8) {
        $errors[] = 'Hasło min. 8 znaków.';
    }
    
    if (!$errors) {
        try {
            // Test połączenia z bazą
            $success_messages[] = 'Testowanie połączenia z bazą danych...';
            $db_test = test_database_connection($db_host, $db_name, $db_user, $db_pass);
            
            if (!$db_test['success']) {
                throw new RuntimeException('Błąd połączenia z bazą danych: ' . $db_test['error']);
            }
            $success_messages[] = '✓ Połączenie z bazą danych OK';
            
            // Tworzenie config.php
            $success_messages[] = 'Tworzenie pliku config.php...';
            if (!write_config($db_host, $db_name, $db_user, $db_pass, $db_prefix)) {
                throw new RuntimeException('Nie udało się zapisać pliku config.php');
            }
            $success_messages[] = '✓ Plik config.php utworzony';
            
            // Wczytanie potrzebnych plików
            require __DIR__.'/db.php'; 
            require __DIR__.'/migrate.php';
            
            // Migracja bazy danych
            $success_messages[] = 'Uruchamianie migracji bazy danych...';
            $pdo = db();
            $ran = migrate($pdo, __DIR__.'/migrations', $db_prefix);
            $success_messages[] = '✓ Migracja bazy danych zakończona';
            
            // Tworzenie administratora
            $success_messages[] = 'Tworzenie konta administratora...';
            $hash = password_hash($admin_pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO `{$db_prefix}users` (email, username, password, role, display_name) VALUES (:e,:u,:p,'admin',:d)");
            $stmt->execute([':e'=>$admin_email, ':u'=>$admin_user, ':p'=>$hash, ':d'=>$admin_user]);
            $success_messages[] = '✓ Konto administratora utworzone';
            
            // Sprawdzenie czy wszystko działa
            $success_messages[] = 'Sprawdzanie instalacji...';
            $tables = $pdo->query("SHOW TABLES LIKE '{$db_prefix}%'")->fetchAll();
            $success_messages[] = '✓ Znaleziono ' . count($tables) . ' tabel w bazie danych';
            
            $success_messages[] = 'Instalacja zakończona pomyślnie!';
            $success_messages[] = 'Przekierowywanie do panelu administracyjnego...';
            
            // Przekierowanie
            header('Location: admin/login.php'); 
            exit;
            
        } catch (Throwable $e) { 
            $errors[] = 'Błąd podczas instalacji: ' . $e->getMessage();
        }
    }
}
?><!doctype html><html lang="pl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Instalacja CMS v1.0 (DEBUG)</title>
<style>body{font-family:system-ui,Segoe UI,Roboto,Inter,Arial;background:#0f1115;color:#e5e7eb;margin:0} .wrap{max-width:900px;margin:40px auto;padding:24px} .card{background:#12151b;border:1px solid #1f2733;border-radius:12px;padding:18px;margin:18px 0} label{display:block;margin-bottom:6px;color:#9ca3af} input{width:100%;padding:10px;border-radius:10px;border:1px solid #252b36;background:#0e1218;color:#e5e7eb;margin-bottom:10px} .row{display:grid;grid-template-columns:1fr 1fr;gap:14px} .btn{background:#3b82f6;color:#fff;border:0;border-radius:10px;padding:12px 16px;font-weight:700;cursor:pointer} .error{background:#1f2937;border-color:#dc2626} .success{background:#1f2937;border-color:#059669} .warning{background:#1f2937;border-color:#f59e0b} .log{background:#0e1218;padding:10px;border-radius:8px;margin:10px 0} .info{color:#9ca3af;margin:5px 0}</style></head>
<body><div class="wrap">
<h1><?php echo $hasConfig ? 'System jest już zainstalowany' : 'Instalacja CMS v1.0 (UNIVERSAL)'; ?></h1>

<?php if ($hasConfig): ?>
  <div class="card"><p>Znaleziono <code>config.php</code>. System jest już zainstalowany.</p>
    <p><a class="btn" href="admin/login.php">Przejdź do panelu administracyjnego</a></p>
  </div>
<?php elseif (!$hasSampleConfig): ?>
  <div class="card warning">
    <h3>⚠️ Brak pliku konfiguracyjnego</h3>
    <p>Nie znaleziono pliku <code>wp-config-sample.php</code>. Upewnij się, że wszystkie pliki zostały wgrane poprawnie.</p>
  </div>
<?php else: ?>
  <?php if (!empty($errors)): ?>
    <div class="card error">
      <h3>Błędy podczas instalacji:</h3>
      <ul>
        <?php foreach ($errors as $error): ?>
          <li><?php echo e($error); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
  
  <?php if (!empty($success_messages)): ?>
    <div class="card success">
      <h3>Postęp instalacji:</h3>
      <div class="log">
        <?php foreach ($success_messages as $msg): ?>
          <div class="info"><?php echo e($msg); ?></div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
  
  <form method="post" class="card">
    <h2>Konfiguracja bazy danych</h2>
    <div class="row">
      <div><label>Host bazy danych</label><input name="db_host" placeholder="root, localhost, mysql.domena.pl" required value="<?php echo e($_POST['db_host'] ?? ''); ?>"></div>
      <div><label>Nazwa bazy danych</label><input name="db_name" placeholder="cms_db" required value="<?php echo e($_POST['db_name'] ?? ''); ?>"></div>
    </div>
    <div class="row">
      <div><label>Użytkownik bazy danych</label><input name="db_user" placeholder="root" required value="<?php echo e($_POST['db_user'] ?? ''); ?>"></div>
      <div><label>Hasło bazy danych</label><input type="password" name="db_pass" placeholder="••••••••" value="<?php echo e($_POST['db_pass'] ?? ''); ?>"></div>
    </div>
    <div><label>Prefix tabel (opcjonalnie)</label><input name="db_prefix" placeholder="cms_" value="<?php echo e($_POST['db_prefix'] ?? 'cms_'); ?>"></div>
    
    <h2>Dane administratora</h2>
    <div class="row">
      <div><label>E-mail administratora</label><input type="email" name="admin_email" placeholder="admin@domena.pl" required value="<?php echo e($_POST['admin_email'] ?? ''); ?>"></div>
      <div><label>Login administratora</label><input name="admin_user" placeholder="admin" required value="<?php echo e($_POST['admin_user'] ?? ''); ?>"></div>
    </div>
    <div><label>Hasło administratora (min. 8 znaków)</label><input type="password" name="admin_pass" placeholder="••••••••" required></div>
    <button class="btn" type="submit">Rozpocznij instalację</button>
  </form>
<?php endif; ?>
</div></body></html>
