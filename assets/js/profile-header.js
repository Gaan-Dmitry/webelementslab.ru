document.addEventListener('DOMContentLoaded', () => {
    const avatarToggle = document.getElementById('avatarToggle');
    const profileMenu = document.getElementById('profileMenu');
    const profileWrapper = document.getElementById('profileWrapper');

    if (!avatarToggle || !profileMenu || !profileWrapper) return;

    let menuOpen = false;

    function toggleMenu(forceClose = false) {
        if (forceClose || menuOpen) {
            profileMenu.classList.remove('show');
            menuOpen = false;
        } else {
            profileMenu.classList.add('show');
            menuOpen = true;
        }
    }

    avatarToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleMenu();
    });

    // Закрытие при клике вне
    document.addEventListener('click', (e) => {
        if (!profileWrapper.contains(e.target)) {
            toggleMenu(true);
        }
    });

    // Закрытие по Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            toggleMenu(true);
        }
    });

    // Закрытие, если мышка ушла с меню и аватарки
    let hoverTimeout;

    profileWrapper.addEventListener('mouseleave', () => {
        hoverTimeout = setTimeout(() => {
            toggleMenu(true);
        }, 400);
    });

    profileWrapper.addEventListener('mouseenter', () => {
        clearTimeout(hoverTimeout);
    });

    // Поиск - разворачивается из иконки лупы
    const searchToggle = document.getElementById('searchToggle');
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchInput');

    if (searchToggle && searchForm && searchInput) {
        let searchOpen = false;

        searchToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            searchOpen = !searchOpen;
            if (searchOpen) {
                searchForm.classList.add('active');
                setTimeout(() => searchInput.focus(), 100);
            } else {
                searchForm.classList.remove('active');
            }
        });

        // Закрытие поиска при клике вне
        document.addEventListener('click', (e) => {
            if (searchOpen && !searchForm.contains(e.target) && e.target !== searchToggle) {
                searchForm.classList.remove('active');
                searchOpen = false;
            }
        });

        // Закрытие поиска по Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && searchOpen) {
                searchForm.classList.remove('active');
                searchOpen = false;
            }
        });
    }
});