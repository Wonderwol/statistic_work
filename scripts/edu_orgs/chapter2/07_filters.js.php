function initDynFilters() {
  const filtersConfig = {
    org_type: {
      isRadio: true,
      searchId: 'org_type-search',
      groupId: 'org_type-group',
      clearId: 'org_type-clear',
      containerId: 'org_type-container',
      placeholder: 'Уровень представления данных'
    },
    locality: {
      isRadio: true,
      searchId: 'locality-search',
      groupId: 'locality-group',
      clearId: 'locality-clear',
      containerId: 'locality-container',
      placeholder: 'Тип местности'
    }
  };

  function initFilter(filterName, config) {
    const searchInput = document.getElementById(config.searchId);
    const checkboxGroup = document.getElementById(config.groupId);
    const clearBtn = document.getElementById(config.clearId);
    const container = document.getElementById(config.containerId);

    if (!searchInput || !checkboxGroup) return;

    const inputType = config.isRadio ? 'radio' : 'checkbox';
    const checkboxes = checkboxGroup.querySelectorAll('input[type="' + inputType + '"]');
    const checkboxItems = checkboxGroup.querySelectorAll('.checkbox-item');
    const noResults = checkboxGroup.querySelector('.no-results');

    function updateSearchInputText() {
      const selectedText = [];
      checkboxes.forEach(cb => {
        if (cb.checked) {
          const label = document.querySelector('label[for="' + cb.id + '"]');
          if (label) selectedText.push(label.textContent.trim());
        }
      });

      searchInput.value = (selectedText.length > 0) ? selectedText.join(', ') : config.placeholder;
    }

    function updateFilterState() {
      updateSearchInputText();
    }

    searchInput.addEventListener('click', function (e) {
      e.stopPropagation();

      Object.keys(filtersConfig).forEach(otherName => {
        if (otherName === filterName) return;
        const otherGroup = document.getElementById(filtersConfig[otherName].groupId);
        const otherContainer = document.getElementById(filtersConfig[otherName].containerId);
        const otherFG = otherContainer ? otherContainer.closest('.filter-group') : null;

        if (otherGroup) otherGroup.classList.remove('active');
        if (otherContainer) otherContainer.classList.remove('active');
        if (otherFG) otherFG.classList.remove('dropdown-open');
      });

      const willOpen = !checkboxGroup.classList.contains('active');
      checkboxGroup.classList.toggle('active', willOpen);
      if (container) container.classList.toggle('active', willOpen);
      const fg = container ? container.closest('.filter-group') : null;
      if (fg) fg.classList.toggle('dropdown-open', willOpen);
    });

    searchInput.addEventListener('input', function () {
      const searchTerm = this.value.toLowerCase().trim();
      let hasVisibleItems = false;

      checkboxItems.forEach(item => {
        const label = item.querySelector('label');
        const text = (label ? label.textContent : '').toLowerCase();
        const ok = text.includes(searchTerm);
        item.style.display = ok ? 'flex' : 'none';
        if (ok) hasVisibleItems = true;
      });

      if (noResults) noResults.style.display = hasVisibleItems ? 'none' : 'block';
    });

    checkboxes.forEach(cb => {
      cb.addEventListener('change', function () {
        if (config.isRadio && this.checked) {
          setTimeout(() => {
            checkboxGroup.classList.remove('active');
            if (container) container.classList.remove('active');
            const fg = container ? container.closest('.filter-group') : null;
            if (fg) fg.classList.remove('dropdown-open');
          }, 200);
        }
        updateFilterState();
      });
    });

    checkboxItems.forEach(item => {
      item.addEventListener('click', function (e) {
        const input = this.querySelector('input');
        if (!input) return;
        if (e.target === input) return;

        if (config.isRadio) input.checked = true;
        else input.checked = !input.checked;

        input.dispatchEvent(new Event('change'));
      });
    });

    if (clearBtn) {
      clearBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        checkboxes.forEach(cb => cb.checked = false);
        updateFilterState();
      });
    }

    updateFilterState();
  }

  Object.keys(filtersConfig).forEach(name => initFilter(name, filtersConfig[name]));

  document.addEventListener('click', function (event) {
    let shouldCloseAll = true;

    Object.keys(filtersConfig).forEach(name => {
      const container = document.getElementById(filtersConfig[name].containerId);
      if (container && container.contains(event.target)) shouldCloseAll = false;
    });

    if (shouldCloseAll) {
      Object.keys(filtersConfig).forEach(name => {
        const group = document.getElementById(filtersConfig[name].groupId);
        const container = document.getElementById(filtersConfig[name].containerId);
        const fg = container ? container.closest('.filter-group') : null;

        if (group) group.classList.remove('active');
        if (container) container.classList.remove('active');
        if (fg) fg.classList.remove('dropdown-open');
      });
    }
  });
}
