@props([
    'code',
    'name',
    'services' => [],
    'weight',
    'phone',
])

<div class="mb-2 w-full rounded-xl border border-sky-200 bg-white p-4 shadow-sm">
    <span class="mb-0.5 block text-[11px] font-mono tracking-wider text-slate-500">
        {{ $code }}
    </span>
    <div class="flex flex-wrap items-center gap-2">
        <h4 class="text-base font-bold leading-tight text-slate-900">
            {{ $name }}
        </h4>
        <p class="text-sm font-normal text-slate-500">
            {{ implode(', ', $services) }} <span class="mx-0.5">•</span> {{ $weight }}
        </p>
    </div>
    <p class="mt-1 text-xs text-slate-400">
        {{ $phone }}
    </p>
</div>
