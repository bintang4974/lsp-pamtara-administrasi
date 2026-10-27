@props([
    'id',
    'title' => 'Konfirmasi',
    'confirmLabel' => 'Ya, lanjutkan',
    'cancelLabel' => 'Batal',
    'formAction' => null,
    'formMethod' => 'POST',
])

<div
    x-data="{ open: false }"
    x-on:open-modal-{{ $id }}.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-cloak
>
    <template x-if="open">
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4" x-on:click.self="open = false">
            <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
                <h2 class="text-base font-semibold text-gray-900">{{ $title }}</h2>

                <div class="mt-2 text-sm text-gray-600">
                    {{ $slot }}
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" x-on:click="open = false"
                        class="min-h-[44px] rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        {{ $cancelLabel }}
                    </button>

                    @if($formAction)
                        <form method="POST" action="{{ $formAction }}">
                            @csrf
                            @if(strtoupper($formMethod) !== 'POST')
                                @method($formMethod)
                            @endif
                            <button type="submit"
                                class="min-h-[44px] rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                                {{ $confirmLabel }}
                            </button>
                        </form>
                    @else
                        <button type="button" x-on:click="open = false; $dispatch('confirmed-{{ $id }}')"
                            class="min-h-[44px] rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                            {{ $confirmLabel }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </template>
</div>
