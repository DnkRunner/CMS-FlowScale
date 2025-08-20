<?php
function migrate(PDO $pdo, string $migrationsDir, string $prefix): array {
    $pdo->exec("CREATE TABLE IF NOT EXISTS schema_migrations (version VARCHAR(50) PRIMARY KEY, applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    $applied = $pdo->query("SELECT version FROM schema_migrations")->fetchAll(PDO::FETCH_COLUMN) ?: [];
    $files = glob(rtrim($migrationsDir,'/')."/*.sql"); natcasesort($files);
    $ran = [];
    foreach ($files as $file) {
        $base = basename($file);
        $version = explode('_', $base, 2)[0];
        if (in_array($version, $applied, true)) continue;
        $sql = file_get_contents($file);
        $sql = str_replace('{{prefix}}', $prefix, $sql);
        $stmts = array_filter(array_map('trim', preg_split('/;\s*$/m', $sql)));
        try {
            foreach ($stmts as $stmt) { if ($stmt === '') continue; $pdo->exec($stmt); }
            $stmtIns = $pdo->prepare("INSERT INTO schema_migrations (version) VALUES (:v)");
            $stmtIns->execute([':v'=>$version]);
            $ran[] = $base;
        } catch (Throwable $e) { throw new RuntimeException("Błąd w migracji {$base}: ".$e->getMessage()); }
    }
    return $ran;
}
