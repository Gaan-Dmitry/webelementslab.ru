document.addEventListener('DOMContentLoaded', () => {
	const searchToggle = document.getElementById('searchToggle');
	const searchDropdown = document.getElementById('searchDropdown');
	const searchInput = document.getElementById('searchInput');

	if (!searchToggle || !searchDropdown || !searchInput) {
		return;
	}

	const emitSearchEvent = query => {
		document.dispatchEvent(
			new CustomEvent('snippets:search', {
				detail: { query },
			})
		);
	};

	function toggleSearchDropdown(forceClose = false) {
		const shouldOpen = !forceClose && !searchDropdown.classList.contains('is-open');
		searchDropdown.classList.toggle('is-open', shouldOpen);
		searchToggle.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
		searchDropdown.setAttribute('aria-hidden', shouldOpen ? 'false' : 'true');
	}

	const debounce = (fn, delay = 220) => {
		let timer = null;
		return (...args) => {
			clearTimeout(timer);
			timer = setTimeout(() => fn(...args), delay);
		};
	};

	searchToggle.addEventListener('click', e => {
		e.stopPropagation();
		const wasOpen = searchDropdown.classList.contains('is-open');
		toggleSearchDropdown();
		if (!wasOpen) {
			searchInput.focus();
		}
	});

	document.addEventListener('click', e => {
		if (!searchDropdown.contains(e.target) && !searchToggle.contains(e.target)) {
			toggleSearchDropdown(true);
		}
	});

	document.addEventListener('keydown', e => {
		if (e.key === 'Escape') {
			toggleSearchDropdown(true);
			searchToggle.focus();
		}
	});

	const debouncedSearch = debounce(query => emitSearchEvent(query), 250);
	searchInput.addEventListener('input', e => {
		debouncedSearch(e.target.value.trim());
	});
});
