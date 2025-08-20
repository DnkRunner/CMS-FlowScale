<?php
$title='Motyw';
require_once __DIR__.'/../core/auth.php'; require_login();
require_once __DIR__.'/../core/helpers.php';
$pdo = db(); $config = require __DIR__.'/../config.php'; $prefix = $config['db']['prefix'];
$errors=[]; $success='';

// Pobierz aktualne ustawienia
$stmt = $pdo->query("SELECT setting_key, setting_value, setting_type FROM `{$prefix}theme_settings`");
$settings = [];
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Pobierz menu items
$stmt = $pdo->query("SELECT * FROM `{$prefix}menu_items` ORDER BY position");
$menu_items = $stmt->fetchAll();

// Pobierz strony dla wyboru strony głównej
$stmt = $pdo->query("SELECT id, title, slug FROM `{$prefix}pages` WHERE status = 'published' ORDER BY title");
$pages = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (isset($_POST['save_menu'])) {
        // Zapisz tylko ustawienia menu
        $menu_settings = [
            'menu_background_color' => $_POST['menu_background_color'] ?? '#ffffff',
            'menu_text_color' => $_POST['menu_text_color'] ?? '#333333',
            'menu_font_family' => $_POST['menu_font_family'] ?? 'Arial, sans-serif',
            'menu_font_size' => (int)($_POST['menu_font_size'] ?? 16),
        ];
        
        foreach ($menu_settings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO `{$prefix}theme_settings` (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value");
            $stmt->execute([':key' => $key, ':value' => $value]);
        }
        $success = 'Ustawienia menu zostały zapisane.';
        
        // Odśwież ustawienia
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM `{$prefix}theme_settings`");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    if (isset($_POST['save_theme'])) {
        // Zapisz tylko ustawienia motywu
        $theme_settings = [
            'theme_mode' => $_POST['theme_mode'] ?? 'light',
            'brand_logo' => $_POST['brand_logo'] ?? '',
            'logo_width' => (int)($_POST['logo_width'] ?? 150),
            'show_header' => isset($_POST['show_header']) ? '1' : '0',
            'cta_type' => $_POST['cta_type'] ?? 'phone',
            'cta_text' => $_POST['cta_text'] ?? 'Zadzwoń teraz',
            'cta_value' => $_POST['cta_value'] ?? '',
            'cta_url' => $_POST['cta_url'] ?? '',
            'cta_background_color' => $_POST['cta_background_color'] ?? '#007bff',
            'cta_text_color' => $_POST['cta_text_color'] ?? '#ffffff',
            'show_cta' => isset($_POST['show_cta']) ? '1' : '0',
            'google_analytics_id' => $_POST['google_analytics_id'] ?? '',
            'facebook_pixel_id' => $_POST['facebook_pixel_id'] ?? ''
        ];
        
        foreach ($theme_settings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO `{$prefix}theme_settings` (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value");
            $stmt->execute([':key' => $key, ':value' => $value]);
        }
        $success = 'Ustawienia motywu zostały zapisane.';
        
        // Odśwież ustawienia
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM `{$prefix}theme_settings`");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    if (isset($_POST['save_footer'])) {
        // Zapisz tylko ustawienia stopki
        $footer_settings = [
            'footer_background_color' => $_POST['footer_background_color'] ?? '#333333',
            'footer_text_color' => $_POST['footer_text_color'] ?? '#ffffff',
            'footer_columns' => (int)($_POST['footer_columns'] ?? 3),
            'footer_column1_title' => $_POST['footer_column1_title'] ?? '',
            'footer_column1_content' => $_POST['footer_column1_content'] ?? '',
            'footer_column1_image' => $_POST['footer_column1_image'] ?? '',
            'footer_column2_title' => $_POST['footer_column2_title'] ?? '',
            'footer_column2_content' => $_POST['footer_column2_content'] ?? '',
            'footer_column2_image' => $_POST['footer_column2_image'] ?? '',
            'footer_column3_title' => $_POST['footer_column3_title'] ?? '',
            'footer_column3_content' => $_POST['footer_column3_content'] ?? '',
            'footer_column3_image' => $_POST['footer_column3_image'] ?? '',
            'footer_column4_title' => $_POST['footer_column4_title'] ?? '',
            'footer_column4_content' => $_POST['footer_column4_content'] ?? '',
            'footer_column4_image' => $_POST['footer_column4_image'] ?? '',
            'footer_column5_title' => $_POST['footer_column5_title'] ?? '',
            'footer_column5_content' => $_POST['footer_column5_content'] ?? '',
            'footer_column5_image' => $_POST['footer_column5_image'] ?? ''
        ];
        
        foreach ($footer_settings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO `{$prefix}theme_settings` (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value");
            $stmt->execute([':key' => $key, ':value' => $value]);
        }
        $success = 'Ustawienia stopki zostały zapisane.';
        
        // Odśwież ustawienia
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM `{$prefix}theme_settings`");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    if (isset($_POST['save_pages'])) {
        // Zapisz tylko ustawienia stron
        $page_settings = [
            'homepage_id' => (int)($_POST['homepage_id'] ?? 0)
        ];
        
        foreach ($page_settings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO `{$prefix}theme_settings` (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value");
            $stmt->execute([':key' => $key, ':value' => $value]);
        }
        $success = 'Ustawienia stron zostały zapisane.';
        
        // Odśwież ustawienia
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM `{$prefix}theme_settings`");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    if (isset($_POST['save_typography'])) {
        // Zapisz ustawienia typografii
        $typography_settings = [
            'body_font_family' => $_POST['body_font_family'] ?? 'Arial, sans-serif',
            'body_font_size' => (int)($_POST['body_font_size'] ?? 16),
            'body_line_height' => (float)($_POST['body_line_height'] ?? 1.6),
            'heading_font_family' => $_POST['heading_font_family'] ?? 'Arial, sans-serif',
            'h1_font_size' => (int)($_POST['h1_font_size'] ?? 32),
            'h2_font_size' => (int)($_POST['h2_font_size'] ?? 28),
            'h3_font_size' => (int)($_POST['h3_font_size'] ?? 24),
            'h4_font_size' => (int)($_POST['h4_font_size'] ?? 20),
            'h5_font_size' => (int)($_POST['h5_font_size'] ?? 18),
            'h6_font_size' => (int)($_POST['h6_font_size'] ?? 16)
        ];
        
        foreach ($typography_settings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO `{$prefix}theme_settings` (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value");
            $stmt->execute([':key' => $key, ':value' => $value]);
        }
        $success = 'Ustawienia typografii zostały zapisane.';
        
        // Odśwież ustawienia
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM `{$prefix}theme_settings`");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        // Wygeneruj i zapisz CSS
        require_once __DIR__.'/../core/theme_css.php';
        $css = generate_theme_css($settings);
        save_theme_css($css);
    }
    
    if (isset($_POST['save_layout'])) {
        // Zapisz ustawienia układu
        $layout_settings = [
            'content_width' => (int)($_POST['content_width'] ?? 1200),
            'content_max_width' => (int)($_POST['content_max_width'] ?? 1200),
            'content_padding' => (int)($_POST['content_padding'] ?? 20)
        ];
        
        foreach ($layout_settings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO `{$prefix}theme_settings` (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value");
            $stmt->execute([':key' => $key, ':value' => $value]);
        }
        $success = 'Ustawienia układu zostały zapisane.';
        
        // Odśwież ustawienia
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM `{$prefix}theme_settings`");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        // Wygeneruj i zapisz CSS
        require_once __DIR__.'/../core/theme_css.php';
        $css = generate_theme_css($settings);
        save_theme_css($css);
    }
    
    if (isset($_POST['save_comments'])) {
        // Zapisz ustawienia komentarzy
        $comments_settings = [
            'comments_enabled' => isset($_POST['comments_enabled']) ? '1' : '0',
            'comments_moderation' => isset($_POST['comments_moderation']) ? '1' : '0',
            'comments_max_per_page' => (int)($_POST['comments_max_per_page'] ?? 10)
        ];
        
        foreach ($comments_settings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO `{$prefix}theme_settings` (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value");
            $stmt->execute([':key' => $key, ':value' => $value]);
        }
        $success = 'Ustawienia komentarzy zostały zapisane.';
        
        // Odśwież ustawienia
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM `{$prefix}theme_settings`");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        // Wygeneruj i zapisz CSS
        require_once __DIR__.'/../core/theme_css.php';
        $css = generate_theme_css($settings);
        save_theme_css($css);
    }
    
    if (isset($_POST['save_display'])) {
        // Zapisz ustawienia wyświetlania
        $display_settings = [
            'show_related_posts' => isset($_POST['show_related_posts']) ? '1' : '0',
            'related_posts_count' => (int)($_POST['related_posts_count'] ?? 3),
            'show_author_info' => isset($_POST['show_author_info']) ? '1' : '0',
            'show_post_date' => isset($_POST['show_post_date']) ? '1' : '0',
            'show_post_categories' => isset($_POST['show_post_categories']) ? '1' : '0',
            'show_post_tags' => isset($_POST['show_post_tags']) ? '1' : '0',
            'posts_per_page' => (int)($_POST['posts_per_page'] ?? 10),
            'excerpt_length' => (int)($_POST['excerpt_length'] ?? 150),
            'show_featured_image' => isset($_POST['show_featured_image']) ? '1' : '0',
            'featured_image_size' => $_POST['featured_image_size'] ?? 'medium'
        ];
        
        foreach ($display_settings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO `{$prefix}theme_settings` (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value");
            $stmt->execute([':key' => $key, ':value' => $value]);
        }
        $success = 'Ustawienia wyświetlania zostały zapisane.';
        
        // Odśwież ustawienia
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM `{$prefix}theme_settings`");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        // Wygeneruj i zapisz CSS
        require_once __DIR__.'/../core/theme_css.php';
        $css = generate_theme_css($settings);
        save_theme_css($css);
    }
    
    if (isset($_POST['save_blog'])) {
        // Zapisz ustawienia bloga
        $blog_settings = [
            'blog_page_id' => (int)($_POST['blog_page_id'] ?? 0),
            'blog_layout' => $_POST['blog_layout'] ?? '1-column',
            'blog_posts_per_page' => (int)($_POST['blog_posts_per_page'] ?? 10),
            'blog_show_filters' => isset($_POST['blog_show_filters']) ? '1' : '0',
            'blog_show_category_filter' => isset($_POST['blog_show_category_filter']) ? '1' : '0',
            'blog_show_search' => isset($_POST['blog_show_search']) ? '1' : '0',
            'blog_show_thumbnail' => isset($_POST['blog_show_thumbnail']) ? '1' : '0',
            'blog_show_title' => isset($_POST['blog_show_title']) ? '1' : '0',
            'blog_show_date' => isset($_POST['blog_show_date']) ? '1' : '0',
            'blog_show_excerpt' => isset($_POST['blog_show_excerpt']) ? '1' : '0',
            'blog_show_button' => isset($_POST['blog_show_button']) ? '1' : '0',
            'blog_button_text' => $_POST['blog_button_text'] ?? 'Czytaj więcej',
            'blog_button_color' => $_POST['blog_button_color'] ?? '#007bff',
            'blog_button_text_color' => $_POST['blog_button_text_color'] ?? '#ffffff',
            'blog_thumbnail_size' => $_POST['blog_thumbnail_size'] ?? 'medium',
            'blog_excerpt_length' => (int)($_POST['blog_excerpt_length'] ?? 150)
        ];
        
        foreach ($blog_settings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO `{$prefix}theme_settings` (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value");
            $stmt->execute([':key' => $key, ':value' => $value]);
        }
        $success = 'Ustawienia bloga zostały zapisane.';
        
        // Odśwież ustawienia
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM `{$prefix}theme_settings`");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        // Wygeneruj i zapisz CSS
        require_once __DIR__.'/../core/theme_css.php';
        $css = generate_theme_css($settings);
        save_theme_css($css);
    }
    
    if (isset($_POST['save_menu'])) {
        // Zapisz tylko ustawienia menu (kolory, czcionka)
        $menu_settings = [
            'menu_background_color' => $_POST['menu_background_color'] ?? '#ffffff',
            'menu_text_color' => $_POST['menu_text_color'] ?? '#333333',
            'menu_font_family' => $_POST['menu_font_family'] ?? 'Arial, sans-serif',
            'menu_font_size' => (int)($_POST['menu_font_size'] ?? 16),
        ];
        
        foreach ($menu_settings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO `{$prefix}theme_settings` (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value");
            $stmt->execute([':key' => $key, ':value' => $value]);
        }
        $success = 'Ustawienia menu zostały zapisane.';
        
        // Odśwież ustawienia
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM `{$prefix}theme_settings`");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
}

require __DIR__.'/layout-header.php';
?>
<h1>Konfiguracja motywu</h1>

<?php if ($success): ?>
<div class="card" style="border:1px solid #10b981;color:#065f46;background:#d1fae5;padding:12px;margin-bottom:20px;">
    <?php echo e($success); ?>
</div>
<?php endif; ?>

<!-- Zakładki -->
<div class="tabs">
    <button class="tab-button active" onclick="showTab('menu')">Ustawienia menu</button>
    <button class="tab-button" onclick="showTab('theme')">Ustawienia motywu</button>
    <button class="tab-button" onclick="showTab('typography')">Typografia</button>
    <button class="tab-button" onclick="showTab('layout')">Układ treści</button>
    <button class="tab-button" onclick="showTab('comments')">Komentarze</button>
    <button class="tab-button" onclick="showTab('display')">Wyświetlanie</button>
    <button class="tab-button" onclick="showTab('blog')">Blog</button>
    <button class="tab-button" onclick="showTab('footer')">Ustawienia stopki</button>
    <button class="tab-button" onclick="showTab('pages')">Ustawienia stron</button>
</div>

<!-- Zakładka: Ustawienia menu -->
<div id="menu-tab" class="tab-content active">
    <div class="card">
        <h2>Ustawienia menu</h2>
        <form method="post">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
                <div>
                    <label>Kolor tła menu</label>
                    <input type="color" class="input" name="menu_background_color" value="<?php echo e($settings['menu_background_color'] ?? '#ffffff'); ?>">
                </div>
                
                <div>
                    <label>Kolor tekstu menu</label>
                    <input type="color" class="input" name="menu_text_color" value="<?php echo e($settings['menu_text_color'] ?? '#333333'); ?>">
                </div>
                
                <div>
                    <label>Czcionka menu</label>
                    <select name="menu_font_family" class="input">
                        <option value="Arial, sans-serif" <?php echo ($settings['menu_font_family'] ?? 'Arial, sans-serif') === 'Arial, sans-serif' ? 'selected' : ''; ?>>Arial</option>
                        <option value="Helvetica, sans-serif" <?php echo ($settings['menu_font_family'] ?? 'Arial, sans-serif') === 'Helvetica, sans-serif' ? 'selected' : ''; ?>>Helvetica</option>
                        <option value="Georgia, serif" <?php echo ($settings['menu_font_family'] ?? 'Arial, sans-serif') === 'Georgia, serif' ? 'selected' : ''; ?>>Georgia</option>
                        <option value="Times New Roman, serif" <?php echo ($settings['menu_font_family'] ?? 'Arial, sans-serif') === 'Times New Roman, serif' ? 'selected' : ''; ?>>Times New Roman</option>
                        <option value="Verdana, sans-serif" <?php echo ($settings['menu_font_family'] ?? 'Arial, sans-serif') === 'Verdana, sans-serif' ? 'selected' : ''; ?>>Verdana</option>
                    </select>
                </div>
                
                <div>
                    <label>Rozmiar czcionki menu (px)</label>
                    <input type="number" class="input" name="menu_font_size" value="<?php echo e($settings['menu_font_size'] ?? 16); ?>" min="12" max="24">
                </div>
            </div>
            
            <p style="margin-top:16px;color:#666;font-size:14px;">
                <strong>Uwaga:</strong> Pozycje menu (Strona główna, O nas, Kontakt) są zarządzane automatycznie. 
                Możesz zmienić tylko wygląd menu (kolory, czcionka).
            </p>
        </form>
    </div>
</div>

<!-- Zakładka: Ustawienia motywu -->
<div id="theme-tab" class="tab-content">
    <div class="card">
        <h2>Ustawienia motywu</h2>
        <form method="post">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <div>
                    <label>Tryb motywu</label>
                    <select name="theme_mode" class="input">
                        <option value="light" <?php echo ($settings['theme_mode'] ?? 'light') === 'light' ? 'selected' : ''; ?>>Jasny</option>
                        <option value="dark" <?php echo ($settings['theme_mode'] ?? 'light') === 'dark' ? 'selected' : ''; ?>>Ciemny</option>
                    </select>
                </div>
                
                <div>
                    <label>Pokaż pasek na górze</label>
                    <label style="display:flex;align-items:center;gap:8px;margin-top:8px;">
                        <input type="checkbox" name="show_header" value="1" <?php echo ($settings['show_header'] ?? '1') === '1' ? 'checked' : ''; ?>> Włączony
                    </label>
                </div>
                
                <div>
                    <label>Logo marki</label>
                    <div style="display:flex;gap:10px;align-items:center;">
                        <input class="input" name="brand_logo" value="<?php echo e($settings['brand_logo'] ?? ''); ?>" placeholder="URL logo">
                        <button type="button" class="btn" onclick="openMediaLibrary('brand_logo')">Wybierz z mediów</button>
                    </div>
                </div>
                
                <div>
                    <label>Szerokość logo (px)</label>
                    <input type="number" class="input" name="logo_width" value="<?php echo e($settings['logo_width'] ?? 150); ?>" min="50" max="300">
                </div>
            </div>
            
            <h3>Ustawienia CTA (Call to Action)</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <div>
                    <label>Typ CTA</label>
                    <select name="cta_type" class="input" onchange="toggleCtaFields()">
                        <option value="phone" <?php echo ($settings['cta_type'] ?? 'phone') === 'phone' ? 'selected' : ''; ?>>Telefon</option>
                        <option value="form" <?php echo ($settings['cta_type'] ?? 'phone') === 'form' ? 'selected' : ''; ?>>Formularz</option>
                    </select>
                </div>
                
                <div>
                    <label>Tekst przycisku</label>
                    <input class="input" name="cta_text" value="<?php echo e($settings['cta_text'] ?? 'Zadzwoń teraz'); ?>">
                </div>
                
                <div id="cta-phone-field">
                    <label>Numer telefonu</label>
                    <input class="input" name="cta_value" value="<?php echo e($settings['cta_value'] ?? '+48 123 456 789'); ?>">
                </div>
                
                <div id="cta-form-field" style="display:none;">
                    <label>URL formularza</label>
                    <input class="input" name="cta_url" value="<?php echo e($settings['cta_url'] ?? ''); ?>">
                </div>
                
                <div>
                    <label>Kolor tła CTA</label>
                    <input type="color" class="input" name="cta_background_color" value="<?php echo e($settings['cta_background_color'] ?? '#007bff'); ?>">
                </div>
                
                <div>
                    <label>Kolor tekstu CTA</label>
                    <input type="color" class="input" name="cta_text_color" value="<?php echo e($settings['cta_text_color'] ?? '#ffffff'); ?>">
                </div>
            </div>
            
            <div style="margin-top:16px;">
                <label style="display:flex;align-items:center;gap:8px;">
                    <input type="checkbox" name="show_cta" value="1" <?php echo ($settings['show_cta'] ?? '1') === '1' ? 'checked' : ''; ?>> Pokaż przycisk CTA w menu
                </label>
            </div>
            
            <h3>Google Analytics</h3>
            <div>
                <label>ID śledzenia Google Analytics</label>
                <input class="input" name="google_analytics_id" value="<?php echo e($settings['google_analytics_id'] ?? ''); ?>" placeholder="G-XXXXXXXXXX lub UA-XXXXXXXX-X">
                <p style="margin-top:8px;color:#666;font-size:14px;">
                    Wklej ID śledzenia z Google Analytics (np. G-XXXXXXXXXX dla GA4 lub UA-XXXXXXXX-X dla Universal Analytics)
                </p>
            </div>
            
            <h3>Facebook Pixel</h3>
            <div>
                <label>Facebook Pixel ID</label>
                <input class="input" name="facebook_pixel_id" value="<?php echo e($settings['facebook_pixel_id'] ?? ''); ?>" placeholder="123456789012345">
                <p style="margin-top:8px;color:#666;font-size:14px;">
                    Wklej ID Pixela z Facebook Ads Manager (np. 123456789012345)
                </p>
            </div>
            
            <button class="btn" type="submit" name="save_theme" style="margin-top:16px;">Zapisz ustawienia motywu</button>
        </form>
    </div>
</div>

<!-- Zakładka: Typografia -->
<div id="typography-tab" class="tab-content">
    <div class="card">
        <h2>Ustawienia typografii</h2>
        <form method="post">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
                <div>
                    <label>Czcionka tekstu głównego</label>
                    <select name="body_font_family" class="input">
                        <option value="Arial, sans-serif" <?php echo ($settings['body_font_family'] ?? 'Arial, sans-serif') === 'Arial, sans-serif' ? 'selected' : ''; ?>>Arial</option>
                        <option value="Helvetica, sans-serif" <?php echo ($settings['body_font_family'] ?? 'Arial, sans-serif') === 'Helvetica, sans-serif' ? 'selected' : ''; ?>>Helvetica</option>
                        <option value="Georgia, serif" <?php echo ($settings['body_font_family'] ?? 'Arial, sans-serif') === 'Georgia, serif' ? 'selected' : ''; ?>>Georgia</option>
                        <option value="Times New Roman, serif" <?php echo ($settings['body_font_family'] ?? 'Arial, sans-serif') === 'Times New Roman, serif' ? 'selected' : ''; ?>>Times New Roman</option>
                        <option value="Verdana, sans-serif" <?php echo ($settings['body_font_family'] ?? 'Arial, sans-serif') === 'Verdana, sans-serif' ? 'selected' : ''; ?>>Verdana</option>
                        <option value="Open Sans, sans-serif" <?php echo ($settings['body_font_family'] ?? 'Arial, sans-serif') === 'Open Sans, sans-serif' ? 'selected' : ''; ?>>Open Sans</option>
                        <option value="Roboto, sans-serif" <?php echo ($settings['body_font_family'] ?? 'Arial, sans-serif') === 'Roboto, sans-serif' ? 'selected' : ''; ?>>Roboto</option>
                    </select>
                </div>
                
                <div>
                    <label>Rozmiar tekstu głównego (px)</label>
                    <input type="number" class="input" name="body_font_size" value="<?php echo e($settings['body_font_size'] ?? 16); ?>" min="12" max="24">
                </div>
                
                <div>
                    <label>Wysokość linii</label>
                    <input type="number" class="input" name="body_line_height" value="<?php echo e($settings['body_line_height'] ?? 1.6); ?>" min="1.2" max="2.0" step="0.1">
                </div>
                
                <div>
                    <label>Czcionka nagłówków</label>
                    <select name="heading_font_family" class="input">
                        <option value="Arial, sans-serif" <?php echo ($settings['heading_font_family'] ?? 'Arial, sans-serif') === 'Arial, sans-serif' ? 'selected' : ''; ?>>Arial</option>
                        <option value="Helvetica, sans-serif" <?php echo ($settings['heading_font_family'] ?? 'Arial, sans-serif') === 'Helvetica, sans-serif' ? 'selected' : ''; ?>>Helvetica</option>
                        <option value="Georgia, serif" <?php echo ($settings['heading_font_family'] ?? 'Arial, sans-serif') === 'Georgia, serif' ? 'selected' : ''; ?>>Georgia</option>
                        <option value="Times New Roman, serif" <?php echo ($settings['heading_font_family'] ?? 'Arial, sans-serif') === 'Times New Roman, serif' ? 'selected' : ''; ?>>Times New Roman</option>
                        <option value="Verdana, sans-serif" <?php echo ($settings['heading_font_family'] ?? 'Arial, sans-serif') === 'Verdana, sans-serif' ? 'selected' : ''; ?>>Verdana</option>
                        <option value="Open Sans, sans-serif" <?php echo ($settings['heading_font_family'] ?? 'Arial, sans-serif') === 'Open Sans, sans-serif' ? 'selected' : ''; ?>>Open Sans</option>
                        <option value="Roboto, sans-serif" <?php echo ($settings['heading_font_family'] ?? 'Arial, sans-serif') === 'Roboto, sans-serif' ? 'selected' : ''; ?>>Roboto</option>
                    </select>
                </div>
            </div>
            
            <h3>Rozmiary nagłówków</h3>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;">
                <div>
                    <label>H1 (px)</label>
                    <input type="number" class="input" name="h1_font_size" value="<?php echo e($settings['h1_font_size'] ?? 32); ?>" min="24" max="48">
                </div>
                <div>
                    <label>H2 (px)</label>
                    <input type="number" class="input" name="h2_font_size" value="<?php echo e($settings['h2_font_size'] ?? 28); ?>" min="20" max="40">
                </div>
                <div>
                    <label>H3 (px)</label>
                    <input type="number" class="input" name="h3_font_size" value="<?php echo e($settings['h3_font_size'] ?? 24); ?>" min="18" max="36">
                </div>
                <div>
                    <label>H4 (px)</label>
                    <input type="number" class="input" name="h4_font_size" value="<?php echo e($settings['h4_font_size'] ?? 20); ?>" min="16" max="32">
                </div>
                <div>
                    <label>H5 (px)</label>
                    <input type="number" class="input" name="h5_font_size" value="<?php echo e($settings['h5_font_size'] ?? 18); ?>" min="14" max="28">
                </div>
                <div>
                    <label>H6 (px)</label>
                    <input type="number" class="input" name="h6_font_size" value="<?php echo e($settings['h6_font_size'] ?? 16); ?>" min="12" max="24">
                </div>
            </div>
            
            <button class="btn" type="submit" name="save_typography" style="margin-top:16px;">Zapisz ustawienia typografii</button>
        </form>
    </div>
</div>

<!-- Zakładka: Układ treści -->
<div id="layout-tab" class="tab-content">
    <div class="card">
        <h2>Układ treści</h2>
        <form method="post">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <div>
                    <label>Szerokość treści (px)</label>
                    <input type="number" class="input" name="content_width" value="<?php echo e($settings['content_width'] ?? 1200); ?>" min="800" max="1600">
                    <p style="margin-top:5px;color:#666;font-size:12px;">Maksymalna szerokość kontenera treści</p>
                </div>
                
                <div>
                    <label>Maksymalna szerokość (px)</label>
                    <input type="number" class="input" name="content_max_width" value="<?php echo e($settings['content_max_width'] ?? 1200); ?>" min="800" max="1600">
                    <p style="margin-top:5px;color:#666;font-size:12px;">Maksymalna szerokość na dużych ekranach</p>
                </div>
                
                <div>
                    <label>Padding treści (px)</label>
                    <input type="number" class="input" name="content_padding" value="<?php echo e($settings['content_padding'] ?? 20); ?>" min="10" max="50">
                    <p style="margin-top:5px;color:#666;font-size:12px;">Odstęp od krawędzi ekranu</p>
                </div>
            </div>
            
            <div style="margin-top:20px;padding:15px;background:#f0f9ff;border:1px solid #0ea5e9;border-radius:6px;">
                <h4 style="margin:0 0 10px 0;color:#0c4a6e;">Podgląd układu</h4>
                <div style="background:#e5e7eb;height:100px;border-radius:4px;position:relative;overflow:hidden;">
                    <div style="background:#3b82f6;height:100%;width:<?php echo min(100, (($settings['content_max_width'] ?? 1200) / 1600) * 100); ?>%;margin:0 auto;border-radius:4px;display:flex;align-items:center;justify-content:center;color:white;font-size:12px;">
                        Treść (<?php echo e($settings['content_max_width'] ?? 1200); ?>px)
                    </div>
                </div>
            </div>
            
            <button class="btn" type="submit" name="save_layout" style="margin-top:16px;">Zapisz ustawienia układu</button>
        </form>
    </div>
</div>

<!-- Zakładka: Komentarze -->
<div id="comments-tab" class="tab-content">
    <div class="card">
        <h2>Ustawienia komentarzy</h2>
        <form method="post">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
                <div>
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="comments_enabled" value="1" <?php echo ($settings['comments_enabled'] ?? '1') === '1' ? 'checked' : ''; ?>> Włącz komentarze
                    </label>
                    <p style="margin-top:5px;color:#666;font-size:12px;">Pozwól użytkownikom komentować wpisy</p>
                </div>
                
                <div>
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="comments_moderation" value="1" <?php echo ($settings['comments_moderation'] ?? '1') === '1' ? 'checked' : ''; ?>> Moderacja komentarzy
                    </label>
                    <p style="margin-top:5px;color:#666;font-size:12px;">Komentarze wymagają zatwierdzenia przed publikacją</p>
                </div>
                
                <div>
                    <label>Maksymalna liczba komentarzy na stronę</label>
                    <input type="number" class="input" name="comments_max_per_page" value="<?php echo e($settings['comments_max_per_page'] ?? 10); ?>" min="5" max="50">
                </div>
            </div>
            
            <div style="background:#fef3c7;border:1px solid #f59e0b;border-radius:6px;padding:15px;margin-bottom:20px;">
                <h4 style="margin:0 0 10px 0;color:#92400e;">Informacje o komentarzach</h4>
                <ul style="margin:0;color:#92400e;font-size:14px;">
                    <li>Komentarze będą wyświetlane pod wpisami</li>
                    <li>Użytkownicy muszą podać imię i email</li>
                    <li>Można opcjonalnie dodać stronę internetową</li>
                    <li>Spam jest automatycznie filtrowany</li>
                </ul>
            </div>
            
            <button class="btn" type="submit" name="save_comments" style="margin-top:16px;">Zapisz ustawienia komentarzy</button>
        </form>
    </div>
</div>

<!-- Zakładka: Wyświetlanie -->
<div id="display-tab" class="tab-content">
    <div class="card">
        <h2>Ustawienia wyświetlania</h2>
        <form method="post">
            <h3>Powiązane treści</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
                <div>
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="show_related_posts" value="1" <?php echo ($settings['show_related_posts'] ?? '1') === '1' ? 'checked' : ''; ?>> Pokaż powiązane wpisy
                    </label>
                </div>
                
                <div>
                    <label>Liczba powiązanych wpisów</label>
                    <input type="number" class="input" name="related_posts_count" value="<?php echo e($settings['related_posts_count'] ?? 3); ?>" min="1" max="6">
                </div>
            </div>
            
            <h3>Informacje o wpisach</h3>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;margin-bottom:20px;">
                <div>
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="show_author_info" value="1" <?php echo ($settings['show_author_info'] ?? '1') === '1' ? 'checked' : ''; ?>> Pokaż informacje o autorze
                    </label>
                </div>
                
                <div>
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="show_post_date" value="1" <?php echo ($settings['show_post_date'] ?? '1') === '1' ? 'checked' : ''; ?>> Pokaż datę publikacji
                    </label>
                </div>
                
                <div>
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="show_post_categories" value="1" <?php echo ($settings['show_post_categories'] ?? '1') === '1' ? 'checked' : ''; ?>> Pokaż kategorie
                    </label>
                </div>
                
                <div>
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="show_post_tags" value="1" <?php echo ($settings['show_post_tags'] ?? '1') === '1' ? 'checked' : ''; ?>> Pokaż tagi
                    </label>
                </div>
                
                <div>
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="show_featured_image" value="1" <?php echo ($settings['show_featured_image'] ?? '1') === '1' ? 'checked' : ''; ?>> Pokaż obrazek wyróżniający
                    </label>
                </div>
            </div>
            
            <h3>Lista wpisów</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
                <div>
                    <label>Liczba wpisów na stronę</label>
                    <input type="number" class="input" name="posts_per_page" value="<?php echo e($settings['posts_per_page'] ?? 10); ?>" min="5" max="50">
                </div>
                
                <div>
                    <label>Długość skrótu (znaki)</label>
                    <input type="number" class="input" name="excerpt_length" value="<?php echo e($settings['excerpt_length'] ?? 150); ?>" min="50" max="500">
                </div>
                
                <div>
                    <label>Rozmiar obrazka wyróżniającego</label>
                    <select name="featured_image_size" class="input">
                        <option value="thumbnail" <?php echo ($settings['featured_image_size'] ?? 'medium') === 'thumbnail' ? 'selected' : ''; ?>>Miniatura (150x150)</option>
                        <option value="medium" <?php echo ($settings['featured_image_size'] ?? 'medium') === 'medium' ? 'selected' : ''; ?>>Średni (300x300)</option>
                        <option value="large" <?php echo ($settings['featured_image_size'] ?? 'medium') === 'large' ? 'selected' : ''; ?>>Duży (1024x1024)</option>
                    </select>
                </div>
            </div>
            
            <button class="btn" type="submit" name="save_display" style="margin-top:16px;">Zapisz ustawienia wyświetlania</button>
        </form>
    </div>
</div>

<!-- Zakładka: Blog -->
<div id="blog-tab" class="tab-content">
    <div class="card">
        <h2>Ustawienia bloga</h2>
        <form method="post">
            <h3>Strona bloga</h3>
            <div style="margin-bottom: 20px;">
                <label>Wybierz stronę dla bloga</label>
                <select name="blog_page_id" class="input">
                    <option value="0">Brak (domyślna strona bloga)</option>
                    <?php foreach ($pages as $page): ?>
                        <option value="<?php echo $page['id']; ?>" <?php echo ($settings['blog_page_id'] ?? 0) == $page['id'] ? 'selected' : ''; ?>>
                            <?php echo e($page['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p style="margin-top:5px;color:#666;font-size:12px;">
                    Wybierz stronę, która będzie wyświetlać wpisy bloga. Jeśli nie wybierzesz żadnej, 
                    blog będzie dostępny pod adresem /blog
                </p>
            </div>
            
            <h3>Układ bloga</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
                <div>
                    <label>Układ kolumn</label>
                    <select name="blog_layout" class="input" onchange="updateBlogPreview()">
                        <option value="1-column" <?php echo ($settings['blog_layout'] ?? '1-column') === '1-column' ? 'selected' : ''; ?>>1 kolumna</option>
                        <option value="2-columns" <?php echo ($settings['blog_layout'] ?? '1-column') === '2-columns' ? 'selected' : ''; ?>>2 kolumny</option>
                    </select>
                </div>
                
                <div>
                    <label>Liczba wpisów na stronę</label>
                    <input type="number" class="input" name="blog_posts_per_page" value="<?php echo e($settings['blog_posts_per_page'] ?? 10); ?>" min="1" max="50">
                </div>
            </div>
            
            <!-- Podgląd układu -->
            <div style="margin-bottom: 20px;padding:15px;background:#f0f9ff;border:1px solid #0ea5e9;border-radius:6px;">
                <h4 style="margin:0 0 10px 0;color:#0c4a6e;">Podgląd układu</h4>
                <div id="blog-layout-preview" style="background:#e5e7eb;height:80px;border-radius:4px;position:relative;overflow:hidden;">
                    <div style="background:#3b82f6;height:100%;width:100%;margin:0 auto;border-radius:4px;display:flex;align-items:center;justify-content:center;color:white;font-size:12px;">
                        1 kolumna
                    </div>
                </div>
            </div>
            
            <h3>Filtry i wyszukiwanie</h3>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;margin-bottom:20px;">
                <div>
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="blog_show_filters" value="1" <?php echo ($settings['blog_show_filters'] ?? '1') === '1' ? 'checked' : ''; ?>> Pokaż filtry
                    </label>
                    <p style="margin-top:5px;color:#666;font-size:12px;">Wyświetl sekcję filtrów nad wpisami</p>
                </div>
                
                <div>
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="blog_show_category_filter" value="1" <?php echo ($settings['blog_show_category_filter'] ?? '1') === '1' ? 'checked' : ''; ?>> Filtr kategorii
                    </label>
                    <p style="margin-top:5px;color:#666;font-size:12px;">Pozwól filtrować wpisy po kategoriach</p>
                </div>
                
                <div>
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="blog_show_search" value="1" <?php echo ($settings['blog_show_search'] ?? '1') === '1' ? 'checked' : ''; ?>> Wyszukiwarka
                    </label>
                    <p style="margin-top:5px;color:#666;font-size:12px;">Dodaj pole wyszukiwania wpisów</p>
                </div>
            </div>
            
            <h3>Elementy wyświetlane</h3>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;margin-bottom:20px;">
                <div>
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="blog_show_thumbnail" value="1" <?php echo ($settings['blog_show_thumbnail'] ?? '1') === '1' ? 'checked' : ''; ?>> Zdjęcie miniatura
                    </label>
                </div>
                
                <div>
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="blog_show_title" value="1" <?php echo ($settings['blog_show_title'] ?? '1') === '1' ? 'checked' : ''; ?>> Tytuł
                    </label>
                </div>
                
                <div>
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="blog_show_date" value="1" <?php echo ($settings['blog_show_date'] ?? '1') === '1' ? 'checked' : ''; ?>> Data publikacji
                    </label>
                </div>
                
                <div>
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="blog_show_excerpt" value="1" <?php echo ($settings['blog_show_excerpt'] ?? '1') === '1' ? 'checked' : ''; ?>> Opis
                    </label>
                </div>
                
                <div>
                    <label style="display:flex;align-items:center;gap:8px;">
                        <input type="checkbox" name="blog_show_button" value="1" <?php echo ($settings['blog_show_button'] ?? '1') === '1' ? 'checked' : ''; ?>> Przycisk
                    </label>
                </div>
            </div>
            
            <h3>Ustawienia przycisku</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
                <div>
                    <label>Tekst przycisku</label>
                    <input type="text" class="input" name="blog_button_text" value="<?php echo e($settings['blog_button_text'] ?? 'Czytaj więcej'); ?>">
                </div>
                
                <div>
                    <label>Kolor tła przycisku</label>
                    <input type="color" class="input" name="blog_button_color" value="<?php echo e($settings['blog_button_color'] ?? '#007bff'); ?>">
                </div>
                
                <div>
                    <label>Kolor tekstu przycisku</label>
                    <input type="color" class="input" name="blog_button_text_color" value="<?php echo e($settings['blog_button_text_color'] ?? '#ffffff'); ?>">
                </div>
                
                <div>
                    <label>Rozmiar miniatur</label>
                    <select name="blog_thumbnail_size" class="input">
                        <option value="thumbnail" <?php echo ($settings['blog_thumbnail_size'] ?? 'medium') === 'thumbnail' ? 'selected' : ''; ?>>Miniatura (150x150)</option>
                        <option value="medium" <?php echo ($settings['blog_thumbnail_size'] ?? 'medium') === 'medium' ? 'selected' : ''; ?>>Średni (300x300)</option>
                        <option value="large" <?php echo ($settings['blog_thumbnail_size'] ?? 'medium') === 'large' ? 'selected' : ''; ?>>Duży (1024x1024)</option>
                    </select>
                </div>
                
                <div>
                    <label>Długość opisu (znaki)</label>
                    <input type="number" class="input" name="blog_excerpt_length" value="<?php echo e($settings['blog_excerpt_length'] ?? 150); ?>" min="50" max="500">
                </div>
            </div>
            
            <!-- Podgląd przycisku -->
            <div style="margin-bottom: 20px;padding:15px;background:#f8f9fa;border:1px solid #e5e7eb;border-radius:6px;">
                <h4 style="margin:0 0 10px 0;">Podgląd przycisku</h4>
                <button type="button" id="blog-button-preview" style="padding: 10px 20px; border: none; border-radius: 4px; font-weight: 500; cursor: default;">
                    <?php echo e($settings['blog_button_text'] ?? 'Czytaj więcej'); ?>
                </button>
            </div>
            
            <button class="btn" type="submit" name="save_blog" style="margin-top:16px;">Zapisz ustawienia bloga</button>
        </form>
    </div>
</div>

<!-- Zakładka: Ustawienia stopki -->
<div id="footer-tab" class="tab-content">
    <div class="card">
        <h2>Ustawienia stopki</h2>
        <form method="post">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
                <div>
                    <label>Kolor tła stopki</label>
                    <input type="color" class="input" name="footer_background_color" value="<?php echo e($settings['footer_background_color'] ?? '#333333'); ?>">
                </div>
                
                <div>
                    <label>Kolor tekstu stopki</label>
                    <input type="color" class="input" name="footer_text_color" value="<?php echo e($settings['footer_text_color'] ?? '#ffffff'); ?>">
                </div>
                
                <div>
                    <label>Liczba kolumn</label>
                    <select name="footer_columns" class="input" onchange="toggleFooterColumns()">
                        <option value="2" <?php echo ($settings['footer_columns'] ?? 3) == 2 ? 'selected' : ''; ?>>2 kolumny</option>
                        <option value="3" <?php echo ($settings['footer_columns'] ?? 3) == 3 ? 'selected' : ''; ?>>3 kolumny</option>
                        <option value="4" <?php echo ($settings['footer_columns'] ?? 3) == 4 ? 'selected' : ''; ?>>4 kolumny</option>
                        <option value="5" <?php echo ($settings['footer_columns'] ?? 3) == 5 ? 'selected' : ''; ?>>5 kolumn</option>
                    </select>
                </div>
            </div>
            
            <div id="footer-columns">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                <div id="footer-column-<?php echo $i; ?>" class="footer-column" style="<?php echo ($i <= ($settings['footer_columns'] ?? 3)) ? '' : 'display:none;'; ?>">
                    <h3>Kolumna <?php echo $i; ?></h3>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div>
                            <label>Tytuł kolumny</label>
                            <input class="input" name="footer_column<?php echo $i; ?>_title" value="<?php echo e($settings["footer_column{$i}_title"] ?? ''); ?>">
                        </div>
                        <div>
                            <label>Obrazek</label>
                            <div style="display:flex;gap:10px;align-items:center;">
                                <input class="input" name="footer_column<?php echo $i; ?>_image" value="<?php echo e($settings["footer_column{$i}_image"] ?? ''); ?>" placeholder="URL obrazka">
                                <button type="button" class="btn" onclick="openMediaLibrary('footer_column<?php echo $i; ?>_image')">Wybierz</button>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:10px;">
                        <label>Treść kolumny</label>
                        <textarea class="input" name="footer_column<?php echo $i; ?>_content" rows="4" placeholder="Treść kolumny..."><?php echo e($settings["footer_column{$i}_content"] ?? ''); ?></textarea>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
            
            <button class="btn" type="submit" name="save_footer" style="margin-top:16px;">Zapisz ustawienia stopki</button>
        </form>
    </div>
</div>

<!-- Zakładka: Ustawienia stron -->
<div id="pages-tab" class="tab-content">
    <div class="card">
        <h2>Ustawienia stron</h2>
        <form method="post">
            <div>
                <label>Strona główna</label>
                <select name="homepage_id" class="input">
                    <option value="0">Domyślna (ostatnie wpisy)</option>
                    <?php foreach ($pages as $page): ?>
                        <option value="<?php echo $page['id']; ?>" <?php echo ($settings['homepage_id'] ?? 0) == $page['id'] ? 'selected' : ''; ?>>
                            <?php echo e($page['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p style="margin-top:8px;color:#666;font-size:14px;">
                    Wybierz stronę, która ma być wyświetlana jako strona główna. Jeśli nie wybierzesz żadnej, 
                    będzie wyświetlana lista ostatnich wpisów.
                </p>
            </div>
            
            <button class="btn" type="submit" name="save_pages" style="margin-top:16px;">Zapisz ustawienia stron</button>
        </form>
    </div>
</div>

<style>
.tabs {
    display: flex;
    gap: 2px;
    margin-bottom: 20px;
    border-bottom: 2px solid #e5e7eb;
}

.tab-button {
    background: #f3f4f6;
    border: none;
    padding: 12px 20px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.tab-button:hover {
    background: #e5e7eb;
}

.tab-button.active {
    background: #3b82f6;
    color: white;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.menu-item {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-bottom: 10px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 6px;
}

.footer-column {
    border: 1px solid #e5e7eb;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 15px;
}
</style>

<script>
function showTab(tabName) {
    // Ukryj wszystkie zakładki
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Pokaż wybraną zakładkę
    document.getElementById(tabName + '-tab').classList.add('active');
    event.target.classList.add('active');
}

function openMediaLibrary(fieldName) {
    const mediaWindow = window.open('<?php echo admin_url('media.php'); ?>','mediaLibrary','width=800,height=600,scrollbars=yes,resizable=yes');
    const handler = function(ev){
        if(ev.data.type==='selectMedia'){
            document.querySelector(`input[name="${fieldName}"]`).value = ev.data.url;
            window.removeEventListener('message', handler);
        }
    };
    window.addEventListener('message', handler);
}

function toggleCtaFields() {
    const ctaType = document.querySelector('select[name="cta_type"]').value;
    const phoneField = document.getElementById('cta-phone-field');
    const formField = document.getElementById('cta-form-field');
    
    if (ctaType === 'phone') {
        phoneField.style.display = 'block';
        formField.style.display = 'none';
    } else {
        phoneField.style.display = 'none';
        formField.style.display = 'block';
    }
}

function toggleFooterColumns() {
    const columns = parseInt(document.querySelector('select[name="footer_columns"]').value);
    
    for (let i = 1; i <= 5; i++) {
        const column = document.getElementById('footer-column-' + i);
        if (i <= columns) {
            column.style.display = 'block';
        } else {
            column.style.display = 'none';
        }
    }
}



// Inicjalizacja
document.addEventListener('DOMContentLoaded', function() {
    toggleCtaFields();
    toggleFooterColumns();
    updateBlogPreview();
    updateBlogButtonPreview();
});

function updateBlogPreview() {
    const layout = document.querySelector('select[name="blog_layout"]').value;
    const preview = document.getElementById('blog-layout-preview');
    const previewContent = preview.querySelector('div');
    
    if (layout === '1-column') {
        previewContent.style.width = '100%';
        previewContent.textContent = '1 kolumna';
    } else {
        previewContent.style.width = '48%';
        previewContent.textContent = '2 kolumny';
        previewContent.style.margin = '0 1%';
    }
}

function updateBlogButtonPreview() {
    const buttonText = document.querySelector('input[name="blog_button_text"]').value;
    const buttonColor = document.querySelector('input[name="blog_button_color"]').value;
    const buttonTextColor = document.querySelector('input[name="blog_button_text_color"]').value;
    const preview = document.getElementById('blog-button-preview');
    
    preview.textContent = buttonText;
    preview.style.backgroundColor = buttonColor;
    preview.style.color = buttonTextColor;
}

// Dodaj event listeners dla podglądu przycisku
document.addEventListener('DOMContentLoaded', function() {
    const buttonTextInput = document.querySelector('input[name="blog_button_text"]');
    const buttonColorInput = document.querySelector('input[name="blog_button_color"]');
    const buttonTextColorInput = document.querySelector('input[name="blog_button_text_color"]');
    
    if (buttonTextInput) {
        buttonTextInput.addEventListener('input', updateBlogButtonPreview);
    }
    if (buttonColorInput) {
        buttonColorInput.addEventListener('input', updateBlogButtonPreview);
    }
    if (buttonTextColorInput) {
        buttonTextColorInput.addEventListener('input', updateBlogButtonPreview);
    }
});
</script>

<?php require __DIR__.'/layout-footer.php'; ?>
