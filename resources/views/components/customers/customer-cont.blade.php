@props([
    'initial' => '',
    'name' => '',
    'phone' => '',
    'orderTotal' => 0,
    'priceTotal' => '0.00',
])

<div class="flex items-center justify-between rounded-2xl border border-sky-200 bg-white p-4 shadow-sm transition-colors hover:border-sky-300">
    <div class="flex items-center gap-3.5">
        <div class="grid size-10 shrink-0 place-items-center rounded-full bg-[#168cff] text-sm font-bold text-white">{{ $initial }}</div>
        <div>
            <h2 class="text-sm font-bold leading-tight text-slate-900">{{ $name }}</h2>
            <p class="mt-0.5 text-xs text-slate-400">{{ $phone }}</p>
        </div>
    </div>
    <div class="text-right"><p class="text-sm font-bold text-slate-800">{{ $orderTotal }} order{{ $orderTotal == 1 ? '' : 's' }}</p><p class="text-xs text-slate-400">₱{{ $priceTotal }} total</p></div>
</div>
