<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Simple in-memory storage for demo
$postsFile = 'posts.json';

// Initialize posts file if it doesn't exist
if (!file_exists($postsFile)) {
    file_put_contents($postsFile, json_encode([]));
}

function getPosts() {
    global $postsFile;
    $content = file_get_contents($postsFile);
    return json_decode($content, true) ?: [];
}

function savePosts($posts) {
    global $postsFile;
    file_put_contents($postsFile, json_encode($posts, JSON_PRETTY_PRINT));
}

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

// Handle different endpoints
if ($method === 'GET') {
    if (strpos($path, '/api/posts') !== false) {
        // Get all posts
        $posts = getPosts();
        echo json_encode(['success' => true, 'data' => $posts]);
    }
} elseif ($method === 'POST') {
    if (strpos($path, '/api/posts') !== false) {
        // Create new post
        $input = json_decode(file_get_contents('php://input'), true);
        
        $posts = getPosts();
        $newPost = [
            'id' => time() . rand(100, 999),
            'title' => $input['title'],
            'slug' => $input['slug'],
            'content' => $input['content'],
            'excerpt' => $input['excerpt'],
            'status' => $input['status'],
            'author' => $input['author'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'featured_image' => $input['featured_image'],
            'meta_title' => $input['meta_title'],
            'meta_description' => $input['meta_description'],
            'categories' => $input['categories'],
            'template' => $input['template']
        ];
        
        array_unshift($posts, $newPost);
        savePosts($posts);
        
        echo json_encode(['success' => true, 'message' => 'Post created successfully', 'id' => $newPost['id']]);
    }
} elseif ($method === 'PUT') {
    if (strpos($path, '/api/posts/') !== false) {
        // Update post
        $id = basename($path);
        $input = json_decode(file_get_contents('php://input'), true);
        
        $posts = getPosts();
        foreach ($posts as &$post) {
            if ($post['id'] == $id) {
                $post['title'] = $input['title'];
                $post['slug'] = $input['slug'];
                $post['content'] = $input['content'];
                $post['excerpt'] = $input['excerpt'];
                $post['status'] = $input['status'];
                $post['featured_image'] = $input['featured_image'];
                $post['meta_title'] = $input['meta_title'];
                $post['meta_description'] = $input['meta_description'];
                $post['categories'] = $input['categories'];
                $post['template'] = $input['template'];
                $post['updated_at'] = date('Y-m-d H:i:s');
                break;
            }
        }
        
        savePosts($posts);
        echo json_encode(['success' => true, 'message' => 'Post updated successfully']);
    }
} elseif ($method === 'DELETE') {
    if (strpos($path, '/api/posts/') !== false) {
        // Delete post
        $id = basename($path);
        
        $posts = getPosts();
        $posts = array_filter($posts, function($post) use ($id) {
            return $post['id'] != $id;
        });
        
        savePosts(array_values($posts));
        echo json_encode(['success' => true, 'message' => 'Post deleted successfully']);
    }
}
?>
