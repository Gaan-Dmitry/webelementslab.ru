<?php
session_start();

// Проверяем, есть ли у пользователя премиум
$hasPremium = false;
if (isset($_SESSION['id'])) {
    require_once __DIR__ . '/../includes/db.php';
    $stmt = $pdo->prepare('SELECT role FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['id']]);
    $user = $stmt->fetch();
    if ($user && $user['role'] === 'premium') {
        $hasPremium = true;
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
    <meta name="description" content="Оформление премиум подписки WebElementsLab">
    <title>Премиум подписка — WebElementsLab</title>
    <link rel="stylesheet" href="/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
    <link rel="stylesheet" href="/assets/css/pages/premium.css?v=<?= filemtime(__DIR__ . '/../assets/css/pages/premium.css') ?>">
    <meta property="og:title" content="Премиум подписка — WebElementsLab">
    <meta property="og:description" content="Получите доступ ко всем функциям сайта включая мобильную версию">
    <meta property="og:image" content="https://webelementslab.ru/assets/img/logo512.png">
    <meta property="og:url" content="https://webelementslab.ru/pages/premium.php">
    <meta property="og:type" content="website">
</head>
<body>
    <?php require_once __DIR__ . '/../templates/header.php'; ?>
    <div class="wrapper">
        <main>
            <section class="premium-page">
                <h1>Премиум подписка</h1>
                <p class="premium-description">Получите полный доступ ко всем функциям сайта без ограничений и рекламы</p>
                
                <div class="premium-benefits">
                    <h2>Преимущества премиум-подписки:</h2>
                    <ul>
                        <li>Безлимитный доступ ко всем сниппетам</li>
                        <li>Смена темы оформления (Светлая, Тёмная, Цветная)</li>
                        <li>Приоритетная поддержка</li>
                        <li>Эксклюзивные материалы</li>
                        <li>Отсутствие рекламы</li>
                        <li>Безлимитное сохранение и экспорт сниппетов</li>
                    </ul>
                </div>

                <div class="premium-price">
                    <span class="price-label">Цена:</span>
                    <span class="price-value">299 ₽ / месяц</span>
                </div>

                <div class="payment-methods">
                    <?php if (!$hasPremium): ?>
                    <h2>Выберите способ оплаты:</h2>
                    <div class="payment-buttons">
                        <button class="payment-btn disabled" data-method="card">Банковская карта — Скоро</button>
                        <button class="payment-btn disabled" data-method="yoomoney">ЮMoney — Скоро</button>
                        <button class="payment-btn disabled" data-method="qiwi">QIWI — Скоро</button>
                        <button class="payment-btn disabled" data-method="crypto">Криптовалюта — Скоро</button>
                    </div>
                    <div class="login-prompt">
                        <p>Уже есть аккаунт? <a href="/pages/login.php" class="login-link">Войти</a></p>
                    </div>
                    <?php else: ?>
                    <div class="premium-active-message">
                        <p>У вас уже оформлена премиум подписка!</p>
                    </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
    <?php require_once __DIR__ . '/../templates/footer.php'; ?>
    
    <script>
        // Передаём статус премиума в JavaScript
        window.userHasPremium = <?= $hasPremium ? 'true' : 'false' ?>;
    </script>
    <script src="/assets/js/premium.js?v=<?= filemtime(__DIR__ . '/../assets/js/premium.js') ?>" defer></script>
</body>
</html>
