<?php
// Główny plik index.php - sprawdza czy system jest zainstalowany
$configPath = __DIR__.'/config.php';

if (file_exists($configPath)) {
    // System jest zainstalowany - przekieruj do strony publicznej
    header('Location: public/');
    exit;
} else {
    // System nie jest zainstalowany - przekieruj do instalatora
    header('Location: installer.php');
    exit;
}
