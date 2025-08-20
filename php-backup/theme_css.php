<?php
/**
 * Generator CSS na podstawie ustawień motywu
 */

function generate_theme_css($settings = []) {
    if (empty($settings)) {
        $pdo = db();
        $config = require __DIR__.'/../config.php';
        $prefix = $config['db']['prefix'];
        
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM `{$prefix}theme_settings`");
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    $css = "/* Automatycznie wygenerowany CSS na podstawie ustawień motywu */\n\n";
    
    // Podstawowe ustawienia układu
    $content_width = $settings['content_width'] ?? 1200;
    $content_max_width = $settings['content_max_width'] ?? 1200;
    $content_padding = $settings['content_padding'] ?? 20;
    
    $css .= "/* Układ treści */\n";
    $css .= ".container {\n";
    $css .= "    max-width: {$content_max_width}px;\n";
    $css .= "    width: 100%;\n";
    $css .= "    margin: 0 auto;\n";
    $css .= "    padding: 0 {$content_padding}px;\n";
    $css .= "}\n\n";
    
    $css .= ".content {\n";
    $css .= "    max-width: {$content_width}px;\n";
    $css .= "    margin: 0 auto;\n";
    $css .= "}\n\n";
    
    // Typografia
    $body_font_family = $settings['body_font_family'] ?? 'Arial, sans-serif';
    $body_font_size = $settings['body_font_size'] ?? 16;
    $body_line_height = $settings['body_line_height'] ?? 1.6;
    $heading_font_family = $settings['heading_font_family'] ?? 'Arial, sans-serif';
    
    $css .= "/* Typografia */\n";
    $css .= "body {\n";
    $css .= "    font-family: {$body_font_family};\n";
    $css .= "    font-size: {$body_font_size}px;\n";
    $css .= "    line-height: {$body_line_height};\n";
    $css .= "}\n\n";
    
    $css .= "h1, h2, h3, h4, h5, h6 {\n";
    $css .= "    font-family: {$heading_font_family};\n";
    $css .= "    margin: 0 0 1rem 0;\n";
    $css .= "    font-weight: 600;\n";
    $css .= "}\n\n";
    
    // Rozmiary nagłówków
    $h1_size = $settings['h1_font_size'] ?? 32;
    $h2_size = $settings['h2_font_size'] ?? 28;
    $h3_size = $settings['h3_font_size'] ?? 24;
    $h4_size = $settings['h4_font_size'] ?? 20;
    $h5_size = $settings['h5_font_size'] ?? 18;
    $h6_size = $settings['h6_font_size'] ?? 16;
    
    $css .= "h1 { font-size: {$h1_size}px; }\n";
    $css .= "h2 { font-size: {$h2_size}px; }\n";
    $css .= "h3 { font-size: {$h3_size}px; }\n";
    $css .= "h4 { font-size: {$h4_size}px; }\n";
    $css .= "h5 { font-size: {$h5_size}px; }\n";
    $css .= "h6 { font-size: {$h6_size}px; }\n\n";
    
    // Menu
    $menu_bg_color = $settings['menu_background_color'] ?? '#ffffff';
    $menu_text_color = $settings['menu_text_color'] ?? '#333333';
    $menu_font_family = $settings['menu_font_family'] ?? 'Arial, sans-serif';
    $menu_font_size = $settings['menu_font_size'] ?? 16;
    
    $css .= "/* Menu */\n";
    $css .= ".main-menu {\n";
    $css .= "    background-color: {$menu_bg_color};\n";
    $css .= "    color: {$menu_text_color};\n";
    $css .= "    font-family: {$menu_font_family};\n";
    $css .= "    font-size: {$menu_font_size}px;\n";
    $css .= "}\n\n";
    
    $css .= ".main-menu a {\n";
    $css .= "    color: {$menu_text_color};\n";
    $css .= "    text-decoration: none;\n";
    $css .= "}\n\n";
    
    $css .= ".main-menu a:hover {\n";
    $css .= "    opacity: 0.8;\n";
    $css .= "}\n\n";
    
    // CTA (Call to Action)
    if (($settings['show_cta'] ?? '1') === '1') {
        $cta_bg_color = $settings['cta_background_color'] ?? '#007bff';
        $cta_text_color = $settings['cta_text_color'] ?? '#ffffff';
        
        $css .= "/* Call to Action */\n";
        $css .= ".cta-button {\n";
        $css .= "    background-color: {$cta_bg_color};\n";
        $css .= "    color: {$cta_text_color};\n";
        $css .= "    padding: 10px 20px;\n";
        $css .= "    border: none;\n";
        $css .= "    border-radius: 4px;\n";
        $css .= "    text-decoration: none;\n";
        $css .= "    display: inline-block;\n";
        $css .= "    font-weight: 500;\n";
        $css .= "    transition: opacity 0.2s;\n";
        $css .= "}\n\n";
        
        $css .= ".cta-button:hover {\n";
        $css .= "    opacity: 0.9;\n";
        $css .= "    color: {$cta_text_color};\n";
        $css .= "}\n\n";
    }
    
    // Stopka
    $footer_bg_color = $settings['footer_background_color'] ?? '#333333';
    $footer_text_color = $settings['footer_text_color'] ?? '#ffffff';
    
    $css .= "/* Stopka */\n";
    $css .= ".footer {\n";
    $css .= "    background-color: {$footer_bg_color};\n";
    $css .= "    color: {$footer_text_color};\n";
    $css .= "    padding: 40px 0;\n";
    $css .= "}\n\n";
    
    $css .= ".footer a {\n";
    $css .= "    color: {$footer_text_color};\n";
    $css .= "    text-decoration: none;\n";
    $css .= "}\n\n";
    
    $css .= ".footer a:hover {\n";
    $css .= "    opacity: 0.8;\n";
    $css .= "}\n\n";
    
    // Responsywność
    $css .= "/* Responsywność */\n";
    $css .= "@media (max-width: 768px) {\n";
    $css .= "    .container {\n";
    $css .= "        padding: 0 15px;\n";
    $css .= "    }\n";
    $css .= "    \n";
    $css .= "    h1 { font-size: " . ($h1_size * 0.8) . "px; }\n";
    $css .= "    h2 { font-size: " . ($h2_size * 0.8) . "px; }\n";
    $css .= "    h3 { font-size: " . ($h3_size * 0.8) . "px; }\n";
    $css .= "}\n\n";
    
    $css .= "@media (max-width: 480px) {\n";
    $css .= "    .container {\n";
    $css .= "        padding: 0 10px;\n";
    $css .= "    }\n";
    $css .= "    \n";
    $css .= "    h1 { font-size: " . ($h1_size * 0.7) . "px; }\n";
    $css .= "    h2 { font-size: " . ($h2_size * 0.7) . "px; }\n";
    $css .= "    h3 { font-size: " . ($h3_size * 0.7) . "px; }\n";
    $css .= "}\n\n";
    
    // Komentarze
    if (($settings['comments_enabled'] ?? '1') === '1') {
        $css .= "/* Komentarze */\n";
        $css .= ".comments-section {\n";
        $css .= "    margin-top: 40px;\n";
        $css .= "    padding-top: 20px;\n";
        $css .= "    border-top: 1px solid #e5e7eb;\n";
        $css .= "}\n\n";
        
        $css .= ".comment {\n";
        $css .= "    background: #f8f9fa;\n";
        $css .= "    padding: 15px;\n";
        $css .= "    margin-bottom: 15px;\n";
        $css .= "    border-radius: 6px;\n";
        $css .= "}\n\n";
        
        $css .= ".comment-author {\n";
        $css .= "    font-weight: 600;\n";
        $css .= "    margin-bottom: 5px;\n";
        $css .= "}\n\n";
        
        $css .= ".comment-date {\n";
        $css .= "    font-size: 12px;\n";
        $css .= "    color: #666;\n";
        $css .= "    margin-bottom: 10px;\n";
        $css .= "}\n\n";
        
        $css .= ".comment-form {\n";
        $css .= "    background: #f8f9fa;\n";
        $css .= "    padding: 20px;\n";
        $css .= "    border-radius: 6px;\n";
        $css .= "    margin-bottom: 20px;\n";
        $css .= "}\n\n";
        
        $css .= ".comment-form input,\n";
        $css .= ".comment-form textarea {\n";
        $css .= "    width: 100%;\n";
        $css .= "    padding: 8px;\n";
        $css .= "    border: 1px solid #d1d5db;\n";
        $css .= "    border-radius: 4px;\n";
        $css .= "    margin-bottom: 10px;\n";
        $css .= "}\n\n";
        
        $css .= ".comment-form textarea {\n";
        $css .= "    min-height: 100px;\n";
        $css .= "    resize: vertical;\n";
        $css .= "}\n\n";
    }
    
    // Powiązane wpisy
    if (($settings['show_related_posts'] ?? '1') === '1') {
        $css .= "/* Powiązane wpisy */\n";
        $css .= ".related-posts {\n";
        $css .= "    margin-top: 40px;\n";
        $css .= "    padding-top: 20px;\n";
        $css .= "    border-top: 1px solid #e5e7eb;\n";
        $css .= "}\n\n";
        
        $css .= ".related-posts h3 {\n";
        $css .= "    margin-bottom: 20px;\n";
        $css .= "}\n\n";
        
        $css .= ".related-posts-grid {\n";
        $css .= "    display: grid;\n";
        $css .= "    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));\n";
        $css .= "    gap: 20px;\n";
        $css .= "}\n\n";
        
        $css .= ".related-post {\n";
        $css .= "    background: #f8f9fa;\n";
        $css .= "    padding: 15px;\n";
        $css .= "    border-radius: 6px;\n";
        $css .= "}\n\n";
        
        $css .= ".related-post h4 {\n";
        $css .= "    margin: 0 0 10px 0;\n";
        $css .= "}\n\n";
        
        $css .= ".related-post a {\n";
        $css .= "    color: inherit;\n";
        $css .= "    text-decoration: none;\n";
        $css .= "}\n\n";
        
        $css .= ".related-post a:hover {\n";
        $css .= "    opacity: 0.8;\n";
        $css .= "}\n\n";
    }
    
    // Informacje o wpisach
    $css .= "/* Informacje o wpisach */\n";
    $css .= ".post-meta {\n";
    $css .= "    color: #666;\n";
    $css .= "    font-size: 14px;\n";
    $css .= "    margin-bottom: 20px;\n";
    $css .= "}\n\n";
    
    $css .= ".post-meta span {\n";
    $css .= "    margin-right: 15px;\n";
    $css .= "}\n\n";
    
    $css .= ".post-meta a {\n";
    $css .= "    color: #666;\n";
    $css .= "    text-decoration: none;\n";
    $css .= "}\n\n";
    
    $css .= ".post-meta a:hover {\n";
    $css .= "    text-decoration: underline;\n";
    $css .= "}\n\n";
    
    // Obrazki wyróżniające
    if (($settings['show_featured_image'] ?? '1') === '1') {
        $css .= "/* Obrazki wyróżniające */\n";
        $css .= ".featured-image {\n";
        $css .= "    width: 100%;\n";
        $css .= "    height: auto;\n";
        $css .= "    border-radius: 6px;\n";
        $css .= "    margin-bottom: 20px;\n";
        $css .= "}\n\n";
    }
    
    // Blog
    $css .= "/* Blog */\n";
    $css .= ".blog-container {\n";
    $css .= "    max-width: {$content_width}px;\n";
    $css .= "    margin: 0 auto;\n";
    $css .= "    padding: 20px 0;\n";
    $css .= "}\n\n";
    
    // Układ bloga
    $blog_layout = $settings['blog_layout'] ?? '1-column';
    if ($blog_layout === '2-columns') {
        $css .= ".blog-grid {\n";
        $css .= "    display: grid !important;\n";
        $css .= "    grid-template-columns: repeat(2, 1fr) !important;\n";
        $css .= "    gap: 30px;\n";
        $css .= "}\n\n";
        
        $css .= "@media (max-width: 768px) {\n";
        $css .= "    .blog-grid {\n";
        $css .= "        grid-template-columns: 1fr;\n";
        $css .= "        gap: 20px;\n";
        $css .= "    }\n";
        $css .= "}\n\n";
    } else {
        $css .= ".blog-grid {\n";
        $css .= "    display: flex;\n";
        $css .= "    flex-direction: column;\n";
        $css .= "    gap: 30px;\n";
        $css .= "}\n\n";
    }
    
    // Filtry bloga
    if (($settings['blog_show_filters'] ?? '1') === '1') {
        $css .= ".blog-filters {\n";
        $css .= "    background: #f8f9fa;\n";
        $css .= "    padding: 20px;\n";
        $css .= "    border-radius: 6px;\n";
        $css .= "    margin-bottom: 30px;\n";
        $css .= "    display: flex;\n";
        $css .= "    gap: 15px;\n";
        $css .= "    flex-wrap: wrap;\n";
        $css .= "    align-items: center;\n";
        $css .= "}\n\n";
        
        $css .= ".blog-filters select,\n";
        $css .= ".blog-filters input {\n";
        $css .= "    padding: 8px 12px;\n";
        $css .= "    border: 1px solid #d1d5db;\n";
        $css .= "    border-radius: 4px;\n";
        $css .= "    font-size: 14px;\n";
        $css .= "}\n\n";
        
        $css .= ".blog-filters button {\n";
        $css .= "    padding: 8px 16px;\n";
        $css .= "    background: #3b82f6;\n";
        $css .= "    color: white;\n";
        $css .= "    border: none;\n";
        $css .= "    border-radius: 4px;\n";
        $css .= "    cursor: pointer;\n";
        $css .= "    font-size: 14px;\n";
        $css .= "}\n\n";
        
        $css .= ".blog-filters button:hover {\n";
        $css .= "    background: #2563eb;\n";
        $css .= "}\n\n";
    }
    
    // Karty wpisów bloga
    $css .= ".blog-post-card {\n";
    $css .= "    background: white;\n";
    $css .= "    border: 1px solid #e5e7eb;\n";
    $css .= "    border-radius: 8px;\n";
    $css .= "    overflow: hidden;\n";
    $css .= "    transition: box-shadow 0.2s;\n";
    $css .= "}\n\n";
    
    $css .= ".blog-post-card:hover {\n";
    $css .= "    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);\n";
    $css .= "}\n\n";
    
    // Miniatura bloga
    if (($settings['blog_show_thumbnail'] ?? '1') === '1') {
        $css .= ".blog-post-thumbnail {\n";
        $css .= "    width: 100%;\n";
        $css .= "    height: 200px;\n";
        $css .= "    object-fit: cover;\n";
        $css .= "}\n\n";
    }
    
    // Treść karty bloga
    $css .= ".blog-post-content {\n";
    $css .= "    padding: 20px;\n";
    $css .= "}\n\n";
    
    // Tytuł bloga
    if (($settings['blog_show_title'] ?? '1') === '1') {
        $css .= ".blog-post-title {\n";
        $css .= "    font-size: 1.5rem;\n";
        $css .= "    font-weight: 600;\n";
        $css .= "    margin: 0 0 10px 0;\n";
        $css .= "    line-height: 1.3;\n";
        $css .= "}\n\n";
        
        $css .= ".blog-post-title a {\n";
        $css .= "    color: inherit;\n";
        $css .= "    text-decoration: none;\n";
        $css .= "}\n\n";
        
        $css .= ".blog-post-title a:hover {\n";
        $css .= "    color: #3b82f6;\n";
        $css .= "}\n\n";
    }
    
    // Data bloga
    if (($settings['blog_show_date'] ?? '1') === '1') {
        $css .= ".blog-post-date {\n";
        $css .= "    color: #666;\n";
        $css .= "    font-size: 14px;\n";
        $css .= "    margin-bottom: 15px;\n";
        $css .= "}\n\n";
    }
    
    // Opis bloga
    if (($settings['blog_show_excerpt'] ?? '1') === '1') {
        $css .= ".blog-post-excerpt {\n";
        $css .= "    color: #374151;\n";
        $css .= "    line-height: 1.6;\n";
        $css .= "    margin-bottom: 20px;\n";
        $css .= "}\n\n";
    }
    
    // Przycisk bloga
    if (($settings['blog_show_button'] ?? '1') === '1') {
        $blog_button_color = $settings['blog_button_color'] ?? '#007bff';
        $blog_button_text_color = $settings['blog_button_text_color'] ?? '#ffffff';
        
        $css .= ".blog-post-button {\n";
        $css .= "    display: inline-block;\n";
        $css .= "    padding: 10px 20px;\n";
        $css .= "    background-color: {$blog_button_color};\n";
        $css .= "    color: {$blog_button_text_color};\n";
        $css .= "    text-decoration: none;\n";
        $css .= "    border-radius: 4px;\n";
        $css .= "    font-weight: 500;\n";
        $css .= "    transition: opacity 0.2s;\n";
        $css .= "}\n\n";
        
        $css .= ".blog-post-button:hover {\n";
        $css .= "    opacity: 0.9;\n";
        $css .= "    color: {$blog_button_text_color};\n";
        $css .= "}\n\n";
    }
    
    // Paginacja bloga
    $css .= ".blog-pagination {\n";
    $css .= "    display: flex;\n";
    $css .= "    justify-content: center;\n";
    $css .= "    gap: 10px;\n";
    $css .= "    margin-top: 40px;\n";
    $css .= "}\n\n";
    
    $css .= ".blog-pagination a,\n";
    $css .= ".blog-pagination span {\n";
    $css .= "    padding: 8px 12px;\n";
    $css .= "    border: 1px solid #d1d5db;\n";
    $css .= "    border-radius: 4px;\n";
    $css .= "    text-decoration: none;\n";
    $css .= "    color: #374151;\n";
    $css .= "}\n\n";
    
    $css .= ".blog-pagination a:hover {\n";
    $css .= "    background: #f3f4f6;\n";
    $css .= "}\n\n";
    
    $css .= ".blog-pagination .current {\n";
    $css .= "    background: #3b82f6;\n";
    $css .= "    color: white;\n";
    $css .= "    border-color: #3b82f6;\n";
    $css .= "}\n\n";
    
    return $css;
}

/**
 * Zapisuje wygenerowany CSS do pliku
 */
function save_theme_css($css, $filename = null) {
    if (!$filename) {
        $filename = __DIR__ . '/../public/assets/theme.css';
    }
    
    $dir = dirname($filename);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    return file_put_contents($filename, $css);
}

/**
 * Pobiera URL do pliku CSS motywu
 */
function get_theme_css_url() {
    return site_url('assets/theme.css');
}
