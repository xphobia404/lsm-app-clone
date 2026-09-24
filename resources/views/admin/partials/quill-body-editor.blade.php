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

    #bodyEditor .quill-table-scroll {
        width: 100%;
        max-width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        margin: 0.75rem 0;
        border-radius: 0.75rem;
        border: 1px solid #e2e8f0;
        background-color: #ffffff;
    }

    #bodyEditor .ql-editor table {
        width: auto;
        min-width: 100%;
        table-layout: auto;
        border-collapse: collapse;
        margin: 0;
    }

    #bodyEditor .ql-editor table th,
    #bodyEditor .ql-editor table td {
        border: 1px solid #cbd5e1;
        padding: 0.5rem 0.75rem;
        min-width: 120px;
        vertical-align: top;
    }

    #bodyEditor .ql-editor table th {
        background-color: #f8fafc;
        font-weight: 600;
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

        function wrapQuillTables(root) {
            root.querySelectorAll('table').forEach(function (tbl) {
                if (tbl.parentElement && tbl.parentElement.classList.contains('quill-table-scroll')) {
                    return;
                }

                const wrapper = document.createElement('div');
                wrapper.className = 'quill-table-scroll table-responsive';
                tbl.parentNode.insertBefore(wrapper, tbl);
                wrapper.appendChild(tbl);
            });
        }

        const tableModule = quill.getModule('table');
        const toolbar = quill.getModule('toolbar');
        if (toolbar && tableModule) {
            toolbar.addHandler('table', function () {
                tableModule.insertTable(3, 3);
                wrapQuillTables(quill.root);
            });
        }

        const existing = document.getElementById('bodyInput').value.trim();
        if (existing) {
            quill.clipboard.dangerouslyPasteHTML(existing);
            wrapQuillTables(quill.root);
        }

        let wrapTimer;
        quill.on('text-change', function () {
            clearTimeout(wrapTimer);
            wrapTimer = setTimeout(function () {
                wrapQuillTables(quill.root);
            }, 150);
        });

        const form = document.getElementById(formId);
        form.addEventListener('submit', function () {
            document.getElementById('bodyInput').value = quill.getSemanticHTML();
        });

        return quill;
    };
</script>
@endonce
