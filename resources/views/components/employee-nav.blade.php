<header class="border-b border-[#ead4dd] bg-[#f8eef2] text-[#5d2943] shadow-sm lg:fixed lg:inset-y-0 lg:left-0 lg:z-40 lg:flex lg:w-[17rem] lg:flex-col lg:border-b-0 lg:border-r">

    <div class="mx-auto max-w-[1536px] px-4 lg:flex lg:h-full lg:w-full lg:max-w-none lg:flex-col lg:px-4 lg:py-6">

        {{-- TOP ROW --}}
        <div class="hidden min-h-14 items-center justify-between gap-3 lg:relative lg:flex lg:min-h-0 lg:flex-col lg:items-stretch lg:justify-start lg:gap-8 lg:flex-1">

            {{-- Logo / Brand --}}
            <a href="{{ route('employee.dashboard') }}"
               class="flex min-w-0 shrink-0 items-center gap-1.5 sm:gap-2">

                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="Laba U logo"
                    class="size-11 shrink-0 rounded-xl object-cover"
                >

                <span class="min-w-0 leading-none">
                    <span class="block text-[15px] font-extrabold tracking-tight">
                        I Laba U
                    </span>

                    <span class="block text-sm text-[#7b4860]">
                        Employee Portal
                    </span>
                </span>

            </a>


            {{-- DESKTOP NAVIGATION --}}
            <nav
                class="absolute left-1/2 hidden -translate-x-1/2 items-center gap-1 text-[15px] lg:static lg:flex lg:translate-x-0 lg:flex-col lg:items-stretch lg:gap-2"
                aria-label="Employee navigation">

                <a href="{{ route('employee.dashboard') }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-3 py-2.5 text-[15px] transition-colors',
                       'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('employee.dashboard'),
                       'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('employee.dashboard'),
                   ])>
                    <i class="fa-solid fa-chart-line w-5 text-center" aria-hidden="true"></i>
                    Dashboard
                </a>

                <a href="{{ route('customers.index') }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-3 py-2.5 text-[15px] transition-colors',
                       'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('customers.index'),
                       'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('customers.index'),
                   ])>
                    <i class="fa-solid fa-users w-5 text-center" aria-hidden="true"></i>
                    Customers
                </a>

                <a href="{{ route('orders.create') }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-3 py-2.5 text-[15px] transition-colors',
                       'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('orders.create'),
                       'text-[#5d2943] hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('orders.create'),
                   ])>
                    <i class="fa-solid fa-plus w-5 text-center" aria-hidden="true"></i>
                    New Order
                </a>

                <a href="{{ route('records.index') }}"
                   @class([
                       'flex items-center gap-3 rounded-lg px-3 py-2.5 text-[15px] transition-colors',
                       'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('records.*'),
                       'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('records.*'),
                   ])>
                    <i class="fa-solid fa-box w-5 text-center" aria-hidden="true"></i>
                    Records
                </a>

            </nav>
        </div>
        <div class="hidden border-t border-[#ead4dd] pt-4 lg:block">
            <div class="mb-3 flex items-center justify-between gap-3 px-2">
                <span class="min-w-0 truncate text-sm font-semibold text-[#5d2943]">{{ auth()->user()->name }}</span>
                <a href="{{ route('profile.edit') }}" aria-label="My profile" title="My profile" class="grid size-12 shrink-0 place-items-center overflow-hidden rounded-full bg-[#168cff] text-xs font-bold text-white">
                    @if (auth()->user()->profile_picture_path)
                        <img src="{{ asset('storage/'.auth()->user()->profile_picture_path) }}" alt="" class="size-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    @endif
                </a>
            </div>
            <button type="button" data-modal-trigger data-modal-target="logout-confirmation" class="flex w-full items-center justify-center gap-2 rounded-lg border border-[#d9b8c6] bg-white/70 px-4 py-3 text-sm font-semibold text-[#5d2943] transition-colors hover:bg-[#2f7dcc] hover:text-white">
                <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                <span>Sign out</span>
            </button>
        </div>
        <details data-mobile-sidebar class="group lg:hidden">
            <summary class="flex min-h-12 cursor-pointer list-none items-center justify-between gap-3 border-t border-[#ead4dd] py-2 text-sm font-semibold text-[#5d2943] [&::-webkit-details-marker]:hidden">
                <span class="flex items-center gap-3">
                    <span class="grid size-9 place-items-center rounded-lg border border-[#d9b8c6] bg-white/70">
                        <i class="fa-solid fa-bars" aria-hidden="true"></i>
                    </span>
                    <span>Menu</span>
                </span>
                <span class="ml-auto flex items-center gap-2">
                    <span class="text-sm font-extrabold">I Laba U</span>
                    <img src="{{ asset('images/logo.png') }}" alt="Laba U logo" class="size-10 rounded-lg object-cover">
                </span>
            </summary>

            <div class="invisible fixed inset-0 z-[80] group-open:visible lg:hidden">
                <button type="button" data-mobile-sidebar-close aria-label="Close menu" class="absolute inset-0 bg-slate-950/50 opacity-0 transition-opacity duration-200 group-open:opacity-100"></button>

                <nav class="absolute inset-y-0 left-0 z-10 flex w-72 max-w-[85vw] -translate-x-full flex-col gap-1 overflow-y-auto border-r border-[#ead4dd] bg-[#f8eef2] p-4 pt-6 text-[15px] text-[#5d2943] shadow-2xl transition-transform duration-200 group-open:translate-x-0" aria-label="Employee navigation">
                    <div class="mb-6 flex items-center gap-2 border-b border-[#ead4dd] pb-4">
                        <a href="{{ route('employee.dashboard') }}" class="flex min-w-0 items-center gap-2">
                            <img src="{{ asset('images/logo.png') }}" alt="Laba U logo" class="size-11 shrink-0 rounded-xl object-cover">
                            <span class="min-w-0">
                                <span class="block text-[15px] font-extrabold">I Laba U</span>
                                <span class="block text-sm text-[#7b4860]">Employee Portal</span>
                            </span>
                        </a>

                    </div>
                    <a href="{{ route('employee.dashboard') }}" @class(['flex items-center gap-3 rounded-lg px-3.5 py-3 text-[15px] transition-colors', 'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('employee.dashboard'), 'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('employee.dashboard')])>
                        <i class="fa-solid fa-chart-line w-5 text-center" aria-hidden="true"></i> Dashboard
                    </a>
                    <a href="{{ route('customers.index') }}" @class(['flex items-center gap-3 rounded-lg px-3.5 py-3 text-[15px] transition-colors', 'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('customers.index'), 'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('customers.index')])>
                        <i class="fa-solid fa-users w-5 text-center" aria-hidden="true"></i> Customers
                    </a>
                    <a href="{{ route('orders.create') }}" @class(['flex items-center gap-3 rounded-lg px-3.5 py-3 text-[15px] transition-colors', 'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('orders.create'), 'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('orders.create')])>
                        <i class="fa-solid fa-plus w-5 text-center" aria-hidden="true"></i> New Order
                    </a>
                    <a href="{{ route('records.index') }}" @class(['flex items-center gap-3 rounded-lg px-3.5 py-3 text-[15px] transition-colors', 'bg-[#2f7dcc] font-bold text-white shadow-sm' => request()->routeIs('records.*'), 'hover:bg-[#2f7dcc] hover:text-white' => !request()->routeIs('records.*')])>
                        <i class="fa-solid fa-box w-5 text-center" aria-hidden="true"></i> Records
                    </a>
                    <div class="mt-auto border-t border-[#ead4dd] pt-4">
                        <div class="mb-3 flex items-center justify-between gap-3 px-2">
                            <span class="min-w-0 truncate text-sm font-semibold text-[#5d2943]">{{ auth()->user()->name }}</span>
                            <a href="{{ route('profile.edit') }}" aria-label="My profile" title="My profile" class="grid size-12 shrink-0 place-items-center overflow-hidden rounded-full bg-[#168cff] text-xs font-bold text-white">
                                @if (auth()->user()->profile_picture_path)
                                    <img src="{{ asset('storage/'.auth()->user()->profile_picture_path) }}" alt="" class="size-full object-cover">
                                @else
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                @endif
                            </a>
                        </div>
                        <button type="button" data-modal-trigger data-modal-target="logout-confirmation" class="flex w-full items-center justify-center gap-2 rounded-lg border border-[#d9b8c6] bg-white/70 px-4 py-3 text-sm font-semibold text-[#5d2943] transition-colors hover:bg-[#2f7dcc] hover:text-white">
                            <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                            <span>Sign out</span>
                        </button>
                    </div>
                </nav>
            </div>
        </details>

    </div>

</header>
