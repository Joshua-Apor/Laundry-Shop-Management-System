<header class="border-b border-pink-200 bg-pink-100 text-[#5d2943] shadow-sm
    lg:fixed lg:inset-y-4 lg:left-4 lg:z-40 lg:flex lg:w-[17rem]
    lg:flex-col lg:rounded-3xl lg:border lg:shadow-xl">

    <div class="mx-auto max-w-[1536px] px-4
        lg:flex lg:h-full lg:w-full lg:max-w-none lg:flex-col lg:px-4 lg:py-6">

        {{-- DESKTOP BRAND AND NAVIGATION --}}
        <div class="hidden min-h-14 items-center justify-between gap-3
            lg:flex lg:min-h-0 lg:flex-1 lg:flex-col lg:items-stretch
            lg:justify-start lg:gap-8">

            {{-- Logo / Brand --}}
            <a href="{{ route('manager.dashboard') }}"
                class="flex min-w-0 shrink-0 items-center gap-3 rounded-2xl px-2 py-3 transition hover:bg-white/60">

                <img src="{{ asset('images/logo.png') }}"
                    alt="Laba U logo"
                    class="size-12 shrink-0 rounded-xl object-cover shadow-sm">

                <span class="min-w-0 leading-tight">
                    <span class="block text-base font-extrabold tracking-tight">
                        I Laba U
                    </span>
                    <span class="mt-1 block text-xs font-medium text-[#7b4860]">
                        Manager Portal
                    </span>
                </span>
            </a>

            {{-- Desktop Navigation --}}
            <nav class="flex flex-col gap-2 text-sm"
                aria-label="Manager navigation">

                <p class="mb-1 px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-[#a16a83]">
                    Main Menu
                </p>

                {{-- Dashboard --}}
                <a href="{{ route('manager.dashboard') }}"
                    @class([
                        'flex items-center gap-3 rounded-xl px-3.5 py-3 font-semibold transition-all duration-200',
                        'bg-[#2f7dcc] text-white shadow-md shadow-blue-200' => request()->routeIs('manager.dashboard'),
                        'text-[#5d2943] hover:bg-blue-100 hover:text-[#2f7dcc]' => !request()->routeIs('manager.dashboard'),
                    ])>
                    <i class="fa-solid fa-chart-line w-5 text-center" aria-hidden="true"></i>
                    <span>Dashboard</span>

                    @if (request()->routeIs('manager.dashboard'))
                        <i class="fa-solid fa-chevron-right ml-auto text-xs" aria-hidden="true"></i>
                    @endif
                </a>

                {{-- Customer Records --}}
                <a href="{{ route('manager.customer-records') }}"
                    @class([
                        'flex items-center gap-3 rounded-xl px-3.5 py-3 font-semibold transition-all duration-200',
                        'bg-[#2f7dcc] text-white shadow-md shadow-blue-200' => request()->routeIs('manager.customer-records'),
                        'text-[#5d2943] hover:bg-blue-100 hover:text-[#2f7dcc]' => !request()->routeIs('manager.customer-records'),
                    ])>
                    <i class="fa-solid fa-users w-5 text-center" aria-hidden="true"></i>
                    <span>Customer Records</span>

                    @if (request()->routeIs('manager.customer-records'))
                        <i class="fa-solid fa-chevron-right ml-auto text-xs" aria-hidden="true"></i>
                    @endif
                </a>

                {{-- Records --}}
                <a href="{{ route('manager.orders-payments') }}"
                    @class([
                        'flex items-center gap-3 rounded-xl px-3.5 py-3 font-semibold transition-all duration-200',
                        'bg-[#2f7dcc] text-white shadow-md shadow-blue-200' => request()->routeIs('manager.orders-payments'),
                        'text-[#5d2943] hover:bg-blue-100 hover:text-[#2f7dcc]' => !request()->routeIs('manager.orders-payments'),
                    ])>
                    <i class="fa-solid fa-receipt w-5 text-center" aria-hidden="true"></i>
                    <span>Records</span>

                    @if (request()->routeIs('manager.orders-payments'))
                        <i class="fa-solid fa-chevron-right ml-auto text-xs" aria-hidden="true"></i>
                    @endif
                </a>

                {{-- Services --}}
                <a href="{{ route('manager.services') }}"
                    @class([
                        'flex items-center gap-3 rounded-xl px-3.5 py-3 font-semibold transition-all duration-200',
                        'bg-[#2f7dcc] text-white shadow-md shadow-blue-200' => request()->routeIs('manager.services*'),
                        'text-[#5d2943] hover:bg-blue-100 hover:text-[#2f7dcc]' => !request()->routeIs('manager.services*'),
                    ])>
                    <i class="fa-solid fa-soap w-5 text-center" aria-hidden="true"></i>
                    <span>Products and Services</span>

                    @if (request()->routeIs('manager.services*'))
                        <i class="fa-solid fa-chevron-right ml-auto text-xs" aria-hidden="true"></i>
                    @endif
                </a>

                {{-- Sales Reports --}}
                <a href="{{ route('manager.sales-reports') }}"
                    @class([
                        'flex items-center gap-3 rounded-xl px-3.5 py-3 font-semibold transition-all duration-200',
                        'bg-[#2f7dcc] text-white shadow-md shadow-blue-200' => request()->routeIs('manager.sales-reports'),
                        'text-[#5d2943] hover:bg-blue-100 hover:text-[#2f7dcc]' => !request()->routeIs('manager.sales-reports'),
                    ])>
                    <i class="fa-solid fa-chart-column w-5 text-center" aria-hidden="true"></i>
                    <span>Sales Reports</span>

                    @if (request()->routeIs('manager.sales-reports'))
                        <i class="fa-solid fa-chevron-right ml-auto text-xs" aria-hidden="true"></i>
                    @endif
                </a>

                {{-- Employees --}}
                <a href="{{ route('manager.employees') }}"
                    @class([
                        'flex items-center gap-3 rounded-xl px-3.5 py-3 font-semibold transition-all duration-200',
                        'bg-[#2f7dcc] text-white shadow-md shadow-blue-200' => request()->routeIs('manager.employees'),
                        'text-[#5d2943] hover:bg-blue-100 hover:text-[#2f7dcc]' => !request()->routeIs('manager.employees'),
                    ])>
                    <i class="fa-solid fa-user-tie w-5 text-center" aria-hidden="true"></i>
                    <span>Employees</span>

                    @if (request()->routeIs('manager.employees'))
                        <i class="fa-solid fa-chevron-right ml-auto text-xs" aria-hidden="true"></i>
                    @endif
                </a>
            </nav>
        </div>

        {{-- DESKTOP PROFILE AND SIGN OUT --}}
        <div class="hidden border-t border-pink-200 pt-4 lg:block">

            <a href="{{ route('profile.edit') }}"
                class="mb-4 flex items-center gap-3 rounded-xl p-2 transition hover:bg-white/70">

                <span class="grid size-11 shrink-0 place-items-center overflow-hidden rounded-full
                    border-2 border-white bg-[#2f7dcc] text-sm font-bold text-white shadow-sm">

                    @if (auth()->user()->profile_picture_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_picture_path) }}"
                            alt="Profile picture"
                            class="size-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    @endif
                </span>

                <span class="min-w-0 flex-1">
                    <span class="block truncate text-sm font-bold text-[#5d2943]">
                        {{ auth()->user()->name }}
                    </span>
                    <span class="mt-0.5 block text-xs text-[#9a6a80]">
                        Manager Account
                    </span>
                </span>

                <i class="fa-solid fa-chevron-right text-xs text-[#a16a83]" aria-hidden="true"></i>
            </a>

            <button type="button"
                data-modal-trigger
                data-modal-target="logout-confirmation"
                class="flex w-full items-center justify-center gap-2 rounded-xl
                    border border-[#2f7dcc] bg-[#2f7dcc] px-4 py-3
                    text-sm font-bold text-white shadow-md
                    transition duration-200 hover:bg-[#1d5fa7] hover:shadow-md">

                <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                <span>Sign Out</span>
            </button>
        </div>

        {{-- MOBILE MENU --}}
        <details data-mobile-sidebar class="group lg:hidden">

            <summary class="flex min-h-14 cursor-pointer list-none items-center justify-between gap-3
                border-t border-pink-200 py-2 text-sm font-semibold text-[#5d2943]
                [&::-webkit-details-marker]:hidden">

                <span class="flex items-center gap-3">
                    <span class="grid size-10 place-items-center rounded-xl
                        border border-pink-200 bg-white text-[#2f7dcc] shadow-sm">
                        <i class="fa-solid fa-bars text-base" aria-hidden="true"></i>
                    </span>
                    <span>Menu</span>
                </span>

                <span class="ml-auto flex items-center gap-2">
                    <span class="text-sm font-extrabold">I Laba U</span>
                    <img src="{{ asset('images/logo.png') }}"
                        alt="Laba U logo"
                        class="size-10 rounded-xl object-cover shadow-sm">
                </span>
            </summary>

            {{-- Mobile Sidebar Overlay --}}
            <div class="invisible fixed inset-0 z-[80] group-open:visible lg:hidden">

                <button type="button"
                    data-mobile-sidebar-close
                    aria-label="Close menu"
                    class="absolute inset-0 bg-slate-950/50 opacity-0
                        transition-opacity duration-200 group-open:opacity-100">
                </button>

                {{-- Mobile Sidebar Panel --}}
                <nav class="absolute inset-y-0 left-0 z-10 flex w-72 max-w-[85vw]
                    -translate-x-full flex-col gap-2 overflow-y-auto
                    border-r border-pink-200 bg-pink-100 p-4 pt-6
                    text-[15px] text-[#5d2943] shadow-2xl
                    transition-transform duration-200 group-open:translate-x-0"
                    aria-label="Manager navigation">

                    {{-- Mobile Brand --}}
                    <div class="mb-4 flex items-center gap-3 border-b border-pink-200 pb-5">
                        <a href="{{ route('manager.dashboard') }}"
                            class="flex min-w-0 items-center gap-3">

                            <img src="{{ asset('images/logo.png') }}"
                                alt="Laba U logo"
                                class="size-12 shrink-0 rounded-xl object-cover shadow-sm">

                            <span class="min-w-0">
                                <span class="block text-base font-extrabold">I Laba U</span>
                                <span class="mt-1 block text-xs font-medium text-[#7b4860]">
                                    Manager Portal
                                </span>
                            </span>
                        </a>
                    </div>

                    <p class="mb-1 px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-[#a16a83]">
                        Main Menu
                    </p>

                    {{-- Mobile Dashboard --}}
                    <a href="{{ route('manager.dashboard') }}"
                        @class([
                            'flex items-center gap-3 rounded-xl px-3.5 py-3 font-semibold transition-colors',
                            'bg-[#2f7dcc] text-white shadow-md' => request()->routeIs('manager.dashboard'),
                            'text-[#5d2943] hover:bg-blue-100 hover:text-[#2f7dcc]' => !request()->routeIs('manager.dashboard'),
                        ])>
                        <i class="fa-solid fa-chart-line w-5 text-center" aria-hidden="true"></i>
                        Dashboard
                    </a>

                    {{-- Mobile Customer Records --}}
                    <a href="{{ route('manager.customer-records') }}"
                        @class([
                            'flex items-center gap-3 rounded-xl px-3.5 py-3 font-semibold transition-colors',
                            'bg-[#2f7dcc] text-white shadow-md' => request()->routeIs('manager.customer-records'),
                            'text-[#5d2943] hover:bg-blue-100 hover:text-[#2f7dcc]' => !request()->routeIs('manager.customer-records'),
                        ])>
                        <i class="fa-solid fa-users w-5 text-center" aria-hidden="true"></i>
                        Customer Records
                    </a>

                    {{-- Mobile Records --}}
                    <a href="{{ route('manager.orders-payments') }}"
                        @class([
                            'flex items-center gap-3 rounded-xl px-3.5 py-3 font-semibold transition-colors',
                            'bg-[#2f7dcc] text-white shadow-md' => request()->routeIs('manager.orders-payments'),
                            'text-[#5d2943] hover:bg-blue-100 hover:text-[#2f7dcc]' => !request()->routeIs('manager.orders-payments'),
                        ])>
                        <i class="fa-solid fa-receipt w-5 text-center" aria-hidden="true"></i>
                        Records
                    </a>

                    {{-- Mobile Services --}}
                    <a href="{{ route('manager.services') }}"
                        @class([
                            'flex items-center gap-3 rounded-xl px-3.5 py-3 font-semibold transition-colors',
                            'bg-[#2f7dcc] text-white shadow-md' => request()->routeIs('manager.services*'),
                            'text-[#5d2943] hover:bg-blue-100 hover:text-[#2f7dcc]' => !request()->routeIs('manager.services*'),
                        ])>
                        <i class="fa-solid fa-soap w-5 text-center" aria-hidden="true"></i>
                        Products and Services
                    </a>

                    {{-- Mobile Sales Reports --}}
                    <a href="{{ route('manager.sales-reports') }}"
                        @class([
                            'flex items-center gap-3 rounded-xl px-3.5 py-3 font-semibold transition-colors',
                            'bg-[#2f7dcc] text-white shadow-md' => request()->routeIs('manager.sales-reports'),
                            'text-[#5d2943] hover:bg-blue-100 hover:text-[#2f7dcc]' => !request()->routeIs('manager.sales-reports'),
                        ])>
                        <i class="fa-solid fa-chart-column w-5 text-center" aria-hidden="true"></i>
                        Sales Reports
                    </a>

                    {{-- Mobile Employees --}}
                    <a href="{{ route('manager.employees') }}"
                        @class([
                            'flex items-center gap-3 rounded-xl px-3.5 py-3 font-semibold transition-colors',
                            'bg-[#2f7dcc] text-white shadow-md' => request()->routeIs('manager.employees'),
                            'text-[#5d2943] hover:bg-blue-100 hover:text-[#2f7dcc]' => !request()->routeIs('manager.employees'),
                        ])>
                        <i class="fa-solid fa-user-tie w-5 text-center" aria-hidden="true"></i>
                        Employees
                    </a>

                    {{-- Mobile Profile and Sign Out --}}
                    <div class="mt-auto border-t border-pink-200 pt-4">

                        <a href="{{ route('profile.edit') }}"
                            class="mb-4 flex items-center gap-3 rounded-xl p-2 transition hover:bg-white/70">

                            <span class="grid size-11 shrink-0 place-items-center overflow-hidden rounded-full
                                border-2 border-white bg-[#2f7dcc] text-sm font-bold text-white shadow-sm">

                                @if (auth()->user()->profile_picture_path)
                                    <img src="{{ asset('storage/' . auth()->user()->profile_picture_path) }}"
                                        alt="Profile picture"
                                        class="size-full object-cover">
                                @else
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                @endif
                            </span>

                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-bold text-[#5d2943]">
                                    {{ auth()->user()->name }}
                                </span>
                                <span class="mt-0.5 block text-xs text-[#9a6a80]">
                                    Manager Account
                                </span>
                            </span>

                            <i class="fa-solid fa-chevron-right text-xs text-[#a16a83]" aria-hidden="true"></i>
                        </a>

                        <button type="button"
                            data-modal-trigger
                            data-modal-target="logout-confirmation"
                            class="flex w-full items-center justify-center gap-2 rounded-xl
                                border border-[#2f7dcc] bg-[#2f7dcc] px-4 py-3
                                text-sm font-bold text-white shadow-md
                                transition duration-200 hover:bg-[#1d5fa7] hover:shadow-md">

                            <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                            <span>Sign Out</span>
                        </button>
                    </div>
                </nav>
            </div>
        </details>
    </div>
</header>