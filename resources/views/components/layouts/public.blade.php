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
    <header class="border-b border-gray-200 bg-white">
        <nav class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-3 px-4 py-4 sm:px-6">
            <a href="{{ route('home') }}" class="text-lg font-bold text-blue-700">LSP Pamtara</a>

            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                <a href="{{ route('info.certifications') }}" class="min-h-[44px] content-center hover:text-blue-700">Sertifikasi</a>
                <a href="{{ route('info.tuks') }}" class="min-h-[44px] content-center hover:text-blue-700">TUK</a>
                <a href="{{ route('info.document-requirements') }}" class="min-h-[44px] content-center hover:text-blue-700">Persyaratan</a>
            </div>

            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="min-h-[44px] content-center text-sm font-medium text-gray-700 hover:text-blue-700">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="min-h-[44px] content-center text-sm font-medium text-gray-700 hover:text-blue-700">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex min-h-[44px] items-center rounded-md bg-blue-700 px-4 text-sm font-medium text-white hover:bg-blue-800">
                        Daftar
                    </a>
                @endauth
            </div>
        </nav>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6">
        @if (session('status'))
            <x-alert type="success" class="mb-6">{{ session('status') }}</x-alert>
        @endif

        {{ $slot }}
    </main>

    <footer class="mt-12 border-t border-gray-200 bg-white py-6 text-center text-sm text-gray-500">
        &copy; {{ now()->year }} LSP Pamtara. Seluruh hak cipta dilindungi.
    </footer>
</body>
</html>
