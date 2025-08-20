<?php
$title = 'Zarządzanie komentarzami';
require_once __DIR__.'/../core/auth.php'; 
require_login();
require_once __DIR__.'/../core/helpers.php';

$pdo = db(); 
$config = require __DIR__.'/../config.php'; 
$prefix = $config['db']['prefix'];

$msg = '';
$msg_type = 'info';

// Obsługa akcji
if ($_POST['action'] ?? '' === 'approve') {
    $comment_id = (int)($_POST['comment_id'] ?? 0);
    if ($comment_id) {
        $stmt = $pdo->prepare("UPDATE `{$prefix}comments` SET status = 'approved' WHERE id = ?");
        $stmt->execute([$comment_id]);
        $msg = 'Komentarz został zatwierdzony.';
        $msg_type = 'success';
    }
}

if ($_POST['action'] ?? '' === 'spam') {
    $comment_id = (int)($_POST['comment_id'] ?? 0);
    if ($comment_id) {
        $stmt = $pdo->prepare("UPDATE `{$prefix}comments` SET status = 'spam' WHERE id = ?");
        $stmt->execute([$comment_id]);
        $msg = 'Komentarz został oznaczony jako spam.';
        $msg_type = 'success';
    }
}

if ($_POST['action'] ?? '' === 'delete') {
    $comment_id = (int)($_POST['comment_id'] ?? 0);
    if ($comment_id) {
        $stmt = $pdo->prepare("DELETE FROM `{$prefix}comments` WHERE id = ?");
        $stmt->execute([$comment_id]);
        $msg = 'Komentarz został usunięty.';
        $msg_type = 'success';
    }
}

if ($_POST['action'] ?? '' === 'bulk_action') {
    $comment_ids = $_POST['comment_ids'] ?? [];
    $bulk_action = $_POST['bulk_action_type'] ?? '';
    
    if (!empty($comment_ids) && $bulk_action) {
        $placeholders = str_repeat('?,', count($comment_ids) - 1) . '?';
        
        switch ($bulk_action) {
            case 'approve':
                $stmt = $pdo->prepare("UPDATE `{$prefix}comments` SET status = 'approved' WHERE id IN ($placeholders)");
                $stmt->execute($comment_ids);
                $msg = 'Wybrane komentarze zostały zatwierdzone.';
                break;
            case 'spam':
                $stmt = $pdo->prepare("UPDATE `{$prefix}comments` SET status = 'spam' WHERE id IN ($placeholders)");
                $stmt->execute($comment_ids);
                $msg = 'Wybrane komentarze zostały oznaczone jako spam.';
                break;
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM `{$prefix}comments` WHERE id IN ($placeholders)");
                $stmt->execute($comment_ids);
                $msg = 'Wybrane komentarze zostały usunięte.';
                break;
        }
        $msg_type = 'success';
    }
}

// Filtry
$status_filter = $_GET['status'] ?? 'all';
$post_filter = (int)($_GET['post_id'] ?? 0);

// Pobierz komentarze
$where_conditions = [];
$params = [];

if ($status_filter !== 'all') {
    $where_conditions[] = "c.status = ?";
    $params[] = $status_filter;
}

if ($post_filter > 0) {
    $where_conditions[] = "c.post_id = ?";
    $params[] = $post_filter;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

$sql = "SELECT c.*, p.title as post_title, p.slug as post_slug 
        FROM `{$prefix}comments` c 
        LEFT JOIN `{$prefix}posts` p ON c.post_id = p.id 
        $where_clause 
        ORDER BY c.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$comments = $stmt->fetchAll();

// Pobierz posty dla filtra
$stmt = $pdo->query("SELECT id, title FROM `{$prefix}posts` ORDER BY title");
$posts = $stmt->fetchAll();

// Statystyki
$stmt = $pdo->query("SELECT status, COUNT(*) as count FROM `{$prefix}comments` GROUP BY status");
$stats = [];
while ($row = $stmt->fetch()) {
    $stats[$row['status']] = $row['count'];
}

require __DIR__.'/layout-header.php';
?>

<h1>Zarządzanie komentarzami</h1>

<?php if ($msg): ?>
    <div class="alert alert-<?php echo $msg_type; ?>" style="margin-bottom: 20px;">
        <?php echo e($msg); ?>
    </div>
<?php endif; ?>

<!-- Statystyki -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 20px;">
    <div class="card" style="text-align: center; padding: 15px;">
        <h3 style="margin: 0; color: #3b82f6;"><?php echo $stats['pending'] ?? 0; ?></h3>
        <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Oczekujące</p>
    </div>
    <div class="card" style="text-align: center; padding: 15px;">
        <h3 style="margin: 0; color: #10b981;"><?php echo $stats['approved'] ?? 0; ?></h3>
        <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Zatwierdzone</p>
    </div>
    <div class="card" style="text-align: center; padding: 15px;">
        <h3 style="margin: 0; color: #ef4444;"><?php echo $stats['spam'] ?? 0; ?></h3>
        <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Spam</p>
    </div>
    <div class="card" style="text-align: center; padding: 15px;">
        <h3 style="margin: 0; color: #f59e0b;"><?php echo array_sum($stats); ?></h3>
        <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Wszystkie</p>
    </div>
</div>

<!-- Filtry -->
<div class="card" style="margin-bottom: 20px;">
    <h3>Filtry</h3>
    <form method="get" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 15px; align-items: end;">
        <div>
            <label>Status</label>
            <select name="status" class="input">
                <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>Wszystkie</option>
                <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Oczekujące</option>
                <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Zatwierdzone</option>
                <option value="spam" <?php echo $status_filter === 'spam' ? 'selected' : ''; ?>>Spam</option>
            </select>
        </div>
        
        <div>
            <label>Wpis</label>
            <select name="post_id" class="input">
                <option value="0">Wszystkie wpisy</option>
                <?php foreach ($posts as $post): ?>
                    <option value="<?php echo $post['id']; ?>" <?php echo $post_filter == $post['id'] ? 'selected' : ''; ?>>
                        <?php echo e($post['title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <button type="submit" class="btn">Filtruj</button>
    </form>
</div>

<!-- Lista komentarzy -->
<div class="card">
    <form method="post" id="comments-form">
        <input type="hidden" name="action" value="bulk_action">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3>Komentarze (<?php echo count($comments); ?>)</h3>
            
            <div style="display: flex; gap: 10px; align-items: center;">
                <select name="bulk_action_type" class="input" style="width: auto;">
                    <option value="">Akcje masowe</option>
                    <option value="approve">Zatwierdź</option>
                    <option value="spam">Oznacz jako spam</option>
                    <option value="delete">Usuń</option>
                </select>
                <button type="submit" class="btn" onclick="return confirm('Czy na pewno chcesz wykonać tę akcję?')">Wykonaj</button>
            </div>
        </div>
        
        <?php if ($comments): ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th style="padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb;">
                                <input type="checkbox" id="select-all" onchange="toggleAllComments()">
                            </th>
                            <th style="padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb;">Autor</th>
                            <th style="padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb;">Komentarz</th>
                            <th style="padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb;">Wpis</th>
                            <th style="padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb;">Status</th>
                            <th style="padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb;">Data</th>
                            <th style="padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb;">Akcje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comments as $comment): ?>
                            <tr>
                                <td style="padding: 12px; border-bottom: 1px solid #f3f4f6;">
                                    <input type="checkbox" name="comment_ids[]" value="<?php echo $comment['id']; ?>" class="comment-checkbox">
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid #f3f4f6;">
                                    <div>
                                        <strong><?php echo e($comment['author_name']); ?></strong><br>
                                        <small style="color: #666;"><?php echo e($comment['author_email']); ?></small>
                                        <?php if ($comment['author_website']): ?>
                                            <br><small><a href="<?php echo e($comment['author_website']); ?>" target="_blank">Strona</a></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid #f3f4f6;">
                                    <div style="max-width: 300px;">
                                        <?php echo nl2br(e(substr($comment['content'], 0, 200))); ?>
                                        <?php if (strlen($comment['content']) > 200): ?>
                                            <span style="color: #666;">...</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid #f3f4f6;">
                                    <a href="<?php echo site_url($comment['post_slug']); ?>" target="_blank">
                                        <?php echo e($comment['post_title']); ?>
                                    </a>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid #f3f4f6;">
                                    <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; 
                                        background: <?php 
                                            echo $comment['status'] === 'approved' ? '#d1fae5' : 
                                                ($comment['status'] === 'pending' ? '#fef3c7' : '#fee2e2'); 
                                        ?>; 
                                        color: <?php 
                                            echo $comment['status'] === 'approved' ? '#065f46' : 
                                                ($comment['status'] === 'pending' ? '#92400e' : '#991b1b'); 
                                        ?>;">
                                        <?php 
                                            echo $comment['status'] === 'approved' ? 'Zatwierdzony' : 
                                                ($comment['status'] === 'pending' ? 'Oczekujący' : 'Spam'); 
                                        ?>
                                    </span>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid #f3f4f6;">
                                    <small style="color: #666;">
                                        <?php echo date('d.m.Y H:i', strtotime($comment['created_at'])); ?>
                                    </small>
                                </td>
                                <td style="padding: 12px; border-bottom: 1px solid #f3f4f6;">
                                    <div style="display: flex; gap: 5px;">
                                        <?php if ($comment['status'] === 'pending'): ?>
                                            <form method="post" style="display: inline;">
                                                <input type="hidden" name="action" value="approve">
                                                <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                                <button type="submit" class="btn" style="padding: 4px 8px; font-size: 12px; background: #10b981;">✓</button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <form method="post" style="display: inline;">
                                            <input type="hidden" name="action" value="spam">
                                            <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                            <button type="submit" class="btn" style="padding: 4px 8px; font-size: 12px; background: #ef4444;">🚫</button>
                                        </form>
                                        
                                        <form method="post" style="display: inline;" onsubmit="return confirm('Czy na pewno chcesz usunąć ten komentarz?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                            <button type="submit" class="btn" style="padding: 4px 8px; font-size: 12px; background: #6b7280;">🗑</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #666; padding: 40px;">Brak komentarzy spełniających kryteria.</p>
        <?php endif; ?>
    </form>
</div>

<script>
function toggleAllComments() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.comment-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

// Aktualizuj checkbox "wybierz wszystkie" na podstawie stanu pojedynczych checkboxów
document.querySelectorAll('.comment-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const allCheckboxes = document.querySelectorAll('.comment-checkbox');
        const checkedCheckboxes = document.querySelectorAll('.comment-checkbox:checked');
        const selectAll = document.getElementById('select-all');
        
        selectAll.checked = allCheckboxes.length === checkedCheckboxes.length;
        selectAll.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
    });
});
</script>

<?php require __DIR__.'/layout-footer.php'; ?>
