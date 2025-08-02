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

	// Загрузка сниппетов
	let offset = 0;
	let loading = false;

	function loadSnippets() {
		if (loading) return;
		loading = true;

		fetch(`/handlers/load_snippets.php?offset=${offset}`)
			.then(res => res.json())
			.then(data => {
				const list = document.getElementById('snippets-list');
				if (!list) return;

				data.forEach(snippet => {
					const a = document.createElement('a');
					a.href = `/pages/card.php?id=${snippet.id}`;
					a.textContent = snippet.name;
					a.className = 'snippet-link';
					list.appendChild(a);
					list.appendChild(document.createElement('br'));
				});

				offset += data.length;
				loading = false;
			})
			.catch(err => {
				console.error('Ошибка загрузки сниппетов:', err);
				loading = false;
			});
	}

	loadSnippets(); // Первый вызов

	window.addEventListener('scroll', () => {
		if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 100) {
			loadSnippets();
		}
	});
});
