<?php
session_start(); 
$errors = [];
$success_messages = [];

function e(string $s): string { 
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); 
}

function test_database_connection($database_url) {
    try {
        $pdo = new PDO($database_url);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return ['success' => true, 'pdo' => $pdo];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database_url = trim($_POST['database_url'] ?? '');
    $admin_email = trim($_POST['admin_email'] ?? '');
    $admin_user = trim($_POST['admin_user'] ?? '');
    $admin_pass = (string)($_POST['admin_pass'] ?? '');
    
    // Walidacja
    if ($database_url === '') {
        $errors[] = 'Podaj URL bazy danych Neon.';
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
            $success_messages[] = 'Testowanie połączenia z bazą danych Neon...';
            $db_test = test_database_connection($database_url);
            
            if (!$db_test['success']) {
                throw new RuntimeException('Błąd połączenia z bazą danych: ' . $db_test['error']);
            }
            $success_messages[] = '✓ Połączenie z bazą danych Neon OK';
            
            // Ustaw zmienną środowiskową (w Vercel to będzie przez panel)
            $success_messages[] = 'Ustawianie zmiennej środowiskowej DATABASE_URL...';
            $success_messages[] = 'W Vercel: Przejdź do Settings → Environment Variables';
            $success_messages[] = 'Dodaj: DATABASE_URL = ' . $database_url;
            
            // Wczytanie potrzebnych plików
            require __DIR__.'/db_vercel.php'; 
            require __DIR__.'/migrate_postgres.php';
            
            // Migracja bazy danych
            $success_messages[] = 'Uruchamianie migracji bazy danych...';
            $pdo = db();
            $ran = migrate_postgres($pdo, __DIR__.'/migrations_postgres');
            $success_messages[] = '✓ Migracja bazy danych zakończona';
            
            // Tworzenie administratora
            $success_messages[] = 'Tworzenie konta administratora...';
            $hash = password_hash($admin_pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO cms_users (email, username, password, role, display_name) VALUES (:e,:u,:p,'admin',:d)");
            $stmt->execute([':e'=>$admin_email, ':u'=>$admin_user, ':p'=>$hash, ':d'=>$admin_user]);
            $success_messages[] = '✓ Konto administratora utworzone';
            
            // Sprawdzenie czy wszystko działa
            $success_messages[] = 'Sprawdzanie instalacji...';
            $tables = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_name LIKE 'cms_%'")->fetchAll();
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
?><!doctype html><html lang="pl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Instalacja CMS v1.0 (Vercel + Neon)</title>
<style>body{font-family:system-ui,Segoe UI,Roboto,Inter,Arial;background:#0f1115;color:#e5e7eb;margin:0} .wrap{max-width:900px;margin:40px auto;padding:24px} .card{background:#12151b;border:1px solid #1f2733;border-radius:12px;padding:18px;margin:18px 0} label{display:block;margin-bottom:6px;color:#9ca3af} input{width:100%;padding:10px;border-radius:10px;border:1px solid #252b36;background:#0e1218;color:#e5e7eb;margin-bottom:10px} .btn{background:#3b82f6;color:#fff;border:0;border-radius:10px;padding:12px 16px;font-weight:700;cursor:pointer} .error{background:#1f2937;border-color:#dc2626} .success{background:#1f2937;border-color:#059669} .warning{background:#1f2937;border-color:#f59e0b} .log{background:#0e1218;padding:10px;border-radius:8px;margin:10px 0} .info{color:#9ca3af;margin:5px 0}</style></head>
<body><div class="wrap">
<h1>Instalacja CMS v1.0 (Vercel + Neon)</h1>

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
  <h2>Konfiguracja bazy danych Neon</h2>
  <div>
    <label>URL bazy danych Neon</label>
    <input name="database_url" placeholder="postgresql://user:pass@host:port/database" required value="<?php echo e($_POST['database_url'] ?? ''); ?>">
    <small>Znajdziesz to w panelu Neon → Settings → Connection Details</small>
  </div>
  
  <h2>Dane administratora</h2>
  <div>
    <label>E-mail administratora</label>
    <input type="email" name="admin_email" placeholder="admin@domena.pl" required value="<?php echo e($_POST['admin_email'] ?? ''); ?>">
  </div>
  <div>
    <label>Login administratora</label>
    <input name="admin_user" placeholder="admin" required value="<?php echo e($_POST['admin_user'] ?? ''); ?>">
  </div>
  <div>
    <label>Hasło administratora (min. 8 znaków)</label>
    <input type="password" name="admin_pass" placeholder="••••••••" required>
  </div>
  
  <button class="btn" type="submit">Rozpocznij instalację</button>
</form>

<div class="card warning">
  <h3>Instrukcja dla Vercel:</h3>
  <ol>
    <li>Wgraj pliki na GitHub</li>
    <li>Połącz z Vercel</li>
    <li>W Vercel: Settings → Environment Variables</li>
    <li>Dodaj: DATABASE_URL = twój_url_neon</li>
    <li>Redeploy aplikacji</li>
  </ol>
</div>
</div></body></html>
