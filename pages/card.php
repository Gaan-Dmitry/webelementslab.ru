<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

$id = $_GET['id'] ?? 1;

$stmt = $pdo->prepare("SELECT s.*, u.username, u.id as user_id FROM snippets s JOIN users u ON s.user_id = u.id WHERE s.id = ?");
$stmt->execute([$id]);
$snippet = $stmt->fetch();

if (!$snippet) {
    die('Сниппет не найден');
}

$is_favorite = false;
if (!empty($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
    $snippet_id = $snippet['id'];

    $stmt = $pdo->prepare("SELECT 1 FROM favorites WHERE user_id = ? AND snippet_id = ?");
    $stmt->execute([$user_id, $snippet_id]);
    $is_favorite = $stmt->fetch() ? true : false;
}

$tags = explode(',', $snippet['tag'] ?? '');

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/assets/img/favicon.ico" >
    <meta name="theme-color" content="#000000" >
    <meta name="robots" content="index, follow">
    <meta name="description" content="<?= htmlspecialchars($snippet['description'] ?? $snippet['name']) ?> — сниппет от пользователя <?= htmlspecialchars($snippet['username']) ?>. Теги: <?= htmlspecialchars($snippet['tag']) ?>. WebElementsLab">
    <meta name="keywords" content="<?= htmlspecialchars($snippet['tag']) ?>, HTML, CSS, JS, сниппет, WebElementsLab, код, пример, компонент">
    <link rel="apple-touch-icon" href="/assets/img/logo192.png" >
    <title><?= htmlspecialchars($snippet['name']) ?> — сниппет от <?= htmlspecialchars($snippet['username']) ?> | WebElementsLab</title>
    <link rel="stylesheet" href="/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
    <meta property="og:title" content="<?= htmlspecialchars($snippet['name']) ?> — сниппет для веба">
    <meta property="og:description" content="<?= htmlspecialchars($snippet['description'] ?? $snippet['name']) ?>">
    <meta property="og:image" content="https://webelementslab.ru/assets/img/logo512.png">
    <meta property="og:url" content="https://webelementslab.ru/card.php?id=<?= $snippet['id'] ?>">
    <meta property="og:type" content="article">
    <!-- Prism CSS -->
    <link id="prism-theme" href="https://cdn.jsdelivr.net/npm/prismjs/themes/prism-tomorrow.css" rel="stylesheet" />

    <!-- Prism JS -->
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/prism.min.js" defer></script>
    <!-- Языки -->
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-css.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-javascript.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-markup.min.js" defer></script>

</head>
<body>
<?php require_once __DIR__ . '/../templates/header.php'; ?>
<div class="wrapper">
<main class="block-main">
    <div class="d-flex gap1 card-page f-d-column">
        <div class="d-flex j-c-space-between">
        <h1 class="card-title"><?= htmlspecialchars($snippet['name']) ?></h1>
        <h2><a href="/pages/profile.php?id=<?= urlencode($snippet['user_id']) ?>" class="author-link"><?= htmlspecialchars($snippet['username']) ?></a></h2>
        </div>
        <div class="block-tag">
            <?php foreach ($tags as $tag): ?>
                <a href="/pages/tag.php?tag=<?= urlencode(trim($tag)) ?>" class="tag-pill" data-full="<?= htmlspecialchars(trim($tag)) ?>"><?= htmlspecialchars(trim($tag)) ?></a>
            <?php endforeach; ?>
        </div>
        <div class="f-d-row d-flex gap1">
            <!-- Preview -->
            <div class="left-card-page d-flex gap1 f-d-column">
                <div class="block-element d-flex j-c-center a-i-center">
                    <iframe id="snippet-frame" sandbox="allow-scripts" referrerpolicy="no-referrer" style="width:100%;min-height:200px;border:none;"></iframe>
                </div>
                <div class="tags d-flex gap05 wrap f-d-column gap1">
                <?php if (isset($_SESSION['username'])): ?>
                    <button class="btn-card j-c-center d-flex<?= $is_favorite ? ' fav' : '' ?>" id="fav-btn" data-id="<?= $snippet['id'] ?>">
                        <?= $is_favorite ? '💖 В избранном' : '🤍 В избранное' ?>
                    </button>

                    <?php if ($_SESSION['id'] === (int)$snippet['user_id']): ?>
                        <button class="btn-card j-c-center d-flex" onclick="location.href='/pages/edit_or_create_card.php?id=<?= $snippet['id'] ?>'">
                            ✏️ Редактировать
                        </button>
                    <?php endif; ?>
                <?php endif; ?>

                </div>

            </div>

            <!-- Code Tabs -->
            <div class="block-code">
                <div class="tabs d-flex">
                <button class="tab-btn active" data-tab="html">HTML</button>
                <button class="tab-btn" data-tab="css">CSS</button>
                <button class="tab-btn" data-tab="js">JS</button>
                </div>

                <div class="tab-content active" id="html">
                    <button class="copy-btn">Копировать</button>
                    <pre><code class="language-html"><?= htmlspecialchars($snippet['html'] ?? '') ?></code></pre>
                </div>
                <div class="tab-content" id="css">
                    <button class="copy-btn">Копировать</button>
                    <pre><code class="language-css"><?= htmlspecialchars($snippet['css'] ?? '') ?></code></pre>
                </div>
                <div class="tab-content" id="js">
                    <button class="copy-btn">Копировать</button>
                    <pre><code class="language-js"><?= htmlspecialchars($snippet['js'] ?? '') ?></code></pre>
                </div>
            </div>   
        </div>
        <div>
            <?php if (!empty($snippet['description'])): ?>
                <div class="snippet-description" style="margin-bottom:1.2em;font-size:1.1em;color:#e0e0e0;line-height:1.5;word-break:break-word;">
                    <?= nl2br(htmlspecialchars($snippet['description'])) ?>
                </div>
            <?php endif; ?>
            <p>
                Как правильно подключать CSS и JS? Подробнее — в <a href="/pages/guide.php" target="_blank">гайде по подключению стилей и скриптов</a>.<br>
                В инструкции: примеры подключения через <code>&lt;link&gt;</code> и <code>&lt;script&gt;</code>, а также варианты вставки кода прямо в HTML-файл.
            </p>
        </div>
    </div>
</main>
</div>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>
<script>
window.snippetPreviewData = {
    html: <?= json_encode($snippet['html']) ?>,
    css: <?= json_encode($snippet['css']) ?>,
    js: <?= json_encode($snippet['js']) ?>
};
</script>
<script src="/assets/js/snippet.js?v=<?= filemtime(__DIR__ . '/../assets/js/snippet.js') ?>" defer></script>
</body>
</html>
