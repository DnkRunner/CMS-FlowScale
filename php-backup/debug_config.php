<?php
// Debug pliku config.php
echo "<h1>Debug konfiguracji CMS</h1>";

// Sprawdź czy config.php istnieje
$configPath = __DIR__.'/config.php';
if (file_exists($configPath)) {
    echo "✅ Plik config.php istnieje<br>";
    
    try {
        $config = require $configPath;
        echo "✅ Plik config.php jest poprawny<br>";
        echo "<h3>Dane konfiguracyjne:</h3>";
        echo "<pre>";
        print_r($config);
        echo "</pre>";
        
        // Test połączenia z bazą
        echo "<h3>Test połączenia z bazą danych:</h3>";
        try {
            require_once 'db.php';
            $pdo = db();
            echo "✅ Połączenie z bazą danych OK<br>";
            
            // Sprawdź tabele
            $prefix = $config['db']['prefix'];
            $stmt = $pdo->query("SHOW TABLES LIKE '{$prefix}%'");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "✅ Znaleziono " . count($tables) . " tabel:<br>";
            foreach ($tables as $table) {
                echo "- {$table}<br>";
            }
            
        } catch (Exception $e) {
            echo "❌ Błąd połączenia z bazą: " . $e->getMessage() . "<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Błąd w config.php: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Plik config.php nie istnieje<br>";
}

// Sprawdź uprawnienia
echo "<h3>Uprawnienia plików:</h3>";
$files = ['config.php', 'db.php', 'helpers.php', 'auth.php'];
foreach ($files as $file) {
    if (file_exists($file)) {
        $perms = fileperms($file);
        echo "{$file}: " . substr(sprintf('%o', $perms), -4) . "<br>";
    } else {
        echo "{$file}: nie istnieje<br>";
    }
}

// Sprawdź błędy PHP
echo "<h3>Ostatnie błędy PHP:</h3>";
$error_log = __DIR__.'/logs/error.log';
if (file_exists($error_log)) {
    $errors = file_get_contents($error_log);
    if ($errors) {
        echo "<pre>" . htmlspecialchars($errors) . "</pre>";
    } else {
        echo "Brak błędów w logu<br>";
    }
} else {
    echo "Plik error.log nie istnieje<br>";
}
?>


