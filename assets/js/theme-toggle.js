document.addEventListener('DOMContentLoaded', () => {
	const toggleButton = document.getElementById('theme-toggle-btn');
	if (!toggleButton) {
		return;
	}

	const storageKey = 'wel_theme';
	const root = document.documentElement;

	const applyTheme = theme => {
		const isLight = theme === 'light';
		root.setAttribute('data-theme', isLight ? 'light' : 'dark');
		toggleButton.textContent = isLight ? '🌙 Тёмная тема' : '☀️ Светлая тема';
		toggleButton.setAttribute('aria-pressed', isLight ? 'true' : 'false');
	};

	const savedTheme = localStorage.getItem(storageKey);
	if (savedTheme === 'light' || savedTheme === 'dark') {
		applyTheme(savedTheme);
	}

	toggleButton.addEventListener('click', () => {
		const currentTheme = root.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
		const nextTheme = currentTheme === 'light' ? 'dark' : 'light';
		applyTheme(nextTheme);
		localStorage.setItem(storageKey, nextTheme);
	});
});
