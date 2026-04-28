<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['id'])) {
    header('Location: /pages/login.php');
    exit;
}

$editing = false;
$snippet = null;
$error = '';

// Проверка режима: создание или редактирование
if (!empty($_GET['id'])) {
    $editing = true;
    $snippet_id = (int)$_GET['id'];
    $stmt = $pdo->prepare('SELECT * FROM snippets WHERE id = ? AND user_id = ?');
    $stmt->execute([$snippet_id, $_SESSION['id']]);
    $snippet = $stmt->fetch();

    if (!$snippet) {
        die('Карточка не найдена или у вас нет доступа.');
    }
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $tag = trim($_POST['tag'] ?? '');
    $html = $_POST['html'] ?? '';
    $css = $_POST['css'] ?? '';
    $js = $_POST['js'] ?? '';
    $user_id = $_SESSION['id'];

    if ($name && $html) {
        if ($editing) {
            $stmt = $pdo->prepare('UPDATE snippets SET name = ?, description = ?, tag = ?, html = ?, css = ?, js = ? WHERE id = ? AND user_id = ?');
            $stmt->execute([$name, $description, $tag, $html, $css, $js, $snippet_id, $user_id]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO snippets (name, description, tag, html, css, js, user_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
            $stmt->execute([$name, $description, $tag, $html, $css, $js, $user_id]);
            $snippet_id = $pdo->lastInsertId();
        }
        header('Location: /pages/card.php?id=' . $snippet_id);
        exit;
    } else {
        $error = 'Название и HTML обязательны';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/assets/img/favicon.ico">
    <meta name="theme-color" content="#000000">
    <meta name="robots" content="noindex, nofollow">
    <title><?= $editing ? 'Редактировать' : 'Создать' ?> карточку | WebElementsLab</title>
    <link rel="stylesheet" href="/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
    <link href="https://cdn.jsdelivr.net/npm/prismjs/themes/prism-tomorrow.css" rel="stylesheet" />
</head>
<body>
<?php require_once __DIR__ . '/../templates/header.php'; ?>
<div class="wrapper">
<main class="block-main">
    <div class="d-flex gap1 card-page f-d-column">
        <h1 class="card-title"><?= $editing ? 'Редактировать карточку' : 'Создать карточку' ?></h1>
        <form method="post" id="snippet-form" autocomplete="off">
            <div class="d-flex j-c-space-between gap1">
                <input type="text" name="name" placeholder="Название..." required maxlength="100"
                       value="<?= htmlspecialchars($_POST['name'] ?? ($snippet['name'] ?? '')) ?>">
                <h2><span class="author-link"><?= htmlspecialchars($_SESSION['username']) ?></span></h2>
            </div>
            <input type="text" name="tag" placeholder="Теги (через запятую)" title="Теги (через запятую)" maxlength="100"
                value="<?= htmlspecialchars($_POST['tag'] ?? ($snippet['tag'] ?? '')) ?>">
            <div class="f-d-row d-flex gap1">
                <div class="left-card-page d-flex gap1 f-d-column">
                    <div class="block-element d-flex j-c-center a-i-center">
                        <iframe id="snippet-frame" sandbox="allow-scripts" referrerpolicy="no-referrer" style="width:100%;min-height:200px;border:none;"></iframe>
                    </div>
                    <?php if ($error): ?>
                        <div class="form-error" style="color:red;"> <?= htmlspecialchars($error) ?> </div>
                    <?php endif; ?>
                    <textarea name="description" placeholder="Описание..." title="Описание" maxlength="255"><?= htmlspecialchars($_POST['description'] ?? ($snippet['description'] ?? '')) ?></textarea>
                    <button type="submit" class="btn-card j-c-center d-flex"><?= $editing ? 'Обновить' : 'Сохранить' ?></button>
                </div>

                <!-- Code Tabs -->
                <div class="block-code">
                    <div class="tabs d-flex">
                        <button type="button" class="tab-btn active" data-tab="html">HTML</button>
                        <button type="button" class="tab-btn" data-tab="css">CSS</button>
                        <button type="button" class="tab-btn" data-tab="js">JS</button>
                    </div>
                    <div class="tab-content active" id="html">
                        <textarea name="html" id="html-input" required rows="8" style="font-family:monospace;"><?= htmlspecialchars($_POST['html'] ?? ($snippet['html'] ?? '')) ?></textarea>
                        <pre><code class="language-html" id="html-preview"></code></pre>
                    </div>
                    <div class="tab-content" id="css">
                        <textarea name="css" id="css-input" rows="6" style="font-family:monospace;"><?= htmlspecialchars($_POST['css'] ?? ($snippet['css'] ?? '')) ?></textarea>
                        <pre><code class="language-css" id="css-preview"></code></pre>
                    </div>
                    <div class="tab-content" id="js">
                        <textarea name="js" id="js-input" rows="6" style="font-family:monospace;"><?= htmlspecialchars($_POST['js'] ?? ($snippet['js'] ?? '')) ?></textarea>
                        <pre><code class="language-js" id="js-preview"></code></pre>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>
</div>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/prism.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-css.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-javascript.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-markup.min.js" defer></script>
<script>
window.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const id = btn.dataset.tab;
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.toggle('active', content.id === id);
            });
        });
    });

    function updatePrism(id, value) {
        const code = document.getElementById(id + '-preview');
        code.textContent = value;
        if (window.Prism) Prism.highlightElement(code);
    }

    ['html', 'css', 'js'].forEach(type => {
        const input = document.getElementById(type + '-input');
        input.addEventListener('input', function () {
            updatePrism(type, input.value);
            updatePreview();
        });
        updatePrism(type, input.value);
    });

    function updatePreview() {
        const html = document.getElementById('html-input').value;
        const css = document.getElementById('css-input').value;
        const js = document.getElementById('js-input').value;
        const iframe = document.getElementById('snippet-frame');
        if (iframe) {
            const safeCss = css.replace(/<\/style>/gi, '<\\/style>');
            const safeJs = js.replace(/<\/script>/gi, '<\\/script>');
            iframe.srcdoc = `<!DOCTYPE html><html><head><meta charset="UTF-8"><style>${safeCss}</style></head><body>${html}<script>document.addEventListener('click',event=>{if(event.target.closest('a, button')){event.preventDefault();}},true);document.addEventListener('submit',event=>event.preventDefault(),true);<\/script><script>${safeJs}<\/script></body></html>`;
        }
    }
    updatePreview();
});
</script>
</body>
</html>
