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

	const escapeHtml = str =>
		String(str ?? '')
			.replace(/&/g, '&amp;')
			.replace(/</g, '<')
			.replace(/>/g, '>')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#39;');

	const createSnippetCard = snippet => {
		const card = document.createElement('article');
		card.className = 'snippet-card';
		card.dataset.snippetId = snippet.id;

		const descriptionText = snippet.description || '';
		const truncatedDesc = descriptionText.length > 100
			? descriptionText.substring(0, 100) + '…'
			: descriptionText;

		card.innerHTML = `
			<div class="snippet-card__preview">
				<div class="snippet-card__favorite">
					<!-- Кнопка "в избранное" будет добавлена позже -->
				</div>
			</div>
			<h3 class="snippet-card__title">${escapeHtml(snippet.name)}</h3>
			<div class="snippet-card__description">
				<p>${escapeHtml(truncatedDesc)}</p>
			</div>
			<div class="snippet-card__actions">
				<a href="/pages/card.php?id=${snippet.id}" class="snippet-card__btn">Открыть</a>
			</div>
		`;

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
		loadSnippets();

		window.addEventListener('scroll', () => {
			if (allLoaded || loading) return;

			if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 150) {
				loadSnippets();
			}
		});
	}
});