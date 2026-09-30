@props([
    'label',
    'icon',
])

<div class="flex min-w-0 items-start gap-3 rounded-xl border border-sky-100 bg-sky-50/70 p-3">
    <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-white text-[#168cff] shadow-sm">
        <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>
    </span>
    <div class="min-w-0">
        <p class="text-xs font-medium text-slate-500">{{ $label }}</p>
        <p class="mt-1 break-words text-sm font-semibold text-slate-900">{{ $slot->isEmpty() ? 'Not recorded' : $slot }}</p>
    </div>
</div>
