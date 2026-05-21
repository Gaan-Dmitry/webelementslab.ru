// Ждем пока страница загрузится
document.addEventListener('DOMContentLoaded', () => {
	// Находим кнопку подписки и инпут почты
	const subBtn = document.getElementById('sub-btn');
	const subInput = document.getElementById('subcribeemail');

	// Если они есть - вешаем обработчик
	if (subBtn && subInput) {
		subBtn.addEventListener('click', () => {
			const email = subInput.value.trim();
			if (email !== '') {
				// Кодируем почту и редиректим на регистрацию
				const encodedEmail = encodeURIComponent(email);
				window.location.href = `/pages/register.php?email=${encodedEmail}`;
			} else {
				alert('Введите почту перед подпиской!');
			}
		});
	}

	// Контейнер для сниппетов
	const list = document.getElementById('snippets-list');

	// Функция собирает HTML документ для превьюшки в iframe
	const buildPreviewDocument = (snippet = {}) => {
		const html = snippet.html ?? '';
		// Экранируем закрывающие теги чтобы не ломали структуру
		const css = (snippet.css ?? '').replace(/<\/style>/gi, '<\\/style>');
		const js = (snippet.js ?? '').replace(/<\/script>/gi, '<\\/script>');

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
					${css}
				</style>
			</head>
			<body>
				${html}
				<script>
					// Блокируем клики по ссылкам и кнопкам внутри превью
					document.addEventListener('click', event => {
						if (event.target.closest('a, button')) {
							event.preventDefault();
						}
					}, true);
					// И формы тоже блокируем
					document.addEventListener('submit', event => {
						event.preventDefault();
					}, true);
				<\/script>
				<script>
					${js}
				<\/script>
			</body>
			</html>
		`;
	};

	// Обновляет состояние кнопки избранного (сердечко)
	const updateFavoriteButtonState = (button, isFavorite) => {
		button.classList.toggle('fav', isFavorite);
		button.textContent = isFavorite ? '💖' : '🤍';
		button.setAttribute('aria-label', isFavorite ? 'Убрать из избранного' : 'Добавить в избранное');
	};

	// Лайк/дизлайк сниппета
	const toggleFavorite = button => {
		const snippetId = button.dataset.id;
		if (!snippetId) return;

		// Блокируем кнопку пока запрос идет
		button.disabled = true;
		fetch('/handlers/toggle_fav.php', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ id: snippetId }),
		})
			.then(response => response.json())
			.then(data => {
				// Если не авторизован - кидаем на логин
				if (data.error === 'unauthorized') {
					window.location.href = '/pages/login.php';
					return;
				}

				if (data.status === 'added') {
					updateFavoriteButtonState(button, true);
				} else if (data.status === 'removed') {
					updateFavoriteButtonState(button, false);
				} else if (data.error) {
					alert('Ошибка: ' + data.error);
				}
			})
			.catch(() => alert('Ошибка соединения с сервером'))
			.finally(() => {
				// Разблокируем кнопку
				button.disabled = false;
			});
	};

	// Создает кнопку избранного для карточки
	const createFavoriteButton = snippet => {
		// Если юзер не залогинен - не показываем кнопку
		if (!snippet.can_favorite) {
			return null;
		}

		const wrapper = document.createElement('div');
		wrapper.className = 'snippet-card__favorite';

		const button = document.createElement('button');
		button.type = 'button';
		button.className = 'btn-card snippet-card__favorite-btn';
		button.dataset.id = snippet.id;
		updateFavoriteButtonState(button, Boolean(snippet.is_favorite));
		button.addEventListener('click', () => toggleFavorite(button));

		wrapper.appendChild(button);
		return wrapper;
	};

	// Создает карточку сниппета
	const createSnippetCard = snippet => {
		const card = document.createElement('article');
		card.className = 'snippet-card';
		card.dataset.snippetId = snippet.id;
		// Ссылка на страницу сниппета
		card.dataset.href = `/pages/card.php?id=${snippet.id}`;
		card.tabIndex = 0;
		card.setAttribute('role', 'link');

		// Контейнер для превью
		const preview = document.createElement('div');
		preview.className = 'snippet-card__preview';

		// Iframe для отображения кода
		const iframe = document.createElement('iframe');
		iframe.className = 'snippet-card__iframe';
		iframe.loading = 'lazy';
		iframe.title = `Предпросмотр сниппета «${snippet.name}»`;
		iframe.setAttribute('aria-hidden', 'true');
		iframe.setAttribute('sandbox', 'allow-scripts');
		iframe.setAttribute('referrerpolicy', 'no-referrer');
		iframe.style.pointerEvents = 'none';
		iframe.srcdoc = buildPreviewDocument(snippet);

		preview.appendChild(iframe);

		// Плейсхолдер с кнопками (избранное, шеринг)
		const pholder = document.createElement('div');
		pholder.className = 'pholder';

		// Кнопка избранного
		const favoriteButton = createFavoriteButton(snippet);
		if (favoriteButton) {
			pholder.appendChild(favoriteButton);
		}

		// Кнопка поделиться
		const shareWrapper = document.createElement('div');
		shareWrapper.className = 'snippet-card__share';

		const shareButton = document.createElement('button');
		shareButton.type = 'button';
		shareButton.className = 'btn-card snippet-card__share-btn';
		shareButton.textContent = '🔗';
		shareButton.setAttribute('aria-label', 'Поделиться сниппетом');
		shareButton.addEventListener('click', () => {
			const shareUrl = `${window.location.origin}/pages/card.php?id=${snippet.id}`;
			// Если есть нативный шеринг (мобилки) - используем его
			if (navigator.share) {
				navigator
					.share({
						title: snippet.name || 'Сниппет',
						url: shareUrl,
					})
					.catch(err => {
						if (err.name !== 'AbortError') {
							console.warn('Ошибка при использовании Web Share API:', err);
						}
					});
			} else {
				// Иначе копируем в буфер
				navigator.clipboard
					.writeText(shareUrl)
					.then(() => {
						alert('Ссылка скопирована в буфер обмена!');
					})
					.catch(() => {
						// Фолбек через временный инпут
						const tempInput = document.createElement('input');
						tempInput.value = shareUrl;
						document.body.appendChild(tempInput);
						tempInput.select();
						document.execCommand('copy');
						document.body.removeChild(tempInput);
						alert('Ссылка скопирована!');
					});
			}
		});

		shareWrapper.appendChild(shareButton);
		pholder.appendChild(shareWrapper);

		preview.appendChild(pholder);

		// Заголовок карточки
		const title = document.createElement('h3');
		title.className = 'snippet-card__title';
		title.textContent = snippet.name ?? '';

		card.append(preview, title);

		return card;
	};

	// Показывает сообщение когда сниппетов нет
	const showEmptyState = message => {
		if (!list) return;
		list.dataset.emptyShown = 'true';
		const empty = document.createElement('div');
		empty.className = 'snippets-empty';
		empty.textContent = message;
		list.appendChild(empty);
	};

	// Добавляем класс сетки
	if (list) {
		list.classList.add('snippets-grid');
	}

	// Пагинация - смещение для загрузки следующей пачки
	let offset = 0;
	let loading = false;
	let allLoaded = false;

	// Формируем URL для запроса
	const buildRequestUrl = () => `/handlers/load_snippets.php?offset=${offset}`;

	// Загрузка сниппетов
	const loadSnippets = () => {
		if (!list || loading || allLoaded) return;
		loading = true;

		fetch(buildRequestUrl())
			.then(res => {
				if (!res.ok) {
					throw new Error(`Request failed with status ${res.status}`);
				}
				return res.json();
			})
			.then(data => {
				// Если данных нет - значит всё загрузили
				if (!Array.isArray(data) || data.length === 0) {
					allLoaded = true;
					if (!list.children.length) {
						showEmptyState('Новых сниппетов пока нет.');
					}
					return;
				}

				// Добавляем карточки в список
				data.forEach(snippet => {
					list.appendChild(createSnippetCard(snippet));
				});

				offset += data.length;
			})
			.catch(err => {
				console.error('Ошибка загрузки сниппетов:', err);
			})
			.finally(() => {
				loading = false;
			});
	};

	if (list) {
		// Первичная загрузка
		loadSnippets();

		// Проверка можно ли открыть карточку (не кликнули ли на кнопку)
		const canOpenSnippetCard = event =>
			!event.target.closest('button, a, input, textarea, select, label, .pholder');

		// Клик по карточке - переход на страницу сниппета
		list.addEventListener('click', event => {
			const card = event.target.closest('.snippet-card[data-href]');
			if (!card || !canOpenSnippetCard(event)) {
				return;
			}

			window.location.href = card.dataset.href;
		});

		// Enter или пробел по карточке - тоже переход
		list.addEventListener('keydown', event => {
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

		// Скролл - подгрузка следующих сниппетов (бесконечный скролл)
		window.addEventListener('scroll', () => {
			if (allLoaded || loading) return;

			// Если доскроллили почти до низа - грузим ещё
			if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 150) {
				loadSnippets();
			}
		});
	}
});
