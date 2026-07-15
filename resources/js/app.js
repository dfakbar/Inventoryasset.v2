import './bootstrap';

(() => {
    const sidebar     = document.getElementById('sidebar');
    const mainWrapper = document.getElementById('main-wrapper');
    const overlay     = document.getElementById('sidebar-overlay');
    const toggleBtn   = document.getElementById('sidebar-toggle');
    const BP          = 992;

    if (!sidebar || !mainWrapper || !toggleBtn) return;

    const isMobile = () => window.innerWidth < BP;

    toggleBtn.addEventListener('click', () => {
        if (isMobile()) {
            const open = sidebar.classList.toggle('show-mobile');
            if (overlay) overlay.classList.toggle('show', open);
        } else {
            sidebar.classList.toggle('collapsed');
            mainWrapper.classList.toggle('expanded');
        }
    });

    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show-mobile');
            overlay.classList.remove('show');
        });
    }

    document.querySelectorAll('.flash-container .alert').forEach(el => {
        setTimeout(() => {
            if (window.bootstrap && bootstrap.Alert) {
                bootstrap.Alert.getOrCreateInstance(el)?.close();
            }
        }, 4000);
    });

    class SearchableSelect {
        constructor(select) {
            this.select = select;
            this.options = Array.from(select.options).filter(o => o.value !== '');
            this.wrap();
            this.render();
            this.bind();
        }

        wrap() {
            this.select.style.display = 'none';
            this.wrapper = document.createElement('div');
            this.wrapper.className = 'searchable-wrapper';
            this.select.parentNode.insertBefore(this.wrapper, this.select);
            this.wrapper.appendChild(this.select);
        }

        render() {
            this.input = document.createElement('input');
            this.input.type = 'text';
            this.input.className = 'form-control searchable-input';
            this.input.placeholder = this.select.options[0]?.text || '-- Cari --';
            this.input.value = this.getSelectedText();
            this.input.readOnly = this.select.disabled;
            this.input.autocomplete = 'off';

            this.arrow = document.createElement('span');
            this.arrow.className = 'searchable-arrow';
            this.arrow.innerHTML = '<i class="bi bi-chevron-down"></i>';

            this.dropdown = document.createElement('div');
            this.dropdown.className = 'searchable-dropdown';

            const searchBox = document.createElement('div');
            searchBox.className = 'search-box';
            this.searchInput = document.createElement('input');
            this.searchInput.type = 'text';
            this.searchInput.placeholder = 'Ketik untuk mencari...';
            searchBox.appendChild(this.searchInput);

            this.list = document.createElement('ul');
            this.list.className = 'options-list';

            this.dropdown.appendChild(searchBox);
            this.dropdown.appendChild(this.list);

            this.wrapper.appendChild(this.input);
            this.wrapper.appendChild(this.arrow);
            this.wrapper.appendChild(this.dropdown);

            this.populateList();
        }

        getSelectedText() {
            const sel = this.select.options[this.select.selectedIndex];
            return sel && sel.value !== '' ? sel.text : '';
        }

        populateList(filter = '') {
            this.list.innerHTML = '';
            const lower = filter.toLowerCase();
            const filtered = this.options.filter(o => o.text.toLowerCase().includes(lower));

            if (filtered.length === 0) {
                const li = document.createElement('li');
                li.className = 'no-result';
                li.textContent = 'Tidak ditemukan';
                this.list.appendChild(li);
                return;
            }

            const selectedVal = this.select.value;
            filtered.forEach(o => {
                const li = document.createElement('li');
                li.textContent = o.text;
                li.dataset.value = o.value;
                if (o.value === selectedVal) li.classList.add('selected');
                li.addEventListener('click', () => this.selectOption(o.value, o.text));
                li.addEventListener('mouseenter', () => {
                    this.list.querySelectorAll('li').forEach(l => l.classList.remove('highlighted'));
                    li.classList.add('highlighted');
                });
                this.list.appendChild(li);
            });
        }

        selectOption(value, text) {
            this.select.value = value;
            this.input.value = text;
            this.select.dispatchEvent(new Event('change', { bubbles: true }));
            this.close();
        }

        open() {
            this.dropdown.classList.add('show');
            this.searchInput.value = '';
            this.populateList();
            this.searchInput.focus();
        }

        close() { this.dropdown.classList.remove('show'); }

        toggle() { this.dropdown.classList.contains('show') ? this.close() : this.open(); }

        bind() {
            this.input.addEventListener('mousedown', (e) => {
                if (this.select.disabled) return;
                if (this.dropdown.classList.contains('show')) { e.preventDefault(); this.close(); }
            });
            this.input.addEventListener('focus', () => {
                if (this.select.disabled) return;
                if (!this.dropdown.classList.contains('show')) this.open();
            });
            this.searchInput.addEventListener('input', () => this.populateList(this.searchInput.value));
            this.searchInput.addEventListener('keydown', (e) => {
                const items = this.list.querySelectorAll('li:not(.no-result)');
                if (items.length === 0) return;
                const current = this.list.querySelector('.highlighted');
                let idx = current ? Array.from(items).indexOf(current) : -1;
                if (e.key === 'ArrowDown') { e.preventDefault(); idx = Math.min(idx + 1, items.length - 1); }
                else if (e.key === 'ArrowUp') { e.preventDefault(); idx = Math.max(idx - 1, 0); }
                else if (e.key === 'Enter' && current) { e.preventDefault(); current.click(); return; }
                else if (e.key === 'Escape') { this.close(); return; }
                items.forEach(l => l.classList.remove('highlighted'));
                if (idx >= 0) items[idx].classList.add('highlighted');
            });
            document.addEventListener('click', (e) => { if (!this.wrapper.contains(e.target)) this.close(); });
            this.select.addEventListener('change', () => { this.input.value = this.getSelectedText(); });
        }
    }

    document.querySelectorAll('select[data-searchable]').forEach(el => new SearchableSelect(el));
})();
