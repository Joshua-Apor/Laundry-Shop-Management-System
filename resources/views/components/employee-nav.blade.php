<header class="border-b border-[#d991b0] bg-[#e8a6c2] text-[#5d2943] shadow-sm">
    <div class="mx-auto flex min-h-14 max-w-[1178px] items-center justify-between gap-3 px-3 sm:px-6">
        <a href="{{ route('employee.dashboard') }}" class="flex shrink-0 items-center gap-2">
            <img src="{{ asset('images/logo.png') }}" alt="Laba U logo" class="size-8 rounded-lg object-cover">
            <span class="hidden leading-none sm:block">
                <span class="block text-xs font-extrabold tracking-tight">I Laba U</span>
                <span class="block text-[11px] text-[#7b4860]">Employee Portal</span>
            </span>
        </a>

        <nav class="flex min-w-0 items-center gap-0.5 overflow-x-auto text-[11px] text-[#5d2943] sm:gap-1 sm:text-[13px]" aria-label="Employee navigation">
            <a href="{{ route('employee.dashboard') }}" @class(['whitespace-nowrap rounded-md px-2 py-1.5 transition-colors sm:px-3', 'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('employee.dashboard'), 'hover:bg-[#2f7dcc] hover:text-white' => ! request()->routeIs('employee.dashboard')])>
                <i class="fa-solid fa-chart-line mr-1" aria-hidden="true"></i>Dashboard
            </a>
            <a href="{{ route('orders.index') }}" @class(['whitespace-nowrap rounded-md px-2 py-1.5 transition-colors sm:px-3', 'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('orders.index', 'orders.show'), 'hover:bg-[#2f7dcc] hover:text-white' => ! request()->routeIs('orders.index', 'orders.show')])>
                <i class="fa-solid fa-box mr-1" aria-hidden="true"></i>Orders
            </a>
            <a href="{{ route('orders.create') }}" @class(['whitespace-nowrap rounded-md px-2 py-1.5 font-semibold transition-colors sm:px-3', 'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('orders.create'), 'text-[#5d2943] hover:bg-[#2f7dcc] hover:text-white' => ! request()->routeIs('orders.create')])>
                <i class="fa-solid fa-plus mr-1" aria-hidden="true"></i>New Order
            </a>
            <a href="{{ route('customers.index') }}" @class(['whitespace-nowrap rounded-md px-2 py-1.5 transition-colors sm:px-3', 'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('customers.index'), 'hover:bg-[#2f7dcc] hover:text-white' => ! request()->routeIs('customers.index')])>
                <i class="fa-solid fa-users mr-1" aria-hidden="true"></i>Customers
            </a>
        </nav>

        <div class="flex shrink-0 items-center gap-2">
            <span class="hidden max-w-24 truncate text-xs font-semibold text-[#5d2943] md:block">{{ auth()->user()->name }}</span>
            <span class="grid size-7 place-items-center rounded-full bg-[#168cff] text-[10px] font-bold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="whitespace-nowrap rounded-md border border-[#d991b0] bg-white/40 px-2 py-1.5 text-[11px] font-semibold text-[#5d2943] transition-colors hover:bg-[#2f7dcc] hover:text-white sm:px-3 sm:text-xs">
                    <i class="fa-solid fa-right-from-bracket mr-1" aria-hidden="true"></i>Sign out
                </button>
            </form>
        </div>
    </div>
</header>