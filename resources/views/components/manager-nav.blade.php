<header class="border-b border-[#ead4dd] bg-[#f8eef2] text-[#5d2943] shadow-sm">

    <div class="mx-auto max-w-[1536px] px-4 sm:px-6 xl:px-10">

        {{-- TOP ROW --}}
        <div class="relative flex min-h-14 items-center justify-between gap-3">

            {{-- Logo / Brand --}}
            <a href="{{ route('manager.dashboard') }}"
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

                    <span class="block text-[10px] text-[#66809b] sm:text-[11px]">
                        Manager Portal
                    </span>
                </span>

            </a>


            {{-- DESKTOP NAVIGATION --}}
            <nav
                class="absolute left-1/2 hidden -translate-x-1/2 items-center gap-1 text-[13px] text-[#52647f] xl:flex"
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

                {{-- Services --}}
                <a href="{{ route('manager.services') }}"
                   @class([
                       'shrink-0 whitespace-nowrap rounded-md px-3 py-1.5 transition-colors',
                       'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('manager.services*'),
                       'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('manager.services*'),
                   ])>
                    <i class="fa-solid fa-soap mr-1" aria-hidden="true"></i>
                    Services
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
                <span class="hidden max-w-32 truncate text-xs font-semibold text-[#52647f] 2xl:block">
                    {{ auth()->user()->name }}
                </span>

                {{-- Avatar --}}
                <a href="{{ route('profile.edit') }}" aria-label="My profile" title="My profile" class="grid size-7 shrink-0 place-items-center rounded-full bg-[#8b5cf6] text-[10px] font-bold text-white transition-opacity hover:opacity-80 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#8b5cf6] sm:size-8">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </a>

                {{-- Sign Out --}}
                <button
                    type="button"
                    data-modal-trigger
                    data-modal-target="logout-confirmation"
                    aria-label="Sign out"
                    title="Sign out"
                    class="grid size-8 shrink-0 place-items-center rounded-md border border-[#d9b8c6] bg-white/70 text-xs font-semibold text-[#5d2943] transition-colors hover:bg-[#2f7dcc] hover:text-white"
                >
                    <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                </button>

            </div>

        </div>


        {{-- MOBILE NAVIGATION --}}
        <details class="group border-t border-[#d8e7f5] xl:hidden">
            <summary class="flex cursor-pointer list-none items-center justify-between py-2 text-xs font-semibold text-[#52647f] [&::-webkit-details-marker]:hidden">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-bars" aria-hidden="true"></i>
                    Menu
                </span>
                <i class="fa-solid fa-chevron-down text-[10px] transition-transform group-open:rotate-180" aria-hidden="true"></i>
            </summary>

            <nav
                class="grid grid-cols-2 gap-1 border-t border-[#d8e7f5] py-2 sm:grid-cols-3"
                aria-label="Mobile manager navigation">

            {{-- Dashboard --}}
            <a href="{{ route('manager.dashboard') }}"
               @class([
                   'flex min-w-0 flex-col items-center justify-center gap-0.5 rounded-md px-1 py-2 text-[10px] transition-colors',
                   'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('manager.dashboard'),
                   'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('manager.dashboard'),
               ])>
                <i class="fa-solid fa-chart-line" aria-hidden="true"></i>
                <span>Dashboard</span>
            </a>

            {{-- Customer Records --}}
            <a href="{{ route('manager.customer-records') }}"
               @class([
                   'flex min-w-0 flex-col items-center justify-center gap-0.5 rounded-md px-1 py-2 text-[10px] transition-colors',
                   'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('manager.customer-records'),
                   'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('manager.customer-records'),
               ])>
                <i class="fa-solid fa-users" aria-hidden="true"></i>
                <span>Customers</span>
            </a>

            {{-- Orders & Payments --}}
            <a href="{{ route('manager.orders-payments') }}"
               @class([
                   'flex min-w-0 flex-col items-center justify-center gap-0.5 rounded-md px-1 py-2 text-[10px] transition-colors',
                   'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('manager.orders-payments'),
                   'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('manager.orders-payments'),
               ])>
                <i class="fa-solid fa-receipt" aria-hidden="true"></i>
                <span>Orders</span>
            </a>

            {{-- Services --}}
            <a href="{{ route('manager.services') }}"
               @class([
                   'flex min-w-0 flex-col items-center justify-center gap-0.5 rounded-md px-1 py-2 text-[10px] transition-colors',
                   'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('manager.services*'),
                   'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('manager.services*'),
               ])>
                <i class="fa-solid fa-soap" aria-hidden="true"></i>
                <span>Services</span>
            </a>

            {{-- Sales Reports --}}
            <a href="{{ route('manager.sales-reports') }}"
               @class([
                   'flex min-w-0 flex-col items-center justify-center gap-0.5 rounded-md px-1 py-2 text-[10px] transition-colors',
                   'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('manager.sales-reports'),
                   'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('manager.sales-reports'),
               ])>
                <i class="fa-solid fa-chart-column" aria-hidden="true"></i>
                <span>Reports</span>
            </a>

            {{-- Employees --}}
            <a href="{{ route('manager.employees') }}"
               @class([
                   'flex min-w-0 flex-col items-center justify-center gap-0.5 rounded-md px-1 py-2 text-[10px] transition-colors',
                   'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('manager.employees'),
                   'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('manager.employees'),
               ])>
                <i class="fa-solid fa-user-tie" aria-hidden="true"></i>
                <span>Employees</span>
            </a>

            </nav>
        </details>

    </div>

</header>
