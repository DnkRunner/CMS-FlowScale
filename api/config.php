<?php
// Database configuration
function getDbConnection() {
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $dbname = $_ENV['DB_NAME'] ?? 'cms_flowscale';
    $username = $_ENV['DB_USER'] ?? 'root';
    $password = $_ENV['DB_PASSWORD'] ?? '';
    
    try {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        return null;
    }
}

// Initialize database tables
function initDatabase($pdo) {
    try {
        // Create posts table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) UNIQUE NOT NULL,
                content LONGTEXT,
                excerpt TEXT,
                status ENUM('draft', 'published', 'scheduled') DEFAULT 'draft',
                author VARCHAR(100) DEFAULT 'Admin',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                featured_image VARCHAR(500),
                meta_title VARCHAR(255),
                meta_description TEXT,
                categories JSON,
                template VARCHAR(50) DEFAULT 'default'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Create pages table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS pages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) UNIQUE NOT NULL,
                content LONGTEXT,
                status ENUM('draft', 'published') DEFAULT 'draft',
                author VARCHAR(100) DEFAULT 'Admin',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                meta_title VARCHAR(255),
                meta_description TEXT,
                template VARCHAR(50) DEFAULT 'default',
                show_title BOOLEAN DEFAULT TRUE,
                show_in_menu BOOLEAN DEFAULT FALSE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Create categories table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                slug VARCHAR(100) UNIQUE NOT NULL,
                description TEXT,
                color VARCHAR(7) DEFAULT '#3b82f6',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Insert default categories if table is empty
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
        $count = $stmt->fetch()['count'];
        
        if ($count == 0) {
            $pdo->exec("
                INSERT INTO categories (name, slug, description, color) VALUES 
                ('Technologie', 'technologie', 'Wpisy o technologiach', '#3b82f6'),
                ('Design', 'design', 'Wpisy o designie', '#10b981'),
                ('Bezpieczeństwo', 'bezpieczenstwo', 'Wpisy o bezpieczeństwie', '#f59e0b'),
                ('Wydajność', 'wydajnosc', 'Wpisy o wydajności', '#ef4444')
            ");
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Database initialization failed: " . $e->getMessage());
        return false;
    }
}
?>
