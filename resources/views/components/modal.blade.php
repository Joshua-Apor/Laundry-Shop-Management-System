@props([
    'id',
    'title',
])

<dialog
    id="{{ $id }}"
    data-modal-dialog
    aria-labelledby="{{ $id }}-title"
    {{ $attributes->merge(['class' => 'm-auto max-h-[90vh] w-[min(32rem,calc(100vw-2rem))] overflow-y-auto rounded-xl border border-slate-200 bg-white p-0 text-slate-900 shadow-xl backdrop:bg-slate-900/40']) }}
>
    <div class="flex items-start justify-between gap-4 border-b border-slate-100 p-5">
        <div class="flex items-center gap-2"><span class="size-1.5 rounded-full bg-[#e92c81]" aria-hidden="true"></span><h2 id="{{ $id }}-title" class="text-lg font-semibold text-slate-900">{{ $title }}</h2></div>
        <button
            type="button"
            data-modal-close
            aria-label="Close {{ strtolower($title) }}"
            class="grid size-9 shrink-0 place-items-center rounded-lg text-slate-500 transition-colors hover:bg-sky-50 hover:text-[#168cff] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#168cff]"
        >
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
    </div>
    <div class="p-5">
        {{ $slot }}
    </div>
</dialog>
