@once
<style>
    #bodyEditor .ql-container.ql-snow {
        border-radius: 0 0 0.75rem 0.75rem;
        border-color: #e2e8f0;
    }

    #bodyEditor .ql-toolbar.ql-snow {
        border-radius: 0.75rem 0.75rem 0 0;
        border-color: #e2e8f0;
    }

    #bodyEditor .ql-editor {
        min-height: 250px;
        overflow-x: auto;
    }

    /* Styling icon tombol table pada Quill Toolbar */
    #bodyEditor .ql-toolbar button.ql-table,
    .ql-toolbar button.ql-table {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    #bodyEditor .ql-toolbar button.ql-table::before,
    .ql-toolbar button.ql-table::before {
        content: "";
        display: inline-block;
        width: 16px;
        height: 16px;
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23374151' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='3' width='18' height='18' rx='2'/%3E%3Cpath d='M3 9h18'/%3E%3Cpath d='M3 15h18'/%3E%3Cpath d='M9 3v18'/%3E%3Cpath d='M15 3v18'/%3E%3C/svg%3E");
    }

    /* Styling Table & Cell di dalam Editor */
    #bodyEditor .ql-editor table,
    .ql-editor table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin: 0.75rem 0 !important;
        table-layout: auto !important;
    }

    #bodyEditor .ql-editor table th,
    #bodyEditor .ql-editor table td,
    .ql-editor table th,
    .ql-editor table td {
        border: 1px solid #cbd5e1 !important;
        padding: 0.5rem 0.75rem !important;
        min-width: 80px !important;
        height: 32px !important;
        vertical-align: top !important;
        box-sizing: border-box !important;
    }

    #bodyEditor .ql-editor table th,
    .ql-editor table th {
        background-color: #f8fafc !important;
        font-weight: 600 !important;
    }
</style>
<script>
    window.initQuillBodyEditor = function (formId) {
        const quill = new Quill('#bodyEditor', {
            theme: 'snow',
            placeholder: 'Tulis isi konten di sini...',
            modules: {
                table: true,
                toolbar: [
                    [{ header: [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ color: [] }, { background: [] }],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    [{ align: [] }],
                    ['link', 'image', 'blockquote', 'code-block', 'table'],
                    ['clean'],
                ],
            },
        });

        // Ensure table toolbar button has visible SVG icon
        const tableBtn = document.querySelector('#bodyEditor .ql-toolbar button.ql-table');
        if (tableBtn && (!tableBtn.innerHTML || !tableBtn.innerHTML.trim())) {
            tableBtn.innerHTML = `<svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M3 15h18"/><path d="M9 3v18"/><path d="M15 3v18"/></svg>`;
        }

        const tableModule = quill.getModule('table');
        const toolbar = quill.getModule('toolbar');
        if (toolbar && tableModule) {
            toolbar.addHandler('table', function () {
                tableModule.insertTable(3, 3);
            });
        }

        const existing = document.getElementById('bodyInput').value.trim();
        if (existing) {
            quill.clipboard.dangerouslyPasteHTML(existing);
        }

        const form = document.getElementById(formId);
        if (form) {
            form.addEventListener('submit', function () {
                document.getElementById('bodyInput').value = quill.getSemanticHTML();
            });
        }

        return quill;
    };
</script>
@endonce
