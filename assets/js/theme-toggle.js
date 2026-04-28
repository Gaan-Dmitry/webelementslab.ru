document.addEventListener('DOMContentLoaded', () => {
	const toggleButton = document.getElementById('theme-toggle-btn');
	if (!toggleButton) {
		return;
	}

	const storageKey = 'wel_theme';
	const root = document.documentElement;

	const applyTheme = theme => {
		const isDarker = theme === 'darker';
		root.setAttribute('data-theme', isDarker ? 'darker' : 'dark');
		toggleButton.textContent = isDarker ? '🌘 Тёмная тема' : '🌑 Ещё темнее';
		toggleButton.setAttribute('aria-pressed', isDarker ? 'true' : 'false');
	};

	const savedTheme = localStorage.getItem(storageKey);
	if (savedTheme === 'darker' || savedTheme === 'dark') {
		applyTheme(savedTheme);
	} else if (savedTheme === 'light') {
		applyTheme('dark');
	}

	toggleButton.addEventListener('click', () => {
		const currentTheme = root.getAttribute('data-theme') === 'darker' ? 'darker' : 'dark';
		const nextTheme = currentTheme === 'darker' ? 'dark' : 'darker';
		applyTheme(nextTheme);
		localStorage.setItem(storageKey, nextTheme);
	});
});
