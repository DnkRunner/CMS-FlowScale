<?php
$title = 'Zarządzanie kategoriami';
require_once __DIR__.'/../core/auth.php'; 
require_login();
require_once __DIR__.'/../core/helpers.php';
$pdo = db(); 
$config = require __DIR__.'/../config.php'; 
$prefix = $config['db']['prefix'];

$errors = [];
$success = '';

// Obsługa dodawania/edycji kategorii
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'add' || $action === 'edit') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $color = $_POST['color'] ?? '#3b82f6';
        $slug = trim($_POST['slug'] ?? '') ?: slugify($name);
        
        if (empty($name)) {
            $errors[] = 'Nazwa kategorii jest wymagana.';
        }
        
        if (!$errors) {
            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO `{$prefix}categories` (name, slug, description, color) VALUES (:name, :slug, :description, :color)");
                $stmt->execute([':name' => $name, ':slug' => $slug, ':description' => $description, ':color' => $color]);
                $success = 'Kategoria została dodana.';
            } else {
                $id = (int)($_POST['id'] ?? 0);
                $stmt = $pdo->prepare("UPDATE `{$prefix}categories` SET name=:name, slug=:slug, description=:description, color=:color WHERE id=:id");
                $stmt->execute([':name' => $name, ':slug' => $slug, ':description' => $description, ':color' => $color, ':id' => $id]);
                $success = 'Kategoria została zaktualizowana.';
            }
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM `{$prefix}categories` WHERE id=:id");
        $stmt->execute([':id' => $id]);
        $success = 'Kategoria została usunięta.';
    }
}

// Pobierz kategorie
$stmt = $pdo->prepare("SELECT c.*, COUNT(pc.post_id) as post_count FROM `{$prefix}categories` c LEFT JOIN `{$prefix}post_categories` pc ON c.id = pc.category_id GROUP BY c.id ORDER BY c.name");
$stmt->execute();
$categories = $stmt->fetchAll();

require __DIR__.'/layout-header.php';
?>

<h1>Zarządzanie kategoriami</h1>

<?php if ($success): ?>
    <div class="card" style="border:1px solid #10b981;color:#dcfce7;background:#064e3b;margin-bottom:16px">
        <?php echo e($success); ?>
    </div>
<?php endif; ?>

<?php if ($errors): ?>
    <div class="card" style="border:1px solid #e11d48;color:#fecaca;background:#450a0a;margin-bottom:16px">
        <?php echo implode('<br>',array_map('e',$errors)); ?>
    </div>
<?php endif; ?>

<!-- Formularz dodawania kategorii -->
<div class="card" style="margin-bottom: 20px;">
    <h3>Dodaj nową kategorię</h3>
    <form method="post">
        <input type="hidden" name="action" value="add">
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 100px; gap: 10px; align-items: end;">
            <div>
                <label>Nazwa</label>
                <input type="text" name="name" class="input" required>
            </div>
            <div>
                <label>Slug (URL)</label>
                <input type="text" name="slug" class="input" placeholder="zostaw puste aby wygenerować">
            </div>
            <div>
                <label>Opis</label>
                <input type="text" name="description" class="input">
            </div>
            <div>
                <label>Kolor</label>
                <input type="color" name="color" value="#3b82f6" class="input" style="height: 40px;">
            </div>
        </div>
        <div style="margin-top: 12px;">
            <button type="submit" class="btn">Dodaj kategorię</button>
        </div>
    </form>
</div>

<!-- Lista kategorii -->
<div class="card">
    <h3>Kategorie (<?php echo count($categories); ?>)</h3>
    
    <?php if (empty($categories)): ?>
        <p style="color: var(--muted); text-align: center; padding: 40px;">Brak kategorii. Dodaj pierwszą kategorię powyżej.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Nazwa</th>
                    <th>Slug</th>
                    <th>Opis</th>
                    <th>Kolor</th>
                    <th>Wpisy</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div style="width: 12px; height: 12px; border-radius: 50%; background: <?php echo e($category['color']); ?>;"></div>
                                <?php echo e($category['name']); ?>
                            </div>
                        </td>
                        <td><code><?php echo e($category['slug']); ?></code></td>
                        <td><?php echo e($category['description']); ?></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 4px;">
                                <div style="width: 20px; height: 20px; border-radius: 4px; background: <?php echo e($category['color']); ?>; border: 1px solid var(--border);"></div>
                                <span style="font-size: 12px;"><?php echo e($category['color']); ?></span>
                            </div>
                        </td>
                        <td>
                            <span class="badge"><?php echo $category['post_count']; ?> wpisów</span>
                        </td>
                        <td>
                            <div class="actions">
                                <button type="button" class="btn" style="font-size: 12px; padding: 4px 8px;" 
                                        onclick="editCategory(<?php echo htmlspecialchars(json_encode($category)); ?>)">
                                    Edytuj
                                </button>
                                <?php if ($category['post_count'] == 0): ?>
                                    <form method="post" style="display: inline;" onsubmit="return confirm('Czy na pewno chcesz usunąć tę kategorię?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                        <button type="submit" class="btn" style="font-size: 12px; padding: 4px 8px; background: #dc2626;">Usuń</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Modal edycji kategorii -->
<div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: var(--bg); padding: 24px; border-radius: 12px; min-width: 400px; border: 1px solid var(--border);">
        <h3>Edytuj kategorię</h3>
        <form method="post" id="editForm">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="editId">
            
            <div style="margin-bottom: 12px;">
                <label>Nazwa</label>
                <input type="text" name="name" id="editName" class="input" required>
            </div>
            
            <div style="margin-bottom: 12px;">
                <label>Slug (URL)</label>
                <input type="text" name="slug" id="editSlug" class="input">
            </div>
            
            <div style="margin-bottom: 12px;">
                <label>Opis</label>
                <input type="text" name="description" id="editDescription" class="input">
            </div>
            
            <div style="margin-bottom: 16px;">
                <label>Kolor</label>
                <input type="color" name="color" id="editColor" class="input" style="height: 40px;">
            </div>
            
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn">Zapisz zmiany</button>
                <button type="button" class="btn" style="background: var(--muted);" onclick="closeEditModal()">Anuluj</button>
            </div>
        </form>
    </div>
</div>

<script>
function editCategory(category) {
    document.getElementById('editId').value = category.id;
    document.getElementById('editName').value = category.name;
    document.getElementById('editSlug').value = category.slug;
    document.getElementById('editDescription').value = category.description || '';
    document.getElementById('editColor').value = category.color;
    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Zamykanie modala po kliknięciu poza nim
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>

<?php require __DIR__.'/layout-footer.php'; ?>

