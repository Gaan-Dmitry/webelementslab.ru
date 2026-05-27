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
		<button class="search-btn" id="searchToggle" type="button" aria-label="Открыть поиск">
			🔍
		</button>
		<div class="search-dropdown" id="searchDropdown">
			<form action="/" method="get">
				<input
					type="search"
					id="searchInput"
					name="q"
					placeholder="Поиск по названию сниппета"
					value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
					autocomplete="off"
				>
			</form>
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
<script src="/assets/js/profile-header.js"></script>
<script>
	document.addEventListener('DOMContentLoaded', function () {
		const toggle = document.getElementById('searchToggle');
		const dropdown = document.getElementById('searchDropdown');
		const input = document.getElementById('searchInput');

		if (!toggle || !dropdown || !input) {
			return;
		}

		const hasQuery = input.value.trim() !== '';
		if (hasQuery) {
			dropdown.classList.add('is-open');
		}

		toggle.addEventListener('click', function () {
			dropdown.classList.toggle('is-open');
			if (dropdown.classList.contains('is-open')) {
				input.focus();
				input.select();
			}
		});

		document.addEventListener('click', function (event) {
			if (!dropdown.contains(event.target) && event.target !== toggle) {
				dropdown.classList.remove('is-open');
			}
		});
	});
</script>
