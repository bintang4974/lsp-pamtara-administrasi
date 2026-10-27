@props([
    'title' => 'Belum ada data',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 px-6 py-10 text-center']) }}>
    <svg class="mb-3 h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9h16.5m-16.5 6.75h16.5M4.5 19.5h15a.75.75 0 00.75-.75V5.25a.75.75 0 00-.75-.75h-15a.75.75 0 00-.75.75v13.5a.75.75 0 00.75.75z" />
    </svg>

    <p class="text-sm font-medium text-gray-900">{{ $title }}</p>

    @if($description)
        <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
    @endif

    @isset($action)
        <div class="mt-4">{{ $action }}</div>
    @endisset
</div>
