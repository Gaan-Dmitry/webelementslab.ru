<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

// Получаем тег из URL
$tag = trim($_GET['tag'] ?? '');

if ($tag === '') {
    header('Location: /');
    exit;
}

// Заголовок для страницы
$pageTitle = "Сниппеты с тегом: " . htmlspecialchars($tag);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/assets/img/favicon.ico">
    <meta name="theme-color" content="#000000">
    <meta name="robots" content="index, follow">
    <meta name="description" content="Все сниппеты с тегом «<?= htmlspecialchars($tag) ?>» на WebElementsLab">
    <meta name="keywords" content="<?= htmlspecialchars($tag) ?>, HTML, CSS, JS, сниппет, WebElementsLab, код">
    <link rel="apple-touch-icon" href="/assets/img/logo192.png">
    <title><?= $pageTitle ?> | WebElementsLab</title>
    <link rel="stylesheet" href="/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
    <meta property="og:title" content="<?= $pageTitle ?>">
    <meta property="og:description" content="Подборка сниппетов с тегом «<?= htmlspecialchars($tag) ?>»">
    <meta property="og:image" content="https://webelementslab.ru/assets/img/logo512.png">
    <meta property="og:url" content="https://webelementslab.ru/pages/tag.php?tag=<?= urlencode($tag) ?>">
    <meta property="og:type" content="website">
</head>
<body>
    <?php require_once __DIR__ . '/../templates/header.php'; ?>
    <div class="wrapper">
        <main>
            <section class="tag-header">
                <h1>Сниппеты с тегом: <span class="tag-highlight"><?= htmlspecialchars($tag) ?></span></h1>
                <a href="/" class="back-link">← Все сниппеты</a>
            </section>
            <!-- Сюда будут грузиться сниппеты через AJAX -->
            <div id="snippets-list"></div>
        </main>
    </div>
    <?php require_once __DIR__ . '/../templates/footer.php'; ?>
    <script>
        window.currentTag = <?= json_encode($tag) ?>;
    </script>
    <script src="/assets/js/main.js" defer></script>
</body>
</html>
