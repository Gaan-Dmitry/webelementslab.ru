/**
 * Theme Toggle Logic
 * Cycles between Dark, Darker, and Light themes.
 */
(function() {
    const themes = ['dark', 'darker', 'light'];
    const prismThemes = {
        'dark': 'https://cdn.jsdelivr.net/npm/prismjs/themes/prism-tomorrow.css',
        'darker': 'https://cdn.jsdelivr.net/npm/prismjs/themes/prism-tomorrow.css',
        'light': 'https://cdn.jsdelivr.net/npm/prismjs/themes/prism.css'
    };

    function applyTheme(theme) {
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
                'dark': '🌙 Темная',
                'darker': '🌑 Еще темнее',
                'light': '☀️ Светлая'
            };
            toggleBtn.textContent = icons[theme] || '🌓 Тема';
        }
    }

    // Initialize theme
    const savedTheme = localStorage.getItem('theme') || 'dark';
    applyTheme(savedTheme);

    window.toggleTheme = function() {
        const currentTheme = localStorage.getItem('theme') || 'dark';
        let currentIndex = themes.indexOf(currentTheme);
        let nextIndex = (currentIndex + 1) % themes.length;
        applyTheme(themes[nextIndex]);
    };

    document.addEventListener('DOMContentLoaded', () => {
        // Ensure the correct text is on the button after DOM load
        const savedTheme = localStorage.getItem('theme') || 'dark';
        applyTheme(savedTheme);
    });
})();
