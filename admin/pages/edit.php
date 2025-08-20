<?php
$title='Edytuj stronę';
require_once __DIR__.'/../../core/auth.php'; require_login();
require_once __DIR__.'/../../core/helpers.php';
$pdo = db(); $config = require __DIR__.'/../../config.php'; $prefix = $config['db']['prefix'];
$id = (int)($_GET['id'] ?? 0);
$errors=[];

$stmt = $pdo->prepare("SELECT * FROM `{$prefix}pages` WHERE id=:id");
$stmt->execute([':id'=>$id]);
$page = $stmt->fetch();
if (!$page) { header('Location: '.admin_url('pages/index.php')); exit; }

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $titleV = trim($_POST['title'] ?? '');
  $slugV = trim($_POST['slug'] ?? '') ?: slugify($titleV);
  $contentV = (string)($_POST['content'] ?? '');
  $statusV = in_array($_POST['status'] ?? 'draft',['draft','published'],true) ? $_POST['status'] : 'draft';
  $templateV = in_array($_POST['template'] ?? 'default',['default','blank'],true) ? $_POST['template'] : 'default';
  $featuredV = trim($_POST['featured_image'] ?? '');
  $showFeaturedV = isset($_POST['show_featured_image']) ? 1 : 0;
  $showTitleV = isset($_POST['show_title']) ? 1 : 0;
  if ($titleV==='') $errors[]='Podaj tytuł.';
  if (!$errors) {
    $stmt = $pdo->prepare("UPDATE `{$prefix}pages` SET title=:t, slug=:s, content=:c, status=:st, template=:tp, featured_image=:fi, show_featured_image=:sfi, show_title=:stitle WHERE id=:id");
    $stmt->execute([':t'=>$titleV,':s'=>$slugV,':c'=>$contentV,':st'=>$statusV,':tp'=>$templateV,':fi'=>$featuredV,':sfi'=>$showFeaturedV,':stitle'=>$showTitleV, ':id'=>$id]);
    header('Location: '.admin_url('pages/index.php')); exit;
  }
}
require __DIR__.'/../../layout-header.php';
?>
<h1>Edytuj stronę</h1>
<?php if ($errors): ?><div class="card" style="border:1px solid #e11d48;color:#fecaca"><?php echo implode('<br>',array_map('e',$errors)); ?></div><?php endif; ?>
<form method="post">
  <label>Tytuł</label>
  <input class="input" name="title" required value="<?php echo e($page['title']); ?>">
  <label>Slug (URL)</label>
  <input class="input" name="slug" value="<?php echo e($page['slug']); ?>">
  
  <label>Obrazek wyróżniający</label>
  <div style="display:flex;gap:10px;align-items:center;">
    <input class="input" name="featured_image" value="<?php echo e($page['featured_image'] ?? ''); ?>" placeholder="URL obrazka">
    <button type="button" class="btn" onclick="openMediaLibrary()">Wybierz z mediów</button>
  </div>
  <div style="display:flex; gap:16px; margin:12px 0; align-items:center;">
    <label style="display:flex; align-items:center; gap:8px;">
      <input type="checkbox" name="show_featured_image" value="1" <?php echo (int)($page['show_featured_image'] ?? 1) ? 'checked' : ''; ?>> Wyświetl zdjęcie główne nad tytułem
    </label>
    <label style="display:flex; align-items:center; gap:8px;">
      <input type="checkbox" name="show_title" value="1" <?php echo (int)($page['show_title'] ?? 1) ? 'checked' : ''; ?>> Wyświetl tytuł
    </label>
  </div>

  <label>Treść</label>
  <div class="editor-container">
    <div class="editor-toolbar">
      <button type="button" id="mode-toggle">WYSIWYG</button>
      <span class="mode-info">Przełącz między edytorem wizualnym a kodem HTML</span>
    </div>
    <div class="editor-area">
      <textarea class="input" name="content" id="content-editor" rows="12" style="display:none;border:none;border-radius:0;"><?php echo e($page['content']); ?></textarea>
    </div>
  </div>
  
  <div style="display:flex;gap:10px;margin-top:8px">
    <div><label>Status</label>
      <select name="status" class="input">
        <option value="draft" <?php echo $page['status']==='draft'?'selected':''; ?>>Szkic</option>
        <option value="published" <?php echo $page['status']==='published'?'selected':''; ?>>Opublikowana</option>
      </select>
    </div>
    <div><label>Szablon</label>
      <select name="template" class="input">
        <option value="default" <?php echo $page['template']==='default'?'selected':''; ?>>Domyślny</option>
        <option value="blank" <?php echo $page['template']==='blank'?'selected':''; ?>>Pusty</option>
      </select>
    </div>
  </div>
  
  <div style="margin-top:12px">
    <button class="btn" type="submit">Zapisz</button>
    <a href="<?php echo admin_url('pages/index.php'); ?>" style="margin-left:10px">Anuluj</a>
  </div>
</form>
<script src="<?php echo admin_url('assets/editor.js'); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
  initEditor('#content-editor','wysiwyg');
  document.getElementById('mode-toggle').addEventListener('click', function(){ toggleEditorMode('#content-editor'); });
});
function openMediaLibrary(){
  const mediaWindow = window.open('<?php echo admin_url('media.php'); ?>','mediaLibrary','width=800,height=600,scrollbars=yes,resizable=yes');
  const handler = function(ev){
    if(ev.data.type==='selectMedia'){
      document.querySelector('input[name="featured_image"]').value = ev.data.url;
      window.removeEventListener('message', handler);
    }
  };
  window.addEventListener('message', handler);
}
</script>
<?php require __DIR__.'/../layout-footer.php'; ?>
