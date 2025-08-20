// Własny edytor WYSIWYG
class SimpleEditor {
    constructor(selector, options = {}) {
        this.container = document.querySelector(selector);
        this.options = {
            height: options.height || 400,
            placeholder: options.placeholder || 'Wpisz treść...',
            ...options
        };
        
        this.init();
    }
    
    init() {
        // Ukryj oryginalny textarea
        this.textarea = this.container;
        this.textarea.style.display = 'none';
        
        // Stwórz kontener edytora
        this.editorContainer = document.createElement('div');
        this.editorContainer.className = 'simple-editor';
        this.editorContainer.style.cssText = `
            border: 1px solid var(--border);
            border-radius: 0 0 10px 10px;
            background: var(--bg);
            color: var(--fg);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 16px;
            line-height: 1.6;
        `;
        
        // Stwórz pasek narzędzi
        this.createToolbar();
        
        // Stwórz obszar edycji
        this.createEditorArea();
        
        // Wstaw edytor po textarea
        this.textarea.parentNode.insertBefore(this.editorContainer, this.textarea.nextSibling);
        
        // Inicjalizuj zawartość
        this.setContent(this.textarea.value);
        
        // Obsługa zmian
        this.editorArea.addEventListener('input', () => {
            this.textarea.value = this.getContent();
        });
        
        // Obsługa focus/blur
        this.editorArea.addEventListener('focus', () => {
            this.editorContainer.style.borderColor = 'var(--acc)';
        });
        
        this.editorArea.addEventListener('blur', () => {
            this.editorContainer.style.borderColor = 'var(--border)';
        });
    }
    
    createToolbar() {
        this.toolbar = document.createElement('div');
        this.toolbar.className = 'editor-toolbar';
        this.toolbar.style.cssText = `
            background: var(--card);
            border: 1px solid var(--border);
            border-bottom: none;
            border-radius: 10px 10px 0 0;
            padding: 8px;
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
            align-items: center;
        `;
        
        // Przyciski formatowania
        const buttons = [
            { icon: 'B', title: 'Pogrubienie', command: 'bold', tag: 'b' },
            { icon: 'I', title: 'Kursywa', command: 'italic', tag: 'i' },
            { icon: 'U', title: 'Podkreślenie', command: 'underline', tag: 'u' },
            { icon: 'S', title: 'Przekreślenie', command: 'strikethrough', tag: 's' },
            { separator: true },
            { icon: 'H1', title: 'Nagłówek 1', command: 'formatBlock', value: 'h1' },
            { icon: 'H2', title: 'Nagłówek 2', command: 'formatBlock', value: 'h2' },
            { icon: 'H3', title: 'Nagłówek 3', command: 'formatBlock', value: 'h3' },
            { separator: true },
            { icon: '◉', title: 'Lista punktowana', command: 'insertUnorderedList' },
            { icon: '①②', title: 'Lista numerowana', command: 'insertOrderedList' },
            { separator: true },
            { icon: '🔗', title: 'Wstaw link', action: 'link' },
            { icon: '🖼️', title: 'Wstaw obraz', action: 'image' },
            { separator: true },
            { icon: '🎨', title: 'Kolor tekstu', action: 'foreColor' },
            { icon: '🖌️', title: 'Kolor tła', action: 'backColor' },
            { separator: true },
            { icon: '⬅️', title: 'Wyrównaj do lewej', command: 'justifyLeft' },
            { icon: '↔️', title: 'Wyśrodkuj', command: 'justifyCenter' },
            { icon: '➡️', title: 'Wyrównaj do prawej', command: 'justifyRight' },
            { separator: true },
            { icon: '📋', title: 'Wklej jako zwykły tekst', action: 'pasteAsText' },
            { icon: '🧹', title: 'Wyczyść formatowanie', command: 'removeFormat' }
        ];
        
        buttons.forEach(btn => {
            if (btn.separator) {
                const separator = document.createElement('div');
                separator.style.cssText = `
                    width: 1px;
                    height: 20px;
                    background: var(--border);
                    margin: 0 4px;
                `;
                this.toolbar.appendChild(separator);
            } else {
                const button = document.createElement('button');
                button.innerHTML = btn.icon;
                button.title = btn.title;
                button.style.cssText = `
                    background: transparent;
                    border: 1px solid transparent;
                    border-radius: 4px;
                    padding: 6px 8px;
                    cursor: pointer;
                    font-size: 14px;
                    color: var(--fg);
                    transition: all 0.2s;
                `;
                
                button.addEventListener('mouseenter', () => {
                    button.style.background = 'var(--border)';
                });
                
                button.addEventListener('mouseleave', () => {
                    button.style.background = 'transparent';
                });
                
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.editorArea.focus();
                    
                    if (btn.action) {
                        this.handleAction(btn.action);
                    } else {
                        document.execCommand(btn.command, false, btn.value);
                    }
                    
                    this.updateToolbar();
                });
                
                this.toolbar.appendChild(button);
            }
        });
        
        this.editorContainer.appendChild(this.toolbar);
    }
    
    createEditorArea() {
        this.editorArea = document.createElement('div');
        this.editorArea.contentEditable = true;
        this.editorArea.style.cssText = `
            min-height: ${this.options.height}px;
            padding: 16px;
            outline: none;
            overflow-y: auto;
        `;
        
        if (this.options.placeholder) {
            this.editorArea.setAttribute('data-placeholder', this.options.placeholder);
        }
        
        // Obsługa placeholder
        this.editorArea.addEventListener('focus', () => {
            if (this.editorArea.textContent === this.options.placeholder) {
                this.editorArea.textContent = '';
            }
        });
        
        this.editorArea.addEventListener('blur', () => {
            if (this.editorArea.textContent.trim() === '') {
                this.editorArea.textContent = this.options.placeholder;
            }
        });
        
        // Obsługa klawiszy
        this.editorArea.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                e.preventDefault();
                document.execCommand('insertHTML', false, '&nbsp;&nbsp;&nbsp;&nbsp;');
            }
        });
        
        this.editorContainer.appendChild(this.editorArea);
    }
    
    handleAction(action) {
        switch (action) {
            case 'link':
                this.insertLink();
                break;
            case 'image':
                this.insertImage();
                break;
            case 'foreColor':
                this.showColorPicker('foreColor');
                break;
            case 'backColor':
                this.showColorPicker('backColor');
                break;
            case 'pasteAsText':
                this.pasteAsText();
                break;
        }
    }
    
    insertLink() {
        const url = prompt('Wprowadź URL:');
        if (url) {
            const selection = window.getSelection();
            if (selection.toString()) {
                document.execCommand('createLink', false, url);
            } else {
                document.execCommand('insertHTML', false, `<a href="${url}">${url}</a>`);
            }
        }
    }
    
    insertImage() {
        // Stwórz input file
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.style.position = 'absolute';
        input.style.left = '-9999px';
        document.body.appendChild(input);
        
        input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                this.uploadImage(file);
            }
            document.body.removeChild(input);
        });
        
        input.click();
    }
    
    uploadImage(file) {
        const formData = new FormData();
        formData.append('file', file);
        
        fetch('/cms/admin/upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.location) {
                document.execCommand('insertHTML', false, `<img src="${data.location}" alt="" style="max-width: 100%; height: auto;">`);
            } else {
                alert('Błąd podczas przesyłania obrazu');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Błąd podczas przesyłania obrazu');
        });
    }
    
    showColorPicker(command) {
        const input = document.createElement('input');
        input.type = 'color';
        input.style.position = 'absolute';
        input.style.left = '-9999px';
        document.body.appendChild(input);
        
        input.addEventListener('change', () => {
            document.execCommand(command, false, input.value);
            document.body.removeChild(input);
            this.updateToolbar();
        });
        
        input.click();
    }
    
    pasteAsText() {
        this.editorArea.addEventListener('paste', (e) => {
            e.preventDefault();
            const text = e.clipboardData.getData('text/plain');
            document.execCommand('insertText', false, text);
        }, { once: true });
    }
    
    updateToolbar() {
        // Aktualizuj stan przycisków na podstawie aktualnego formatowania
        const buttons = this.toolbar.querySelectorAll('button');
        buttons.forEach(btn => {
            const command = btn.getAttribute('data-command');
            if (command) {
                if (document.queryCommandState(command)) {
                    btn.style.background = 'var(--acc)';
                    btn.style.color = 'white';
                } else {
                    btn.style.background = 'transparent';
                    btn.style.color = 'var(--fg)';
                }
            }
        });
    }
    
    getContent() {
        return this.editorArea.innerHTML;
    }
    
    setContent(content) {
        if (content && content.trim()) {
            this.editorArea.innerHTML = content;
        } else {
            this.editorArea.textContent = this.options.placeholder;
        }
    }
    
    destroy() {
        if (this.editorContainer && this.editorContainer.parentNode) {
            this.editorContainer.parentNode.removeChild(this.editorContainer);
        }
        this.textarea.style.display = 'block';
    }
}

// Funkcja inicjalizacji edytora
function initEditor(selector, mode = 'wysiwyg') {
    const textarea = document.querySelector(selector);
    const button = document.getElementById('mode-toggle');
    
    if (mode === 'html') {
        // Tryb HTML - zwykły textarea
        if (window.currentEditor) {
            window.currentEditor.destroy();
            window.currentEditor = null;
        }
        textarea.style.display = 'block';
        textarea.style.fontFamily = 'monospace';
        textarea.style.fontSize = '14px';
        textarea.style.lineHeight = '1.5';
        button.textContent = 'WYSIWYG';
    } else {
        // Tryb WYSIWYG - własny edytor
        textarea.style.display = 'none';
        window.currentEditor = new SimpleEditor(selector, {
            height: 400,
            placeholder: textarea.placeholder || 'Wpisz treść...'
        });
        button.textContent = 'HTML';
    }
}

// Funkcja do przełączania między trybami
function toggleEditorMode(selector) {
    const textarea = document.querySelector(selector);
    const button = document.getElementById('mode-toggle');
    
    if (textarea.style.display === 'none') {
        // Przełącz na HTML
        if (window.currentEditor) {
            const content = window.currentEditor.getContent();
            textarea.value = content;
            window.currentEditor.destroy();
            window.currentEditor = null;
        }
        textarea.style.display = 'block';
        button.textContent = 'WYSIWYG';
    } else {
        // Przełącz na WYSIWYG
        textarea.style.display = 'none';
        window.currentEditor = new SimpleEditor(selector, {
            height: 400,
            placeholder: textarea.placeholder || 'Wpisz treść...'
        });
        button.textContent = 'HTML';
    }
}
