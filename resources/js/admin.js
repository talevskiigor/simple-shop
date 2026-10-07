import { Editor, Node, mergeAttributes } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Image from '@tiptap/extension-image';
import { TableKit } from '@tiptap/extension-table';

const Video = Node.create({
    name: 'video', group: 'block', atom: true,
    addAttributes() { return { src: { default: null }, controls: { default: true }, preload: { default: 'metadata' } }; },
    parseHTML() { return [{ tag: 'video' }]; },
    renderHTML({ HTMLAttributes }) { return ['video', mergeAttributes(HTMLAttributes, { controls: '', preload: 'metadata' })]; },
});
const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
const picker = document.querySelector('#media-picker');
let chooseMedia = null, nextPage = null;
async function request(url, options = {}) {
    const response = await fetch(url, { ...options, headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf, ...options.headers } });
    const data = await response.json().catch(() => ({}));
    if (!response.ok) throw new Error(Object.values(data.errors || {}).flat().join(' ') || data.message || 'Request failed. Please try again.');
    return data;
}
function button(label, action, className = 'btn btn-sm btn-outline-secondary') {
    const element = document.createElement('button'); element.type = 'button'; element.className = className; element.textContent = label; element.addEventListener('click', action); return element;
}
async function loadMedia(url = '/admin/media', append = false) {
    const status = picker.querySelector('[data-picker-status]'); status.textContent = 'Loading…';
    try {
        const data = await request(url); const results = picker.querySelector('[data-picker-results]');
        if (!append) results.replaceChildren();
        for (const item of data.data) {
            const col = document.createElement('div'); col.className = 'col-6 col-md-4';
            const select = button('', () => { chooseMedia?.(item); picker.close(); }, 'btn btn-light border w-100 h-100 text-start');
            if (item.type === 'image') { const img = document.createElement('img'); img.src = item.preview; img.alt = item.alt; img.className = 'media-thumb'; img.loading = 'lazy'; select.append(img); }
            else { const video = document.createElement('div'); video.className = 'media-thumb d-flex align-items-center justify-content-center'; video.textContent = '▶ Video'; select.append(video); }
            const name = document.createElement('div'); name.className = 'small text-truncate mt-2'; name.textContent = item.name; select.append(name); col.append(select); results.append(col);
        }
        nextPage = data.next; picker.querySelector('[data-picker-more]').hidden = !nextPage;
        status.textContent = data.data.length ? 'Select a file to insert it.' : 'No media found.';
    } catch (error) { status.textContent = error.message; }
}
function openPicker(callback) { chooseMedia = callback; picker.showModal(); loadMedia(); }
if (picker) {
    picker.querySelector('[data-close-picker]').addEventListener('click', () => picker.close());
    picker.querySelector('[data-picker-search]').addEventListener('submit', event => { event.preventDefault(); loadMedia('/admin/media?q=' + encodeURIComponent(new FormData(event.target).get('q'))); });
    picker.querySelector('[data-picker-more]').addEventListener('click', () => { if (nextPage) loadMedia(nextPage, true); });
    picker.querySelector('[data-picker-upload]').addEventListener('submit', async event => {
        event.preventDefault(); const submit = event.target.querySelector('button'); submit.disabled = true;
        try { await request('/admin/media', { method: 'POST', body: new FormData(event.target) }); event.target.reset(); await loadMedia(); }
        catch (error) { picker.querySelector('[data-picker-status]').textContent = error.message; }
        finally { submit.disabled = false; }
    });
}
for (const textarea of document.querySelectorAll('[data-editor]')) {
    const wrapper = document.createElement('div'); wrapper.className = 'rich-editor';
    const toolbar = document.createElement('div'); toolbar.className = 'editor-toolbar d-flex flex-wrap gap-1 p-2 border rounded-top bg-light'; toolbar.setAttribute('role', 'toolbar'); toolbar.setAttribute('aria-label', 'Text formatting');
    const surface = document.createElement('div'); surface.className = 'editor-surface border rounded-bottom';
    textarea.before(wrapper); wrapper.append(toolbar, surface); textarea.hidden = true;
    const editor = new Editor({ element: surface, extensions: [StarterKit.configure({ link: { openOnClick: false } }), Image, Video, TableKit], content: textarea.value,
        editorProps: { attributes: { role: 'textbox', 'aria-multiline': 'true', 'aria-label': document.querySelector(`label[for="${textarea.id}"]`)?.textContent || 'Content' } },
        onUpdate: ({ editor }) => { textarea.value = editor.getHTML(); },
    });
    const commands = [ ['Bold', () => editor.chain().focus().toggleBold().run()], ['Italic', () => editor.chain().focus().toggleItalic().run()], ['Heading', () => editor.chain().focus().toggleHeading({ level: 2 }).run()], ['Bullets', () => editor.chain().focus().toggleBulletList().run()], ['Numbered list', () => editor.chain().focus().toggleOrderedList().run()], ['Link', () => { const url = window.prompt('Link address (https://…)', editor.getAttributes('link').href || 'https://'); if (url === '') editor.chain().focus().unsetLink().run(); else if (url !== null) editor.chain().focus().setLink({ href: url }).run(); }], ['Table', () => editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()], ['Remove table', () => editor.chain().focus().deleteTable().run()], ['Image / video', () => openPicker(item => { editor.chain().focus().insertContent(item.type === 'image' ? { type: 'image', attrs: { src: item.embed || item.url, alt: item.alt || item.name } } : { type: 'video', attrs: { src: item.url } }).run(); })], ['Undo', () => editor.chain().focus().undo().run()], ['Redo', () => editor.chain().focus().redo().run()] ];
    for (const [label, action] of commands) toolbar.append(button(label, action));
    toolbar.append(button('HTML', () => { if (textarea.hidden) { textarea.hidden = false; surface.hidden = true; } else { editor.commands.setContent(textarea.value); textarea.hidden = true; surface.hidden = false; } }));
}
const gallery = document.querySelector('[data-gallery]');
if (gallery) {
    document.querySelector('[data-pick-gallery]').addEventListener('click', () => openPicker(item => {
        if (gallery.querySelector(`[data-media-id="${item.id}"]`)) return;
        const col = document.createElement('div'); col.className = 'col-6 col-md-4'; col.dataset.mediaId = item.id;
        const card = document.createElement('div'); card.className = 'card card-body h-100';
        if (item.type === 'image') { const img = document.createElement('img'); img.src = item.preview; img.alt = item.alt; img.className = 'media-thumb'; card.append(img); }
        else { const video = document.createElement('div'); video.className = 'media-thumb'; video.textContent = '▶ Video'; card.append(video); }
        const name = document.createElement('div'); name.className = 'small text-truncate my-2'; name.textContent = item.name;
        const input = document.createElement('input'); input.type = 'hidden'; input.name = 'media_ids[]'; input.value = item.id;
        const controls = document.createElement('div'); controls.className = 'btn-group btn-group-sm';
        for (const [label, move] of [['←', '-1'], ['→', '1']]) { const b = button(label, () => {}); b.dataset.move = move; b.setAttribute('aria-label', move === '-1' ? 'Move earlier' : 'Move later'); controls.append(b); }
        const remove = button('Remove', () => {}, 'btn btn-sm btn-outline-danger'); remove.dataset.removeMedia = ''; controls.append(remove);
        card.append(name, input, controls); col.append(card); gallery.append(col);
    }));
    gallery.addEventListener('click', event => {
        const col = event.target.closest('[data-media-id]'); if (!col) return;
        if (event.target.hasAttribute('data-remove-media')) col.remove();
        if (event.target.dataset.move === '-1' && col.previousElementSibling) gallery.insertBefore(col, col.previousElementSibling);
        if (event.target.dataset.move === '1' && col.nextElementSibling) gallery.insertBefore(col.nextElementSibling, col);
    });
}
for (const form of document.querySelectorAll('[data-confirm]')) form.addEventListener('submit', event => { if (!window.confirm(form.dataset.confirm)) event.preventDefault(); });
