@props([
    'label',
    'tone' => 'neutral', // success | warning | danger | info | neutral
])

@php
    $toneClasses = [
        'success' => 'bg-green-100 text-green-800',
        'warning' => 'bg-yellow-100 text-yellow-800',
        'danger' => 'bg-red-100 text-red-800',
        'info' => 'bg-blue-100 text-blue-800',
        'neutral' => 'bg-gray-100 text-gray-800',
    ][$tone] ?? 'bg-gray-100 text-gray-800';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium whitespace-nowrap $toneClasses"]) }}>
    {{ $label }}
</span>
