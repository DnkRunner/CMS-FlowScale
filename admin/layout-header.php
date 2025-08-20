<?php
require_once __DIR__.'/../core/auth.php'; require_login();
require_once __DIR__.'/../core/helpers.php';
$pdo = db(); $config = require __DIR__.'/../config.php'; $prefix = $config['db']['prefix'];
$theme = get_setting($pdo, $prefix, 'admin_theme', 'dark'); $dark = ($theme==='dark');
?>
<!doctype html><html lang="pl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $title ?? 'Panel'; ?></title>
<style>
:root{
  --bg: <?php echo $dark ? '#0f1115' : '#ffffff'; ?>;
  --fg: <?php echo $dark ? '#e5e7eb' : '#0f1115'; ?>;
  --muted: <?php echo $dark ? '#9ca3af' : '#6b7280'; ?>;
  --card: <?php echo $dark ? '#12151b' : '#f9fafb'; ?>;
  --border: <?php echo $dark ? '#1f2733' : '#e5e7eb'; ?>;
  --acc: <?php echo $dark ? '#3b82f6' : '#2563eb'; ?>;
}
*{box-sizing:border-box;font-family:system-ui,Segoe UI,Roboto,Inter,Arial}
body{background:var(--bg);color:var(--fg);margin:0;display:grid;grid-template-columns:220px 1fr;min-height:100vh}
aside{background:<?php echo $dark ? '#0d1016' : '#f3f4f6'; ?>;border-right:1px solid var(--border);padding:14px 10px}
.main{display:block}
.top{background:var(--card);border-bottom:1px solid var(--border);padding:12px 16px}
.content{padding:18px}
a{color:<?php echo $dark ? '#93c5fd' : '#1d4ed8'; ?>;text-decoration:none}
.nav a{display:flex;align-items:center;gap:10px;padding:10px;border-radius:10px;color:var(--fg)}
.nav a:hover{background:<?php echo $dark ? '#12151b' : '#e5e7eb'; ?>}
.nav .icon{width:18px;height:18px;display:inline-block;color:var(--fg)}
.nav .icon svg{width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2}
.btn{background:var(--acc);color:#fff;border:0;border-radius:8px;padding:8px 12px;cursor:pointer;font-weight:600;font-size:14px}
.table{width:100%;border-collapse:collapse}
.table th,.table td{padding:10px;border-bottom:1px solid var(--border);text-align:left}
.badge{font-size:12px;padding:2px 8px;border-radius:999px;border:1px solid var(--border);}
.right{float:right}
.input,select,textarea{width:100%;padding:10px;border-radius:10px;border:1px solid var(--border);background:<?php echo $dark ? '#0e1218' : '#fff'; ?>;color:inherit}
.actions a{margin-right:8px}

/* Style dla edytora */
.editor-container { margin-bottom: 20px; }
.editor-toolbar { 
    background: var(--card); 
    border: 1px solid var(--border); 
    border-bottom: none; 
    border-radius: 10px 10px 0 0; 
    padding: 10px; 
    display: flex; 
    gap: 10px; 
    align-items: center; 
}
.editor-toolbar button { 
    background: var(--acc); 
    color: white; 
    border: none; 
    padding: 8px 12px; 
    border-radius: 6px; 
    cursor: pointer; 
    font-size: 14px; 
    font-weight: 500; 
}
.editor-toolbar button:hover { 
    opacity: 0.9; 
}
.editor-toolbar .mode-info { 
    color: var(--muted); 
    font-size: 14px; 
    margin-left: auto; 
}
.editor-area { 
    border: 1px solid var(--border); 
    border-radius: 0 0 10px 10px; 
    overflow: hidden; 
}

/* Style dla własnego edytora */
.simple-editor {
    border: 1px solid var(--border);
    border-radius: 0 0 10px 10px;
    background: var(--bg);
    color: var(--fg);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 16px;
    line-height: 1.6;
}

.simple-editor .editor-toolbar {
    background: var(--card);
    border: 1px solid var(--border);
    border-bottom: none;
    border-radius: 10px 10px 0 0;
    padding: 8px;
    display: flex;
    gap: 4px;
    flex-wrap: wrap;
    align-items: center;
}

.simple-editor .editor-toolbar button {
    background: transparent;
    border: 1px solid transparent;
    border-radius: 4px;
    padding: 6px 8px;
    cursor: pointer;
    font-size: 14px;
    color: var(--fg);
    transition: all 0.2s;
}

.simple-editor .editor-toolbar button:hover {
    background: var(--border);
}

.simple-editor .editor-toolbar button.active {
    background: var(--acc);
    color: white;
}

.simple-editor [contenteditable] {
    min-height: 400px;
    padding: 16px;
    outline: none;
    overflow-y: auto;
}

.simple-editor [contenteditable]:empty:before {
    content: attr(data-placeholder);
    color: var(--muted);
    pointer-events: none;
}
</style>
</head>
<body>
<aside>
  <div style="font-weight:800;margin:4px 8px 12px 8px">CMS</div>
  <nav class="nav">
    <a href="<?php echo admin_url('dashboard.php'); ?>"><span class="icon"><svg viewBox="0 0 24 24"><path d="M3 9l9-6 9 6v9a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V12H9v6a2 2 0 0 1-2 2H3z"/></svg></span>Dashboard</a>
    <a href="<?php echo admin_url('posts/index.php'); ?>"><span class="icon"><svg viewBox="0 0 24 24"><path d="M4 4h16M4 8h16M4 12h10M4 16h8"/></svg></span>Wpisy</a>
    <a href="<?php echo admin_url('pages/index.php'); ?>"><span class="icon"><svg viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg></span>Strony</a>
    <a href="<?php echo admin_url('theme.php'); ?>"><span class="icon"><svg viewBox="0 0 24 24"><path d="M12 3a6 6 0 0 0 6 6V7a4 4 0 0 1 4 4v1a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2V9a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v1a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7a4 4 0 0 1 4-4h2z"/></svg></span>Motyw</a>
    <a href="<?php echo admin_url('categories.php'); ?>"><span class="icon"><svg viewBox="0 0 24 24"><path d="M3 3h18v18H3zM8 12h8M12 8v8"/></svg></span>Kategorie</a>
                    <a href="<?php echo admin_url('media.php'); ?>"><span class="icon"><svg viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></span>Media</a>
                <a href="<?php echo admin_url('comments.php'); ?>"><span class="icon"><svg viewBox="0 0 24 24"><path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg></span>Komentarze</a>
    <a href="<?php echo admin_url('system/update.php'); ?>"><span class="icon"><svg viewBox="0 0 24 24"><path d="M4 4v6h6M20 20v-6h-6M20 9a8 8 0 1 0-6 13"/></svg></span>Aktualizuj bazę</a>
    <a href="<?php echo admin_url('logout.php'); ?>"><span class="icon"><svg viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"/></svg></span>Wyloguj</a>
  </nav>
  <form method="post" action="<?php echo admin_url('theme.php'); ?>" style="margin:16px 8px 0">
    <select name="theme" class="input" onchange="this.form.submit()">
      <option value="dark" <?php echo $dark?'selected':''; ?>>Motyw: Dark</option>
      <option value="light" <?php echo !$dark?'selected':''; ?>>Motyw: Light</option>
    </select>
  </form>
</aside>
<div class="main">
  <div class="top">
    <strong>CMS • Panel</strong> — zalogowany: <?php echo e($_SESSION['user_name'] ?? 'admin'); ?>
  </div>
  <div class="content">
