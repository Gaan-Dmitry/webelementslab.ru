const buildPreviewDocument = (html = '', css = '', js = '') => {
	const safeCss = css.replace(/<\/style>/gi, '<\\/style>');
	const safeJs = js.replace(/<\/script>/gi, '<\\/script>');

	return `
		<!DOCTYPE html>
		<html lang="ru">
		<head>
			<meta charset="UTF-8" />
			<style>
				html, body {
					height: 100%;
					margin: 0;
					padding: 0;
					background: transparent;
				}
				body {
					min-height: 100vh;
					display: flex;
					justify-content: center;
					align-items: center;
				}
				${safeCss}
			</style>
		</head>
		<body>
			${html}
			<script>
				document.addEventListener('click', event => {
					if (event.target.closest('a, button')) {
						event.preventDefault();
					}
				}, true);
				document.addEventListener('submit', event => {
					event.preventDefault();
				}, true);
			<\/script>
			<script>
				${safeJs}
			<\/script>
		</body>
		</html>
	`;
};

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

	const updateFavoriteButtonState = (button, isFavorite) => {
		button.classList.toggle('fav', isFavorite);
		button.textContent = isFavorite ? '💖' : '🤍';
		button.setAttribute(
			'aria-label',
			isFavorite ? 'Убрать из избранного' : 'Добавить в избранное'
		);
	};

	const showFavoritesEmptyState = () => {
		const list = document.querySelector('.favorites-list');
		if (list && list.children.length === 0) {
			const empty = document.createElement('div');
			empty.className = 'empty-favorites';
			empty.innerHTML =
				'<h2 class="empty-favorites__title">Пусто</h2><p class="empty-favorites__desc">Добавляйте сниппеты в избранное на странице карточки, чтобы видеть их здесь.</p><br><a class="reg-btn anim-hover-box-shadow" href="/">Перейти к сниппетам</a>';
			list.replaceWith(empty);
		}
	};

	const toggleFavorite = button => {
		const snippetId = button.dataset.id;
		if (!snippetId) return;

		button.disabled = true;
		fetch('/handlers/toggle_fav.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ id: snippetId }),
		})
			.then(response => response.json())
			.then(data => {
				if (data.error === 'unauthorized') {
					window.location.href = '/pages/login.php';
					return;
				}

				if (data.status === 'added') {
					updateFavoriteButtonState(button, true);
				} else if (data.status === 'removed') {
					updateFavoriteButtonState(button, false);

					if (button.dataset.removeOnUnfav === 'true') {
						const card = button.closest('.snippet-card');
						if (card) {
							card.remove();
							showFavoritesEmptyState();
						}
					}
				} else if (data.error) {
					alert('Ошибка: ' + data.error);
				}
			})
			.catch(() => alert('Ошибка соединения с сервером'))
			.finally(() => {
				button.disabled = false;
			});
	};

	// Кнопка избранного на странице сниппета
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

	// Делегирование кликов по сердечкам на карточках
	document.addEventListener('click', event => {
		const button = event.target.closest('.snippet-card__favorite-btn');
		if (!button) {
			return;
		}

		event.preventDefault();
		if (button.disabled) {
			return;
		}
		toggleFavorite(button);
	});

	const canOpenSnippetCard = event =>
		!event.target.closest('button, a, input, textarea, select, label, .pholder');

	document.addEventListener('click', event => {
		const card = event.target.closest('.snippet-card[data-href]');
		if (!card || !canOpenSnippetCard(event)) {
			return;
		}

		window.location.href = card.dataset.href;
	});

	document.addEventListener('keydown', event => {
		if (event.key !== 'Enter' && event.key !== ' ') {
			return;
		}

		const card = event.target.closest('.snippet-card[data-href]');
		if (!card) {
			return;
		}

		event.preventDefault();
		window.location.href = card.dataset.href;
	});

	// Iframe для превью сниппета на странице карточки
	const iframe = document.getElementById('snippet-frame');
	if (iframe && window.snippetPreviewData) {
		iframe.setAttribute('sandbox', 'allow-scripts');
		iframe.setAttribute('referrerpolicy', 'no-referrer');
		iframe.srcdoc = buildPreviewDocument(
			window.snippetPreviewData.html ?? '',
			window.snippetPreviewData.css ?? '',
			window.snippetPreviewData.js ?? ''
		);

		// Contrast toggle for the main preview
		const contrastBtn = document.createElement('button');
		contrastBtn.className = 'btn-card contrast-toggle-main';
		contrastBtn.innerHTML = '🌓 Изменить фон';
		contrastBtn.style.position = 'absolute';
		contrastBtn.style.bottom = '1rem';
		contrastBtn.style.right = '1rem';
		contrastBtn.style.zIndex = '10';

		contrastBtn.onclick = () => {
			const currentBg = iframe.style.background;
			if (currentBg === 'rgb(255, 255, 255)' || currentBg === 'white') {
				iframe.style.background = 'var(--preview-bg)';
			} else {
				iframe.style.background = 'white';
			}
		};

		if (iframe.parentElement) {
			iframe.parentElement.style.position = 'relative';
			iframe.parentElement.appendChild(contrastBtn);
		}
	}

	// Рендер превью в карточках, где данные передаются через data-атрибуты
	document.querySelectorAll('.snippet-card__preview[data-html]').forEach(preview => {
		if (preview.dataset.previewReady === 'true') {
			return;
		}

		const previewIframe = preview.querySelector('iframe');
		if (!previewIframe) {
			return;
		}

		previewIframe.setAttribute('sandbox', 'allow-scripts');
		previewIframe.setAttribute('referrerpolicy', 'no-referrer');
		previewIframe.style.pointerEvents = 'none';
		previewIframe.srcdoc = buildPreviewDocument(
			preview.dataset.html || '',
			preview.dataset.css || '',
			preview.dataset.js || ''
		);

		// Add contrast toggle if pholder exists or create it
		let pholder = preview.querySelector('.pholder');
		if (pholder) {
			const contrastWrapper = document.createElement('div');
			contrastWrapper.className = 'snippet-card__contrast';
			const contrastButton = document.createElement('button');
			contrastButton.type = 'button';
			contrastButton.className = 'btn-card snippet-card__contrast-btn';
			contrastButton.textContent = '🌓';
			contrastButton.onclick = (e) => {
				e.stopPropagation();
				const currentBg = previewIframe.style.background;
				if (currentBg === 'rgb(255, 255, 255)' || currentBg === 'white') {
					previewIframe.style.background = 'var(--preview-bg)';
				} else {
					previewIframe.style.background = 'white';
				}
			};
			contrastWrapper.appendChild(contrastButton);
			pholder.appendChild(contrastWrapper);
		}

		preview.dataset.previewReady = 'true';
	});
});
