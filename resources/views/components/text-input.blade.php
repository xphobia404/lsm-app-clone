@props(['disabled' => false])

<input
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge([
        'class' => 'block w-full rounded-2xl border-slate-300 px-4 py-3 text-sm shadow-sm
                    focus:border-blue-600 focus:ring-blue-600
                    disabled:opacity-50 disabled:cursor-not-allowed'
    ]) !!}
/>
