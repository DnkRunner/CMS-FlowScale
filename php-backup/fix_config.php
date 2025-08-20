<?php
// Naprawa konfiguracji CMS
echo "<h1>Naprawa konfiguracji CMS</h1>";

// Sprawdź czy config.php istnieje
$configPath = __DIR__.'/config.php';
if (!file_exists($configPath)) {
    echo "❌ Plik config.php nie istnieje - uruchom instalator<br>";
    echo "<a href='installer.php'>Przejdź do instalatora</a><br>";
    exit;
}

// Pokaż aktualną konfigurację
echo "<h3>Aktualna konfiguracja:</h3>";
try {
    $config = require $configPath;
    echo "<pre>";
    print_r($config);
    echo "</pre>";
} catch (Exception $e) {
    echo "❌ Błąd w config.php: " . $e->getMessage() . "<br>";
    exit;
}

// Formularz naprawy
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = $_POST['db_host'] ?? '';
    $db_name = $_POST['db_name'] ?? '';
    $db_user = $_POST['db_user'] ?? '';
    $db_pass = $_POST['db_pass'] ?? '';
    $db_prefix = $_POST['db_prefix'] ?? 'cms_';
    
    // Test połączenia
    try {
        $pdo = new PDO(
            "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4",
            $db_user,
            $db_pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        echo "✅ Połączenie z bazą danych OK<br>";
        
        // Aktualizuj config.php
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
        'host' => '{$db_host}',
        'database' => '{$db_name}',
        'user' => '{$db_user}',
        'pass' => '{$db_pass}',
        'prefix' => '{$db_prefix}',
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
        
        if (file_put_contents($configPath, $content)) {
            echo "✅ Plik config.php został zaktualizowany<br>";
            echo "<a href='admin/login.php'>Przejdź do panelu administracyjnego</a><br>";
        } else {
            echo "❌ Nie udało się zapisać config.php<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Błąd połączenia z bazą: " . $e->getMessage() . "<br>";
    }
} else {
    // Pokaż formularz
    ?>
    <form method="post">
        <h3>Napraw konfigurację bazy danych:</h3>
        <p>
            <label>Host bazy danych:</label><br>
            <input type="text" name="db_host" value="<?php echo htmlspecialchars($config['db']['host'] ?? ''); ?>" required>
        </p>
        <p>
            <label>Nazwa bazy danych:</label><br>
            <input type="text" name="db_name" value="<?php echo htmlspecialchars($config['db']['database'] ?? ''); ?>" required>
        </p>
        <p>
            <label>Użytkownik bazy danych:</label><br>
            <input type="text" name="db_user" value="<?php echo htmlspecialchars($config['db']['user'] ?? ''); ?>" required>
        </p>
        <p>
            <label>Hasło bazy danych:</label><br>
            <input type="password" name="db_pass" value="<?php echo htmlspecialchars($config['db']['pass'] ?? ''); ?>" required>
        </p>
        <p>
            <label>Prefix tabel:</label><br>
            <input type="text" name="db_prefix" value="<?php echo htmlspecialchars($config['db']['prefix'] ?? 'cms_'); ?>">
        </p>
        <p>
            <button type="submit">Napraw konfigurację</button>
        </p>
    </form>
    <?php
}
?>


