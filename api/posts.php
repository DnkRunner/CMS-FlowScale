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
    if (strpos($path, '/api/posts') !== false) {
        // Get all posts
        try {
            $stmt = $pdo->prepare('SELECT * FROM posts ORDER BY created_at DESC');
            $stmt->execute();
            $posts = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'data' => $posts]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }
} elseif ($method === 'POST') {
    if (strpos($path, '/api/posts') !== false) {
        // Create new post
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $pdo->prepare('
                INSERT INTO posts (title, slug, content, excerpt, status, author, featured_image, meta_title, meta_description, categories, template)
                VALUES (:title, :slug, :content, :excerpt, :status, :author, :featured_image, :meta_title, :meta_description, :categories, :template)
                RETURNING id
            ');
            
            $stmt->bindParam(':title', $input['title']);
            $stmt->bindParam(':slug', $input['slug']);
            $stmt->bindParam(':content', $input['content']);
            $stmt->bindParam(':excerpt', $input['excerpt']);
            $stmt->bindParam(':status', $input['status']);
            $stmt->bindParam(':author', $input['author']);
            $stmt->bindParam(':featured_image', $input['featured_image']);
            $stmt->bindParam(':meta_title', $input['meta_title']);
            $stmt->bindParam(':meta_description', $input['meta_description']);
            $stmt->bindParam(':categories', json_encode($input['categories']));
            $stmt->bindParam(':template', $input['template']);
            
            $stmt->execute();
            $result = $stmt->fetch();
            
            echo json_encode(['success' => true, 'message' => 'Post created successfully', 'id' => $result['id']]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }
} elseif ($method === 'PUT') {
    if (strpos($path, '/api/posts/') !== false) {
        // Update post
        try {
            $id = basename($path);
            $input = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $pdo->prepare('
                UPDATE posts SET 
                    title = :title, 
                    slug = :slug, 
                    content = :content, 
                    excerpt = :excerpt, 
                    status = :status,
                    featured_image = :featured_image,
                    meta_title = :meta_title,
                    meta_description = :meta_description,
                    categories = :categories,
                    template = :template,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = :id
            ');
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':title', $input['title']);
            $stmt->bindParam(':slug', $input['slug']);
            $stmt->bindParam(':content', $input['content']);
            $stmt->bindParam(':excerpt', $input['excerpt']);
            $stmt->bindParam(':status', $input['status']);
            $stmt->bindParam(':featured_image', $input['featured_image']);
            $stmt->bindParam(':meta_title', $input['meta_title']);
            $stmt->bindParam(':meta_description', $input['meta_description']);
            $stmt->bindParam(':categories', json_encode($input['categories']));
            $stmt->bindParam(':template', $input['template']);
            
            $stmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'Post updated successfully']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }
} elseif ($method === 'DELETE') {
    if (strpos($path, '/api/posts/') !== false) {
        // Delete post
        try {
            $id = basename($path);
            
            $stmt = $pdo->prepare('DELETE FROM posts WHERE id = :id');
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'Post deleted successfully']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }
}
?>
