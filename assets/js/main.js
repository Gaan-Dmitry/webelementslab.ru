document.addEventListener('DOMContentLoaded', () => {
	// Обработка подписки
	const subBtn = document.getElementById('sub-btn');
	const subInput = document.getElementById('subcribeemail');

	if (subBtn && subInput) {
		subBtn.addEventListener('click', () => {
			const email = subInput.value.trim();
			if (email !== '') {
				const encodedEmail = encodeURIComponent(email);
				window.location.href = `/pages/register.php?email=${encodedEmail}`;
			} else {
				alert('Введите почту перед подпиской!');
			}
		});
	}

	// Лента сниппетов
	const list = document.getElementById('snippets-list');

	const buildPreviewDocument = (snippet = {}, { includeScripts = false } = {}) => {
		const html = snippet.html ?? '';
		const css = (snippet.css ?? '').replace(/<\/style>/gi, '<\\/style>');
		const js = includeScripts
			? (snippet.js ?? '').replace(/<\/script>/gi, '<\\/script>')
			: '';

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
					document.addEventListener('click', event => {
						const link = event.target.closest('a');
						if (link) {
							event.preventDefault();
						}
					}, true);

					${js}
				<\/script>
			</body>
			</html>
		`;
	};

	const updateFavoriteButtonState = (button, isFavorite) => {
		button.classList.toggle('fav', isFavorite);
		button.textContent = isFavorite ? '💖' : '🤍';
		button.setAttribute(
			'aria-label',
			isFavorite ? 'Убрать из избранного' : 'Добавить в избранное'
		);
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
				} else if (data.error) {
					alert('Ошибка: ' + data.error);
				}
			})
			.catch(() => alert('Ошибка соединения с сервером'))
			.finally(() => {
				button.disabled = false;
			});
	};

	const createFavoriteButton = snippet => {
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
		button.addEventListener('click', event => {
			event.stopPropagation();
			toggleFavorite(button);
		});

		wrapper.appendChild(button);
		return wrapper;
	};

	const createSnippetCard = snippet => {
		const card = document.createElement('article');
		card.className = 'snippet-card';
		card.dataset.snippetId = snippet.id;
	
		const preview = document.createElement('div');
		preview.className = 'snippet-card__preview';
	
		const iframe = document.createElement('iframe');
		iframe.className = 'snippet-card__iframe';
		iframe.loading = 'lazy';
		iframe.title = `Предпросмотр сниппета «${snippet.name}»`;
		iframe.setAttribute('aria-hidden', 'true');
		iframe.tabIndex = -1;
		iframe.sandbox = 'allow-scripts';
		iframe.dataset.srcdoc = buildPreviewDocument(snippet);

		preview.appendChild(iframe);
	
		const pholder = document.createElement('div');
		pholder.className = 'pholder';
	
		const favoriteButton = createFavoriteButton(snippet);
		if (favoriteButton) {
			pholder.appendChild(favoriteButton);
		}
	
		// Кнопка "Поделиться"
		const shareWrapper = document.createElement('div');
		shareWrapper.className = 'snippet-card__share';
	
		const shareButton = document.createElement('button');
		shareButton.type = 'button';
		shareButton.className = 'btn-card snippet-card__share-btn';
		shareButton.textContent = '🔗'; 
		shareButton.setAttribute('aria-label', 'Поделиться сниппетом');
		shareButton.addEventListener('click', () => {
			const shareUrl = `${window.location.origin}/pages/card.php?id=${snippet.id}`;
			if (navigator.share) {
				navigator.share({
					title: snippet.name || 'Сниппет',
					url: shareUrl,
				}).catch(err => {
					if (err.name !== 'AbortError') {
						console.warn('Ошибка при использовании Web Share API:', err);
					}
				});
			} else {
				// fallback: копирование в буфер обмена
				navigator.clipboard.writeText(shareUrl)
					.then(() => {
						alert('Ссылка скопирована в буфер обмена!');
					})
					.catch(() => {
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
		shareButton.addEventListener('click', event => {
			event.stopPropagation();
		});
	
		shareWrapper.appendChild(shareButton);
		pholder.appendChild(shareWrapper);
	
		preview.appendChild(pholder);
	
		const title = document.createElement('h3');
		title.className = 'snippet-card__title';
		title.textContent = snippet.name ?? '';

		card.tabIndex = 0;
		card.setAttribute('role', 'link');
		card.setAttribute('aria-label', `Открыть карточку «${snippet.name ?? ''}»`);
		card.addEventListener('click', () => {
			window.location.href = `/pages/card.php?id=${snippet.id}`;
		});
		card.addEventListener('keydown', event => {
			if (event.key === 'Enter' || event.key === ' ') {
				event.preventDefault();
				window.location.href = `/pages/card.php?id=${snippet.id}`;
			}
		});
	
		card.append(preview, title);
	
		return card;
	};

	const showEmptyState = () => {
		if (!list || list.dataset.emptyShown === 'true') return;
		list.dataset.emptyShown = 'true';
		const empty = document.createElement('div');
		empty.className = 'snippets-empty';
		empty.textContent = 'Новых сниппетов пока нет.';
		list.appendChild(empty);
	};

	if (list) {
		list.classList.add('snippets-grid');
	}

	let offset = 0;
	let loading = false;
	let allLoaded = false;
	let previewObserver;
	let loadMoreObserver;

	const hydratePreview = iframe => {
		if (!iframe || iframe.dataset.hydrated === 'true') return;
		iframe.srcdoc = iframe.dataset.srcdoc ?? '';
		iframe.dataset.hydrated = 'true';
	};

	const observePreview = iframe => {
		if (!iframe) return;

		if (previewObserver) {
			previewObserver.observe(iframe);
			return;
		}

		hydratePreview(iframe);
	};

	const ensureObservers = () => {
		if (!('IntersectionObserver' in window)) {
			return;
		}

		if (!previewObserver) {
			previewObserver = new IntersectionObserver(
				entries => {
					entries.forEach(entry => {
						if (!entry.isIntersecting) return;
						hydratePreview(entry.target);
						previewObserver.unobserve(entry.target);
					});
				},
				{
					rootMargin: '280px 0px',
				}
			);
		}

		if (!loadMoreObserver) {
			loadMoreObserver = new IntersectionObserver(entries => {
				entries.forEach(entry => {
					if (entry.isIntersecting) {
						loadSnippets();
					}
				});
			}, { rootMargin: '600px 0px' });
		}
	};

	const loadSnippets = () => {
		if (!list || loading || allLoaded) return;
		loading = true;

		fetch(`/handlers/load_snippets.php?offset=${offset}`)
			.then(res => {
				if (!res.ok) {
					throw new Error(`Request failed with status ${res.status}`);
				}
				return res.json();
			})
			.then(data => {
				if (!Array.isArray(data) || data.length === 0) {
					allLoaded = true;
					if (!list.children.length) {
						showEmptyState();
					}
					return;
				}

				data.forEach(snippet => {
					const card = createSnippetCard(snippet);
					list.appendChild(card);
					observePreview(card.querySelector('.snippet-card__iframe'));
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
		ensureObservers();
		loadSnippets();
		if (loadMoreObserver) {
			const sentinel = document.createElement('div');
			sentinel.className = 'snippets-sentinel';
			list.parentNode?.appendChild(sentinel);
			loadMoreObserver.observe(sentinel);
		} else {
			window.addEventListener('scroll', () => {
				if (allLoaded || loading) return;

				if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 150) {
					loadSnippets();
				}
			});
		}
	}
});
