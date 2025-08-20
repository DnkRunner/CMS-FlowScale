<?php
session_start();
require_once '../db.php';
require_once '../helpers.php';
require_once '../auth.php';

// Sprawdź czy użytkownik jest zalogowany
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$pdo = db();
$config = require '../config.php';
$prefix = $config['db']['prefix'];

// Pobierz statystyki
$stats = [
    'posts' => $pdo->query("SELECT COUNT(*) FROM `{$prefix}posts`")->fetchColumn(),
    'pages' => $pdo->query("SELECT COUNT(*) FROM `{$prefix}pages`")->fetchColumn(),
    'comments' => $pdo->query("SELECT COUNT(*) FROM `{$prefix}comments`")->fetchColumn(),
    'users' => $pdo->query("SELECT COUNT(*) FROM `{$prefix}users`")->fetchColumn()
];

// Pobierz ostatnie wpisy
$recent_posts = $pdo->query("SELECT * FROM `{$prefix}posts` ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Pobierz ostatnie komentarze
$recent_comments = $pdo->query("SELECT c.*, p.title as post_title FROM `{$prefix}comments` c LEFT JOIN `{$prefix}posts` p ON c.post_id = p.id ORDER BY c.created_at DESC LIMIT 5")->fetchAll();

$user = get_current_user();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel administracyjny - CMS</title>
    <link rel="stylesheet" href="../assets/theme.css">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .stat-card {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #007bff;
        }
        .recent-section {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .recent-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .recent-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <?php include 'layout-header.php'; ?>
    
    <div class="container">
        <h1>Panel administracyjny</h1>
        <p>Witaj, <?php echo htmlspecialchars($user['display_name'] ?? $user['username']); ?>!</p>
        
        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['posts']; ?></div>
                <div>Wpisy</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['pages']; ?></div>
                <div>Strony</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['comments']; ?></div>
                <div>Komentarze</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['users']; ?></div>
                <div>Użytkownicy</div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="recent-section">
                    <h3>Ostatnie wpisy</h3>
                    <?php foreach ($recent_posts as $post): ?>
                        <div class="recent-item">
                            <strong><?php echo htmlspecialchars($post['title']); ?></strong><br>
                            <small><?php echo date('d.m.Y H:i', strtotime($post['created_at'])); ?> - <?php echo $post['status']; ?></small>
                        </div>
                    <?php endforeach; ?>
                    <p><a href="posts/" class="btn btn-primary">Zobacz wszystkie wpisy</a></p>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="recent-section">
                    <h3>Ostatnie komentarze</h3>
                    <?php foreach ($recent_comments as $comment): ?>
                        <div class="recent-item">
                            <strong><?php echo htmlspecialchars($comment['author_name']); ?></strong> w <em><?php echo htmlspecialchars($comment['post_title']); ?></em><br>
                            <small><?php echo date('d.m.Y H:i', strtotime($comment['created_at'])); ?></small>
                        </div>
                    <?php endforeach; ?>
                    <p><a href="comments.php" class="btn btn-primary">Zobacz wszystkie komentarze</a></p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <h3>Szybkie akcje</h3>
                <a href="posts/new.php" class="btn btn-success">Nowy wpis</a>
                <a href="pages/new.php" class="btn btn-info">Nowa strona</a>
                <a href="media.php" class="btn btn-warning">Media</a>
                <a href="theme.php" class="btn btn-secondary">Motywy</a>
            </div>
        </div>
    </div>
    
    <?php include 'layout-footer.php'; ?>
</body>
</html>
