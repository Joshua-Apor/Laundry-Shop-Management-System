<x-layout>
    <div class="space-y-4">

        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Employee Management</h1>
                <p class="text-sm text-slate-500">
                    {{ $employees->where('is_logged_in', true)->count() }} active ·
                    {{ $employees->where('is_logged_in', false)->count() }} inactive
                </p>
            </div>

            <details class="group relative shrink-0" @if ($errors->any()) open @endif>
                <summary class="inline-flex cursor-pointer list-none items-center gap-1.5 rounded-lg bg-[#168cff] px-3 py-2 text-xs font-bold text-white shadow-sm transition-colors hover:bg-[#0878df] [&::-webkit-details-marker]:hidden">
                    <i class="fa-solid fa-plus" aria-hidden="true"></i>
                    Add Employee
                </summary>

                <form method="POST" action="{{ route('manager.employees.store') }}" class="absolute right-0 top-full z-10 mt-3 w-[min(90vw,600px)] rounded-xl border border-sky-200 bg-white p-4 shadow-sm sm:p-5">
                    @csrf
                    <h2 class="text-sm font-bold text-slate-800">Create employee account</h2>
                    <p class="mt-1 text-xs text-slate-500">Set a username and email. A generated password will be sent to the employee.</p>

                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div>
                            <label for="name" class="mb-1 block text-xs font-semibold text-slate-700">Full name</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="255" autocomplete="name" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-[#168cff] focus:ring-2 focus:ring-sky-100">
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="username" class="mb-1 block text-xs font-semibold text-slate-700">Username</label>
                            <input id="username" name="username" type="text" value="{{ old('username') }}" required maxlength="255" autocomplete="username" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-[#168cff] focus:ring-2 focus:ring-sky-100">
                            @error('username')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="mb-1 block text-xs font-semibold text-slate-700">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-[#168cff] focus:ring-2 focus:ring-sky-100">
                            @error('email')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>


                    </div>

                    <div class="mt-4 flex justify-end">
                        <button type="submit" data-loading-button data-loading-message="Creating account..." class="inline-flex h-10 w-48 shrink-0 items-center justify-center gap-2 whitespace-nowrap rounded-lg bg-[#168cff] px-3 text-xs font-bold text-white transition-colors hover:bg-[#0878df] disabled:cursor-not-allowed disabled:opacity-70">
                            <span data-loading-spinner class="hidden size-4 animate-spin rounded-full border-2 border-white/40 border-t-white" aria-hidden="true"></span>
                            <span data-loading-text>
                                <i class="fa-solid fa-user-plus mr-1.5" aria-hidden="true"></i>
                                Create Employee Account
                            </span>
                        </button>
                    </div>
                </form>
            </details>
        </div>

        <div class="grid grid-cols-3 gap-2">
            <x-dashboard.metric-card
                title="Total Staff"
                :value="$employees->count()"
            />

            <x-dashboard.metric-card
                title="Active"
                :value="$employees->where('is_logged_in', true)->count()"
            />

            <x-dashboard.metric-card
                title="Inactive"
                :value="$employees->where('is_logged_in', false)->count()"
            />
        </div>

        {{-- Employee List --}}
        <div class="grid gap-3 md:grid-cols-2">

            @forelse ($employees as $employee)

                @php($isActive = (bool) $employee->is_logged_in)

                <article class="rounded-xl border border-sky-200 bg-white p-4 shadow-sm">

                    <div class="flex items-start gap-3">

                        <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-[#168cff] text-xs font-bold text-white">
                            {{ strtoupper(substr($employee->name, 0, 2)) }}
                        </span>

                        <div class="min-w-0 flex-1">

                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="font-bold text-slate-800">
                                    {{ $employee->name }}
                                </h2>

                                <span @class([
                                    'inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-[10px] font-semibold',
                                    'bg-emerald-50 text-emerald-700' => $isActive,
                                    'bg-slate-100 text-slate-500' => ! $isActive,
                                ])>
                                    <i @class(['fa-solid fa-circle text-[7px]', 'text-emerald-500' => $isActive, 'text-slate-400' => ! $isActive]) aria-hidden="true"></i>
                                    {{ $isActive ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-500">
                                @ {{ $employee->username }}
                            </p>

                            @if ($employee->email)
                                <p class="mt-1 break-all text-xs text-slate-500">{{ $employee->email }}</p>
                            @endif

                            <p class="mt-1 text-xs text-slate-400">
                                <i class="fa-solid fa-calendar-plus mr-1" aria-hidden="true"></i>
                                Account holder
                            </p>

                        </div>
                    </div>

                    <div class="mt-4 flex gap-2">

                        <button type="button" data-modal-trigger data-modal-target="employee-delete-{{ $employee->user_id }}" class="w-full rounded-lg border border-red-200 bg-white px-3 py-2 text-xs font-semibold text-red-600 transition-colors hover:bg-red-50">
                                <i class="fa-solid fa-trash mr-1" aria-hidden="true"></i>
                                Remove
                        </button>

                    </div>

                </article>

                <x-modal :id="'employee-delete-'.$employee->user_id" title="Permanently delete employee?">
                    <p class="text-sm leading-6 text-slate-600">Permanently delete {{ $employee->name }}'s employee account? Their customer records and orders will be kept.</p>
                    <form method="POST" action="{{ route('manager.employees.destroy', $employee) }}" class="mt-5 flex justify-end gap-2">
                        @csrf
                        @method('DELETE')
                        <button type="button" data-modal-close class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-xs font-bold text-white hover:bg-red-700">Delete employee</button>
                    </form>
                </x-modal>

            @empty

                <p class="rounded-xl border border-sky-100 bg-white p-6 text-center text-sm text-slate-400 md:col-span-2">
                    No employees found.
                </p>

            @endforelse

        </div>

    </div>
</x-layout>
