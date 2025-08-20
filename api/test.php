<?php
header('Content-Type: application/json');

// Simple test without database
echo json_encode([
    'status' => 'API is working',
    'timestamp' => date('Y-m-d H:i:s'),
    'php_version' => PHP_VERSION,
    'message' => 'CMS-FlowScale API is running correctly'
]);
?>
