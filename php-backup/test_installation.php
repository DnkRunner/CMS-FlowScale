<?php
// Test instalacji CMS - sprawdza czy wszystko jest poprawnie skonfigurowane

echo "<h1>Test instalacji CMS</h1>";

// 1. Sprawdź czy config.php istnieje
echo "<h2>1. Sprawdzenie pliku config.php</h2>";
$configPath = __DIR__.'/config.php';
if (file_exists($configPath)) {
    echo "✅ Plik config.php istnieje<br>";
    try {
        $config = require $configPath;
        echo "✅ Plik config.php jest poprawny<br>";
        echo "Baza danych: {$config['db']['database']}<br>";
        echo "Prefix: {$config['db']['prefix']}<br>";
    } catch (Exception $e) {
        echo "❌ Błąd w config.php: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Plik config.php nie istnieje - system nie jest zainstalowany<br>";
    echo "<a href='installer.php'>Przejdź do instalatora</a><br>";
    exit;
}

// 2. Sprawdź połączenie z bazą danych
echo "<h2>2. Sprawdzenie połączenia z bazą danych</h2>";
try {
    require_once 'db.php';
    $pdo = db();
    echo "✅ Połączenie z bazą danych OK<br>";
} catch (Exception $e) {
    echo "❌ Błąd połączenia z bazą danych: " . $e->getMessage() . "<br>";
    exit;
}

// 3. Sprawdź tabele w bazie danych
echo "<h2>3. Sprawdzenie tabel w bazie danych</h2>";
$prefix = $config['db']['prefix'];
$tables = ['users', 'posts', 'pages', 'settings', 'categories', 'comments', 'theme_settings'];
$existing_tables = [];

foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '{$prefix}{$table}'");
        if ($stmt->rowCount() > 0) {
            echo "✅ Tabela {$prefix}{$table} istnieje<br>";
            $existing_tables[] = $table;
        } else {
            echo "❌ Tabela {$prefix}{$table} nie istnieje<br>";
        }
    } catch (Exception $e) {
        echo "❌ Błąd sprawdzania tabeli {$prefix}{$table}: " . $e->getMessage() . "<br>";
    }
}

// 4. Sprawdź czy istnieje administrator
echo "<h2>4. Sprawdzenie konta administratora</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM `{$prefix}users` WHERE role = 'admin'");
    $admin_count = $stmt->fetchColumn();
    if ($admin_count > 0) {
        echo "✅ Znaleziono {$admin_count} administratorów<br>";
    } else {
        echo "❌ Brak administratorów w systemie<br>";
    }
} catch (Exception $e) {
    echo "❌ Błąd sprawdzania administratorów: " . $e->getMessage() . "<br>";
}

// 5. Sprawdź ustawienia systemu
echo "<h2>5. Sprawdzenie ustawień systemu</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM `{$prefix}settings`");
    $settings_count = $stmt->fetchColumn();
    echo "✅ Znaleziono {$settings_count} ustawień systemowych<br>";
} catch (Exception $e) {
    echo "❌ Błąd sprawdzania ustawień: " . $e->getMessage() . "<br>";
}

// 6. Sprawdź ustawienia motywu
echo "<h2>6. Sprawdzenie ustawień motywu</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM `{$prefix}theme_settings`");
    $theme_settings_count = $stmt->fetchColumn();
    echo "✅ Znaleziono {$theme_settings_count} ustawień motywu<br>";
} catch (Exception $e) {
    echo "❌ Błąd sprawdzania ustawień motywu: " . $e->getMessage() . "<br>";
}

// 7. Sprawdź strukturę katalogów
echo "<h2>7. Sprawdzenie struktury katalogów</h2>";
$directories = ['admin', 'public', 'assets', 'media', 'uploads', 'storage', 'migrations'];
foreach ($directories as $dir) {
    if (is_dir(__DIR__.'/'.$dir)) {
        echo "✅ Katalog {$dir} istnieje<br>";
    } else {
        echo "❌ Katalog {$dir} nie istnieje<br>";
    }
}

// 8. Sprawdź pliki .htaccess
echo "<h2>8. Sprawdzenie plików .htaccess</h2>";
$htaccess_files = ['.htaccess', 'admin/.htaccess', 'public/.htaccess'];
foreach ($htaccess_files as $file) {
    if (file_exists(__DIR__.'/'.$file)) {
        echo "✅ Plik {$file} istnieje<br>";
    } else {
        echo "❌ Plik {$file} nie istnieje<br>";
    }
}

// 9. Sprawdź dostępność panelu administracyjnego
echo "<h2>9. Sprawdzenie panelu administracyjnego</h2>";
$admin_files = ['admin/index.php', 'admin/login.php', 'admin/dashboard.php'];
foreach ($admin_files as $file) {
    if (file_exists(__DIR__.'/'.$file)) {
        echo "✅ Plik {$file} istnieje<br>";
    } else {
        echo "❌ Plik {$file} nie istnieje<br>";
    }
}

// 10. Sprawdź dostępność strony publicznej
echo "<h2>10. Sprawdzenie strony publicznej</h2>";
$public_files = ['public/index.php', 'public/blog.php'];
foreach ($public_files as $file) {
    if (file_exists(__DIR__.'/'.$file)) {
        echo "✅ Plik {$file} istnieje<br>";
    } else {
        echo "❌ Plik {$file} nie istnieje<br>";
    }
}

echo "<h2>Podsumowanie</h2>";
if (count($existing_tables) >= 5) {
    echo "✅ System jest poprawnie zainstalowany!<br>";
    echo "<a href='admin/login.php'>Przejdź do panelu administracyjnego</a><br>";
    echo "<a href='public/'>Przejdź do strony publicznej</a><br>";
} else {
    echo "❌ System wymaga naprawy lub ponownej instalacji<br>";
    echo "<a href='installer.php'>Przejdź do instalatora</a><br>";
}
?>
