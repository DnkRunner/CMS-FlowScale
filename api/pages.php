<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection
$db = new SQLite3('cms.db');

// Create pages table if not exists
$db->exec('
    CREATE TABLE IF NOT EXISTS pages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        slug TEXT UNIQUE NOT NULL,
        content TEXT,
        status TEXT DEFAULT "draft",
        author TEXT DEFAULT "Admin",
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        meta_title TEXT,
        meta_description TEXT,
        template TEXT DEFAULT "default",
        show_title BOOLEAN DEFAULT 1,
        show_in_menu BOOLEAN DEFAULT 0
    )
');

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

// Handle different endpoints
if ($method === 'GET') {
    if (strpos($path, '/api/pages') !== false) {
        // Get all pages
        $stmt = $db->prepare('SELECT * FROM pages ORDER BY created_at DESC');
        $result = $stmt->execute();
        
        $pages = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $pages[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $pages]);
    }
} elseif ($method === 'POST') {
    if (strpos($path, '/api/pages') !== false) {
        // Create new page
        $input = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $db->prepare('
            INSERT INTO pages (title, slug, content, status, author, meta_title, meta_description, template, show_title, show_in_menu)
            VALUES (:title, :slug, :content, :status, :author, :meta_title, :meta_description, :template, :show_title, :show_in_menu)
        ');
        
        $stmt->bindValue(':title', $input['title']);
        $stmt->bindValue(':slug', $input['slug']);
        $stmt->bindValue(':content', $input['content']);
        $stmt->bindValue(':status', $input['status']);
        $stmt->bindValue(':author', $input['author']);
        $stmt->bindValue(':meta_title', $input['meta_title']);
        $stmt->bindValue(':meta_description', $input['meta_description']);
        $stmt->bindValue(':template', $input['template']);
        $stmt->bindValue(':show_title', $input['show_title'] ? 1 : 0);
        $stmt->bindValue(':show_in_menu', $input['show_in_menu'] ? 1 : 0);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Page created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error creating page']);
        }
    }
} elseif ($method === 'PUT') {
    if (strpos($path, '/api/pages/') !== false) {
        // Update page
        $id = basename($path);
        $input = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $db->prepare('
            UPDATE pages SET 
                title = :title, 
                slug = :slug, 
                content = :content, 
                status = :status,
                meta_title = :meta_title,
                meta_description = :meta_description,
                template = :template,
                show_title = :show_title,
                show_in_menu = :show_in_menu,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ');
        
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':title', $input['title']);
        $stmt->bindValue(':slug', $input['slug']);
        $stmt->bindValue(':content', $input['content']);
        $stmt->bindValue(':status', $input['status']);
        $stmt->bindValue(':meta_title', $input['meta_title']);
        $stmt->bindValue(':meta_description', $input['meta_description']);
        $stmt->bindValue(':template', $input['template']);
        $stmt->bindValue(':show_title', $input['show_title'] ? 1 : 0);
        $stmt->bindValue(':show_in_menu', $input['show_in_menu'] ? 1 : 0);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Page updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating page']);
        }
    }
} elseif ($method === 'DELETE') {
    if (strpos($path, '/api/pages/') !== false) {
        // Delete page
        $id = basename($path);
        
        $stmt = $db->prepare('DELETE FROM pages WHERE id = :id');
        $stmt->bindValue(':id', $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Page deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting page']);
        }
    }
}
?>
