<?php
$title='Dashboard';
require_once __DIR__.'/../core/auth.php'; require_login();
require_once __DIR__.'/../core/helpers.php';
require_once __DIR__.'/../core/analytics.php';
$pdo = db(); $config = require __DIR__.'/../config.php'; $prefix = $config['db']['prefix'];

// Pobierz statystyki
$stats = [];

// Liczba postów
$stmt = $pdo->query("SELECT COUNT(*) FROM `{$prefix}posts`");
$stats['posts'] = $stmt->fetchColumn();

// Liczba stron
$stmt = $pdo->query("SELECT COUNT(*) FROM `{$prefix}pages`");
$stats['pages'] = $stmt->fetchColumn();

// Liczba kategorii
$stmt = $pdo->query("SELECT COUNT(*) FROM `{$prefix}categories`");
$stats['categories'] = $stmt->fetchColumn();

// Ostatnie posty
$stmt = $pdo->query("SELECT title, slug, created_at FROM `{$prefix}posts` ORDER BY created_at DESC LIMIT 5");
$recent_posts = $stmt->fetchAll();

// Ostatnie strony
$stmt = $pdo->query("SELECT title, slug, created_at FROM `{$prefix}pages` ORDER BY created_at DESC LIMIT 5");
$recent_pages = $stmt->fetchAll();

// Pobierz statystyki odwiedzin
$visit_stats = get_visit_statistics(30);

// Pobierz ustawienia analityki
$ga_id = get_theme_setting('google_analytics_id', '');
$fb_pixel_id = get_theme_setting('facebook_pixel_id', '');

require __DIR__.'/layout-header.php';
?>

<h1>Dashboard</h1>

<!-- Statystyki -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:30px;">
    <div class="card" style="text-align:center;padding:20px;">
        <h3 style="margin:0;color:#3b82f6;"><?php echo $stats['posts']; ?></h3>
        <p style="margin:5px 0 0 0;color:#666;">Wpisy</p>
    </div>
    <div class="card" style="text-align:center;padding:20px;">
        <h3 style="margin:0;color:#10b981;"><?php echo $stats['pages']; ?></h3>
        <p style="margin:5px 0 0 0;color:#666;">Strony</p>
    </div>
    <div class="card" style="text-align:center;padding:20px;">
        <h3 style="margin:0;color:#f59e0b;"><?php echo $stats['categories']; ?></h3>
        <p style="margin:5px 0 0 0;color:#666;">Kategorie</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">
    <!-- Statystyki odwiedzin -->
    <div class="card">
        <h2>Statystyki odwiedzin</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:15px;margin-bottom:20px;">
            <div style="text-align:center;padding:15px;background:#f0f9ff;border-radius:6px;">
                <h3 style="margin:0;color:#0ea5e9;font-size:1.5rem;"><?php echo number_format($visit_stats['total_visits']); ?></h3>
                <p style="margin:5px 0 0 0;color:#0c4a6e;font-size:12px;">Wszystkie odwiedziny</p>
            </div>
            <div style="text-align:center;padding:15px;background:#f0fdf4;border-radius:6px;">
                <h3 style="margin:0;color:#16a34a;font-size:1.5rem;"><?php echo number_format($visit_stats['recent_visits']); ?></h3>
                <p style="margin:5px 0 0 0;color:#15803d;font-size:12px;">Ostatnie 30 dni</p>
            </div>
            <div style="text-align:center;padding:15px;background:#fef3c7;border-radius:6px;">
                <h3 style="margin:0;color:#f59e0b;font-size:1.5rem;"><?php echo number_format($visit_stats['today_visits']); ?></h3>
                <p style="margin:5px 0 0 0;color:#92400e;font-size:12px;">Dzisiaj</p>
            </div>
            <div style="text-align:center;padding:15px;background:#fef2f2;border-radius:6px;">
                <h3 style="margin:0;color:#ef4444;font-size:1.5rem;"><?php echo number_format($visit_stats['yesterday_visits']); ?></h3>
                <p style="margin:5px 0 0 0;color:#991b1b;font-size:12px;">Wczoraj</p>
            </div>
        </div>
        
        <?php if (!empty($visit_stats['popular_pages'])): ?>
            <h3 style="margin-bottom:15px;">Najpopularniejsze strony (30 dni)</h3>
            <div style="max-height:200px;overflow-y:auto;">
                <?php foreach ($visit_stats['popular_pages'] as $page): ?>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f3f4f6;">
                        <span style="font-size:14px;"><?php echo e($page['page_url']); ?></span>
                        <span style="color:#666;font-size:12px;"><?php echo $page['visits']; ?> wizyt</span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div style="margin-top:15px;">
            <a href="<?php echo site_url(); ?>" target="_blank" class="btn">Otwórz stronę główną</a>
        </div>
    </div>
    
    <!-- Analityka -->
    <div class="card">
        <h2>Analityka</h2>
        
        <!-- Google Analytics -->
        <div style="margin-bottom:20px;">
            <h3 style="margin-bottom:10px;font-size:16px;">Google Analytics</h3>
            <?php if ($ga_id): ?>
                <div style="background:#f0f9ff;border:1px solid #0ea5e9;border-radius:6px;padding:12px;margin-bottom:10px;">
                    <p style="margin:0;color:#0c4a6e;font-size:14px;">
                        <strong>ID śledzenia:</strong> <?php echo e($ga_id); ?>
                    </p>
                </div>
                <div style="background:#fef3c7;border:1px solid #f59e0b;border-radius:6px;padding:12px;">
                    <p style="margin:0;color:#92400e;font-size:12px;">
                        <strong>Status:</strong> Aktywne śledzenie
                    </p>
                </div>
            <?php else: ?>
                <div style="background:#fef2f2;border:1px solid #ef4444;border-radius:6px;padding:12px;">
                    <p style="margin:0;color:#991b1b;font-size:12px;">
                        <strong>Google Analytics nie jest skonfigurowane.</strong>
                    </p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Facebook Pixel -->
        <div style="margin-bottom:20px;">
            <h3 style="margin-bottom:10px;font-size:16px;">Facebook Pixel</h3>
            <?php if ($fb_pixel_id): ?>
                <div style="background:#f0f9ff;border:1px solid #0ea5e9;border-radius:6px;padding:12px;margin-bottom:10px;">
                    <p style="margin:0;color:#0c4a6e;font-size:14px;">
                        <strong>Pixel ID:</strong> <?php echo e($fb_pixel_id); ?>
                    </p>
                </div>
                <div style="background:#fef3c7;border:1px solid #f59e0b;border-radius:6px;padding:12px;">
                    <p style="margin:0;color:#92400e;font-size:12px;">
                        <strong>Status:</strong> Aktywne śledzenie
                    </p>
                </div>
            <?php else: ?>
                <div style="background:#fef2f2;border:1px solid #ef4444;border-radius:6px;padding:12px;">
                    <p style="margin:0;color:#991b1b;font-size:12px;">
                        <strong>Facebook Pixel nie jest skonfigurowany.</strong>
                    </p>
                </div>
            <?php endif; ?>
        </div>
        
        <div style="margin-top:15px;">
            <a href="<?php echo admin_url('theme.php'); ?>" class="btn">Konfiguruj analitykę</a>
        </div>
    </div>
</div>

<!-- Ostatnie wpisy i strony -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:30px;">
    <div class="card">
        <h2>Ostatnie wpisy</h2>
        <?php if ($recent_posts): ?>
            <div style="display:flex;flex-direction:column;gap:10px;">
                <?php foreach ($recent_posts as $post): ?>
                    <div style="padding:10px;background:#f8f9fa;border-radius:6px;">
                        <h4 style="margin:0 0 5px 0;"><?php echo e($post['title']); ?></h4>
                        <p style="margin:0;color:#666;font-size:12px;">
                            Utworzono: <?php echo date('d.m.Y H:i', strtotime($post['created_at'])); ?>
                        </p>
                        <a href="<?php echo site_url($post['slug']); ?>" target="_blank" style="font-size:12px;">Podgląd</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color:#666;">Brak wpisów.</p>
        <?php endif; ?>
        <div style="margin-top:15px;">
            <a href="<?php echo admin_url('posts/index.php'); ?>" class="btn">Wszystkie wpisy</a>
        </div>
    </div>
    
    <div class="card">
        <h2>Ostatnie strony</h2>
        <?php if ($recent_pages): ?>
            <div style="display:flex;flex-direction:column;gap:10px;">
                <?php foreach ($recent_pages as $page): ?>
                    <div style="padding:10px;background:#f8f9fa;border-radius:6px;">
                        <h4 style="margin:0 0 5px 0;"><?php echo e($page['title']); ?></h4>
                        <p style="margin:0;color:#666;font-size:12px;">
                            Utworzono: <?php echo date('d.m.Y H:i', strtotime($page['created_at'])); ?>
                        </p>
                        <a href="<?php echo site_url($page['slug']); ?>" target="_blank" style="font-size:12px;">Podgląd</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color:#666;">Brak stron.</p>
        <?php endif; ?>
        <div style="margin-top:15px;">
            <a href="<?php echo admin_url('pages/index.php'); ?>" class="btn">Wszystkie strony</a>
        </div>
    </div>
</div>

<!-- Szybkie akcje -->
<div class="card" style="margin-top:30px;">
    <h2>Szybkie akcje</h2>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="<?php echo admin_url('posts/new.php'); ?>" class="btn">+ Nowy wpis</a>
        <a href="<?php echo admin_url('pages/new.php'); ?>" class="btn">+ Nowa strona</a>
        <a href="<?php echo admin_url('categories.php'); ?>" class="btn">Zarządzaj kategoriami</a>
        <a href="<?php echo admin_url('media.php'); ?>" class="btn">Zarządzaj mediami</a>
        <a href="<?php echo admin_url('theme.php'); ?>" class="btn">Konfiguruj motyw</a>
    </div>
</div>

<?php require __DIR__.'/layout-footer.php'; ?>
