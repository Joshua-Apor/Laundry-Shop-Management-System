<x-layout>
    <div class="space-y-4">

        <div class="flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Employee Management</h1>
                <p class="text-sm text-slate-500">
                    {{ $employees->where('account_status', true)->count() }} active ·
                    {{ $employees->where('account_status', false)->count() }} inactive
                </p>
            </div>

            <button
                type="button"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-[#168cff] px-3 py-2 text-xs font-bold text-white shadow-sm transition-colors hover:bg-[#0878df]"
            >
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Add Employee
            </button>
        </div>

        {{-- Metrics: Always in one row, including mobile --}}
        <div class="grid grid-cols-3 gap-2">
            <x-dashboard.metric-card
                title="Total Staff"
                :value="$employees->count()"
            />

            <x-dashboard.metric-card
                title="Active"
                :value="$employees->where('account_status', true)->count()"
            />

            <x-dashboard.metric-card
                title="Inactive"
                :value="$employees->where('account_status', false)->count()"
            />
        </div>

        {{-- Employee List --}}
        <div class="grid gap-3 md:grid-cols-2">

            @forelse ($employees as $employee)

                @php($isActive = (bool) $employee->account_status)

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

                                <span class="rounded-full bg-sky-50 px-2 py-0.5 text-[10px] font-semibold text-[#168cff]">
                                    <i class="fa-solid fa-circle text-[7px]" aria-hidden="true"></i>
                                    {{ $isActive ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-500">
                                @{{ $employee->username }}
                            </p>

                            <p class="mt-1 text-xs text-slate-400">
                                <i class="fa-solid fa-calendar-plus mr-1" aria-hidden="true"></i>
                                Account holder
                            </p>

                        </div>
                    </div>

                    <div class="mt-4 flex gap-2">

                        <button
                            type="button"
                            class="flex-1 rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-semibold text-[#168cff] transition-colors hover:bg-[#168cff] hover:text-white"
                        >
                            <i class="fa-solid fa-pause mr-1" aria-hidden="true"></i>
                            {{ $isActive ? 'Set Inactive' : 'Set Active' }}
                        </button>

                        <button
                            type="button"
                            class="rounded-lg border border-sky-200 bg-white px-3 py-2 text-xs font-semibold text-[#168cff] transition-colors hover:bg-sky-50"
                        >
                            <i class="fa-solid fa-trash mr-1" aria-hidden="true"></i>
                            Remove
                        </button>

                    </div>

                </article>

            @empty

                <p class="rounded-xl border border-sky-100 bg-white p-6 text-center text-sm text-slate-400 md:col-span-2">
                    No employees found.
                </p>

            @endforelse

        </div>

    </div>
</x-layout>