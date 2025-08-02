document.addEventListener('DOMContentLoaded', () => {
    const searchToggle = document.getElementById('searchToggle');
    const searchDropdown = document.getElementById('searchDropdown');
  
    function toggleSearchDropdown(forceClose = false) {
      if (forceClose || searchDropdown.style.display === 'none') {
        searchDropdown.style.display = forceClose ? 'none' : 'block';
      } else {
        searchDropdown.style.display = 'none';
      }
    }
  
    searchToggle.addEventListener('click', (e) => {
      e.stopPropagation();
      toggleSearchDropdown();
      document.getElementById('searchInput').focus();
    });
  
    // Закрываем меню при клике вне области
    document.addEventListener('click', (e) => {
      if (!searchDropdown.contains(e.target) && e.target !== searchToggle) {
        toggleSearchDropdown(true);
      }
    });
  
    // Закрываем меню по Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        toggleSearchDropdown(true);
        searchToggle.focus();
      }
    });
  });
  