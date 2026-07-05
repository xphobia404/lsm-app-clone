@props(['status'])
@php
$map = [
    'completed'   => ['label' => 'Selesai',           'class' => 'bg-green-100 text-green-700'],
    'in_progress' => ['label' => 'Sedang Dipelajari', 'class' => 'bg-yellow-100 text-yellow-700'],
    'not_started' => ['label' => 'Belum Dimulai',     'class' => 'bg-slate-100 text-slate-500'],
];
$item = $map[$status] ?? $map['not_started'];
@endphp
<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $item['class'] }}">
    {{ $item['label'] }}
</span>
