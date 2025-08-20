<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

// Get database connection
$pdo = getDbConnection();
if (!$pdo) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Initialize database
initDatabase($pdo);

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

// Handle different endpoints
if ($method === 'GET') {
    if (strpos($path, '/api/categories') !== false) {
        // Get all categories
        try {
            $stmt = $pdo->prepare('SELECT * FROM categories ORDER BY name ASC');
            $stmt->execute();
            $categories = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'data' => $categories]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }
} elseif ($method === 'POST') {
    if (strpos($path, '/api/categories') !== false) {
        // Create new category
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $pdo->prepare('
                INSERT INTO categories (name, slug, description, color)
                VALUES (:name, :slug, :description, :color)
                RETURNING id
            ');
            
            $stmt->bindParam(':name', $input['name']);
            $stmt->bindParam(':slug', $input['slug']);
            $stmt->bindParam(':description', $input['description']);
            $stmt->bindParam(':color', $input['color']);
            
            $stmt->execute();
            $result = $stmt->fetch();
            
            echo json_encode(['success' => true, 'message' => 'Category created successfully', 'id' => $result['id']]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }
} elseif ($method === 'PUT') {
    if (strpos($path, '/api/categories/') !== false) {
        // Update category
        try {
            $id = basename($path);
            $input = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $pdo->prepare('
                UPDATE categories SET 
                    name = :name, 
                    slug = :slug, 
                    description = :description,
                    color = :color
                WHERE id = :id
            ');
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $input['name']);
            $stmt->bindParam(':slug', $input['slug']);
            $stmt->bindParam(':description', $input['description']);
            $stmt->bindParam(':color', $input['color']);
            
            $stmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'Category updated successfully']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }
} elseif ($method === 'DELETE') {
    if (strpos($path, '/api/categories/') !== false) {
        // Delete category
        try {
            $id = basename($path);
            
            $stmt = $pdo->prepare('DELETE FROM categories WHERE id = :id');
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }
}
?>
