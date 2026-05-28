<?php
    $isPremium = false;
    if (isset($_SESSION['id'])) {
        require_once __DIR__ . '/../includes/db.php';
        $stmt = $pdo->prepare('SELECT avatar FROM user_profiles WHERE user_id = ? LIMIT 1');
        $stmt->execute([$_SESSION['id']]);
        $user_profile = $stmt->fetch();
        $avatar = !empty($user_profile['avatar']) ? 
        htmlspecialchars($user_profile['avatar']) : '/uploads/default-avatar.png';
        $avatarFile = $_SERVER['DOCUMENT_ROOT'] . $avatar;
        $avatarTime = file_exists($avatarFile) ? filemtime($avatarFile) : time();
        $avatar .= '?t=' . $avatarTime;
        
        // Проверяем премиум статус
        $stmtRole = $pdo->prepare('SELECT role FROM users WHERE id = ? LIMIT 1');
        $stmtRole->execute([$_SESSION['id']]);
        $userData = $stmtRole->fetch();
        $isPremium = ($userData && $userData['role'] === 'premium');
    }
?>

<script src="/assets/js/theme-toggle.js?v=<?= filemtime(__DIR__ . '/../assets/js/theme-toggle.js') ?>" data-is-premium="<?= $isPremium ? 'true' : 'false' ?>"></script>
<header>
        <div class="nav-left">
                <a href="/">
                        <img src="/assets/img/logo.svg" class="logo-header" alt="logo">
                </a>
                <button class="mobile-menu-toggle" aria-label="Меню" aria-expanded="false">
                        <span></span>
                        <span></span>
                        <span></span>
                </button>
                <nav class="main-nav">
                        <a href="/">Главная</a>
                        <a href="/pages/random_snippet.php">Случайный</a>
                        <a href="/pages/edit_or_create_card.php">Создать</a>
                </nav>
        </div>
        <div class="nav-right">
                <button id="theme-toggle-btn" class="theme-toggle-btn" aria-label="Переключить тему">
                        <svg class="sun-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                </button>
                <form action="/" method="get" class="search-form search-form-always-active" id="searchForm">
                        <label class="visually-hidden" for="searchInput">Поиск сниппетов</label>
                        <svg class="search-icon-svg-inline" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                        </svg>
                        <input
                                type="search"
                                id="searchInput"
                                name="q"
                                placeholder="Поиск по названию сниппета"
                                value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                                autocomplete="off"
                        >
                        <button class="search-submit-btn" type="submit">Найти</button>
                </form>
                <?php if (isset($_SESSION['username'])): ?>
                        <div class="profile-menu-wrapper" id="profileWrapper">
                                <span class="username-label"><?= htmlspecialchars($_SESSION['username']) ?></span>
                                <img src="<?= $avatar ?>" alt="avatar" class="avatar" id="avatarToggle">
                                <div class="dropdown-menu" id="profileMenu">
                                        <a href="/pages/profile.php">Профиль</a>
                                        <a href="/pages/settings.php">Настройки</a>
                                        <a href="/pages/edit_or_create_card.php">Создать</a>
                                        <a href="/pages/favorites.php">Избранное</a>
                                        <a href="/pages/logout.php" class="red-link">Выход</a>
                                </div>
                        </div>
                <?php else: ?>
                        <a href="/pages/login.php" class="login-link mobile-nav-link">Вход</a>
                        <a class="reg-btn anim-hover-box-shadow mobile-nav-link" href="/pages/register.php">Регистрация</a>
                <?php endif; ?>
        </div>
        <div class="mobile-nav-overlay" id="mobileNavOverlay">
                <nav class="mobile-nav-menu">
                        <a href="/">Главная</a>
                        <a href="/pages/random_snippet.php">Случайный</a>
                        <a href="/pages/edit_or_create_card.php">Создать</a>
                        <?php if (isset($_SESSION['username'])): ?>
                                <a href="/pages/profile.php">Профиль</a>
                                <a href="/pages/settings.php">Настройки</a>
                                <a href="/pages/favorites.php">Избранное</a>
                                <a href="/pages/logout.php" class="red-link">Выход</a>
                        <?php else: ?>
                                <a href="/pages/login.php">Вход</a>
                                <a href="/pages/register.php">Регистрация</a>
                        <?php endif; ?>
                </nav>
        </div>
</header>
<script src="/assets/js/profile-header.js?v=<?= filemtime(__DIR__ . '/../assets/js/profile-header.js') ?>"></script>
<script src="/assets/js/mobile-menu.js?v=<?= filemtime(__DIR__ . '/../assets/js/mobile-menu.js') ?>"></script>
