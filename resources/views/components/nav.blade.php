<header class="border-b border-[#dfdbd3] bg-white">

    <div class="mx-auto flex h-12 max-w-[1178px] items-center justify-between px-4 sm:px-6">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <span class="grid size-7 place-items-center rounded-md bg-[#e91e8c] text-[10px] font-extrabold text-white">ILU</span>
            <span class="leading-none">
                <span class="block text-xs font-extrabold tracking-tight">I Laba U</span>
                <span class="block text-[11px] text-[#725f52]">Staff Portal</span>
            </span>
        </a>
        <nav class="hidden items-center gap-1 text-[13px] text-[#705b4c] md:flex" aria-label="Main navigation">
            <a href="{{ route('dashboard') }}" @class([ 'rounded-md px-3 py-1.5' , 'bg-[#e91e8c] font-bold text-white'=> request()->routeIs('dashboard'),
                'hover:bg-[#f4f1ec]' => ! request()->routeIs('dashboard')])>Dashboard</a>
            <a href="{{ route('orders.index') }}" @class([ 'rounded-md px-3 py-1.5' , 'bg-[#e91e8c] font-bold text-white'=> request()->routeIs('orders.*'),
                'hover:bg-[#f4f1ec]' => ! request()->routeIs('orders.*')])>Orders</a>
            <a href="{{ route('customers.index') }}" @class([ 'rounded-md px-3 py-1.5' , 'bg-[#e91e8c] font-bold text-white'=> request()->routeIs('customers.index'),
                'hover:bg-[#f4f1ec]' => ! request()->routeIs('customers.index')])>Customers</a>
        </nav>
        <div class="flex items-center gap-2">
            <a href="/orders">
                <span class="hidden rounded-full border border-[#f3d46a] bg-[#fff5cf] px-3 py-1 text-[11px] font-medium text-[#a34507] sm:inline-flex"><span class="mr-1 text-[#f0a800]">•</span> notification part</span>
            </a>
            <span class="grid size-7 place-items-center rounded-full bg-[#d8880d] text-[11px] font-bold text-white"></span>
        </div>
    </div>

</header>