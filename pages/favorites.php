<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (!isset($_SESSION['id'])) {
    header('Location: /pages/login.php');
    exit;
}

$user_id = $_SESSION['id'];

// Получаем избранные сниппеты с полями name и tag
$stmt = $pdo->prepare('
    SELECT s.id, s.name, s.tag
    FROM favorites f
    JOIN snippets s ON s.id = f.snippet_id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
');
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Избранное — WebElementsLab</title>
    <link rel="apple-touch-icon" href="/assets/img/logo192.png" >
    <link rel="icon" href="/assets/img/favicon.ico" >
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/pages/favorites.css">
</head>
<body>
<?php require_once __DIR__ . '/../templates/header.php'; ?>
<div class="wrapper">
    <main>
        <section class="block-main">
            <h1>Избранное</h1>
            <?php if (empty($favorites)): ?>
                <div class="empty-favorites">
                    <div class="empty-favorites__icon">💤</div>
                    <h2 class="empty-favorites__title">Пусто</h2>
                    <p class="empty-favorites__desc">Добавляйте сниппеты в избранное на странице карточки, чтобы видеть их здесь.</p>
                    <a class="reg-btn anim-hover-box-shadow" href="/">Перейти к сниппетам</a>
                </div>
            <?php else: ?>
                <div class="favorites-list">
                    <?php foreach ($favorites as $snippet): ?>
                        <div class="snippet-card">
                            <h3 class="snippet-card__title"><?= htmlspecialchars($snippet['name']) ?></h3>
                            <span class="snippet-card__badge"><?= htmlspecialchars($snippet['tag']) ?></span>
                            <div class="snippet-card__actions">
                                <a href="/pages/card.php?id=<?= $snippet['id'] ?>" class="reg-btn anim-hover-box-shadow">Открыть</a>
                                <button class="btn-remove-fav" data-id="<?= $snippet['id'] ?>" aria-label="Удалить из избранного">✕</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
</div>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>
</body>
<script src="/assets/js/snippet.js"></script>
</html>
