@props([
    'code',
    'name',
    'services' => [],
    'weight',
    'phone',
])

<div class="mb-2 w-full rounded-xl border border-sky-100 bg-white p-4 shadow-sm">
    <span class="mb-0.5 flex items-center gap-1.5 font-mono text-[11px] tracking-wider text-[#168cff]">
        <i class="fa-solid fa-receipt text-[#168cff]" aria-hidden="true"></i>
        {{ $code }}
    </span>
    <div class="flex flex-wrap items-center gap-2">
        <h4 class="flex items-center gap-1.5 text-base font-bold leading-tight text-slate-900">
            <i class="fa-solid fa-user text-xs text-[#168cff]" aria-hidden="true"></i>
            {{ $name }}
        </h4>
        <p class="flex items-center gap-1 text-sm font-normal text-slate-500">
            <i class="fa-solid fa-shirt text-xs text-[#168cff]" aria-hidden="true"></i>
            {{ implode(', ', $services) }} <span class="mx-0.5">•</span> {{ $weight }}
        </p>
    </div>
    <p class="mt-1 flex items-center gap-1.5 text-xs text-slate-400">
        <i class="fa-solid fa-phone text-[#168cff]" aria-hidden="true"></i>
        {{ $phone }}
    </p>
</div>
