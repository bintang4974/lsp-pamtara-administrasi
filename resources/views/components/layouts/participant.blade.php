<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'LSP Pamtara') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased">
    <header class="sticky top-0 z-10 border-b border-gray-200 bg-white">
        <div class="mx-auto flex max-w-4xl items-center justify-between px-4 py-3 sm:px-6">
            <a href="{{ route('participant.dashboard') }}" class="text-base font-bold text-blue-700">LSP Pamtara</a>

            <div class="flex items-center gap-3">
                @php($unreadCount = auth()->user()->appNotifications()->whereNull('read_at')->count())
                <a href="{{ route('participant.notifications.index') }}" class="relative min-h-[44px] min-w-[44px] content-center text-center text-gray-700 hover:text-blue-700" aria-label="Notifikasi">
                    &#128276;
                    @if($unreadCount > 0)
                        <span class="absolute -top-1 right-1 inline-flex h-5 min-w-[20px] items-center justify-center rounded-full bg-red-600 px-1 text-xs font-semibold text-white">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('profile.edit') }}" class="min-h-[44px] content-center text-sm text-gray-700 hover:text-blue-700">
                    {{ auth()->user()->name }}
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="min-h-[44px] rounded-md px-3 text-sm font-medium text-gray-700 hover:bg-gray-100">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-4xl px-4 py-6 sm:px-6">
        @if (session('status'))
            <x-alert type="success" class="mb-6">{{ session('status') }}</x-alert>
        @endif
        @if (session('error'))
            <x-alert type="danger" class="mb-6">{{ session('error') }}</x-alert>
        @endif

        {{ $slot }}
    </main>
</body>
</html>
