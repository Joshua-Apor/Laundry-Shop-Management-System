<header class="border-b border-[#ead4dd] bg-[#f8eef2] text-[#5d2943] shadow-sm">

    <div class="mx-auto max-w-[1536px] px-4 sm:px-6 xl:px-10">

        {{-- TOP ROW --}}
        <div class="relative flex min-h-14 items-center justify-between gap-3">

            {{-- Logo / Brand --}}
            <a href="{{ route('employee.dashboard') }}"
               class="flex min-w-0 shrink-0 items-center gap-1.5 sm:gap-2">

                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="Laba U logo"
                    class="size-7 shrink-0 rounded-md object-cover sm:size-8 sm:rounded-lg"
                >

                <span class="min-w-0 leading-none">
                    <span class="block text-[11px] font-extrabold tracking-tight sm:text-xs">
                        I Laba U
                    </span>

                    <span class="block text-[10px] text-[#7b4860] sm:text-[11px]">
                        Employee Portal
                    </span>
                </span>

            </a>


            {{-- DESKTOP NAVIGATION --}}
            <nav
                class="absolute left-1/2 hidden -translate-x-1/2 items-center gap-1 text-[13px] lg:flex"
                aria-label="Employee navigation">

                <a href="{{ route('employee.dashboard') }}"
                   @class([
                       'shrink-0 whitespace-nowrap rounded-md px-3 py-1.5 transition-colors',
                       'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('employee.dashboard'),
                       'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('employee.dashboard'),
                   ])>
                    <i class="fa-solid fa-chart-line mr-1" aria-hidden="true"></i>
                    Dashboard
                </a>

                <a href="{{ route('customers.index') }}"
                   @class([
                       'shrink-0 whitespace-nowrap rounded-md px-3 py-1.5 transition-colors',
                       'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('customers.index'),
                       'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('customers.index'),
                   ])>
                    <i class="fa-solid fa-users mr-1" aria-hidden="true"></i>
                    Customers
                </a>

                <a href="{{ route('orders.create') }}"
                   @class([
                       'shrink-0 whitespace-nowrap rounded-md px-3 py-1.5 transition-colors',
                       'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('orders.create'),
                       'text-[#5d2943] hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('orders.create'),
                   ])>
                    <i class="fa-solid fa-plus mr-1" aria-hidden="true"></i>
                    New Order
                </a>

                <a href="{{ route('records.index') }}"
                   @class([
                       'shrink-0 whitespace-nowrap rounded-md px-3 py-1.5 transition-colors',
                       'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('records.*'),
                       'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('records.*'),
                   ])>
                    <i class="fa-solid fa-box mr-1" aria-hidden="true"></i>
                    Records
                </a>

            </nav>


            {{-- USER / SIGN OUT --}}
            <div class="ml-auto flex shrink-0 items-center gap-1.5 sm:gap-2">

                {{-- Username - desktop only --}}
                <span class="hidden max-w-32 truncate text-xs font-semibold text-[#5d2943] lg:block">
                    {{ auth()->user()->name }}
                </span>

                {{-- Avatar --}}
                <span class="grid size-7 shrink-0 place-items-center rounded-full bg-[#168cff] text-[10px] font-bold text-white sm:size-8">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </span>

                {{-- Sign Out --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        data-loading-button
                        data-loading-message="Signing out..."
                        class="inline-flex h-8 w-32 items-center justify-center gap-1 whitespace-nowrap rounded-md border border-[#d9b8c6] bg-white/70 px-2 text-[10px] font-semibold text-[#5d2943] transition-colors hover:bg-[#2f7dcc] hover:text-white disabled:cursor-not-allowed disabled:opacity-70 sm:w-36 sm:text-xs"
                    >
                        <span data-loading-spinner class="hidden size-4 animate-spin rounded-full border-2 border-[#d9b8c6] border-t-[#5d2943]" aria-hidden="true"></span>

                        <i class="fa-solid fa-right-from-bracket mr-1" aria-hidden="true"></i>

                        <span data-loading-text>Sign out</span>
                    </button>
                </form>

            </div>

        </div>


        {{-- MOBILE NAVIGATION --}}
        <nav
            class="grid grid-cols-4 border-t border-[#ead4dd] py-1 lg:hidden"
            aria-label="Mobile employee navigation">

            <a href="{{ route('employee.dashboard') }}"
               @class([
                   'flex min-w-0 items-center justify-center gap-1 rounded-md px-1 py-1.5 text-[10px] transition-colors',
                   'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('employee.dashboard'),
                   'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('employee.dashboard'),
               ])>
                <i class="fa-solid fa-chart-line" aria-hidden="true"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('customers.index') }}"
               @class([
                   'flex min-w-0 items-center justify-center gap-1 rounded-md px-1 py-1.5 text-[10px] transition-colors',
                   'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('customers.index'),
                   'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('customers.index'),
               ])>
                <i class="fa-solid fa-users" aria-hidden="true"></i>
                <span>Customers</span>
            </a>

            <a href="{{ route('orders.create') }}"
               @class([
                   'flex min-w-0 items-center justify-center gap-1 rounded-md px-1 py-1.5 text-[10px] transition-colors',
                   'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('orders.create'),
                   'text-[#5d2943] hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('orders.create'),
               ])>
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                <span>New Order</span>
            </a>

            <a href="{{ route('records.index') }}"
               @class([
                   'flex min-w-0 items-center justify-center gap-1 rounded-md px-1 py-1.5 text-[10px] transition-colors',
                   'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('records.*'),
                   'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('records.*'),
               ])>
                <i class="fa-solid fa-box" aria-hidden="true"></i>
                <span>Records</span>
            </a>

        </nav>

    </div>

</header>
