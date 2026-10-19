<x-layout>
    <div class="space-y-4">

        @error('employee')
            <div role="alert" class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                {{ $message }}
            </div>
        @enderror

        {{-- Page Header --}}
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <h1 class="text-xl font-bold text-slate-900 sm:text-2xl">
                    Employee Management
                </h1>

                <p class="text-xs text-slate-500 sm:text-sm">
                    {{ $employees->where('is_logged_in', true)->count() }} active
                    ·
                    {{ $employees->where('is_logged_in', false)->count() }} inactive
                </p>
            </div>

            {{-- Add Employee --}}
            <details class="group relative shrink-0"
                @if ($errors->hasAny(['name', 'username', 'email'])) open @endif>

                <summary
                    class="inline-flex cursor-pointer list-none items-center gap-1.5 rounded-lg bg-[#168cff] px-3 py-2 text-xs font-bold text-white shadow-sm transition-colors hover:bg-[#0878df] [&::-webkit-details-marker]:hidden">
                    <i class="fa-solid fa-plus" aria-hidden="true"></i>
                    <span>Add Employee</span>
                </summary>

                <form method="POST"
                    action="{{ route('manager.employees.store') }}"
                    class="absolute right-0 top-full z-20 mt-3 w-[min(90vw,600px)] rounded-xl border border-sky-200 bg-white p-4 shadow-lg sm:p-5">

                    @csrf

                    <h2 class="text-sm font-bold text-slate-800">
                        Create employee account
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Set a username and email. A generated password will be sent to the employee.
                    </p>

                    <div class="mt-4 grid gap-3 sm:grid-cols-2">

                        {{-- Full Name --}}
                        <div class="min-w-0">
                            <label for="name"
                                class="mb-1 block text-xs font-semibold text-slate-700">
                                Full name
                            </label>

                            <input id="name" name="name" type="text"
                                value="{{ old('name') }}" required maxlength="255"
                                autocomplete="name"
                                class="w-full min-w-0 rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-[#168cff] focus:ring-2 focus:ring-sky-100">

                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Username --}}
                        <div class="min-w-0">
                            <label for="username"
                                class="mb-1 block text-xs font-semibold text-slate-700">
                                Username
                            </label>

                            <input id="username" name="username" type="text"
                                value="{{ old('username') }}" required maxlength="255"
                                autocomplete="username"
                                class="w-full min-w-0 rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-[#168cff] focus:ring-2 focus:ring-sky-100">

                            @error('username')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="min-w-0 sm:col-span-2">
                            <label for="email"
                                class="mb-1 block text-xs font-semibold text-slate-700">
                                Email
                            </label>

                            <input id="email" name="email" type="email"
                                value="{{ old('email') }}" required maxlength="255"
                                autocomplete="email"
                                class="w-full min-w-0 rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-[#168cff] focus:ring-2 focus:ring-sky-100">

                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    <div class="mt-4 flex justify-end">
                        <button type="submit"
                            data-loading-button
                            data-loading-message="Creating account..."
                            class="inline-flex h-10 w-48 shrink-0 items-center justify-center gap-2 whitespace-nowrap rounded-lg bg-[#168cff] px-3 text-xs font-bold text-white transition-colors hover:bg-[#0878df] disabled:cursor-not-allowed disabled:opacity-70">

                            <span data-loading-spinner
                                class="hidden size-4 animate-spin rounded-full border-2 border-white/40 border-t-white"
                                aria-hidden="true"></span>

                            <span data-loading-text>
                                <i class="fa-solid fa-user-plus mr-1.5"
                                    aria-hidden="true"></i>
                                Create Employee Account
                            </span>
                        </button>
                    </div>

                </form>
            </details>
        </div>

        {{-- Summary Metric Cards --}}
        <div class="grid grid-cols-3 gap-2">
            <x-dashboard.metric-card
                title="Total Staff"
                :entry-animation="true"
                :value="$employees->count()"
            />

            <x-dashboard.metric-card
                title="Active"
                :entry-animation="true"
                :value="$employees->where('is_logged_in', true)->count()"
            />

            <x-dashboard.metric-card
                title="Inactive"
                :entry-animation="true"
                :value="$employees->where('is_logged_in', false)->count()"
            />
        </div>

        <form method="GET" action="{{ route('manager.employees') }}" role="search">
            <label for="employee-search" class="sr-only">Search employees</label>
            <div class="flex gap-2">
                <div class="relative min-w-0 flex-1">
                    <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"
                        aria-hidden="true"></i>
                    <input id="employee-search" name="search" type="search"
                        value="{{ $search }}" placeholder="Search by name, username, or email"
                        class="w-full rounded-lg border border-slate-200 bg-white py-2 pl-9 pr-3 text-sm outline-none focus:border-[#168cff] focus:ring-2 focus:ring-sky-100">
                </div>
                <button type="submit"
                    class="shrink-0 rounded-lg bg-[#168cff] px-4 py-2 text-xs font-bold text-white transition-colors hover:bg-[#0878df]">
                    Search
                </button>
                @if ($search !== '')
                    <a href="{{ route('manager.employees') }}"
                        class="inline-flex shrink-0 items-center rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition-colors hover:bg-slate-50">
                        Clear
                    </a>
                @endif
            </div>
        </form>

        {{-- Employee List --}}
        <div class="grid grid-cols-1 gap-3" data-manager-employee-list>

            @forelse ($employees as $employee)

                @php($isActive = (bool) $employee->is_logged_in)
                @php($hasAccess = (bool) $employee->account_status)

                {{-- Fixed Horizontal Employee Card --}}
                <article
                    class="card-entry-animation flex w-full min-w-0 flex-nowrap items-center gap-2 rounded-xl border border-sky-200 bg-white p-2.5 shadow-sm sm:gap-4 sm:p-4">

                    {{-- Employee Information --}}
                    <div class="flex min-w-0 flex-1 items-center gap-2 sm:gap-3">

                        {{-- Profile Picture --}}
                        <span
                            class="relative grid size-8 shrink-0 place-items-center overflow-visible rounded-full text-[10px] font-bold text-white sm:size-10 sm:text-xs">

                            <span class="grid size-full place-items-center overflow-hidden rounded-full bg-[#168cff]">
                                @if ($employee->profile_picture_path)
                                    <img
                                        src="{{ asset('storage/' . $employee->profile_picture_path) }}"
                                        alt="{{ $employee->name }}"
                                        class="size-full object-cover">
                                @else
                                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                                @endif
                            </span>

                            <span
                                role="img"
                                aria-label="{{ $isActive ? 'Online' : 'Offline' }}"
                                title="{{ $isActive ? 'Online' : 'Offline' }}"
                                @class([
                                    'absolute -right-0.5 -top-0.5 size-3 rounded-full border-2 border-white',
                                    'bg-emerald-500' => $isActive,
                                    'bg-slate-400' => ! $isActive,
                                ])></span>
                        </span>

                        {{-- Employee Details --}}
                        <div class="min-w-0 flex-1">

                            {{-- Name and Status Badges --}}
                            <div class="flex min-w-0 flex-wrap items-center gap-1.5">

                                <h2 class="min-w-0 break-words text-xs font-bold text-slate-800 sm:text-sm">
                                    {{ $employee->name }}
                                </h2>

                                {{-- Account Access Status --}}
                                <span @class([
                                    'inline-flex shrink-0 items-center gap-1 rounded-full px-1.5 py-0.5 text-[9px] font-semibold sm:px-2 sm:text-[10px]',
                                    'bg-emerald-50 text-emerald-700' => $hasAccess,
                                    'bg-red-50 text-red-700' => ! $hasAccess,
                                ])>
                                    <i @class([
                                        'fa-solid fa-circle text-[6px]',
                                        'text-emerald-500' => $hasAccess,
                                        'text-red-500' => ! $hasAccess,
                                    ]) aria-hidden="true"></i>

                                    {{ $hasAccess ? 'Access enabled' : 'Access revoked' }}
                                </span>

                            </div>

                            {{-- Username --}}
                            <p class="mt-0.5 break-words text-[10px] text-slate-500 sm:text-xs">
                                @ {{ $employee->username }}
                            </p>

                            {{-- Email --}}
                            @if ($employee->email)
                                <p class="mt-1 break-all text-[10px] text-slate-500 sm:text-xs">
                                    {{ $employee->email }}
                                </p>
                            @endif

                            {{-- Account Information --}}
                            <p class="mt-1 text-[10px] text-slate-400 sm:text-xs">
                                <i class="fa-solid fa-calendar-plus mr-1"
                                    aria-hidden="true"></i>
                                Account holder
                            </p>

                        </div>
                    </div>

                    {{-- Fixed Action Button Column --}}
                    <div class="flex w-[clamp(6.5rem,24%,10rem)] shrink-0 flex-col gap-1.5 sm:gap-2">

                        {{-- Revoke / Restore Access --}}
                        @if ($hasAccess)
                            <button type="button"
                                data-modal-trigger
                                data-modal-target="employee-revoke-{{ $employee->user_id }}"
                                class="w-full rounded-md border border-amber-200 bg-white px-1.5 py-2 text-[9px] font-semibold leading-tight text-amber-700 transition-colors hover:bg-amber-50 sm:rounded-lg sm:px-3 sm:text-xs">
                                <i class="fa-solid fa-user-slash mr-0.5 sm:mr-1" aria-hidden="true"></i>
                                Revoke Access
                            </button>
                        @else
                            <form method="POST"
                                action="{{ route('manager.employees.access.update', $employee) }}"
                                class="w-full">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="account_status" value="1">
                                <button type="submit"
                                    class="w-full rounded-md border border-emerald-200 bg-white px-1.5 py-2 text-[9px] font-semibold leading-tight text-emerald-700 transition-colors hover:bg-emerald-50 sm:rounded-lg sm:px-3 sm:text-xs">
                                    <i class="fa-solid fa-user-check mr-0.5 sm:mr-1" aria-hidden="true"></i>
                                    Restore Access
                                </button>
                            </form>
                        @endif

                        {{-- Remove Employee --}}
                        <button type="button"
                            data-modal-trigger
                            data-modal-target="employee-delete-{{ $employee->user_id }}"
                            class="w-full rounded-md border border-red-200 bg-white px-1.5 py-2 text-[9px] font-semibold leading-tight text-red-600 transition-colors hover:bg-red-50 sm:rounded-lg sm:px-3 sm:text-xs">

                            <i class="fa-solid fa-trash mr-0.5 sm:mr-1"
                                aria-hidden="true"></i>

                            Remove
                        </button>

                    </div>
                </article>

                @if ($hasAccess)
                    <x-modal :id="'employee-revoke-'.$employee->user_id" title="Revoke employee access?">
                        <div class="space-y-5">
                            <p class="text-sm leading-6 text-slate-600">
                                Revoke sign-in access for <strong class="text-slate-900">{{ $employee->name }}</strong>? They will no longer be able to access their employee account until access is restored.
                            </p>
                            <form method="POST" action="{{ route('manager.employees.access.update', $employee) }}" class="flex justify-end gap-2">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="account_status" value="0">
                                <button type="button" data-modal-close class="rounded-lg border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-50">Cancel</button>
                                <button type="submit" class="rounded-lg bg-amber-600 px-4 py-2.5 text-xs font-bold text-white transition-colors hover:bg-amber-700">Revoke Access</button>
                            </form>
                        </div>
                    </x-modal>
                @endif

                {{-- Delete Confirmation Modal --}}
                <x-modal
                    :id="'employee-delete-' . $employee->user_id"
                    title="Permanently delete employee?">

                    <p class="text-sm leading-6 text-slate-600">
                        This action is irreversible. To confirm deletion, please type
                        the full name <strong>{{ $employee->name }}</strong> below.
                        Employees with order history cannot be permanently deleted. Revoke access to prevent sign-in while keeping their records.
                    </p>

                    <form method="POST"
                        action="{{ route('manager.employees.destroy', $employee) }}"
                        data-name-confirmation-form
                        data-confirmation-name="{{ $employee->name }}"
                        class="mt-4 space-y-4">

                        @csrf
                        @method('DELETE')

                        <label
                            for="employee-delete-name-{{ $employee->user_id }}"
                            class="block text-xs font-semibold text-slate-700">
                            Employee name
                        </label>

                        <input
                            id="employee-delete-name-{{ $employee->user_id }}"
                            name="confirmation_name"
                            type="text"
                            autocomplete="off"
                            data-name-confirmation-input
                            aria-describedby="employee-delete-help-{{ $employee->user_id }}"
                            class="-mt-3 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none transition-colors focus:ring-2 focus:ring-sky-200">

                        <p
                            id="employee-delete-help-{{ $employee->user_id }}"
                            class="-mt-3 text-xs text-slate-500">
                            Enter the name exactly as shown above.
                        </p>

                        <div class="flex justify-end gap-2">

                            <button type="button"
                                data-modal-close
                                class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                Cancel
                            </button>

                            <button type="submit"
                                disabled
                                data-name-confirmation-submit
                                class="cursor-not-allowed rounded-lg bg-red-600 px-4 py-2 text-xs font-bold text-white opacity-50 transition-opacity">
                                Delete Permanently
                            </button>

                        </div>
                    </form>
                </x-modal>

            @empty

                <p class="rounded-xl border border-sky-100 bg-white p-6 text-center text-sm text-slate-400">
                    No employees found.
                </p>

            @endforelse

        </div>
    </div>
</x-layout>
