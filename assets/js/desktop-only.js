document.addEventListener('DOMContentLoaded', () => {
const isMobileUserAgent =
/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
navigator.userAgent
);

if (!isMobileUserAgent) {
return;
}

// Не показываем заглушку на странице оформления премиума
const currentPath = window.location.pathname;
if (currentPath.includes('/pages/premium.php')) {
return;
}

// Проверяем, есть ли у пользователя премиум (данные из PHP)
const hasPremium = window.userHasPremium === true;

if (hasPremium) {
return; // Если премиум есть, не показываем заглушку
}

const blocker = document.createElement('div');
blocker.setAttribute('aria-live', 'assertive');
blocker.innerHTML = `
<div class="desktop-only__card">
<h1>Мобильная версия доступна только премиум подписчикам</h1>
<p>Получите доступ к мобильной версии сайта и другим эксклюзивным функциям с премиум подпиской.</p>
<a href="/pages/premium.php" class="premium-link-btn">Оформить премиум подписку</a>
</div>
`;

const style = document.createElement('style');
style.textContent = `
.desktop-only-overlay {
position: fixed;
inset: 0;
z-index: 2147483647;
display: flex;
align-items: center;
justify-content: center;
padding: 1.2rem;
background: #0b0b0d;
color: #f2f5f9;
text-align: center;
font-family: 'Segoe UI', sans-serif;
}
.desktop-only__card {
max-width: 36rem;
background: #181a1f;
border: 1px solid #3f5062;
border-radius: 12px;
padding: 1.4rem 1.2rem;
box-shadow: 0 10px 35px rgba(0, 0, 0, 0.35);
}
.desktop-only__card h1 {
margin: 0 0 0.8rem 0;
font-size: 1.35rem;
color: #9bc3ea;
}
.desktop-only__card p {
margin: 0.55rem 0;
line-height: 1.45;
}
.premium-link-btn {
display: inline-block;
margin-top: 1rem;
padding: 0.8rem 1.5rem;
background: #77a8d9;
color: #1d1a18;
text-decoration: none;
border-radius: 8px;
font-weight: 600;
transition: all 0.3s ease;
}
.premium-link-btn:hover {
background: #9bc3ea;
transform: translateY(-2px);
box-shadow: 0 4px 12px rgba(119, 168, 217, 0.4);
}
`;

blocker.className = 'desktop-only-overlay';
document.head.appendChild(style);
document.body.innerHTML = '';
document.body.appendChild(blocker);
document.body.style.overflow = 'hidden';
});
