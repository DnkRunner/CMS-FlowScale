<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection
$db = new SQLite3('cms.db');

// Create posts table if not exists
$db->exec('
    CREATE TABLE IF NOT EXISTS posts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        slug TEXT UNIQUE NOT NULL,
        content TEXT,
        excerpt TEXT,
        status TEXT DEFAULT "draft",
        author TEXT DEFAULT "Admin",
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        featured_image TEXT,
        meta_title TEXT,
        meta_description TEXT,
        categories TEXT,
        template TEXT DEFAULT "default"
    )
');

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

// Handle different endpoints
if ($method === 'GET') {
    if (strpos($path, '/api/posts') !== false) {
        // Get all posts
        $stmt = $db->prepare('SELECT * FROM posts ORDER BY created_at DESC');
        $result = $stmt->execute();
        
        $posts = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $posts[] = $row;
        }
        
        echo json_encode(['success' => true, 'data' => $posts]);
    }
} elseif ($method === 'POST') {
    if (strpos($path, '/api/posts') !== false) {
        // Create new post
        $input = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $db->prepare('
            INSERT INTO posts (title, slug, content, excerpt, status, author, featured_image, meta_title, meta_description, categories, template)
            VALUES (:title, :slug, :content, :excerpt, :status, :author, :featured_image, :meta_title, :meta_description, :categories, :template)
        ');
        
        $stmt->bindValue(':title', $input['title']);
        $stmt->bindValue(':slug', $input['slug']);
        $stmt->bindValue(':content', $input['content']);
        $stmt->bindValue(':excerpt', $input['excerpt']);
        $stmt->bindValue(':status', $input['status']);
        $stmt->bindValue(':author', $input['author']);
        $stmt->bindValue(':featured_image', $input['featured_image']);
        $stmt->bindValue(':meta_title', $input['meta_title']);
        $stmt->bindValue(':meta_description', $input['meta_description']);
        $stmt->bindValue(':categories', json_encode($input['categories']));
        $stmt->bindValue(':template', $input['template']);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Post created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error creating post']);
        }
    }
} elseif ($method === 'PUT') {
    if (strpos($path, '/api/posts/') !== false) {
        // Update post
        $id = basename($path);
        $input = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $db->prepare('
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
        
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':title', $input['title']);
        $stmt->bindValue(':slug', $input['slug']);
        $stmt->bindValue(':content', $input['content']);
        $stmt->bindValue(':excerpt', $input['excerpt']);
        $stmt->bindValue(':status', $input['status']);
        $stmt->bindValue(':featured_image', $input['featured_image']);
        $stmt->bindValue(':meta_title', $input['meta_title']);
        $stmt->bindValue(':meta_description', $input['meta_description']);
        $stmt->bindValue(':categories', json_encode($input['categories']));
        $stmt->bindValue(':template', $input['template']);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Post updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating post']);
        }
    }
} elseif ($method === 'DELETE') {
    if (strpos($path, '/api/posts/') !== false) {
        // Delete post
        $id = basename($path);
        
        $stmt = $db->prepare('DELETE FROM posts WHERE id = :id');
        $stmt->bindValue(':id', $id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Post deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting post']);
        }
    }
}
?>
