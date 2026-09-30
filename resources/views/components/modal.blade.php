@props([
    'id',
    'title',
])

<dialog
    id="{{ $id }}"
    data-modal-dialog
    aria-labelledby="{{ $id }}-title"
    {{ $attributes->merge(['class' => 'm-auto max-h-[90vh] w-[min(32rem,calc(100vw-2rem))] overflow-y-auto rounded-2xl border border-sky-100 bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-900/50']) }}
>
    <div class="flex items-start justify-between gap-4 border-b border-sky-100 p-5">
        <h2 id="{{ $id }}-title" class="text-xl font-bold text-slate-900">{{ $title }}</h2>
        <button
            type="button"
            data-modal-close
            aria-label="Close {{ strtolower($title) }}"
            class="grid size-9 shrink-0 place-items-center rounded-lg text-slate-500 transition-colors hover:bg-sky-50 hover:text-slate-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500"
        >
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
    </div>
    <div class="p-5">
        {{ $slot }}
    </div>
</dialog>
