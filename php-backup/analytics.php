<?php
/**
 * Funkcje do śledzenia odwiedzin i analityki
 */

/**
 * Rejestruje wizytę na stronie
 */
function track_page_visit($page_url = null) {
    if (!$page_url) {
        $page_url = $_SERVER['REQUEST_URI'] ?? '/';
    }
    
    $pdo = db();
    $config = require __DIR__.'/../config.php';
    $prefix = $config['db']['prefix'];
    
    // Pobierz informacje o wizycie
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    $referrer = $_SERVER['HTTP_REFERER'] ?? null;
    $visit_date = date('Y-m-d');
    $visit_time = date('H:i:s');
    
    // Sprawdź czy to nie bot
    if (is_bot($user_agent)) {
        return false;
    }
    
    // Sprawdź czy to nie powtórna wizyta z tego samego IP w ciągu ostatnich 30 minut
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `{$prefix}page_visits` WHERE ip_address = ? AND page_url = ? AND created_at > DATE_SUB(NOW(), INTERVAL 30 MINUTE)");
    $stmt->execute([$ip_address, $page_url]);
    if ($stmt->fetchColumn() > 0) {
        return false; // Powtórna wizyta
    }
    
    // Zapisz wizytę
    $stmt = $pdo->prepare("INSERT INTO `{$prefix}page_visits` (page_url, ip_address, user_agent, referrer, visit_date, visit_time) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$page_url, $ip_address, $user_agent, $referrer, $visit_date, $visit_time]);
    
    // Zaktualizuj licznik odwiedzin
    update_page_visits_counter();
    
    return true;
}

/**
 * Sprawdza czy user agent to bot
 */
function is_bot($user_agent) {
    if (!$user_agent) return false;
    
    $bots = [
        'bot', 'crawler', 'spider', 'scraper', 'slurp', 'baiduspider', 
        'bingbot', 'googlebot', 'yandex', 'duckduckbot', 'facebookexternalhit',
        'twitterbot', 'linkedinbot', 'whatsapp', 'telegrambot', 'discordbot'
    ];
    
    $user_agent_lower = strtolower($user_agent);
    
    foreach ($bots as $bot) {
        if (strpos($user_agent_lower, $bot) !== false) {
            return true;
        }
    }
    
    return false;
}

/**
 * Aktualizuje licznik odwiedzin
 */
function update_page_visits_counter() {
    $pdo = db();
    $config = require __DIR__.'/../config.php';
    $prefix = $config['db']['prefix'];
    
    // Pobierz całkowitą liczbę unikalnych wizyt
    $stmt = $pdo->query("SELECT COUNT(DISTINCT CONCAT(ip_address, DATE(created_at))) FROM `{$prefix}page_visits`");
    $total_visits = $stmt->fetchColumn();
    
    // Zaktualizuj ustawienie
    $stmt = $pdo->prepare("INSERT INTO `{$prefix}theme_settings` (setting_key, setting_value) VALUES ('page_visits', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
    $stmt->execute([$total_visits, $total_visits]);
}

/**
 * Pobiera statystyki odwiedzin
 */
function get_visit_statistics($days = 30) {
    $pdo = db();
    $config = require __DIR__.'/../config.php';
    $prefix = $config['db']['prefix'];
    
    $stats = [];
    
    // Całkowita liczba odwiedzin
    $stmt = $pdo->query("SELECT COUNT(DISTINCT CONCAT(ip_address, DATE(created_at))) FROM `{$prefix}page_visits`");
    $stats['total_visits'] = $stmt->fetchColumn();
    
    // Odwiedziny z ostatnich X dni
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT CONCAT(ip_address, DATE(created_at))) FROM `{$prefix}page_visits` WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)");
    $stmt->execute([$days]);
    $stats['recent_visits'] = $stmt->fetchColumn();
    
    // Dzisiejsze odwiedziny
    $stmt = $pdo->query("SELECT COUNT(DISTINCT CONCAT(ip_address, DATE(created_at))) FROM `{$prefix}page_visits` WHERE DATE(created_at) = CURDATE()");
    $stats['today_visits'] = $stmt->fetchColumn();
    
    // Wczorajsze odwiedziny
    $stmt = $pdo->query("SELECT COUNT(DISTINCT CONCAT(ip_address, DATE(created_at))) FROM `{$prefix}page_visits` WHERE DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)");
    $stats['yesterday_visits'] = $stmt->fetchColumn();
    
    // Najpopularniejsze strony
    $stmt = $pdo->prepare("SELECT page_url, COUNT(*) as visits FROM `{$prefix}page_visits` WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) GROUP BY page_url ORDER BY visits DESC LIMIT 10");
    $stmt->execute([$days]);
    $stats['popular_pages'] = $stmt->fetchAll();
    
    // Odwiedziny z ostatnich 7 dni (dla wykresu)
    $stmt = $pdo->query("SELECT DATE(created_at) as date, COUNT(DISTINCT CONCAT(ip_address, DATE(created_at))) as visits FROM `{$prefix}page_visits` WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY DATE(created_at) ORDER BY date");
    $stats['weekly_chart'] = $stmt->fetchAll();
    
    return $stats;
}

/**
 * Pobiera kod Google Analytics
 */
function get_google_analytics_code() {
    $ga_id = get_theme_setting('google_analytics_id', '');
    
    if (!$ga_id) {
        return '';
    }
    
    // Sprawdź format ID (GA4 lub Universal Analytics)
    if (strpos($ga_id, 'G-') === 0) {
        // Google Analytics 4
        return "
        <!-- Google Analytics 4 -->
        <script async src=\"https://www.googletagmanager.com/gtag/js?id={$ga_id}\"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{$ga_id}');
        </script>";
    } elseif (strpos($ga_id, 'UA-') === 0) {
        // Universal Analytics
        return "
        <!-- Google Analytics -->
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
            ga('create', '{$ga_id}', 'auto');
            ga('send', 'pageview');
        </script>";
    }
    
    return '';
}

/**
 * Pobiera kod Facebook Pixel
 */
function get_facebook_pixel_code() {
    $pixel_id = get_theme_setting('facebook_pixel_id', '');
    
    if (!$pixel_id) {
        return '';
    }
    
    return "
    <!-- Facebook Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{$pixel_id}');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height=\"1\" width=\"1\" style=\"display:none\" 
             src=\"https://www.facebook.com/tr?id={$pixel_id}&ev=PageView&noscript=1\"/>
    </noscript>
    <!-- End Facebook Pixel Code -->";
}

/**
 * Wyświetla wszystkie kody śledzenia
 */
function display_tracking_codes() {
    echo get_google_analytics_code();
    echo get_facebook_pixel_code();
}
