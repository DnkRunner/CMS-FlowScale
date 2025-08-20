<?php
$title='Strony';
require_once __DIR__.'/../../core/auth.php'; require_login();
require_once __DIR__.'/../../core/helpers.php';
$pdo = db(); $config = require __DIR__.'/../../config.php'; $prefix = $config['db']['prefix'];

$stmt = $pdo->query("SELECT id, title, slug, status, created_at, updated_at FROM `{$prefix}pages` ORDER BY created_at DESC");
$pages = $stmt->fetchAll();

require __DIR__.'/../../layout-header.php';
?>
<h1>Strony</h1>
<p><a class="btn" href="<?php echo admin_url('pages/new.php'); ?>">+ Nowa strona</a></p>
<table class="table">
  <thead><tr><th>Tytuł</th><th>Slug</th><th>Status</th><th>Utworzono</th><th>Akcje</th></tr></thead>
  <tbody>
  <?php foreach ($pages as $page): ?>
    <tr>
      <td><?php echo e($page['title']); ?></td>
      <td><code><?php echo e($page['slug']); ?></code></td>
      <td><span class="badge"><?php echo e($page['status']); ?></span></td>
      <td><?php echo e($page['created_at']); ?></td>
      <td class="actions">
        <a href="<?php echo admin_url('pages/edit.php?id='.$page['id']); ?>">Edytuj</a>
        <a href="<?php echo site_url($page['slug']); ?>" target="_blank">Podgląd</a>
        <a href="<?php echo admin_url('pages/delete.php?id='.$page['id']); ?>" onclick="return confirm('Usunąć stronę?')">Usuń</a>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php require __DIR__.'/../layout-footer.php'; ?>
