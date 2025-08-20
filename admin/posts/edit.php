<?php
$title='Edytuj wpis';
require_once __DIR__.'/../../core/auth.php'; require_login();
require_once __DIR__.'/../../core/helpers.php';
$pdo = db(); $config = require __DIR__.'/../../config.php'; $prefix = $config['db']['prefix'];
$id = (int)($_GET['id'] ?? 0);
$errors=[];

$stmt = $pdo->prepare("SELECT * FROM `{$prefix}posts` WHERE id=:id"); $stmt->execute([':id'=>$id]); $post = $stmt->fetch();
if (!$post) { header('Location: '.admin_url('posts/index.php')); exit; }

// Pobierz kategorie
$stmt = $pdo->prepare("SELECT * FROM `{$prefix}categories` ORDER BY name");
$stmt->execute();
$categories = $stmt->fetchAll();

// Pobierz kategorie posta
$stmt = $pdo->prepare("SELECT category_id FROM `{$prefix}post_categories` WHERE post_id=:pid");
$stmt->execute([':pid'=>$id]);
$postCategories = array_column($stmt->fetchAll(), 'category_id');

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $titleV = trim($_POST['title'] ?? '');
  $contentV = (string)($_POST['content'] ?? '');
  $statusV = in_array($_POST['status'] ?? 'draft',['draft','published','scheduled'],true) ? $_POST['status'] : 'draft';
  $templateV = in_array($_POST['template'] ?? 'default',['default','blank'],true) ? $_POST['template'] : 'default';
  $slugV = trim($_POST['slug'] ?? '') ?: slugify($titleV);
  $featuredImageV = trim($_POST['featured_image'] ?? '');
  $excerptV = trim($_POST['excerpt'] ?? '');
  $metaTitleV = trim($_POST['meta_title'] ?? '');
  $metaDescriptionV = trim($_POST['meta_description'] ?? '');
  $scheduledAtV = trim($_POST['scheduled_at'] ?? '');
  $categoriesV = $_POST['categories'] ?? [];
  $showFeaturedV = isset($_POST['show_featured_image']) ? 1 : 0;
  $showTitleV = isset($_POST['show_title']) ? 1 : 0;
  
  if ($titleV==='') $errors[]='Podaj tytuł.';
  if (!$errors) {
    // Obsługa dat publikacji
    $published_at = null;
    $scheduled_at = null;
    
    if ($statusV === 'published') {
      $published_at = $post['published_at'] ?: date('Y-m-d H:i:s');
    } elseif ($statusV === 'scheduled' && $scheduledAtV) {
      $scheduled_at = $scheduledAtV;
    }
    
    // Aktualizuj post
    $stmt = $pdo->prepare("UPDATE `{$prefix}posts` SET title=:t, slug=:s, content=:c, status=:st, template=:tp, published_at=:p, scheduled_at=:sch, featured_image=:fi, show_featured_image=:sfi, show_title=:stitle, excerpt=:ex, meta_title=:mt, meta_description=:md WHERE id=:id");
    $stmt->execute([
      ':t'=>$titleV, ':s'=>$slugV, ':c'=>$contentV, ':st'=>$statusV, ':tp'=>$templateV, 
      ':p'=>$published_at, ':sch'=>$scheduled_at, ':fi'=>$featuredImageV, ':sfi'=>$showFeaturedV, ':stitle'=>$showTitleV, ':ex'=>$excerptV,
      ':mt'=>$metaTitleV, ':md'=>$metaDescriptionV, ':id'=>$id
    ]);
    
    // Aktualizuj kategorie
    $stmt = $pdo->prepare("DELETE FROM `{$prefix}post_categories` WHERE post_id=:pid");
    $stmt->execute([':pid'=>$id]);
    
    if (!empty($categoriesV)) {
      $stmt = $pdo->prepare("INSERT INTO `{$prefix}post_categories` (post_id, category_id) VALUES (:pid, :cid)");
      foreach ($categoriesV as $catId) {
        $stmt->execute([':pid'=>$id, ':cid'=>$catId]);
      }
    }
    
    header('Location: '.admin_url('posts/index.php')); exit;
  }
}
require __DIR__.'/../../layout-header.php';
?>
<h1>Edytuj wpis</h1>
<?php if ($errors): ?><div class="card" style="border:1px solid #e11d48;color:#fecaca"><?php echo implode('<br>',array_map('e',$errors)); ?></div><?php endif; ?>
<form method="post">
  <label>Tytuł</label>
  <input class="input" name="title" required value="<?php echo e($post['title']); ?>">
  <label>Slug (URL)</label>
  <input class="input" name="slug" value="<?php echo e($post['slug']); ?>">
  
  <label>Kategorie</label>
  <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 5px;">
    <?php foreach ($categories as $category): ?>
      <label style="display: flex; align-items: center; gap: 4px; padding: 6px 12px; border: 1px solid var(--border); border-radius: 6px; cursor: pointer; background: var(--card);">
        <input type="checkbox" name="categories[]" value="<?php echo $category['id']; ?>" 
               <?php echo in_array($category['id'], $postCategories) ? 'checked' : ''; ?>>
        <div style="width: 12px; height: 12px; border-radius: 50%; background: <?php echo e($category['color']); ?>;"></div>
        <?php echo e($category['name']); ?>
      </label>
    <?php endforeach; ?>
  </div>
  
  <label>Obrazek wyróżniający</label>
  <div style="display: flex; gap: 10px; align-items: center;">
    <input class="input" name="featured_image" value="<?php echo e($post['featured_image'] ?? ''); ?>" placeholder="URL obrazka">
    <button type="button" class="btn" onclick="openMediaLibrary()">Wybierz z mediów</button>
  </div>
  <?php if (!empty($post['featured_image'])): ?>
    <div style="margin-top: 8px;">
      <img src="<?php echo e($post['featured_image']); ?>" alt="Obrazek wyróżniający" style="max-width: 200px; max-height: 150px; border-radius: 8px;">
    </div>
  <?php endif; ?>

  <div style="display:flex; gap:16px; margin:12px 0; align-items:center;">
    <label style="display:flex; align-items:center; gap:8px;">
      <input type="checkbox" name="show_featured_image" value="1" <?php echo (int)($post['show_featured_image'] ?? 1) ? 'checked' : ''; ?>>
      Wyświetl zdjęcie główne nad tytułem
    </label>
    <label style="display:flex; align-items:center; gap:8px;">
      <input type="checkbox" name="show_title" value="1" <?php echo (int)($post['show_title'] ?? 1) ? 'checked' : ''; ?>>
      Wyświetl tytuł
    </label>
  </div>
  
  <label>Skrót (excerpt)</label>
  <textarea class="input" name="excerpt" rows="3" placeholder="Krótki opis wpisu..."><?php echo e($post['excerpt'] ?? ''); ?></textarea>
  
  <label>Meta tytuł (SEO)</label>
  <input class="input" name="meta_title" value="<?php echo e($post['meta_title'] ?? ''); ?>" placeholder="Tytuł dla wyszukiwarek">
  
  <label>Meta opis (SEO)</label>
  <textarea class="input" name="meta_description" rows="2" placeholder="Opis dla wyszukiwarek..."><?php echo e($post['meta_description'] ?? ''); ?></textarea>
  <label>Treść</label>
  <div class="editor-container">
    <div class="editor-toolbar">
      <button type="button" id="mode-toggle">WYSIWYG</button>
      <span class="mode-info">Przełącz między edytorem wizualnym a kodem HTML</span>
    </div>
    <div class="editor-area">
      <textarea class="input" name="content" id="content-editor" rows="12" style="display: none; border: none; border-radius: 0;"><?php echo e($post['content']); ?></textarea>
    </div>
  </div>
  <div style="display:flex;gap:10px;margin-top:8px">
    <div><label>Status</label>
      <select name="status" class="input" onchange="toggleScheduledField()">
        <option value="draft" <?php echo $post['status']==='draft'?'selected':''; ?>>Szkic</option>
        <option value="published" <?php echo $post['status']==='published'?'selected':''; ?>>Opublikowany</option>
        <option value="scheduled" <?php echo $post['status']==='scheduled'?'selected':''; ?>>Zaplanowany</option>
      </select>
    </div>
    <div><label>Szablon</label>
      <select name="template" class="input">
        <option value="default" <?php echo $post['template']==='default'?'selected':''; ?>>Domyślny</option>
        <option value="blank" <?php echo $post['template']==='blank'?'selected':''; ?>>Pusty</option>
      </select>
    </div>
  </div>
  
  <div id="scheduledField" style="margin-top: 10px; <?php echo $post['status'] !== 'scheduled' ? 'display: none;' : ''; ?>">
    <label>Data i godzina publikacji</label>
    <input type="datetime-local" name="scheduled_at" class="input" 
           value="<?php echo $post['scheduled_at'] ? date('Y-m-d\TH:i', strtotime($post['scheduled_at'])) : ''; ?>">
  </div>
  <div style="margin-top:12px">
    <button class="btn" type="submit">Zapisz</button>
    <a href="<?php echo admin_url('posts/index.php'); ?>" style="margin-left:10px">Anuluj</a>
  </div>
</form>

<!-- Własny edytor WYSIWYG -->
<script src="<?php echo admin_url('assets/editor.js'); ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicjalizuj edytor w trybie WYSIWYG
    initEditor('#content-editor', 'wysiwyg');
    
    // Obsługa przełączania trybu
    document.getElementById('mode-toggle').addEventListener('click', function() {
        toggleEditorMode('#content-editor');
    });
});

function toggleScheduledField() {
    const status = document.querySelector('select[name="status"]').value;
    const scheduledField = document.getElementById('scheduledField');
    
    if (status === 'scheduled') {
        scheduledField.style.display = 'block';
    } else {
        scheduledField.style.display = 'none';
    }
}

function openMediaLibrary() {
    console.log('Otwieram bibliotekę mediów...');
    // Otwórz nowe okno z biblioteką mediów
    const mediaWindow = window.open('<?php echo admin_url('media.php'); ?>', 'mediaLibrary', 'width=800,height=600,scrollbars=yes,resizable=yes');
    
    if (!mediaWindow) {
        alert('Nie można otworzyć okna popup. Sprawdź czy blokujesz popupy.');
        return;
    }
    
    console.log('Okno mediów otwarte:', mediaWindow);
    
    // Nasłuchuj na wiadomości z okna mediów
    const messageHandler = function(event) {
        console.log('Otrzymano wiadomość:', event.data);
        if (event.data.type === 'selectMedia') {
            console.log('Wybrano media:', event.data.url);
            document.querySelector('input[name="featured_image"]').value = event.data.url;
            // Dodaj podgląd obrazka
            const previewContainer = document.querySelector('input[name="featured_image"]').parentNode;
            let preview = previewContainer.querySelector('.featured-image-preview');
            if (!preview) {
                preview = document.createElement('div');
                preview.className = 'featured-image-preview';
                preview.style.cssText = 'margin-top: 8px;';
                previewContainer.appendChild(preview);
            }
            preview.innerHTML = `<img src="${event.data.url}" alt="Obrazek wyróżniający" style="max-width: 200px; max-height: 150px; border-radius: 8px;">`;
            window.removeEventListener('message', messageHandler);
        }
    };
    
    window.addEventListener('message', messageHandler);
}
</script>

<?php require __DIR__.'/../layout-footer.php'; ?>
