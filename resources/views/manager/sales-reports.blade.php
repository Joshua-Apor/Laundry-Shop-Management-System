<x-layout>

    <div class="space-y-4">

        <div>
            <h1 class="text-2xl font-bold text-slate-900">Sales &amp; Operational Reports</h1>
            <p class="text-sm text-slate-500">Generate daily, weekly, or monthly summaries</p>
        </div>

        <section class="rounded-xl border border-sky-200 bg-white p-5 shadow-sm sm:p-6">

            <h2 class="text-sm font-bold text-slate-800">Report Configuration</h2>

            <form class="mt-5 space-y-4" method="GET" action="{{ route('manager.sales-reports') }}">

                <fieldset>
                    <legend class="text-[11px] font-bold uppercase tracking-wide text-slate-500">
                        Report Period
                    </legend>

                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach (['Daily' => 'fa-calendar-day', 'Weekly' => 'fa-calendar-week', 'Monthly' => 'fa-calendar'] as $period => $icon)
                            <label class="cursor-pointer">
                                <input
                                    type="radio"
                                    name="period"
                                    value="{{ $period }}"
                                    class="peer sr-only"
                                    @checked(request('period', 'Daily') === $period)
                                >

                                <span class="inline-flex items-center gap-1.5 rounded-lg border border-sky-200 px-3 py-2 text-xs font-semibold text-slate-600 transition-colors peer-checked:border-[#168cff] peer-checked:bg-[#168cff] peer-checked:text-white hover:bg-sky-50">
                                    <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>
                                    {{ $period }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <div>
                    <label
                        for="report-date"
                        class="text-[11px] font-bold uppercase tracking-wide text-slate-500"
                    >
                        Select Date
                    </label>

                    <input
                        id="report-date"
                        type="date"
                        name="date"
                        value="{{ request('date', now()->toDateString()) }}"
                        class="mt-2 block rounded-lg border border-sky-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300"
                    >
                </div>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-lg bg-[#168cff] px-4 py-2.5 text-xs font-bold text-white shadow-sm transition-colors hover:bg-[#0878df]"
                >
                    <i class="fa-solid fa-file-chart-column" aria-hidden="true"></i>
                    Generate Report
                </button>

            </form>

        </section>

    </div>

</x-layout>