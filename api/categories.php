<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Simple in-memory storage for demo
$categoriesFile = 'categories.json';

// Initialize categories file if it doesn't exist
if (!file_exists($categoriesFile)) {
    $defaultCategories = [
        [
            'id' => 1,
            'name' => 'Technologie',
            'slug' => 'technologie',
            'description' => 'Wpisy o technologiach',
            'color' => '#3b82f6',
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'id' => 2,
            'name' => 'Design',
            'slug' => 'design',
            'description' => 'Wpisy o designie',
            'color' => '#10b981',
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'id' => 3,
            'name' => 'Bezpieczeństwo',
            'slug' => 'bezpieczenstwo',
            'description' => 'Wpisy o bezpieczeństwie',
            'color' => '#f59e0b',
            'created_at' => date('Y-m-d H:i:s')
        ],
        [
            'id' => 4,
            'name' => 'Wydajność',
            'slug' => 'wydajnosc',
            'description' => 'Wpisy o wydajności',
            'color' => '#ef4444',
            'created_at' => date('Y-m-d H:i:s')
        ]
    ];
    file_put_contents($categoriesFile, json_encode($defaultCategories, JSON_PRETTY_PRINT));
}

function getCategories() {
    global $categoriesFile;
    $content = file_get_contents($categoriesFile);
    return json_decode($content, true) ?: [];
}

function saveCategories($categories) {
    global $categoriesFile;
    file_put_contents($categoriesFile, json_encode($categories, JSON_PRETTY_PRINT));
}

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

// Handle different endpoints
if ($method === 'GET') {
    if (strpos($path, '/api/categories') !== false) {
        // Get all categories
        $categories = getCategories();
        echo json_encode(['success' => true, 'data' => $categories]);
    }
} elseif ($method === 'POST') {
    if (strpos($path, '/api/categories') !== false) {
        // Create new category
        $input = json_decode(file_get_contents('php://input'), true);
        
        $categories = getCategories();
        $newCategory = [
            'id' => time() . rand(100, 999),
            'name' => $input['name'],
            'slug' => $input['slug'],
            'description' => $input['description'],
            'color' => $input['color'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $categories[] = $newCategory;
        saveCategories($categories);
        
        echo json_encode(['success' => true, 'message' => 'Category created successfully', 'id' => $newCategory['id']]);
    }
} elseif ($method === 'PUT') {
    if (strpos($path, '/api/categories/') !== false) {
        // Update category
        $id = basename($path);
        $input = json_decode(file_get_contents('php://input'), true);
        
        $categories = getCategories();
        foreach ($categories as &$category) {
            if ($category['id'] == $id) {
                $category['name'] = $input['name'];
                $category['slug'] = $input['slug'];
                $category['description'] = $input['description'];
                $category['color'] = $input['color'];
                break;
            }
        }
        
        saveCategories($categories);
        echo json_encode(['success' => true, 'message' => 'Category updated successfully']);
    }
} elseif ($method === 'DELETE') {
    if (strpos($path, '/api/categories/') !== false) {
        // Delete category
        $id = basename($path);
        
        $categories = getCategories();
        $categories = array_filter($categories, function($category) use ($id) {
            return $category['id'] != $id;
        });
        
        saveCategories(array_values($categories));
        echo json_encode(['success' => true, 'message' => 'Category deleted successfully']);
    }
}
?>
