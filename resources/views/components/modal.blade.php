@props([
    'id',
    'title',
])

<dialog
    id="{{ $id }}"
    data-modal-dialog
    aria-labelledby="{{ $id }}-title"
    {{ $attributes->merge([
        'class' => 'm-auto max-h-[90vh] w-[min(34rem,calc(100vw-2rem))] overflow-y-auto rounded-2xl border border-[#ead4dd] bg-white p-0 text-[#5d2943] shadow-2xl backdrop:bg-slate-950/40 backdrop:backdrop-blur-[3px]'
    ]) }}
>
    {{-- Modal Header --}}
    <div class="relative border-b border-[#ead4dd] bg-[#f8eef2] px-5 py-5 sm:px-6">
        <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-[#e6007e] via-[#d946a6] to-[#2f7dcc]"></div>

        <div class="flex min-h-10 items-center justify-center px-9">
            <h2
                id="{{ $id }}-title"
                class="text-center text-lg font-bold tracking-tight text-[#5d2943] sm:text-xl"
            >
                {{ $title }}
            </h2>
        </div>

        <button
            type="button"
            data-modal-close
            aria-label="Close {{ strtolower($title) }}"
            class="absolute right-4 top-1/2 grid size-9 -translate-y-1/2 place-items-center rounded-xl border border-[#ead4dd] bg-white text-[#7b4860] transition duration-200 hover:border-[#e6007e] hover:bg-[#e6007e] hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#2f7dcc] sm:right-5"
        >
            <i class="fa-solid fa-xmark text-base" aria-hidden="true"></i>
        </button>
    </div>

    {{-- Modal Content --}}
    <div class="space-y-5 p-5 sm:p-6">
        {{ $slot }}
    </div>
</dialog>