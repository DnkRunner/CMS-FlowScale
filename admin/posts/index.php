<?php
$title='Wpisy';
require_once __DIR__.'/../../core/auth.php'; require_login();
require_once __DIR__.'/../../core/helpers.php';
$pdo = db(); $config = require __DIR__.'/../../config.php'; $prefix = $config['db']['prefix'];

$rows = $pdo->query("SELECT id,title,slug,status,published_at,created_at FROM `{$prefix}posts` ORDER BY created_at DESC")->fetchAll();
require __DIR__.'/../../layout-header.php';
?>
<h1 style="display:flex;align-items:center;justify-content:space-between">
  <span>Wpisy</span>
  <a class="btn" href="<?php echo admin_url('posts/new.php'); ?>">Dodaj nowy wpis</a>
</h1>

<table class="table">
  <thead><tr><th>Tytuł</th><th>URL</th><th>Status</th><th>Opublikowano</th><th>Akcje</th></tr></thead>
  <tbody>
    <?php foreach ($rows as $r): $url = site_url($r['slug']); ?>
      <tr>
        <td><?php echo e($r['title']); ?></td>
        <td><a href="<?php echo e($url); ?>" target="_blank"><?php echo e($url); ?></a>
          <div style="font-size:12px;color:var(--muted)">Fallback: <code><?php echo e(site_url('index.php?slug='.$r['slug'])); ?></code></div>
        </td>
        <td><span class="badge"><?php echo e($r['status']); ?></span></td>
        <td><?php echo e($r['published_at'] ?: '—'); ?></td>
        <td class="actions">
          <a href="<?php echo admin_url('posts/edit.php?id='.$r['id']); ?>">Edytuj</a>
          <a href="<?php echo admin_url('posts/duplicate.php?id='.$r['id']); ?>">Duplikuj</a>
          <a href="<?php echo admin_url('posts/delete.php?id='.$r['id']); ?>" onclick="return confirm('Usunąć wpis?')">Usuń</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require __DIR__.'/../layout-footer.php'; ?>
