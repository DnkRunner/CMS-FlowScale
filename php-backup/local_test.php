<?php
// Lokalny test CMS-a
echo "<h1>🧪 Lokalny test CMS-a</h1>";

// Sprawdź czy PHP działa
echo "<h2>1. Sprawdzenie PHP</h2>";
echo "✅ PHP wersja: " . PHP_VERSION . "<br>";
echo "✅ PDO dostępne: " . (extension_loaded('pdo') ? 'Tak' : 'Nie') . "<br>";
echo "✅ PDO PostgreSQL dostępne: " . (extension_loaded('pgsql') ? 'Tak' : 'Nie') . "<br>";

// Sprawdź pliki
echo "<h2>2. Sprawdzenie plików</h2>";
$files = [
    'vercel.json',
    'package.json', 
    'installer_vercel.php',
    'db_vercel.php',
    'migrate_postgres.php',
    'admin/index.php',
    'public/index.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "✅ {$file}<br>";
    } else {
        echo "❌ {$file}<br>";
    }
}

// Sprawdź migracje
echo "<h2>3. Sprawdzenie migracji PostgreSQL</h2>";
$migrations = glob('migrations_postgres/*.sql');
if ($migrations) {
    echo "✅ Znaleziono " . count($migrations) . " migracji:<br>";
    foreach ($migrations as $migration) {
        echo "- " . basename($migration) . "<br>";
    }
} else {
    echo "❌ Brak migracji PostgreSQL<br>";
}

// Test połączenia z bazą (jeśli DATABASE_URL jest ustawione)
echo "<h2>4. Test połączenia z bazą danych</h2>";
if (getenv('DATABASE_URL')) {
    try {
        require_once 'db_vercel.php';
        $pdo = db();
        echo "✅ Połączenie z bazą danych OK<br>";
        
        // Sprawdź tabele
        $tables = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_name LIKE 'cms_%'")->fetchAll();
        echo "✅ Znaleziono " . count($tables) . " tabel w bazie danych<br>";
        
    } catch (Exception $e) {
        echo "❌ Błąd połączenia z bazą: " . $e->getMessage() . "<br>";
    }
} else {
    echo "ℹ️ Zmienna DATABASE_URL nie jest ustawiona<br>";
    echo "Ustaw ją w Vercel: Settings → Environment Variables<br>";
}

// Instrukcje
echo "<h2>5. Następne kroki</h2>";
echo "<ol>";
echo "<li>Wgraj pliki na GitHub</li>";
echo "<li>Połącz z Vercel</li>";
echo "<li>Utwórz bazę na Neon</li>";
echo "<li>Ustaw DATABASE_URL w Vercel</li>";
echo "<li>Dodaj domenę konsumenckaugoda.pl</li>";
echo "<li>Uruchom instalator: /installer_vercel</li>";
echo "</ol>";

echo "<h2>6. Linki testowe</h2>";
echo "<a href='installer_vercel.php'>🧪 Test instalatora</a><br>";
echo "<a href='public/'>🌐 Test strony publicznej</a><br>";
echo "<a href='admin/'>⚙️ Test panelu admin</a><br>";

echo "<h2>7. Informacje o systemie</h2>";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Current Path: " . __DIR__ . "<br>";
?>
