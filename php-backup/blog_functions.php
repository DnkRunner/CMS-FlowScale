<?php
/**
 * Funkcje pomocnicze do wyświetlania bloga
 */

/**
 * Pobiera ustawienia bloga
 */
function get_blog_settings() {
    $pdo = db();
    $config = require __DIR__.'/../config.php';
    $prefix = $config['db']['prefix'];
    
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM `{$prefix}theme_settings` WHERE setting_key LIKE 'blog_%'");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    return $settings;
}

/**
 * Wyświetla filtry bloga
 */
function display_blog_filters($current_category = '', $current_search = '') {
    $settings = get_blog_settings();
    
    if (($settings['blog_show_filters'] ?? '1') !== '1') {
        return;
    }
    
    $pdo = db();
    $config = require __DIR__.'/../config.php';
    $prefix = $config['db']['prefix'];
    
    // Pobierz kategorie
    $categories = [];
    if (($settings['blog_show_category_filter'] ?? '1') === '1') {
        $stmt = $pdo->query("SELECT id, name, slug FROM `{$prefix}categories` ORDER BY name");
        $categories = $stmt->fetchAll();
    }
    
    echo '<div class="blog-filters">';
    
    // Wyszukiwarka
    if (($settings['blog_show_search'] ?? '1') === '1') {
        echo '<form method="get" style="display: flex; gap: 10px; align-items: center;">';
        echo '<input type="text" name="search" placeholder="Szukaj wpisów..." value="' . e($current_search) . '" style="min-width: 200px;">';
        echo '<button type="submit">Szukaj</button>';
        echo '</form>';
    }
    
    // Filtr kategorii
    if (($settings['blog_show_category_filter'] ?? '1') === '1' && !empty($categories)) {
        echo '<select name="category" onchange="window.location.href=this.value" style="min-width: 150px;">';
        echo '<option value="' . site_url('blog') . '">Wszystkie kategorie</option>';
        
        foreach ($categories as $category) {
            $selected = ($current_category == $category['slug']) ? 'selected' : '';
            $url = site_url('blog/category/' . $category['slug']);
            echo '<option value="' . $url . '" ' . $selected . '>' . e($category['name']) . '</option>';
        }
        
        echo '</select>';
    }
    
    echo '</div>';
}

/**
 * Wyświetla wpisy bloga
 */
function display_blog_posts($posts, $settings = null) {
    if (!$settings) {
        $settings = get_blog_settings();
    }
    
    $layout = $settings['blog_layout'] ?? '1-column';
    $show_thumbnail = ($settings['blog_show_thumbnail'] ?? '1') === '1';
    $show_title = ($settings['blog_show_title'] ?? '1') === '1';
    $show_date = ($settings['blog_show_date'] ?? '1') === '1';
    $show_excerpt = ($settings['blog_show_excerpt'] ?? '1') === '1';
    $show_button = ($settings['blog_show_button'] ?? '1') === '1';
    $button_text = $settings['blog_button_text'] ?? 'Czytaj więcej';
    $excerpt_length = (int)($settings['blog_excerpt_length'] ?? 150);
    
    echo '<div class="blog-grid">';
    
    foreach ($posts as $post) {
        echo '<article class="blog-post-card">';
        
        // Miniatura
        if ($show_thumbnail && $post['featured_image']) {
            echo '<img src="' . e($post['featured_image']) . '" alt="' . e($post['title']) . '" class="blog-post-thumbnail">';
        }
        
        echo '<div class="blog-post-content">';
        
        // Tytuł
        if ($show_title) {
            echo '<h2 class="blog-post-title">';
            echo '<a href="' . site_url($post['slug']) . '">' . e($post['title']) . '</a>';
            echo '</h2>';
        }
        
        // Data
        if ($show_date) {
            echo '<div class="blog-post-date">';
            echo date('d.m.Y', strtotime($post['created_at']));
            echo '</div>';
        }
        
        // Opis
        if ($show_excerpt) {
            $excerpt = strip_tags($post['content']);
            if (strlen($excerpt) > $excerpt_length) {
                $excerpt = substr($excerpt, 0, $excerpt_length) . '...';
            }
            echo '<div class="blog-post-excerpt">' . e($excerpt) . '</div>';
        }
        
        // Przycisk
        if ($show_button) {
            echo '<a href="' . site_url($post['slug']) . '" class="blog-post-button">' . e($button_text) . '</a>';
        }
        
        echo '</div>'; // .blog-post-content
        echo '</article>';
    }
    
    echo '</div>'; // .blog-grid
}

/**
 * Wyświetla paginację bloga
 */
function display_blog_pagination($current_page, $total_pages, $base_url = '') {
    if ($total_pages <= 1) {
        return;
    }
    
    if (!$base_url) {
        $base_url = site_url('blog');
    }
    
    echo '<div class="blog-pagination">';
    
    // Poprzednia strona
    if ($current_page > 1) {
        $prev_url = $base_url . (strpos($base_url, '?') !== false ? '&' : '?') . 'page=' . ($current_page - 1);
        echo '<a href="' . $prev_url . '">← Poprzednia</a>';
    }
    
    // Numery stron
    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);
    
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $current_page) {
            echo '<span class="current">' . $i . '</span>';
        } else {
            $page_url = $base_url . (strpos($base_url, '?') !== false ? '&' : '?') . 'page=' . $i;
            echo '<a href="' . $page_url . '">' . $i . '</a>';
        }
    }
    
    // Następna strona
    if ($current_page < $total_pages) {
        $next_url = $base_url . (strpos($base_url, '?') !== false ? '&' : '?') . 'page=' . ($current_page + 1);
        echo '<a href="' . $next_url . '">Następna →</a>';
    }
    
    echo '</div>';
}

/**
 * Pobiera wpisy bloga z filtrowaniem
 */
function get_blog_posts($page = 1, $category_slug = '', $search = '', $settings = null) {
    if (!$settings) {
        $settings = get_blog_settings();
    }
    
    $pdo = db();
    $config = require __DIR__.'/../config.php';
    $prefix = $config['db']['prefix'];
    
    $posts_per_page = (int)($settings['blog_posts_per_page'] ?? 10);
    $offset = ($page - 1) * $posts_per_page;
    
    // Buduj zapytanie
    $where_conditions = ["p.status = 'published'"];
    $params = [];
    
    if ($category_slug) {
        $where_conditions[] = "c.slug = ?";
        $params[] = $category_slug;
    }
    
    if ($search) {
        $where_conditions[] = "(p.title LIKE ? OR p.content LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    // Zapytanie o liczbę wpisów
    $count_sql = "SELECT COUNT(*) FROM `{$prefix}posts` p";
    if ($category_slug) {
        $count_sql .= " LEFT JOIN `{$prefix}post_categories` pc ON p.id = pc.post_id";
        $count_sql .= " LEFT JOIN `{$prefix}categories` c ON pc.category_id = c.id";
    }
    $count_sql .= " WHERE $where_clause";
    
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($params);
    $total_posts = $stmt->fetchColumn();
    
    // Zapytanie o wpisy
    $sql = "SELECT p.* FROM `{$prefix}posts` p";
    if ($category_slug) {
        $sql .= " LEFT JOIN `{$prefix}post_categories` pc ON p.id = pc.post_id";
        $sql .= " LEFT JOIN `{$prefix}categories` c ON pc.category_id = c.id";
    }
    $sql .= " WHERE $where_clause ORDER BY p.created_at DESC LIMIT $posts_per_page OFFSET $offset";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $posts = $stmt->fetchAll();
    
    $total_pages = ceil($total_posts / $posts_per_page);
    
    return [
        'posts' => $posts,
        'total_posts' => $total_posts,
        'total_pages' => $total_pages,
        'current_page' => $page
    ];
}

/**
 * Sprawdza czy strona jest stroną bloga
 */
function is_blog_page($page_id = null) {
    $settings = get_blog_settings();
    $blog_page_id = (int)($settings['blog_page_id'] ?? 0);
    
    if ($page_id === null) {
        // Sprawdź na podstawie URL
        $current_url = $_SERVER['REQUEST_URI'] ?? '';
        return strpos($current_url, '/blog') === 0;
    }
    
    return $page_id == $blog_page_id;
}

/**
 * Renderuje zawartość bloga jako HTML
 */
function render_blog_content($blog_data, $settings) {
    ob_start();
    
    // Dodaj inline CSS dla 2 kolumn (test)
    $layout = $settings['blog_layout'] ?? '1-column';
    if ($layout === '2-columns') {
        echo '<style>
        .blog-grid {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 30px !important;
        }
        @media (max-width: 768px) {
            .blog-grid {
                grid-template-columns: 1fr !important;
                gap: 20px !important;
            }
        }
        </style>';
    }
    
    // Wyświetl filtry
    display_blog_filters();
    
    // Wyświetl wpisy
    if (!empty($blog_data['posts'])) {
        display_blog_posts($blog_data['posts'], $settings);
        
        // Wyświetl paginację
        display_blog_pagination($blog_data['current_page'], $blog_data['total_pages']);
        
        echo '<div style="margin-top: 2rem; text-align: center; color: #666;">';
        echo 'Znaleziono ' . $blog_data['total_posts'] . ' wpisów';
        echo '</div>';
    } else {
        echo '<div style="text-align: center; padding: 3rem; color: #666;">';
        echo '<h2>Brak wpisów</h2>';
        echo '<p>Brak opublikowanych wpisów.</p>';
        echo '</div>';
    }
    
    return ob_get_clean();
}
