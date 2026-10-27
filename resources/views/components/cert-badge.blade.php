@props(['certificationType'])

@php
    $hex = ltrim($certificationType->color_code ?? '#6b7280', '#');
    if (strlen($hex) === 3) {
        $hex = implode('', array_map(fn ($c) => $c . $c, str_split($hex)));
    }
    [$r, $g, $b] = array_map(fn ($part) => hexdec($part), str_split(str_pad($hex, 6, '0'), 2));
    // Luminansi relatif sederhana untuk menentukan warna teks yang kontras.
    $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    $textColor = $luminance > 0.6 ? '#111827' : '#ffffff';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold whitespace-nowrap']) }}
    style="background-color: {{ $certificationType->color_code ?? '#6b7280' }}; color: {{ $textColor }};">
    {{ $certificationType->name }}
</span>
