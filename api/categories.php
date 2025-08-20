<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection
$db = new SQLite3('cms.db');

// Create categories table if not exists
$db->exec('
    CREATE TABLE IF NOT EXISTS categories (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        slug TEXT UNIQUE NOT NULL,
        description TEXT,
        color TEXT DEFAULT "#3b82f6",
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
');

// Insert default categories if table is empty
$result = $db->query('SELECT COUNT(*) as count FROM categories');
$count = $result->fetchArray(SQLITE3_ASSOC)['count'];

if ($count == 0) {
    $db->exec("
        INSERT INTO categories (name, slug, description, color) VALUES 
        ('Technologie', 'technologie', 'Wpisy o technologiach', '#3b82f6'),
        ('Design', 'design', 'Wpisy o designie', '#10b981'),
        ('Bezpieczeństwo', 'bezpieczenstwo', 'Wpisy o bezpieczeństwie', '#f59e0b'),
        ('Wydajność', 'wydajnosc', 'Wpisy o wydajności', '#ef4444')
    ");
}

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

// Handle different endpoints
if ($method === 'GET') {
    if (strpos($path, '/api/categories') !== false) {
        // Get all categories
        $stmt = $db->prepare('SELECT * FROM categories ORDER BY name ASC');
        $result = $stmt->execute();
        
        $categories = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $categories[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $categories]);
    }
} elseif ($method === 'POST') {
    if (strpos($path, '/api/categories') !== false) {
        // Create new category
        $input = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $db->prepare('
            INSERT INTO categories (name, slug, description, color)
            VALUES (:name, :slug, :description, :color)
        ');
        
        $stmt->bindValue(':name', $input['name']);
        $stmt->bindValue(':slug', $input['slug']);
        $stmt->bindValue(':description', $input['description']);
        $stmt->bindValue(':color', $input['color']);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Category created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error creating category']);
        }
    }
} elseif ($method === 'PUT') {
    if (strpos($path, '/api/categories/') !== false) {
        // Update category
        $id = basename($path);
        $input = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $db->prepare('
            UPDATE categories SET 
                name = :name, 
                slug = :slug, 
                description = :description,
                color = :color
            WHERE id = :id
        ');
        
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':name', $input['name']);
        $stmt->bindValue(':slug', $input['slug']);
        $stmt->bindValue(':description', $input['description']);
        $stmt->bindValue(':color', $input['color']);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Category updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating category']);
        }
    }
} elseif ($method === 'DELETE') {
    if (strpos($path, '/api/categories/') !== false) {
        // Delete category
        $id = basename($path);
        
        $stmt = $db->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->bindValue(':id', $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting category']);
        }
    }
}
?>
