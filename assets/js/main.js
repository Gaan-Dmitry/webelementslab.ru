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
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#39;');

	const normalizeTags = snippet => {
		if (Array.isArray(snippet?.tags)) {
			return snippet.tags;
		}

		if (typeof snippet?.tag === 'string') {
			return snippet.tag
				.split(',')
				.map(tag => tag.trim())
				.filter(Boolean);
		}

		return [];
	};

	const renderExtraTags = tags =>
		tags
			.slice(0, 3)
			.map(tag => `<span class="tag-pill tag-pill--compact">${escapeHtml(tag)}</span>`)
			.join('');

	const createSnippetCard = snippet => {
		const card = document.createElement('article');
		card.className = 'snippet-card';
		card.dataset.snippetId = snippet.id;

		const tags = normalizeTags(snippet);
		const primaryTag = snippet.primary_tag || tags[0] || null;
		const extraTags = primaryTag ? tags.slice(1) : tags;

		const description = snippet.description
			? `<p class="snippet-card__desc">${escapeHtml(snippet.description)}</p>`
			: '';

		const previewText =
			snippet.preview && snippet.preview.trim() !== ''
				? snippet.preview
				: 'Нет HTML-кода для предпросмотра';

		card.innerHTML = `
			<div class="snippet-card__head">
				${primaryTag ? `<span class="snippet-card__badge">${escapeHtml(primaryTag)}</span>` : ''}
				${extraTags.length ? `<div class="snippet-card__tags">${renderExtraTags(extraTags)}</div>` : ''}
			</div>
			<h3 class="snippet-card__title">${escapeHtml(snippet.name)}</h3>
			${description}
			<div class="snippet-card__preview">
				<pre><code>${escapeHtml(previewText)}</code></pre>
			</div>
			<div class="snippet-card__actions">
				<a href="/pages/card.php?id=${snippet.id}" class="reg-btn anim-hover-box-shadow">Открыть</a>
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
