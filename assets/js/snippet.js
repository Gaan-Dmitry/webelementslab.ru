document.addEventListener('DOMContentLoaded', function () {
	// Вкладки
	document.querySelectorAll('.tab-btn').forEach(btn => {
		btn.addEventListener('click', () => {
			document
				.querySelectorAll('.tab-btn')
				.forEach(b => b.classList.remove('active'));
			btn.classList.add('active');
			const id = btn.dataset.tab;
			document.querySelectorAll('.tab-content').forEach(content => {
				content.classList.toggle('active', content.id === id);
			});
		});
	});

	// Копировать
	document.querySelectorAll('.copy-btn').forEach(btn => {
		btn.addEventListener('click', () => {
			const codeBlock = btn.closest('.tab-content').querySelector('code');
			const text = codeBlock.textContent;
			navigator.clipboard.writeText(text).then(() => {
				btn.textContent = '✓ Скопировано';
				setTimeout(() => (btn.textContent = 'Копировать'), 1500);
			});
		});
	});

	// Кнопка избранного
	const favBtn = document.getElementById('fav-btn');
	if (favBtn) {
		favBtn.addEventListener('click', function () {
			const snippetId = favBtn.dataset.id;
			favBtn.disabled = true;
			fetch('/handlers/toggle_fav.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ id: snippetId }),
			})
				.then(response => response.json())
				.then(data => {
					if (data.status === 'added') {
						favBtn.classList.add('fav');
						favBtn.textContent = '💖 В избранном';
					} else if (data.status === 'removed') {
						favBtn.classList.remove('fav');
						favBtn.textContent = '🤍 В избранное';
					} else if (data.error) {
						alert('Ошибка: ' + data.error);
					}
				})
				.catch(() => {
					alert('Ошибка соединения с сервером');
				})
				.finally(() => {
					favBtn.disabled = false;
				});
		});
	}

	// Удаление из избранного на странице избранного
	document.querySelectorAll('.btn-remove-fav').forEach(btn => {
		btn.addEventListener('click', () => {
			const id = btn.dataset.id;
			btn.disabled = true;
			fetch('/handlers/toggle_fav.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ id })
			})
				.then(r => r.json())
				.then(data => {
					if (data.status === 'removed') {
						const card = btn.closest('.snippet-card');
						if (card) card.remove();
						// Если карточек не осталось — показать пустое состояние без перезагрузки
						const list = document.querySelector('.favorites-list');
						if (list && list.children.length === 0) {
							const empty = document.createElement('div');
							empty.className = 'empty-favorites';
							empty.innerHTML = '<h2 class="empty-favorites__title">Пусто</h2><p class="empty-favorites__desc">Добавляйте сниппеты в избранное на странице карточки, чтобы видеть их здесь.</p><br><a class="reg-btn anim-hover-box-shadow" href="/">Перейти к сниппетам</a>';
							list.replaceWith(empty);
						}
					} else if (data.error) {
						alert('Ошибка: ' + data.error);
					}
				})
				.catch(() => alert('Ошибка соединения с сервером'))
				.finally(() => (btn.disabled = false));
		});
	});

	// Iframe для превью сниппета
	const iframe = document.getElementById('snippet-frame');
	if (iframe && window.snippetPreviewData) {
		const doc = iframe.contentDocument || iframe.contentWindow.document;
		doc.open();
		doc.write(`
			<!DOCTYPE html>
			<html>
			<head>
				<style>
					html, body {
						height: 100%;
						margin: 0;
						padding: 0;
					}
					body {
						min-height: 100vh;
						display: flex;
						justify-content: center;
						align-items: center;
					}
				</style>
				<style>${window.snippetPreviewData.css}</style>
			</head>
			<body>
				${window.snippetPreviewData.html}
				<script>${window.snippetPreviewData.js}<\/script>
			</body>
			</html>
		`);
		doc.close();
	}
});
