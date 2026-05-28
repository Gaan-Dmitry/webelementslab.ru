/**
 * Theme Toggle Logic
 * Cycles between Colored, Dark, and Light themes.
 */
(function() {
    const themes = ['colored', 'dark', 'light'];
    const prismThemes = {
        'colored': 'https://cdn.jsdelivr.net/npm/prismjs/themes/prism-tomorrow.css',
        'dark': 'https://cdn.jsdelivr.net/npm/prismjs/themes/prism-tomorrow.css',
        'light': 'https://cdn.jsdelivr.net/npm/prismjs/themes/prism.css'
    };

    function applyTheme(theme) {
        // Fallback for old saved themes
        if (theme === 'darker') theme = 'dark';
        if (!themes.includes(theme)) theme = 'colored';

        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);

        // Update Prism theme if link exists
        const prismLink = document.getElementById('prism-theme');
        if (prismLink && prismThemes[theme]) {
            prismLink.href = prismThemes[theme];
        }

        // Update toggle button text if exists
        const toggleBtn = document.getElementById('theme-toggle-btn');
        if (toggleBtn) {
            const icons = {
                'colored': '🌈 Цветная',
                'dark': '🌑 Темная',
                'light': '☀️ Светлая'
            };
            toggleBtn.textContent = icons[theme] || '🌓 Тема';
        }
    }

    // Initialize theme
    const savedTheme = localStorage.getItem('theme') || 'colored';
    applyTheme(savedTheme);

    window.toggleTheme = function() {
        const currentTheme = localStorage.getItem('theme') || 'colored';
        let currentIndex = themes.indexOf(currentTheme);
        // Fallback if currentTheme was 'darker'
        if (currentIndex === -1) currentIndex = 0;

        let nextIndex = (currentIndex + 1) % themes.length;
        applyTheme(themes[nextIndex]);
    };

    document.addEventListener('DOMContentLoaded', () => {
        const savedTheme = localStorage.getItem('theme') || 'colored';
        applyTheme(savedTheme);
    });
})();
