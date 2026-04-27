<?php
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
    }
?>

<header>
	<div class="nav-left">
		<a href="/">
			<img src="/assets/img/logo.svg" class="logo-header" alt="logo">
		</a>
		<nav class="main-nav">
			<a href="/">Главная</a>
			<a href="/pages/random_snippet.php">Случайный</a>
			<a href="/pages/edit_or_create_card.php">Создать</a>
			<a href="#">Категории</a>
			<a href="#">Коллекции</a>
		</nav>
	</div>
	<div class="nav-right">
        <button id="searchToggle" class="search-btn" aria-label="Поиск">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                <line x1="16.5" y1="16.5" x2="21" y2="21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            </button>

            <div id="searchDropdown" class="search-dropdown" style="display:none;">
            <!-- Здесь будет твоя форма поиска или поле ввода -->
            <input type="text" id="searchInput" placeholder="Искать..." autocomplete="off" />
            </div>

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
			<a href="/pages/login.php">Вход</a>
			<a class="reg-btn anim-hover-box-shadow" href="/pages/register.php">Регистрация</a>
		<?php endif; ?>
	</div>
</header>
<script src="/assets/js/search-dropdown.js"></script>
<script src="/assets/js/profile-header.js"></script>

