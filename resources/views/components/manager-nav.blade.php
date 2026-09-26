<header class="border-b border-[#d8e7f5] bg-white text-[#263653] shadow-sm">

    <div class="mx-auto max-w-[1178px] px-3 sm:px-6">

        {{-- TOP ROW --}}
        <div class="flex min-h-14 items-center gap-3">

            {{-- Logo / Brand --}}
            <a href="{{ route('manager.dashboard') }}"
               class="flex min-w-0 flex-1 items-center gap-1.5 sm:flex-none sm:gap-2">

                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="Laba U logo"
                    class="size-7 shrink-0 rounded-md object-cover sm:size-8 sm:rounded-lg"
                >

                <span class="min-w-0 leading-none">
                    <span class="block text-[11px] font-extrabold tracking-tight sm:text-xs">
                        I Laba U
                    </span>

                    <span class="block text-[10px] text-[#66809b] sm:text-[11px]">
                        Manager Portal
                    </span>
                </span>

            </a>


            {{-- DESKTOP NAVIGATION --}}
            <nav
                class="absolute left-1/2 hidden -translate-x-1/2 items-center gap-1 text-[13px] text-[#52647f] sm:flex"
                aria-label="Manager navigation">

                {{-- Dashboard --}}
                <a href="{{ route('manager.dashboard') }}"
                   @class([
                       'shrink-0 whitespace-nowrap rounded-md px-3 py-1.5 transition-colors',
                       'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('manager.dashboard'),
                       'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('manager.dashboard'),
                   ])>
                    <i class="fa-solid fa-chart-line mr-1" aria-hidden="true"></i>
                    Dashboard
                </a>

                {{-- Customer Records --}}
                <a href="{{ route('manager.customer-records') }}"
                   @class([
                       'shrink-0 whitespace-nowrap rounded-md px-3 py-1.5 transition-colors',
                       'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('manager.customer-records'),
                       'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('manager.customer-records'),
                   ])>
                    <i class="fa-solid fa-users mr-1" aria-hidden="true"></i>
                    Customer Records
                </a>

                {{-- Orders & Payments --}}
                <a href="{{ route('manager.orders-payments') }}"
                   @class([
                       'shrink-0 whitespace-nowrap rounded-md px-3 py-1.5 transition-colors',
                       'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('manager.orders-payments'),
                       'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('manager.orders-payments'),
                   ])>
                    <i class="fa-solid fa-receipt mr-1" aria-hidden="true"></i>
                    Orders & Payments
                </a>

                {{-- Sales Reports --}}
                <a href="{{ route('manager.sales-reports') }}"
                   @class([
                       'shrink-0 whitespace-nowrap rounded-md px-3 py-1.5 transition-colors',
                       'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('manager.sales-reports'),
                       'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('manager.sales-reports'),
                   ])>
                    <i class="fa-solid fa-chart-column mr-1" aria-hidden="true"></i>
                    Sales Reports
                </a>

                {{-- Employees --}}
                <a href="{{ route('manager.employees') }}"
                   @class([
                       'shrink-0 whitespace-nowrap rounded-md px-3 py-1.5 transition-colors',
                       'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('manager.employees'),
                       'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('manager.employees'),
                   ])>
                    <i class="fa-solid fa-user-tie mr-1" aria-hidden="true"></i>
                    Employees
                </a>

            </nav>


            {{-- USER / SIGN OUT --}}
            <div class="ml-auto flex shrink-0 items-center gap-1.5 sm:gap-2">

                {{-- Username - desktop only --}}
                <span class="hidden max-w-32 truncate text-xs font-semibold text-[#52647f] lg:block">
                    {{ auth()->user()->name }}
                </span>

                {{-- Avatar --}}
                <span class="grid size-7 shrink-0 place-items-center rounded-full bg-[#8b5cf6] text-[10px] font-bold text-white sm:size-8">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </span>

                {{-- Sign Out --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="whitespace-nowrap rounded-md border border-[#c8dced] bg-white px-2 py-1.5 text-[10px] font-semibold text-[#52647f] transition-colors hover:bg-[#2f7dcc] hover:text-white sm:px-3 sm:text-xs">

                        <i class="fa-solid fa-right-from-bracket mr-1" aria-hidden="true"></i>
                        Sign out

                    </button>
                </form>

            </div>

        </div>


        {{-- MOBILE NAVIGATION --}}
        <nav
            class="grid grid-cols-5 border-t border-[#d8e7f5] py-1 sm:hidden"
            aria-label="Mobile manager navigation">

            {{-- Dashboard --}}
            <a href="{{ route('manager.dashboard') }}"
               @class([
                   'flex min-w-0 items-center justify-center gap-1 rounded-md px-0.5 py-1.5 text-[9px] transition-colors',
                   'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('manager.dashboard'),
                   'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('manager.dashboard'),
               ])>
                <i class="fa-solid fa-chart-line" aria-hidden="true"></i>
                <span>Dashboard</span>
            </a>

            {{-- Customer Records --}}
            <a href="{{ route('manager.customer-records') }}"
               @class([
                   'flex min-w-0 items-center justify-center gap-1 rounded-md px-0.5 py-1.5 text-[9px] transition-colors',
                   'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('manager.customer-records'),
                   'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('manager.customer-records'),
               ])>
                <i class="fa-solid fa-users" aria-hidden="true"></i>
                <span>Customers</span>
            </a>

            {{-- Orders & Payments --}}
            <a href="{{ route('manager.orders-payments') }}"
               @class([
                   'flex min-w-0 items-center justify-center gap-1 rounded-md px-0.5 py-1.5 text-[9px] transition-colors',
                   'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('manager.orders-payments'),
                   'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('manager.orders-payments'),
               ])>
                <i class="fa-solid fa-receipt" aria-hidden="true"></i>
                <span>Orders</span>
            </a>

            {{-- Sales Reports --}}
            <a href="{{ route('manager.sales-reports') }}"
               @class([
                   'flex min-w-0 items-center justify-center gap-1 rounded-md px-0.5 py-1.5 text-[9px] transition-colors',
                   'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('manager.sales-reports'),
                   'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('manager.sales-reports'),
               ])>
                <i class="fa-solid fa-chart-column" aria-hidden="true"></i>
                <span>Reports</span>
            </a>

            {{-- Employees --}}
            <a href="{{ route('manager.employees') }}"
               @class([
                   'flex min-w-0 items-center justify-center gap-1 rounded-md px-0.5 py-1.5 text-[9px] transition-colors',
                   'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('manager.employees'),
                   'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('manager.employees'),
               ])>
                <i class="fa-solid fa-user-tie" aria-hidden="true"></i>
                <span>Employees</span>
            </a>

        </nav>

    </div>

</header>