document.addEventListener('DOMContentLoaded', () => {
const isMobileUserAgent =
/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
navigator.userAgent
);

if (!isMobileUserAgent) {
return;
}

// Не показываем заглушку на страницах оформления премиума, логина и регистрации
const currentPath = window.location.pathname;
if (
  currentPath.includes('/pages/premium.php') ||
  currentPath.includes('/pages/login.php') ||
  currentPath.includes('/pages/register.php')
) {
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
<div class="login-block">
<p>Уже есть аккаунт?</p>
<a href="/pages/login.php" class="login-link-btn">Войти</a>
</div>
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
padding: 0.4rem 1.2rem;
background: transparent;
color: #d1d7e0;
text-decoration: none;
border: 1px solid white;
border-radius: 0.5rem;
font-weight: 600;
transition: all 0.3s ease;
}
.premium-link-btn:hover {
background: transparent;
box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
transform: translateY(-1px);
}
.login-block {
margin-top: 1.2rem;
padding-top: 1rem;
}
.login-block p {
margin: 0 0 0.6rem 0;
font-size: 0.9rem;
color: #adb5bd;
}
.login-link-btn {
display: inline-block;
padding: 0.6rem 1.2rem;
background: transparent;
color: #9bc3ea;
text-decoration: none;
border: none;
border-radius: 8px;
font-weight: 500;
transition: all 0.3s ease;
}
.login-link-btn:hover {
text-shadow: 0 0 4px #77a8d9, 0 0 10px #9bc3ea;
}
`;

blocker.className = 'desktop-only-overlay';
document.head.appendChild(style);
document.body.innerHTML = '';
document.body.appendChild(blocker);
document.body.style.overflow = 'hidden';
});
