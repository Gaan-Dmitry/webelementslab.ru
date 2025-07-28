<?php
session_start();

// Получаем id пользователя для просмотра профиля
if (!empty($_GET['id'])) {
    $user_id = (int)$_GET['id'];
} elseif (!empty($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
} else {
    header('Location: /pages/login.php');
    exit;
}

require_once __DIR__ . '/../includes/db.php';

// Получаем профиль пользователя
$stmt = $pdo->prepare('SELECT * FROM user_profiles WHERE user_id = ? LIMIT 1');
$stmt->execute([$user_id]);
$profile = $stmt->fetch();

// Получаем основную информацию
$stmt = $pdo->prepare('SELECT username, email, role FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Если пользователь не найден, редирект на 404
if (!$user) {
    header('Location: /404.php');
    exit;
}

// Проверка: это свой профиль?
$is_own_profile = isset($_SESSION['id']) && $_SESSION['id'] == $user_id;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль пользователя — WebElementsLab</title>
    <link rel="icon" href="/assets/img/favicon.ico">
    <meta name="theme-color" content="#000000">
    <meta name="robots" content="index, follow">
    <meta name="description" content="Профиль пользователя WebElementsLab">
    <link rel="apple-touch-icon" href="/assets/img/logo192.png">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/pages/profile.css">
    <meta property="og:title" content="WebElementsLab — HTML/CSS/JS элементы">
    <meta property="og:description" content="Готовые сниппеты и UI для твоих проектов.">
    <meta property="og:image" content="https://webelementslab.ru/assets/img/logo512.png">
    <meta property="og:url" content="https://webelementslab.ru/">
    <meta property="og:type" content="website">
</head>
<body>
<?php require_once __DIR__ . '/../templates/header.php'; ?>

<div class="wrapper">
    <main>
        <section class="block-main">
            <!-- Фон -->
            <div class="profile-bg"
                 style="background-image: url('<?php
                     $bgPath = !empty($profile['bg_img']) ? htmlspecialchars($profile['bg_img']) : '/uploads/default-bg.jpg';
                     $bgFile = $_SERVER['DOCUMENT_ROOT'] . $bgPath;
                     $bgTime = file_exists($bgFile) ? filemtime($bgFile) : time();
                     echo $bgPath . '?t=' . $bgTime;
                 ?>');">
            </div>

            <!-- Аватар с ролью как дополнительным классом -->
            <div class="profile-avatar <?= htmlspecialchars($user['role'] ?? 'user') ?>">
                <div class="avatar-inner">
                    <?php
                        $avatarPath = !empty($profile['avatar']) ? htmlspecialchars($profile['avatar']) : '/uploads/default-avatar.png';
                        $avatarFile = $_SERVER['DOCUMENT_ROOT'] . $avatarPath;
                        $avatarTime = file_exists($avatarFile) ? filemtime($avatarFile) : time();
                    ?>
                    <img class="avatar" loading="lazy"
                        src="<?= $avatarPath ?>?t=<?= $avatarTime ?>"
                        alt="Аватар" />
                </div>
            </div>


            <div class="profile-info">
                <h1><?= htmlspecialchars($user['username'] ?? 'Гость') ?></h1>
                <p class="profile-email">Email: <span><?= htmlspecialchars($user['email'] ?? '') ?></span></p>
                <p class="profile-bio">О себе: <span><?= htmlspecialchars($profile['bio'] ?? '') ?></span></p>

                <div class="profile-socials">
                    <?php if (!empty($profile['vk'])): ?>
                        <a href="https://vk.com/<?= htmlspecialchars($profile['vk']) ?>" class="profile-social vk" title="VK" target="_blank" rel="noopener">
                            <!-- VK SVG -->
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21.5 7.5C21.7 6.7 21.5 6.2 20.4 6.2H18.3C17.5 6.2 17.2 6.6 17 7C16.2 8.6 14.9 10.5 14.2 10.5C13.9 10.5 14 9.7 14 9.2V7.5C14 7 13.8 6.2 12.6 6.2H10.2C9.5 6.2 9.2 6.6 9.2 7.1C9.2 7.7 10.1 7.8 10.2 9.2V10.5C10.2 11.1 10 11.2 9.7 11.2C8.9 11.2 7.6 9.3 6.8 7.7C6.6 7.3 6.3 6.9 5.5 6.9H3.6C2.5 6.9 2.3 7.4 2.5 8.2C3.6 11.7 7.1 17.8 12.1 17.8C15.2 17.8 17.1 14.7 18.2 12.2C18.5 11.5 18.7 10.9 18.9 10.2C19.1 9.5 19.7 8.2 21.5 7.5Z" fill="#2787F5"/></svg>
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($profile['tg'])): ?>
                        <a href="https://t.me/<?= htmlspecialchars($profile['tg']) ?>" class="profile-social tg" title="Telegram" target="_blank" rel="noopener">
                            <!-- TG SVG -->
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21.9 4.6C21.7 4.1 21.2 3.9 20.7 4L3.6 9.2C3.1 9.3 2.8 9.7 2.8 10.2C2.8 10.7 3.1 11.1 3.6 11.2L8.2 12.5L10.1 18.1C10.3 18.6 10.7 18.9 11.2 18.9C11.4 18.9 11.7 18.8 11.9 18.6L14.2 16.7L17.6 18.7C17.8 18.8 18 18.9 18.2 18.9C18.5 18.9 18.7 18.8 18.9 18.6C19.2 18.3 19.3 17.8 19.2 17.4L21.9 5.3C22 4.9 22 4.7 21.9 4.6ZM11.2 16.7L9.7 12.7L17.2 7.5L11.2 16.7ZM17.6 16.7L14.2 14.7L15.7 13.6L17.6 16.7Z" fill="#229ED9"/></svg>
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($profile['github'])): ?>
                        <a href="https://github.com/<?= htmlspecialchars($profile['github']) ?>" class="profile-social gh" title="GitHub" target="_blank" rel="noopener">
                            <!-- GH SVG -->
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.5 2 2 6.5 2 12C2 16.4 5.1 20 9.2 20.9C9.7 21 9.9 20.7 9.9 20.4C9.9 20.1 9.9 19.3 9.9 18.4C7 19 6.4 17.3 6.4 17.3C5.9 16.1 5.2 15.8 5.2 15.8C4.2 15.1 5.3 15.1 5.3 15.1C6.4 15.2 7 16.3 7 16.3C8 18 9.7 17.5 10.3 17.2C10.4 16.5 10.7 16 11 15.7C8.7 15.4 6.3 14.5 6.3 10.7C6.3 9.6 6.7 8.7 7.3 8C7.2 7.7 6.9 6.6 7.4 5.1C7.4 5.1 8.3 4.8 9.9 6.1C10.8 5.9 11.7 5.8 12.6 5.8C13.5 5.8 14.4 5.9 15.3 6.1C16.9 4.8 17.8 5.1 17.8 5.1C18.3 6.6 18 7.7 17.9 8C18.5 8.7 18.9 9.6 18.9 10.7C18.9 14.5 16.5 15.4 14.2 15.7C14.6 16.1 15 16.8 15 18C15 19.3 15 20.1 15 20.4C15 20.7 15.2 21 15.7 20.9C19.8 20 22.9 16.4 22.9 12C22.9 6.5 18.4 2 12 2Z" fill="#181717"/></svg>
                        </a>
                    <?php endif; ?>
                </div>

                <?php if ($is_own_profile): ?>
                    <a href="/pages/logout.php" class="reg-btn anim-hover-box-shadow">Выход</a>
                <?php endif; ?>
            </div>
        </section>

        <script>
            document.querySelectorAll('.profile-social').forEach(btn => {
                btn.addEventListener('mouseup', e => btn.blur());
                btn.addEventListener('mouseleave', e => btn.blur());
            });
        </script>
    </main>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
</body>
</html>
