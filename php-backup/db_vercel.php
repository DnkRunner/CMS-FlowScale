<?php
function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    
    // Sprawdź czy używamy zmiennych środowiskowych (Vercel)
    if (getenv('DATABASE_URL')) {
        $database_url = getenv('DATABASE_URL');
        $pdo = new PDO($database_url);
    } else {
        // Fallback do config.php (lokalne środowisko)
        $config = require __DIR__.'/config.php';
        $dsn = sprintf('pgsql:host=%s;dbname=%s;port=%s',
            $config['db']['host'],
            $config['db']['database'],
            $config['db']['port'] ?? 5432
        );
        $pdo = new PDO($dsn, $config['db']['user'], $config['db']['pass']);
    }
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    return $pdo;
}

// Funkcja do konwersji MySQL -> PostgreSQL
function mysql_to_pgsql($sql) {
    // Zamień ` na " dla PostgreSQL
    $sql = str_replace('`', '"', $sql);
    
    // Zamień AUTO_INCREMENT na SERIAL
    $sql = preg_replace('/AUTO_INCREMENT/', 'SERIAL', $sql);
    
    // Zamień ENGINE=InnoDB na nic (PostgreSQL nie używa)
    $sql = preg_replace('/ENGINE=InnoDB/', '', $sql);
    
    // Zamień DEFAULT CHARSET na nic
    $sql = preg_replace('/DEFAULT CHARSET=[^;]+/', '', $sql);
    
    // Zamień COLLATE na nic
    $sql = preg_replace('/COLLATE=[^;]+/', '', $sql);
    
    // Zamień TIMESTAMP na TIMESTAMP
    $sql = str_replace('TIMESTAMP', 'TIMESTAMP', $sql);
    
    // Zamień DATETIME na TIMESTAMP
    $sql = str_replace('DATETIME', 'TIMESTAMP', $sql);
    
    return $sql;
}
