@props(['initial' => 'test',
        'name' => 'test',
        'phone' => 'test',
        'orderTotal' => 'test', 
        'priceTotal' => 'test',
        ])

<div class="bg-white rounded-2xl border border-sky-100 p-4 shadow-sm flex items-center justify-between hover:border-sky-200 transition-colors">
    <div class="flex items-center gap-3.5">
        <!-- Avatar Circle -->
        <div class="w-10 h-10 rounded-full bg-[#e92c81] text-white flex items-center justify-center font-bold text-sm shrink-0">
            {{ $initial }}
        </div>
        <!-- Customer Details -->
        <div>
            <h3 class="font-bold text-slate-900 text-sm leading-tight">{{ $name }}</h3>
            <p class="text-xs text-slate-400 mt-0.5">{{ $phone }}</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="text-right">
        <p class="text-sm font-bold text-slate-800">{{ $orderTotal }}</p>
        <p class="text-xs text-slate-400">₱{{ $priceTotal }}</p>
    </div>
</div>