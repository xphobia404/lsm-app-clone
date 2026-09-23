@if($adminPreview ?? false)
    <div class="mb-4 flex flex-wrap items-center justify-between gap-2 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3">
        <p class="text-xs font-semibold text-amber-800">
            Mode preview admin — tampilan seperti user, progress &amp; quiz tidak disimpan.
        </p>
        <a href="{{ $previewExitUrl ?? route('admin.learning-schemas.index') }}"
           class="shrink-0 rounded-full bg-white px-3 py-1.5 text-[11px] font-bold text-amber-700 ring-1 ring-amber-200">
            Keluar preview
        </a>
    </div>
@endif
