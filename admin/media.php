<?php
$title = 'Zarządzanie mediami';
require_once __DIR__.'/../core/auth.php'; 
require_login();
require_once __DIR__.'/../core/helpers.php';
$pdo = db(); 
$config = require __DIR__.'/../config.php'; 
$prefix = $config['db']['prefix'];

// Obsługa uploadu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media'])) {
    $uploadedFiles = [];
    $errors = [];
    
    foreach ($_FILES['media']['tmp_name'] as $key => $tmpName) {
        if ($_FILES['media']['error'][$key] === UPLOAD_ERR_OK) {
            $fileName = $_FILES['media']['name'][$key];
            $fileSize = $_FILES['media']['size'][$key];
            $fileType = $_FILES['media']['type'][$key];
            
            // Sprawdź typ pliku
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
            if (!in_array($fileType, $allowedTypes)) {
                $errors[] = "Plik $fileName ma nieobsługiwany typ.";
                continue;
            }
            
            // Sprawdź rozmiar (max 10MB)
            if ($fileSize > 10 * 1024 * 1024) {
                $errors[] = "Plik $fileName jest za duży (max 10MB).";
                continue;
            }
            
            // Wygeneruj unikalną nazwę
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            $uniqueName = uniqid() . '_' . time() . '.' . $extension;
            $uploadPath = __DIR__ . '/../storage/media/' . $uniqueName;
            
            // Przenieś plik
            if (move_uploaded_file($tmpName, $uploadPath)) {
                $uploadedFiles[] = [
                    'original_name' => $fileName,
                    'file_name' => $uniqueName,
                    'file_size' => $fileSize,
                    'file_type' => $fileType,
                    'upload_date' => date('Y-m-d H:i:s')
                ];
            } else {
                $errors[] = "Błąd podczas zapisywania pliku $fileName.";
            }
        }
    }
}

// Pobierz listę plików
$mediaDir = __DIR__ . '/../storage/media/';
$mediaFiles = [];
if (is_dir($mediaDir)) {
    $files = scandir($mediaDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && is_file($mediaDir . $file)) {
            $filePath = $mediaDir . $file;
            $fileInfo = pathinfo($file);
            $mediaFiles[] = [
                'name' => $file,
                'original_name' => $file,
                'size' => filesize($filePath),
                'type' => mime_content_type($filePath),
                'date' => date('Y-m-d H:i:s', filemtime($filePath)),
                'url' => site_url('storage/media/' . $file)
            ];
        }
    }
    // Sortuj po dacie (najnowsze pierwsze)
    usort($mediaFiles, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
}

require __DIR__.'/layout-header.php';
?>

<h1>Zarządzanie mediami</h1>

<!-- Upload form -->
<div class="card" style="margin-bottom: 20px;">
    <h3>Dodaj nowe pliki</h3>
    <form method="post" enctype="multipart/form-data">
        <div style="margin-bottom: 10px;">
            <label>Wybierz pliki (JPG, PNG, GIF, WebP, SVG - max 10MB każdy)</label>
            <input type="file" name="media[]" multiple accept="image/*" class="input" required>
        </div>
        <button type="submit" class="btn">Prześlij pliki</button>
    </form>
    
    <?php if (!empty($uploadedFiles)): ?>
        <div style="margin-top: 10px; padding: 10px; background: #dcfce7; border-radius: 8px; color: #166534;">
            <strong>Przesłano pliki:</strong>
            <ul style="margin: 5px 0 0 20px;">
                <?php foreach ($uploadedFiles as $file): ?>
                    <li><?php echo e($file['original_name']); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div style="margin-top: 10px; padding: 10px; background: #fecaca; border-radius: 8px; color: #dc2626;">
            <strong>Błędy:</strong>
            <ul style="margin: 5px 0 0 20px;">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>

<!-- Media grid -->
<div class="card">
    <h3>Pliki mediów (<?php echo count($mediaFiles); ?>)</h3>
    
    <?php if (empty($mediaFiles)): ?>
        <p style="color: var(--muted); text-align: center; padding: 40px;">Brak plików mediów. Dodaj pierwszy plik powyżej.</p>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; margin-top: 16px;">
            <?php foreach ($mediaFiles as $file): ?>
                <div style="border: 1px solid var(--border); border-radius: 8px; padding: 12px; background: var(--card);">
                    <?php if (strpos($file['type'], 'image/') === 0): ?>
                        <img src="<?php echo e($file['url']); ?>" alt="<?php echo e($file['original_name']); ?>" 
                             style="width: 100%; height: 120px; object-fit: cover; border-radius: 4px; margin-bottom: 8px;">
                    <?php else: ?>
                        <div style="width: 100%; height: 120px; background: var(--border); display: flex; align-items: center; justify-content: center; border-radius: 4px; margin-bottom: 8px;">
                            <span style="font-size: 24px;">📄</span>
                        </div>
                    <?php endif; ?>
                    
                    <div style="font-size: 12px; color: var(--muted); margin-bottom: 4px;">
                        <?php echo e($file['original_name']); ?>
                    </div>
                    
                    <div style="font-size: 11px; color: var(--muted); margin-bottom: 8px;">
                        <?php echo number_format($file['size'] / 1024, 1); ?> KB • 
                        <?php echo date('d.m.Y H:i', strtotime($file['date'])); ?>
                    </div>
                    
                    <div style="display: flex; gap: 4px;">
                        <button type="button" class="btn" style="font-size: 11px; padding: 4px 8px;" 
                                onclick="copyToClipboard('<?php echo e($file['url']); ?>')">
                            Kopiuj URL
                        </button>
                        <button type="button" class="btn" style="font-size: 11px; padding: 4px 8px; background: #10b981;" 
                                onclick="selectMedia('<?php echo e($file['url']); ?>')">
                            Wybierz
                        </button>
                        <button type="button" class="btn" style="font-size: 11px; padding: 4px 8px; background: #dc2626;" 
                                onclick="deleteMedia('<?php echo e($file['name']); ?>')">
                            Usuń
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('URL skopiowany do schowka!');
    }).catch(() => {
        // Fallback dla starszych przeglądarek
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('URL skopiowany do schowka!');
    });
}

function selectMedia(url) {
    console.log('selectMedia wywołane z URL:', url);
    console.log('window.opener:', window.opener);
    
    // Sprawdź czy to popup
    if (window.opener) {
        console.log('Wysyłam wiadomość do okna nadrzędnego:', url);
        try {
            window.opener.postMessage({
                type: 'selectMedia',
                url: url
            }, '*');
            console.log('Wiadomość wysłana pomyślnie');
            // Zamknij okno po krótkim opóźnieniu
            setTimeout(() => {
                window.close();
            }, 100);
        } catch (error) {
            console.error('Błąd podczas wysyłania wiadomości:', error);
            copyToClipboard(url);
        }
    } else {
        console.log('To nie jest popup, kopiuję URL');
        copyToClipboard(url);
    }
}

function deleteMedia(fileName) {
    if (confirm('Czy na pewno chcesz usunąć ten plik?')) {
        fetch('<?php echo admin_url('media.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=delete&file=' + encodeURIComponent(fileName)
        }).then(() => {
            location.reload();
        });
    }
}
</script>

<?php require __DIR__.'/layout-footer.php'; ?>
