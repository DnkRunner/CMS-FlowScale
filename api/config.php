<?php
// Database configuration for Neon PostgreSQL
function getDbConnection() {
    $database_url = $_ENV['database_url'] ?? '';
    
    if (empty($database_url)) {
        error_log("Database URL not configured");
        return null;
    }
    
    try {
        $pdo = new PDO($database_url);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
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
                id SERIAL PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) UNIQUE NOT NULL,
                content TEXT,
                excerpt TEXT,
                status VARCHAR(20) DEFAULT 'draft',
                author VARCHAR(100) DEFAULT 'Admin',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                featured_image VARCHAR(500),
                meta_title VARCHAR(255),
                meta_description TEXT,
                categories JSONB,
                template VARCHAR(50) DEFAULT 'default'
            )
        ");
        
        // Create pages table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS pages (
                id SERIAL PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) UNIQUE NOT NULL,
                content TEXT,
                status VARCHAR(20) DEFAULT 'draft',
                author VARCHAR(100) DEFAULT 'Admin',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                meta_title VARCHAR(255),
                meta_description TEXT,
                template VARCHAR(50) DEFAULT 'default',
                show_title BOOLEAN DEFAULT TRUE,
                show_in_menu BOOLEAN DEFAULT FALSE
            )
        ");
        
        // Create categories table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS categories (
                id SERIAL PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                slug VARCHAR(100) UNIQUE NOT NULL,
                description TEXT,
                color VARCHAR(7) DEFAULT '#3b82f6',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
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
