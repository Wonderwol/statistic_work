function initCh3Filters() {
  const cfg = {
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

  function closeAll(exceptName) {
    Object.keys(cfg).forEach((name) => {
      if (name === exceptName) return;
      const group = document.getElementById(cfg[name].groupId);
      const container = document.getElementById(cfg[name].containerId);
      const fg = container ? container.closest('.filter-group') : null;
      if (group) group.classList.remove('active');
      if (container) container.classList.remove('active');
      if (fg) fg.classList.remove('dropdown-open');
    });
  }

  function initOne(name, c) {
    const searchInput = document.getElementById(c.searchId);
    const group = document.getElementById(c.groupId);
    const container = document.getElementById(c.containerId);
    const clearBtn = document.getElementById(c.clearId);
    if (!searchInput || !group) return;

    const inputType = c.isRadio ? 'radio' : 'checkbox';
    const inputs = Array.from(group.querySelectorAll('input[type="' + inputType + '"]'));
    const items = Array.from(group.querySelectorAll('.checkbox-item'));

    function updateText() {
      const selected = [];
      inputs.forEach((inp) => {
        if (inp.checked) {
          const lbl = group.querySelector('label[for="' + inp.id + '"]');
          if (lbl) selected.push(lbl.textContent.trim());
        }
      });
      searchInput.value = selected.length ? selected.join(', ') : c.placeholder;
    }

    searchInput.addEventListener('click', function (e) {
      e.stopPropagation();
      closeAll(name);
      const willOpen = !group.classList.contains('active');
      group.classList.toggle('active', willOpen);
      if (container) container.classList.toggle('active', willOpen);
      const fg = container ? container.closest('.filter-group') : null;
      if (fg) fg.classList.toggle('dropdown-open', willOpen);
    });

    inputs.forEach((inp) => {
      inp.addEventListener('change', function () {
        if (c.isRadio && inp.checked) {
          setTimeout(() => {
            group.classList.remove('active');
            if (container) container.classList.remove('active');
            const fg = container ? container.closest('.filter-group') : null;
            if (fg) fg.classList.remove('dropdown-open');
          }, 180);
        }
        updateText();
      });
    });

    items.forEach((item) => {
      item.addEventListener('click', function (e) {
        const input = item.querySelector('input');
        if (!input) return;
        if (e.target === input) return;
        if (c.isRadio) input.checked = true;
        else input.checked = !input.checked;
        input.dispatchEvent(new Event('change'));
      });
    });

    if (clearBtn) {
      clearBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        inputs.forEach(inp => inp.checked = false);
        updateText();
      });
    }

    updateText();
  }

  Object.keys(cfg).forEach((name) => initOne(name, cfg[name]));

  document.addEventListener('click', function (event) {
    let inside = false;
    Object.keys(cfg).forEach((name) => {
      const container = document.getElementById(cfg[name].containerId);
      if (container && container.contains(event.target)) inside = true;
    });
    if (!inside) closeAll(null);
  });
}
