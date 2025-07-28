<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['id'])) {
    header('Location: /pages/login.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $tag = trim($_POST['tag'] ?? '');
    $html = $_POST['html'] ?? '';
    $css = $_POST['css'] ?? '';
    $js = $_POST['js'] ?? '';
    $user_id = $_SESSION['id'];

    if ($name && $html) {
        $stmt = $pdo->prepare('INSERT INTO snippets (name, description, tag, html, css, js, user_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$name, $description, $tag, $html, $css, $js, $user_id]);
        $snippet_id = $pdo->lastInsertId();
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
    <link rel="icon" href="/assets/img/favicon.ico" >
    <meta name="theme-color" content="#000000" >
    <meta name="robots" content="noindex, nofollow">
    <title>Создать карточку | WebElementsLab</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/prismjs/themes/prism-tomorrow.css" rel="stylesheet" />
</head>
<body>
<?php require_once __DIR__ . '/../templates/header.php'; ?>
<div class="wrapper">
<main class="block-main">
    <div class="d-flex gap1 card-page f-d-column">
    <h1 class="card-title">Создать карточку</h1>
        <div class="d-flex j-c-space-between">
        <input type="text" name="name" placeholder="Название..." required maxlength="100" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            <h2><span class="author-link"><?= htmlspecialchars($_SESSION['username']) ?></span></h2>
        </div>
        <div class="f-d-row d-flex gap1">
            <!-- Preview & Meta -->
            <div class="left-card-page d-flex gap1 f-d-column">
                <div class="block-element d-flex j-c-center a-i-center">
                    <iframe id="snippet-frame" style="width:100%;min-height:200px;border:none;"></iframe>
                </div>
                <form method="post" class="d-flex f-d-column gap1" id="snippet-form" autocomplete="off">
                    <?php if ($error): ?>
                        <div class="form-error" style="color:red;"> <?= htmlspecialchars($error) ?> </div>
                    <?php endif; ?>
                        <textarea name="description" placeholder="Описание..." title="Описание" maxlength="255"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                        <input type="text" name="tag" placeholder="Теги (через запятую)" title="Теги (через запятую)" maxlength="100" value="<?= htmlspecialchars($_POST['tag'] ?? '') ?>">
                    <button type="submit" class="btn-card j-c-center d-flex">Сохранить</button>
                </form>
            </div>
            <!-- Code Tabs -->
            <div class="block-code">
                <div class="tabs d-flex">
                    <button class="tab-btn active" data-tab="html">HTML</button>
                    <button class="tab-btn" data-tab="css">CSS</button>
                    <button class="tab-btn" data-tab="js">JS</button>
                </div>
                <div class="tab-content active" id="html">
                    <textarea name="html" id="html-input" required rows="8" style="font-family:monospace;"><?= htmlspecialchars($_POST['html'] ?? '') ?></textarea>
                    <pre><code class="language-html" id="html-preview"></code></pre>
                </div>
                <div class="tab-content" id="css">
                    <textarea name="css" id="css-input" rows="6" style="font-family:monospace;"><?= htmlspecialchars($_POST['css'] ?? '') ?></textarea>
                    <pre><code class="language-css" id="css-preview"></code></pre>
                </div>
                <div class="tab-content" id="js">
                    <textarea name="js" id="js-input" rows="6" style="font-family:monospace;"><?= htmlspecialchars($_POST['js'] ?? '') ?></textarea>
                    <pre><code class="language-js" id="js-preview"></code></pre>
                </div>
            </div>
        </div>
    </div>
</main>
</div>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/prism.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-css.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-javascript.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-markup.min.js" defer></script>
<script>
// Вкладки
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

    // Prism live highlight
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
        // Первичная инициализация
        updatePrism(type, input.value);
    });

    // Live preview
    function updatePreview() {
        const html = document.getElementById('html-input').value;
        const css = document.getElementById('css-input').value;
        const js = document.getElementById('js-input').value;
        const iframe = document.getElementById('snippet-frame');
        if (iframe) {
            const doc = iframe.contentDocument || iframe.contentWindow.document;
            doc.open();
            doc.write(`<!DOCTYPE html><html><head><style>${css}</style></head><body>${html}<script>${js}<\/script></body></html>`);
            doc.close();
        }
    }
    updatePreview();
});
</script>
</body>
</html> 