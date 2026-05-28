(function () {
	const themes = ['colored', 'dark', 'light'];
	const storageKey = 'user-theme';
	const html = document.documentElement;

	const getSavedTheme = () => localStorage.getItem(storageKey) || 'colored';
	const saveTheme = theme => localStorage.setItem(storageKey, theme);

	const applyTheme = theme => {
		html.setAttribute('data-theme', theme);
		updatePrismTheme(theme);
	};

	const updatePrismTheme = theme => {
		const prismLink = document.getElementById('prism-theme');
		if (!prismLink) return;

		const isDark = theme === 'dark' || theme === 'colored';
		prismLink.href = isDark
			? 'https://cdn.jsdelivr.net/npm/prismjs/themes/prism-tomorrow.css'
			: 'https://cdn.jsdelivr.net/npm/prismjs/themes/prism.css';
	};

	const initTheme = () => {
		const savedTheme = getSavedTheme();
		applyTheme(savedTheme);
	};

	initTheme();

	window.addEventListener('DOMContentLoaded', () => {
		const themeToggle = document.getElementById('theme-toggle-btn');
		if (themeToggle) {
			themeToggle.addEventListener('click', () => {
				const currentTheme = getSavedTheme();
				const nextIndex = (themes.indexOf(currentTheme) + 1) % themes.length;
				const nextTheme = themes[nextIndex];

				applyTheme(nextTheme);
				saveTheme(nextTheme);
			});
		}
	});
})();
