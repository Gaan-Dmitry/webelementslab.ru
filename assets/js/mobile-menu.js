// Мобильное меню (гамбургер)
document.addEventListener('DOMContentLoaded', () => {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileNavOverlay = document.getElementById('mobileNavOverlay');
    const mainNav = document.querySelector('.main-nav');
    
    if (!mobileMenuToggle || !mobileNavOverlay) return;
    
    // Открытие/закрытие меню
    mobileMenuToggle.addEventListener('click', () => {
        const isExpanded = mobileMenuToggle.getAttribute('aria-expanded') === 'true';
        mobileMenuToggle.setAttribute('aria-expanded', !isExpanded);
        mobileNavOverlay.classList.toggle('active');
        document.body.style.overflow = !isExpanded ? 'hidden' : '';
    });
    
    // Закрытие меню при клике на ссылку
    const mobileNavLinks = mobileNavOverlay.querySelectorAll('a');
    mobileNavLinks.forEach(link => {
        link.addEventListener('click', () => {
            mobileMenuToggle.setAttribute('aria-expanded', 'false');
            mobileNavOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    });
    
    // Закрытие меню при клике вне области
    document.addEventListener('click', (e) => {
        if (!mobileNavOverlay.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
            mobileMenuToggle.setAttribute('aria-expanded', 'false');
            mobileNavOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
});
