@props([
    'label',
    'icon',
])

<div class="flex min-w-0 items-start gap-2 rounded-xl border border-sky-100 bg-sky-50/70 p-2 sm:gap-3 sm:p-3">
    <span class="grid size-7 shrink-0 place-items-center rounded-lg bg-white text-xs text-[#168cff] shadow-sm sm:size-9 sm:text-base">
        <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>
    </span>
    <div class="min-w-0 flex-1">
        <p class="break-words text-[11px] font-medium leading-tight text-slate-500 sm:text-xs">{{ $label }}</p>
        <p class="mt-1 break-words text-xs font-semibold leading-tight text-slate-900 sm:text-sm">{{ $slot->isEmpty() ? 'Not recorded' : $slot }}</p>
    </div>
</div>
