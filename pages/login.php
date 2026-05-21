<?php
session_start();
// Проверка авторизации - если уже залогинен, редирект на профиль
if (!empty($_SESSION['username'])) {
    header("Location: /pages/profile.php"); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="/assets/img/favicon.ico" />
    <meta name="theme-color" content="#000000" />
    <meta name="description" content="Авторизация | WebElementsLab" />
    <link rel="apple-touch-icon" href="/assets/img/logo192.png" />
    <title>Авторизация | WebElementsLab</title>
    <link rel="stylesheet" href="/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>" />
</head>
<body>
<div class="wrapper">
<main>
<div class="authentication">
    <div class="auth-text-up">
        <a href="/">
            <img src="/assets/img/logo.svg" alt="logo" class="auth-logo" />
        </a>
        <h1>Войти</h1>
    </div>
    <div class="auth-form">
        <form id="login-form" method="post" novalidate>
            <div>
                <label for="login_field">Почта:</label>
                <input
                    type="email"
                    name="login"
                    id="login_field"
                    autocomplete="email"
                    class="form-control"
                    placeholder="Ваша почта"
                />
                <span class="error-message" id="login-error">&nbsp;</span>
            </div>
            <div class="position-relative">
                <label for="password">Пароль:</label>
                <input
                    type="password"
                    name="password"
                    id="password"
                    autocomplete="current-password"
                    class="form-control"
                    placeholder="Введите ваш пароль"
                />
                <a
                    class="label-link link-form position-absolute top-0 right-0"
                    href="/pages/password_reset.php"
                    >Забыли пароль?</a
                >
                <span class="error-message" id="password-error">&nbsp;</span>
            </div>
            <div class="button-with-spinner">
                <input type="submit" value="Войти" id="submit-button" />
                <div class="spinner" id="login-spinner"></div>
            </div>
        </form>
    </div>
    <div class="auth-footer">
        <div class="auth-down">
            <p>Нет аккаунта?</p>
            <a class="link-form" href="/pages/register.php">Создать аккаунт</a>
        </div>
    </div>
</div>

<script>
  document.getElementById('login-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const login = document.getElementById('login_field');
    const password = document.getElementById('password');
    const loginError = document.getElementById('login-error');
    const passwordError = document.getElementById('password-error');
    const submitButton = document.getElementById('submit-button');
    const spinner = document.getElementById('login-spinner');

    // Сброс ошибок
    loginError.textContent = '\u00A0';
    passwordError.textContent = '\u00A0';
    login.classList.remove('invalid');
    password.classList.remove('invalid');

    let hasError = false;

    if (login.value.trim() === '') {
      login.classList.add('invalid');
      loginError.textContent = 'Укажите вашу почту';
      hasError = true;
    }
    if (password.value.trim() === '') {
      password.classList.add('invalid');
      passwordError.textContent = 'Введите пароль';
      hasError = true;
    }

    if (hasError) return;

    const formData = new FormData(this);
    submitButton.disabled = true;
    spinner.style.display = 'block';

    let result = null;
    let countdownTimer = null;

    try {
      const response = await fetch('/handlers/login_handler.php', {
        method: 'POST',
        body: formData,
        credentials: 'include'
      });

      result = await response.json();

      if (result.success) {
        window.location.href = document.referrer || '/';
      } else if (result.blocked) {
        login.classList.add('invalid');

        let wait = result.wait;
        loginError.textContent = `Слишком много попыток. Повторите через ${wait} сек.`;

        countdownTimer = setInterval(() => {
          wait--;
          if (wait <= 0) {
            clearInterval(countdownTimer);
            loginError.textContent = '\u00A0';
            submitButton.disabled = false;
          } else {
            loginError.textContent = `Слишком много попыток. Повторите через ${wait} сек.`;
          }
        }, 1000);
      } else if (result.errors && result.errors.length > 0) {
        const errorText = result.errors[0].toLowerCase();

        if (errorText.includes('почт') || errorText.includes('пользовател')) {
          login.classList.add('invalid');
          loginError.textContent = result.errors[0];
        } else if (errorText.includes('парол')) {
          password.classList.add('invalid');
          passwordError.textContent = result.errors[0];
        } else {
          login.classList.add('invalid');
          loginError.textContent = result.errors[0];
        }
      }
    } catch (err) {
      console.error('Ошибка при входе:', err);
      login.classList.add('invalid');
      loginError.textContent = 'Ошибка соединения с сервером.';
    } finally {
      // В случае блокировки — кнопка останется отключенной до конца таймера
      if (!result || (!result.success && !result.blocked)) {
        submitButton.disabled = false;
        spinner.style.display = 'none';
      }

      // Если блокировка — просто прячем спиннер, кнопку не разблокируем
      if (result?.blocked) {
        spinner.style.display = 'none';
      }
    }
  });
</script>

</main>
</div>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>
</body>
</html>
