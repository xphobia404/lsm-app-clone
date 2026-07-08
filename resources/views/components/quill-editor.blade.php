@props([
    'name'        => 'body',
    'value'       => '',
    'placeholder' => 'Tulis isi konten di sini...',
    'formId'      => 'quillForm',
    'height'      => '250px',
])

@once
<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
<style>
    /* ── Toolbar & Editor border radius ── */
    .ql-toolbar.ql-snow {
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem 0.75rem 0 0;
        background: #f8fafc;
    }
    .ql-container.ql-snow {
        border: 1px solid #e2e8f0;
        border-top: none;
        border-radius: 0 0 0.75rem 0.75rem;
        font-size: 0.875rem;
    }
    /* ── Ordered & Bullet list: tampilkan nomor dan dot ── */
    .ql-editor ol,
    .ql-editor ul {
        padding-left: 1.5em;
    }
    .ql-editor ol > li {
        list-style-type: decimal;
    }
    .ql-editor ol > li::before {
        content: none !important;
    }
    .ql-editor ul > li {
        list-style-type: disc;
    }
    .ql-editor ul > li::before {
        content: none !important;
    }
    .ql-editor li.ql-indent-1 { padding-left: 3em; }
    .ql-editor li.ql-indent-2 { padding-left: 4.5em; }
</style>
@endonce

@php $editorId = 'quillEditor_' . Str::random(6); @endphp

<div>
    <div id="{{ $editorId }}" style="min-height: {{ $height }}; background: white;"></div>
    <textarea name="{{ $name }}" id="{{ $editorId }}_input" class="hidden">{{ $value }}</textarea>
</div>

@once
<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
@endonce

<script>
(function () {
    const editorEl = document.getElementById('{{ $editorId }}');
    const inputEl  = document.getElementById('{{ $editorId }}_input');

    const quill = new Quill(editorEl, {
        theme: 'snow',
        placeholder: '{{ $placeholder }}',
        modules: {
            toolbar: [
                [{ header: [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ color: [] }, { background: [] }],
                [{ list: 'ordered' }, { list: 'bullet' }],
                [{ indent: '-1' }, { indent: '+1' }],
                [{ align: [] }],
                ['link', 'blockquote', 'code-block'],
                ['clean']
            ]
        }
    });

    // Load existing value
    const existing = inputEl.value.trim();
    if (existing) quill.clipboard.dangerouslyPasteHTML(existing);

    // Sync ke hidden textarea sebelum form submit
    const form = document.getElementById('{{ $formId }}');
    if (form) {
        form.addEventListener('submit', function () {
            inputEl.value = quill.getSemanticHTML();
        }, { once: false });
    }
})();
</script>
