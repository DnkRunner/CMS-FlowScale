<?php
require_once __DIR__.'/../../core/auth.php'; 
require_login();
require_once __DIR__.'/../../core/migrate.php';
require_once __DIR__.'/../../core/helpers.php';

$pdo = db(); 
$config = require __DIR__.'/../../config.php'; 
$prefix = $config['db']['prefix'];

$msg = '';
$msg_type = 'info';

// Obsługa wgrywania plików
if ($_POST['action'] ?? '' === 'upload_update') {
    try {
        if (!isset($_FILES['update_file']) || $_FILES['update_file']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Błąd podczas wgrywania pliku');
        }
        
        $uploaded_file = $_FILES['update_file'];
        $file_extension = strtolower(pathinfo($uploaded_file['name'], PATHINFO_EXTENSION));
        
        if ($file_extension !== 'zip') {
            throw new Exception('Dozwolone są tylko pliki ZIP');
        }
        
        $temp_dir = __DIR__.'/../../storage/temp/';
        if (!is_dir($temp_dir)) {
            mkdir($temp_dir, 0755, true);
        }
        
        $temp_file = $temp_dir . 'update_' . time() . '.zip';
        if (!move_uploaded_file($uploaded_file['tmp_name'], $temp_file)) {
            throw new Exception('Nie udało się zapisać pliku tymczasowego');
        }
        
        // Rozpakuj plik
        $zip = new ZipArchive();
        if ($zip->open($temp_file) !== TRUE) {
            throw new Exception('Nie można otworzyć pliku ZIP');
        }
        
        $extract_dir = $temp_dir . 'extracted_' . time() . '/';
        if (!mkdir($extract_dir)) {
            throw new Exception('Nie można utworzyć katalogu tymczasowego');
        }
        
        $zip->extractTo($extract_dir);
        $zip->close();
        
        // Sprawdź strukturę pliku
        if (!file_exists($extract_dir . 'version.txt')) {
            throw new Exception('Nieprawidłowa struktura pliku aktualizacji - brak version.txt');
        }
        
        $new_version = trim(file_get_contents($extract_dir . 'version.txt'));
        $current_version = get_current_version();
        
        if (version_compare($new_version, $current_version, '<=')) {
            throw new Exception("Nowa wersja ($new_version) nie jest nowsza niż obecna ($current_version)");
        }
        
        // Wykonaj aktualizację
        perform_update($extract_dir, $new_version);
        
        // Wyczyść pliki tymczasowe
        unlink($temp_file);
        delete_directory($extract_dir);
        
        $msg = "Aktualizacja do wersji $new_version została pomyślnie zainstalowana!";
        $msg_type = 'success';
        
    } catch (Exception $e) {
        $msg = 'Błąd aktualizacji: ' . $e->getMessage();
        $msg_type = 'error';
        
        // Wyczyść pliki tymczasowe w przypadku błędu
        if (isset($temp_file) && file_exists($temp_file)) {
            unlink($temp_file);
        }
        if (isset($extract_dir) && is_dir($extract_dir)) {
            delete_directory($extract_dir);
        }
    }
}

// Wykonaj migracje bazy danych
if ($_POST['action'] ?? '' === 'run_migrations') {
    try {
        $ran = migrate($pdo, __DIR__.'/../../core/migrations', $prefix);
        $msg = $ran ? ('Wykonano migracje: ' . implode(', ', $ran)) : 'Brak nowych migracji.';
        $msg_type = 'success';
    } catch (Throwable $e) {
        $msg = 'Błąd migracji: ' . $e->getMessage();
        $msg_type = 'error';
    }
}

// Pobierz informacje o systemie
$current_version = get_current_version();
$applied_migrations = get_applied_migrations($pdo, $prefix);
$available_migrations = get_available_migrations();

$title = 'Aktualizacja systemu';
require __DIR__.'/../../layout-header.php';
?>

<h1>Aktualizacja systemu</h1>

<?php if ($msg): ?>
    <div class="alert alert-<?php echo $msg_type; ?>" style="margin-bottom: 20px;">
        <?php echo e($msg); ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
    
    <!-- Informacje o systemie -->
    <div class="card">
        <h2>Informacje o systemie</h2>
        <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
            <p style="margin: 0 0 10px 0;"><strong>Aktualna wersja:</strong> <?php echo e($current_version); ?></p>
            <p style="margin: 0 0 10px 0;"><strong>Zastosowane migracje:</strong> <?php echo count($applied_migrations); ?></p>
            <p style="margin: 0;"><strong>Dostępne migracje:</strong> <?php echo count($available_migrations); ?></p>
        </div>
        
        <h3>Zastosowane migracje</h3>
        <?php if ($applied_migrations): ?>
            <div style="max-height: 200px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px;">
                <?php foreach ($applied_migrations as $migration): ?>
                    <div style="padding: 5px 0; border-bottom: 1px solid #f3f4f6;">
                        <span style="font-family: monospace; color: #059669;">✓</span>
                        <?php echo e($migration); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color: #666;">Brak zastosowanych migracji.</p>
        <?php endif; ?>
        
        <form method="post" style="margin-top: 15px;">
            <input type="hidden" name="action" value="run_migrations">
            <button type="submit" class="btn">Uruchom migracje bazy danych</button>
        </form>
    </div>
    
    <!-- Aktualizacja CMS -->
    <div class="card">
        <h2>Aktualizacja CMS</h2>
        <div style="background: #f0f9ff; border: 1px solid #0ea5e9; border-radius: 6px; padding: 15px; margin-bottom: 15px;">
            <p style="margin: 0; color: #0c4a6e; font-size: 14px;">
                <strong>Instrukcja:</strong> Wgraj plik ZIP z nową wersją CMS. Plik powinien zawierać:
            </p>
            <ul style="margin: 10px 0 0 0; color: #0c4a6e; font-size: 14px;">
                <li>Plik <code>version.txt</code> z numerem wersji</li>
                <li>Katalog <code>core/</code> z nowymi plikami</li>
                <li>Katalog <code>admin/</code> z aktualizacjami panelu</li>
                <li>Pliki migracji w <code>core/migrations/</code></li>
            </ul>
        </div>
        
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="upload_update">
            
            <div style="margin-bottom: 15px;">
                <label for="update_file" style="display: block; margin-bottom: 5px; font-weight: 500;">
                    Wybierz plik aktualizacji (ZIP):
                </label>
                <input type="file" id="update_file" name="update_file" accept=".zip" required 
                       style="width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 4px;">
            </div>
            
            <button type="submit" class="btn" style="background: #059669; color: white;">
                Zainstaluj aktualizację
            </button>
        </form>
        
        <div style="margin-top: 15px; padding: 10px; background: #fef3c7; border: 1px solid #f59e0b; border-radius: 6px;">
            <p style="margin: 0; color: #92400e; font-size: 12px;">
                <strong>Uwaga:</strong> Przed aktualizacją zalecane jest wykonanie kopii zapasowej bazy danych i plików.
            </p>
        </div>
    </div>
</div>

<!-- Historia aktualizacji -->
<div class="card">
    <h2>Historia aktualizacji</h2>
    <?php
    $update_history = get_update_history();
    if ($update_history): ?>
        <div style="max-height: 300px; overflow-y: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Data</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Wersja</th>
                        <th style="padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($update_history as $update): ?>
                        <tr>
                            <td style="padding: 10px; border-bottom: 1px solid #f3f4f6;">
                                <?php echo date('d.m.Y H:i', strtotime($update['date'])); ?>
                            </td>
                            <td style="padding: 10px; border-bottom: 1px solid #f3f4f6;">
                                <?php echo e($update['version']); ?>
                            </td>
                            <td style="padding: 10px; border-bottom: 1px solid #f3f4f6;">
                                <span style="color: <?php echo $update['status'] === 'success' ? '#059669' : '#dc2626'; ?>;">
                                    <?php echo $update['status'] === 'success' ? '✓ Sukces' : '✗ Błąd'; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="color: #666;">Brak historii aktualizacji.</p>
    <?php endif; ?>
</div>

<div style="margin-top: 20px;">
    <a href="<?php echo admin_url('dashboard.php'); ?>" class="btn">← Wróć do Dashboardu</a>
</div>

<?php require __DIR__.'/../layout-footer.php'; ?>

<?php
// Funkcje pomocnicze
function get_current_version() {
    $version_file = __DIR__.'/../../version.txt';
    if (file_exists($version_file)) {
        return trim(file_get_contents($version_file));
    }
    return '1.0.0';
}

function get_applied_migrations(PDO $pdo, string $prefix) {
    try {
        $stmt = $pdo->query("SELECT version FROM `{$prefix}schema_migrations` ORDER BY version");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {
        return [];
    }
}

function get_available_migrations() {
    $migrations_dir = __DIR__.'/../../core/migrations';
    if (!is_dir($migrations_dir)) {
        return [];
    }
    
    $files = glob($migrations_dir . '/*.sql');
    return array_map('basename', $files);
}

function perform_update($extract_dir, $new_version) {
    $root_dir = __DIR__.'/../../';
    
    // Kopiuj pliki z wyjątkiem config.php i storage/
    $exclude_dirs = ['config.php', 'storage', 'temp'];
    
    copy_directory($extract_dir, $root_dir, $exclude_dirs);
    
    // Zaktualizuj wersję
    file_put_contents($root_dir . 'version.txt', $new_version);
    
    // Zapisz historię aktualizacji
    log_update($new_version, 'success');
}

function copy_directory($source, $destination, $exclude = []) {
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }
    
    $dir = opendir($source);
    while (($file = readdir($dir)) !== false) {
        if ($file === '.' || $file === '..') continue;
        
        $source_path = $source . '/' . $file;
        $dest_path = $destination . '/' . $file;
        
        if (in_array($file, $exclude)) continue;
        
        if (is_dir($source_path)) {
            copy_directory($source_path, $dest_path, $exclude);
        } else {
            copy($source_path, $dest_path);
        }
    }
    closedir($dir);
}

function delete_directory($dir) {
    if (!is_dir($dir)) return;
    
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            delete_directory($path);
        } else {
            unlink($path);
        }
    }
    rmdir($dir);
}

function log_update($version, $status) {
    $log_file = __DIR__.'/../../storage/logs/updates.log';
    $log_dir = dirname($log_file);
    
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $log_entry = date('Y-m-d H:i:s') . '|' . $version . '|' . $status . PHP_EOL;
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

function get_update_history() {
    $log_file = __DIR__.'/../../storage/logs/updates.log';
    if (!file_exists($log_file)) {
        return [];
    }
    
    $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $history = [];
    
    foreach (array_reverse($lines) as $line) {
        $parts = explode('|', $line);
        if (count($parts) === 3) {
            $history[] = [
                'date' => $parts[0],
                'version' => $parts[1],
                'status' => $parts[2]
            ];
        }
    }
    
    return array_slice($history, 0, 10); // Ostatnie 10 aktualizacji
}
?>
