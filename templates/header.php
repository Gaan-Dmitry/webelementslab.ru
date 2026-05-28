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
		</nav>
	</div>
	<div class="nav-right">
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
			<a href="/pages/login.php">Вход</a>
			<a class="reg-btn anim-hover-box-shadow" href="/pages/register.php">Регистрация</a>
		<?php endif; ?>
	</div>
</header>
<script src="/assets/js/profile-header.js?v=<?= filemtime(__DIR__ . '/../assets/js/profile-header.js') ?>"></script>
