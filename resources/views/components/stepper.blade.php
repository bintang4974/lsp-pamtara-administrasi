@props([
    'current' => 1,
    'steps' => ['Sertifikasi', 'TUK', 'Jadwal', 'Data Diri'],
])

<ol class="flex flex-wrap gap-2 text-xs sm:text-sm">
    @foreach($steps as $index => $label)
        @php($number = $index + 1)
        <li class="flex items-center gap-1.5 rounded-full px-3 py-1.5 font-medium {{ match(true) {
            $number < $current => 'bg-green-100 text-green-800',
            $number === $current => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-500',
        } }}">
            <span>{{ $number }}.</span>
            <span>{{ $label }}</span>
        </li>
    @endforeach
</ol>
