<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Simple in-memory storage for demo
$pagesFile = 'pages.json';

// Initialize pages file if it doesn't exist
if (!file_exists($pagesFile)) {
    file_put_contents($pagesFile, json_encode([]));
}

function getPages() {
    global $pagesFile;
    $content = file_get_contents($pagesFile);
    return json_decode($content, true) ?: [];
}

function savePages($pages) {
    global $pagesFile;
    file_put_contents($pagesFile, json_encode($pages, JSON_PRETTY_PRINT));
}

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

// Handle different endpoints
if ($method === 'GET') {
    if (strpos($path, '/api/pages') !== false) {
        // Get all pages
        $pages = getPages();
        echo json_encode(['success' => true, 'data' => $pages]);
    }
} elseif ($method === 'POST') {
    if (strpos($path, '/api/pages') !== false) {
        // Create new page
        $input = json_decode(file_get_contents('php://input'), true);
        
        $pages = getPages();
        $newPage = [
            'id' => time() . rand(100, 999),
            'title' => $input['title'],
            'slug' => $input['slug'],
            'content' => $input['content'],
            'status' => $input['status'],
            'author' => $input['author'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'meta_title' => $input['meta_title'],
            'meta_description' => $input['meta_description'],
            'template' => $input['template'],
            'show_title' => $input['show_title'],
            'show_in_menu' => $input['show_in_menu']
        ];
        
        array_unshift($pages, $newPage);
        savePages($pages);
        
        echo json_encode(['success' => true, 'message' => 'Page created successfully', 'id' => $newPage['id']]);
    }
} elseif ($method === 'PUT') {
    if (strpos($path, '/api/pages/') !== false) {
        // Update page
        $id = basename($path);
        $input = json_decode(file_get_contents('php://input'), true);
        
        $pages = getPages();
        foreach ($pages as &$page) {
            if ($page['id'] == $id) {
                $page['title'] = $input['title'];
                $page['slug'] = $input['slug'];
                $page['content'] = $input['content'];
                $page['status'] = $input['status'];
                $page['meta_title'] = $input['meta_title'];
                $page['meta_description'] = $input['meta_description'];
                $page['template'] = $input['template'];
                $page['show_title'] = $input['show_title'];
                $page['show_in_menu'] = $input['show_in_menu'];
                $page['updated_at'] = date('Y-m-d H:i:s');
                break;
            }
        }
        
        savePages($pages);
        echo json_encode(['success' => true, 'message' => 'Page updated successfully']);
    }
} elseif ($method === 'DELETE') {
    if (strpos($path, '/api/pages/') !== false) {
        // Delete page
        $id = basename($path);
        
        $pages = getPages();
        $pages = array_filter($pages, function($page) use ($id) {
            return $page['id'] != $id;
        });
        
        savePages(array_values($pages));
        echo json_encode(['success' => true, 'message' => 'Page deleted successfully']);
    }
}
?>
