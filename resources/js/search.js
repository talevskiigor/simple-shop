const form = document.querySelector('[data-live-search]');
if (form) {
    const input = form.querySelector('input');
    const popup = form.querySelector('[data-search-popup]');
    const list = form.querySelector('[role="listbox"]');
    const status = form.querySelector('[data-search-status]');
    const all = form.querySelector('[data-search-all]');
    let timer, controller, sequence = 0, selected = -1, items = [];
    const close = () => { popup.hidden = true; input.setAttribute('aria-expanded', 'false'); input.removeAttribute('aria-activedescendant'); selected = -1; };
    const choose = index => {
        selected = index;
        [...list.children].forEach((el, i) => el.setAttribute('aria-selected', String(i === index)));
        if (index >= 0) { input.setAttribute('aria-activedescendant', list.children[index].id); list.children[index].scrollIntoView({block: 'nearest'}); }
        else input.removeAttribute('aria-activedescendant');
    };
    const search = async () => {
        const id = ++sequence, query = input.value.trim();
        controller?.abort(); close();
        if (Array.from(query).length < 2) return;
        controller = new AbortController();
        try {
            const response = await fetch(form.dataset.suggestions + '?' + new URLSearchParams({find: query}), {signal: controller.signal, headers: {Accept: 'application/json'}});
            if (!response.ok) throw new Error('Search unavailable');
            const data = await response.json();
            if (id !== sequence || query !== input.value.trim()) return;
            items = data.items; list.replaceChildren();
            items.forEach((item, index) => {
                const link = document.createElement('a'); link.href = item.url; link.id = 'search-option-' + index;
                link.className = 'search-suggestion'; link.setAttribute('role', 'option'); link.setAttribute('aria-selected', 'false'); link.tabIndex = -1;
                const image = document.createElement('img'); image.src = item.image; image.alt = ''; image.width = 48; image.height = 48;
                const text = document.createElement('span'), name = document.createElement('strong'), detail = document.createElement('small');
                name.textContent = item.name; detail.textContent = item.in_stock ? item.price : 'Нема на залиха';
                text.append(name, detail); link.append(image, text); list.append(link);
            });
            status.textContent = data.total ? `${data.total} резултати` : 'Нема резултати.';
            all.href = form.action + '?' + new URLSearchParams({find: query}); all.hidden = !data.total;
            popup.hidden = false; input.setAttribute('aria-expanded', 'true');
        } catch (error) { if (error.name !== 'AbortError') close(); }
    };
    input.addEventListener('input', event => { clearTimeout(timer); ++sequence; controller?.abort(); close(); if (!event.isComposing) timer = setTimeout(search, 220); });
    input.addEventListener('compositionend', () => { clearTimeout(timer); timer = setTimeout(search, 220); });
    input.addEventListener('focus', () => { if (input.value.trim().length >= 2) search(); });
    input.addEventListener('keydown', event => {
        if (event.key === 'Escape') { ++sequence; controller?.abort(); clearTimeout(timer); close(); return; }
        if (popup.hidden || !items.length) return;
        if (['ArrowDown','ArrowUp'].includes(event.key)) { event.preventDefault(); choose(selected < 0 ? (event.key === 'ArrowDown' ? 0 : items.length - 1) : (selected + (event.key === 'ArrowDown' ? 1 : -1) + items.length) % items.length); }
        if (event.key === 'Enter' && selected >= 0) { event.preventDefault(); window.location.assign(items[selected].url); }
        if (event.key === 'Tab' && selected >= 0) { input.value = items[selected].name; close(); }
    });
    form.addEventListener('focusout', () => setTimeout(() => { if (!form.contains(document.activeElement)) { ++sequence; controller?.abort(); clearTimeout(timer); close(); } }, 0));
    document.addEventListener('pointerdown', event => { if (!form.contains(event.target)) { ++sequence; controller?.abort(); clearTimeout(timer); close(); } });
}
