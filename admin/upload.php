<?php
require_once __DIR__.'/../core/auth.php'; 
require_login();

// Sprawdź czy to żądanie POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// Sprawdź czy plik został przesłany
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    exit('No file uploaded or upload error');
}

$file = $_FILES['file'];
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$maxSize = 5 * 1024 * 1024; // 5MB

// Sprawdź typ pliku
if (!in_array($file['type'], $allowedTypes)) {
    http_response_code(400);
    exit('Invalid file type. Only JPEG, PNG, GIF and WebP are allowed.');
}

// Sprawdź rozmiar pliku
if ($file['size'] > $maxSize) {
    http_response_code(400);
    exit('File too large. Maximum size is 5MB.');
}

// Utwórz katalog uploads jeśli nie istnieje
$uploadDir = __DIR__ . '/../storage/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Wygeneruj unikalną nazwę pliku
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid() . '.' . $extension;
$filepath = $uploadDir . $filename;

// Przenieś plik
if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    http_response_code(500);
    exit('Failed to save file');
}

// Zwróć URL do pliku
$config = require __DIR__.'/../config.php';
$baseUrl = $config['app']['base_url'] . $config['app']['base_path'];
$fileUrl = $baseUrl . '/storage/uploads/' . $filename;

// Zwróć odpowiedź w formacie JSON dla TinyMCE
header('Content-Type: application/json');
echo json_encode([
    'location' => $fileUrl
]);
