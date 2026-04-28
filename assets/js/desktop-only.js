document.addEventListener('DOMContentLoaded', () => {
	const isMobileUserAgent =
		/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
			navigator.userAgent
		);

	if (!isMobileUserAgent) {
		return;
	}

	const blocker = document.createElement('div');
	blocker.setAttribute('aria-live', 'assertive');
	blocker.innerHTML = `
		<div class="desktop-only__card">
			<h1>Сайт доступен только с ПК</h1>
			<p>Пожалуйста, откройте сайт с компьютера.</p>
			<p>Если вы на телефоне — включите режим <b>«Версия для ПК»</b> в браузере и перезагрузите страницу.</p>
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
	`;

	blocker.className = 'desktop-only-overlay';
	document.head.appendChild(style);
	document.body.innerHTML = '';
	document.body.appendChild(blocker);
	document.body.style.overflow = 'hidden';
});
