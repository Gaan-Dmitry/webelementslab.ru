<footer class="footer">
<div class="footer-col left">
<p>&copy; <?= date('Y'); ?> Все права защищены.</p>
</div>
<div class="footer-col center">
<p>Данный сайт был разработан Анастасией Леоненко</p>
</div>
<div class="footer-col right">
<a href="https://github.com/4gdv5fg1qq">GitHub</a>
<a href="/pages/privacy.php">Политика конфиденциальности</a>
</div>

</footer>
<?php
// Передаём статус премиума в JavaScript для всех страниц
$hasPremiumGlobal = false;
if (isset($_SESSION['id'])) {
    require_once __DIR__ . '/../includes/db.php';
    $stmt = $pdo->prepare('SELECT is_premium FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['id']]);
    $user = $stmt->fetch();
    if ($user && !empty($user['is_premium'])) {
        $hasPremiumGlobal = true;
    }
}
?>
<script>
    window.userHasPremium = <?= $hasPremiumGlobal ? 'true' : 'false' ?>;
</script>
<script src="/assets/js/desktop-only.js" defer></script>
