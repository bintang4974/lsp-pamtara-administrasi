<x-layouts.participant title="Notifikasi">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-semibold text-gray-900">Notifikasi</h1>
        @if($notifications->whereNull('read_at')->isNotEmpty())
            <form method="POST" action="{{ route('participant.notifications.read-all') }}">
                @csrf
                <button type="submit" class="min-h-[44px] rounded-md border border-gray-300 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Tandai semua dibaca
                </button>
            </form>
        @endif
    </div>

    @if($notifications->isEmpty())
        <div class="mt-6">
            <x-empty-state title="Belum ada notifikasi" />
        </div>
    @else
        <div class="mt-6 space-y-3">
            @foreach($notifications as $notification)
                <div class="flex items-start justify-between gap-3 rounded-lg border bg-white p-4 {{ $notification->isRead() ? 'border-gray-200' : 'border-blue-300 bg-blue-50' }}">
                    <div>
                        <p class="font-medium text-gray-900">{{ $notification->title }}</p>
                        <p class="mt-1 text-sm text-gray-600">{{ $notification->message }}</p>
                        <p class="mt-1 text-xs text-gray-400">{{ $notification->created_at->format('d M Y H:i') }}</p>
                    </div>

                    @unless($notification->isRead())
                        <form method="POST" action="{{ route('participant.notifications.read', $notification) }}">
                            @csrf
                            <button type="submit" class="min-h-[44px] rounded-md px-3 text-xs font-medium text-blue-700 hover:bg-blue-100">
                                Tandai dibaca
                            </button>
                        </form>
                    @endunless
                </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $notifications->links() }}</div>
    @endif
</x-layouts.participant>
