<x-layout>
    <div class="mx-auto max-w-7xl space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Sales Reports</h1>
            <p class="text-sm text-slate-500">Choose a day, week, month, or custom date range to review and download sales.</p>
        </div>

        <section class="card-entry-animation rounded-xl border border-sky-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-sm font-bold text-slate-800">Report configuration</h2>
            <form id="sales-report-filters" class="mt-5 flex flex-col gap-5 sm:flex-row sm:flex-wrap sm:items-end" method="GET" action="{{ route('manager.sales-reports') }}">
                <fieldset class="w-full">
                    <legend class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Report period</legend>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach (['Daily' => 'fa-calendar-day', 'Weekly' => 'fa-calendar-week', 'Monthly' => 'fa-calendar', 'Custom' => 'fa-calendar-days'] as $periodOption => $icon)
                            <label class="cursor-pointer">
                                <input type="radio" name="period" value="{{ $periodOption }}" class="peer sr-only" @checked($period === $periodOption)>
                                <span class="inline-flex items-center gap-1.5 rounded-lg border border-sky-200 px-3 py-2 text-xs font-semibold text-slate-600 transition-colors hover:bg-sky-50 peer-checked:border-[#168cff] peer-checked:bg-[#168cff] peer-checked:text-white">
                                    <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>
                                    {{ $periodOption }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('period')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </fieldset>

                <div data-period-fields="Daily" @if ($period !== 'Daily') hidden @endif>
                    <label for="report-date" class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Report date</label>
                    <input id="report-date" type="date" name="date" value="{{ $date }}" @if ($period === 'Daily') required @endif class="mt-2 block rounded-lg border border-sky-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300">
                    @error('date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div data-period-fields="Weekly" @if ($period !== 'Weekly') hidden @endif>
                    <label for="report-week-start" class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Week starting</label>
                    <input id="report-week-start" type="date" name="start_date" value="{{ $startDate }}" @if ($period === 'Weekly') required @endif class="mt-2 block rounded-lg border border-sky-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300">
                    <p class="mt-2 text-xs text-slate-600">Ends <span id="report-week-end" class="font-semibold text-slate-800">—</span></p>
                    @error('start_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div data-period-fields="Monthly" @if ($period !== 'Monthly') hidden @endif>
                    <label for="report-month" class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Month and year</label>
                    <div class="mt-2 flex gap-2">
                        <select id="report-month" name="month" @if ($period === 'Monthly') required @endif class="rounded-lg border border-sky-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300">
                            @foreach ([1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $monthNumber => $monthName)
                                <option value="{{ $monthNumber }}" @selected((int) $month === $monthNumber)>{{ $monthName }}</option>
                            @endforeach
                        </select>
                        <input id="report-year" type="number" name="year" min="1900" max="2100" step="1" value="{{ $year }}" required @if ($period !== 'Monthly') disabled @endif aria-label="Report year" class="w-28 rounded-lg border border-sky-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300">
                    </div>
                    @error('month')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    @error('year')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div data-period-fields="Custom" class="flex flex-wrap items-end gap-3" @if ($period !== 'Custom') hidden @endif>
                    <div>
                        <label for="report-range-start" class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Start date</label>
                        <input id="report-range-start" type="date" name="start_date" value="{{ $startDate }}" @if ($period === 'Custom') required @endif class="mt-2 block rounded-lg border border-sky-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300">
                    </div>
                    <div>
                        <label for="report-range-end" class="text-[11px] font-bold uppercase tracking-wide text-slate-500">End date</label>
                        <input id="report-range-end" type="date" name="end_date" value="{{ $endDate }}" @if ($period === 'Custom') required @endif class="mt-2 block rounded-lg border border-sky-200 px-3 py-2 text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300">
                    </div>
                    @error('start_date')
                        <p class="basis-full text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    @error('end_date')
                        <p class="basis-full text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#168cff] px-4 py-2.5 text-xs font-bold text-white shadow-sm transition-colors hover:bg-[#0878df]">
                    <i class="fa-solid fa-chart-column" aria-hidden="true"></i>
                    Generate report
                </button>
            </form>
            <script>
                (() => {
                    const form = document.getElementById('sales-report-filters');

                    if (!form) {
                        return;
                    }

                    const updateFields = () => {
                        const selectedPeriod = form.querySelector('input[name="period"]:checked')?.value;

                        form.querySelectorAll('[data-period-fields]').forEach((group) => {
                            const isActive = group.dataset.periodFields === selectedPeriod;

                            group.hidden = !isActive;
                            group.querySelectorAll('input, select').forEach((field) => {
                                field.disabled = !isActive;
                                field.required = isActive;
                            });
                        });

                        updateWeeklyEndDate();
                    };

                    const updateWeeklyEndDate = () => {
                        const startDate = form.querySelector('#report-week-start').value;
                        const endDate = form.querySelector('#report-week-end');

                        if (!startDate) {
                            endDate.textContent = 'Choose a start date';
                            return;
                        }

                        const date = new Date(`${startDate}T00:00:00Z`);
                        date.setUTCDate(date.getUTCDate() + 6);
                        endDate.textContent = date.toLocaleDateString(undefined, {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            timeZone: 'UTC',
                        });
                    };

                    form.querySelectorAll('input[name="period"]').forEach((input) => {
                        input.addEventListener('change', updateFields);
                    });
                    form.querySelector('#report-week-start').addEventListener('input', updateWeeklyEndDate);
                    updateFields();
                })();
            </script>
        </section>

        @if ($errors->any())
            <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if ($report)
            <section class="card-entry-animation space-y-5 rounded-xl border border-sky-200 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">{{ $report['period'] }} sales report</h2>
                        <p class="text-sm text-slate-500">{{ $report['date_label'] }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach ([
                            'pdf' => ['PDF', 'fa-file-pdf'],
                            'docx' => ['Word (.docx)', 'fa-file-word'],
                            'xlsx' => ['Excel (.xlsx)', 'fa-file-excel'],
                        ] as $format => [$label, $icon])
                            <a href="{{ route('manager.sales-reports.export', ['format' => $format] + $exportFilters) }}" hx-boost="false" class="inline-flex items-center gap-2 rounded-lg border border-sky-200 px-3 py-2 text-xs font-semibold text-slate-700 transition-colors hover:border-sky-300 hover:bg-sky-50">
                                <i class="fa-solid {{ $icon }} text-[#168cff]" aria-hidden="true"></i>
                                Download {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                    <article class="rounded-xl bg-sky-50 p-4">
                        <p class="text-xs font-semibold text-slate-500">Orders</p>
                        <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($report['order_count']) }}</p>
                    </article>
                    <article class="rounded-xl bg-sky-50 p-4">
                        <p class="text-xs font-semibold text-slate-500">Total sales</p>
                        <p class="mt-1 text-xl font-bold text-slate-900">PHP {{ number_format($report['total_sales'], 2) }}</p>
                    </article>
                    <article class="rounded-xl bg-sky-50 p-4">
                        <p class="text-xs font-semibold text-slate-500">Payments collected in period</p>
                        <p class="mt-1 text-xl font-bold text-slate-900">PHP {{ number_format($report['payments_collected'], 2) }}</p>
                    </article>
                    <article class="rounded-xl bg-sky-50 p-4">
                        <p class="text-xs font-semibold text-slate-500">Balance on these orders</p>
                        <p class="mt-1 text-xl font-bold text-slate-900">PHP {{ number_format($report['outstanding_balance'], 2) }}</p>
                    </article>
                    <article class="rounded-xl bg-sky-50 p-4">
                        <p class="text-xs font-semibold text-slate-500">Average order</p>
                        <p class="mt-1 text-xl font-bold text-slate-900">PHP {{ number_format($report['average_order'], 2) }}</p>
                    </article>
                </div>

                <div>
                    <h3 class="mb-2 text-sm font-bold text-slate-800">Orders by status</h3>
                    <div class="overflow-x-auto rounded-xl border border-sky-100">
                        <table class="w-full min-w-[28rem] text-left text-sm">
                            <thead class="bg-sky-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr><th class="px-4 py-3">Status</th><th class="px-4 py-3">Orders</th><th class="px-4 py-3">Sales</th></tr>
                            </thead>
                            <tbody class="divide-y divide-sky-50">
                                @forelse ($report['statuses'] as $status => $totals)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-slate-800">{{ $status }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ number_format($totals['count']) }}</td>
                                        <td class="px-4 py-3 text-slate-600">PHP {{ number_format($totals['total'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="px-4 py-5 text-center text-slate-400">No orders in this period.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div>
                    <h3 class="mb-2 text-sm font-bold text-slate-800">Order details</h3>
                    <div class="overflow-x-auto rounded-xl border border-sky-100">
                        <table class="w-full min-w-[58rem] text-left text-sm">
                            <thead class="bg-sky-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Order</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Customer</th>
                                    <th class="px-4 py-3">Recorded by</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Total</th>
                                    <th class="px-4 py-3">Paid</th><th class="px-4 py-3">Balance</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sky-50">
                                @forelse ($report['orders'] as $order)
                                    <tr>
                                        <td class="px-4 py-3"><span class="inline-flex rounded-md border border-sky-200 bg-sky-50 px-1.5 py-0.5 font-mono text-xs font-bold text-sky-800">#{{ $order['order_id'] }}</span></td>
                                        <td class="px-4 py-3 text-slate-600">{{ $order['order_date'] }}</td>
                                        <td class="px-4 py-3 font-medium text-slate-800">{{ $order['customer'] }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $order['employee'] }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $order['status'] }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ number_format($order['total'], 2) }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ number_format($order['paid'], 2) }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ number_format($order['balance'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No orders in this period.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        @else
            <div class="card-entry-animation rounded-xl border border-dashed border-sky-200 bg-white p-8 text-center text-sm text-slate-500">
                Choose a period and date, then generate a report to see the sales summary and export options.
            </div>
        @endif
    </div>
</x-layout>
