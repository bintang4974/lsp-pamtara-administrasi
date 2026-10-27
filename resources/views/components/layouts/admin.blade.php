<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'LSP Pamtara') }} &middot; Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 font-sans text-gray-900 antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        <!-- Overlay mobile -->
        <div x-show="sidebarOpen" x-cloak x-on:click="sidebarOpen = false"
             class="fixed inset-0 z-20 bg-black/50 lg:hidden"></div>

        <!-- Sidebar: drawer di mobile, statis di layar besar -->
        <aside
            class="fixed inset-y-0 left-0 z-30 w-64 transform bg-gray-900 text-gray-100 transition-transform duration-200 lg:static lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex h-16 items-center justify-between px-4">
                <span class="text-lg font-bold">LSP Pamtara</span>
                <button type="button" class="min-h-[44px] min-w-[44px] lg:hidden" x-on:click="sidebarOpen = false" aria-label="Tutup menu">
                    &times;
                </button>
            </div>

            <nav class="space-y-1 px-2">
                <a href="{{ route('admin.dashboard') }}"
                   class="block min-h-[44px] content-center rounded-md px-3 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    Dashboard
                </a>

                <p class="mt-4 px-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Master Data</p>

                <a href="{{ route('admin.certification-types.index') }}"
                   class="block min-h-[44px] content-center rounded-md px-3 text-sm font-medium {{ request()->routeIs('admin.certification-types.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    Sertifikasi
                </a>
                <a href="{{ route('admin.tuks.index') }}"
                   class="block min-h-[44px] content-center rounded-md px-3 text-sm font-medium {{ request()->routeIs('admin.tuks.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    TUK
                </a>
                <a href="{{ route('admin.assessors.index') }}"
                   class="block min-h-[44px] content-center rounded-md px-3 text-sm font-medium {{ request()->routeIs('admin.assessors.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    Asesor
                </a>
                <a href="{{ route('admin.document-requirements.index') }}"
                   class="block min-h-[44px] content-center rounded-md px-3 text-sm font-medium {{ request()->routeIs('admin.document-requirements.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    Persyaratan Dokumen
                </a>
                <a href="{{ route('admin.assessment-schedules.index') }}"
                   class="block min-h-[44px] content-center rounded-md px-3 text-sm font-medium {{ request()->routeIs('admin.assessment-schedules.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                    Jadwal Ujikom
                </a>
            </nav>
        </aside>

        <div class="flex-1 lg:ml-0">
            <header class="sticky top-0 z-10 flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 sm:px-6">
                <button type="button" class="min-h-[44px] min-w-[44px] text-gray-600 lg:hidden" x-on:click="sidebarOpen = true" aria-label="Buka menu">
                    &#9776;
                </button>

                <div class="flex items-center gap-3">
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
            </header>

            <main class="px-4 py-6 sm:px-6">
                @if (session('status'))
                    <x-alert type="success" class="mb-6">{{ session('status') }}</x-alert>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
