<x-layout>
    <div class="mx-auto max-w-6xl space-y-4 p-4 sm:space-y-6 sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-3 sm:gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    {{ $view === 'history' ? 'Order History & Payments' : 'Active Orders & Payments' }}
                </h1>
                <p class="text-sm text-slate-500">
                    Order activity and payment records.
                </p>
            </div>
            <a href="{{ route('orders.create') }}" class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-[#168cff] px-3 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-[#0878df] sm:px-4">
                <i class="fa-solid fa-plus" aria-hidden="true"></i> New Order
            </a>
        </div>

        @php
        $hasActiveFilters = !empty(array_filter([
        $filters['min_total'] ?? null,
        $filters['max_total'] ?? null,
        $filters['status'] ?? null,
        $filters['payment_status'] ?? null,
        $filters['payment_method'] ?? null,
        $filters['date_from'] ?? null,
        $filters['date_to'] ?? null,
        ]));
        @endphp

        <nav class="flex gap-2 border-b border-sky-100" aria-label="Order views">
            <a href="{{ route('records.index', ['view' => 'active']) }}" @class(['border-b-2 px-3 py-2 text-sm font-semibold', 'border-[#168cff] text-[#168cff]' => $view === 'active', 'border-transparent text-slate-500 hover:text-slate-800' => $view !== 'active'])>Active orders</a>
            <a href="{{ route('records.index', ['view' => 'history']) }}" @class(['border-b-2 px-3 py-2 text-sm font-semibold', 'border-[#168cff] text-[#168cff]' => $view === 'history', 'border-transparent text-slate-500 hover:text-slate-800' => $view !== 'history'])>Order history</a>
        </nav>

        <form method="GET" action="{{ route('records.index') }}" class="card-entry-animation space-y-4 rounded-2xl border border-sky-200 bg-white p-3 shadow-sm sm:p-4" data-order-filter-form>
            <input type="hidden" name="view" value="{{ $view }}">
            {{-- Search Bar & Filter Toggle Row --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="grow">
                    <label for="order-search" class="mb-1 block text-xs font-semibold text-slate-700">Search</label>
                    <input
                        id="order-search"
                        type="search"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Order ID, customer, phone, or payment reference"
                        class="w-full rounded-lg border border-sky-200 bg-white px-4 py-2 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300">
                </div>
            </div>

            {{-- Collapsible Advanced Filters --}}
            <details class="group">
                <summary class="inline-flex cursor-pointer list-none items-center gap-2 rounded-lg border border-sky-200 bg-slate-50 px-3.5 py-1.5 text-xs font-semibold text-slate-700 transition-colors hover:bg-sky-50 [&::-webkit-details-marker]:hidden">
                    <i class="fa-solid fa-filter text-sky-600" aria-hidden="true"></i>
                    <span>Advanced Filters</span>
                    @if($hasActiveFilters)
                    <span class="flex size-2 rounded-full bg-sky-500"></span>
                    @endif
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform group-open:rotate-180" aria-hidden="true"></i>
                </summary>

                <div class="mt-4 grid grid-cols-2 gap-3 border-t border-sky-100 pt-2 lg:grid-cols-4">
                    <div>
                        <label for="order-date-preset" class="mb-1 block text-xs font-semibold text-slate-700">Date range</label>
                        <select id="order-date-preset" name="date_preset" data-order-date-preset class="w-full rounded-lg border border-sky-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
                            <option value="today" @selected($datePreset === 'today')>Today</option>
                            <option value="week" @selected($datePreset === 'week')>This week</option>
                            <option value="all" @selected($datePreset === 'all')>All time</option>
                            <option value="custom" @selected($datePreset === 'custom')>Custom range</option>
                        </select>
                    </div>
                    <div>
                        <label for="min-total" class="mb-1 block text-xs font-semibold text-slate-700">Minimum total</label>
                        <input id="min-total" type="number" name="min_total" value="{{ $filters['min_total'] ?? '' }}" min="0" step="0.01" placeholder="0.00" class="w-full rounded-lg border border-sky-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
                    </div>

                    <div>
                        <label for="max-total" class="mb-1 block text-xs font-semibold text-slate-700">Maximum total</label>
                        <input id="max-total" type="number" name="max_total" value="{{ $filters['max_total'] ?? '' }}" min="0" step="0.01" placeholder="No maximum" class="w-full rounded-lg border border-sky-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
                        @error('max_total')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="order-status" class="mb-1 block text-xs font-semibold text-slate-700">Order status</label>
                        <select id="order-status" name="status" class="w-full rounded-lg border border-sky-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
                            <option value="">All statuses</option>
                            @foreach ($view === 'history' ? \App\Models\Order::HISTORY_STATUSES : array_slice(\App\Models\Order::STATUSES, 0, 2) as $statusOption)
                            <option value="{{ $statusOption }}" @selected(($filters['status'] ?? '' )===$statusOption)>{{ $statusOption }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="payment-status" class="mb-1 block text-xs font-semibold text-slate-700">Payment status</label>
                        <select id="payment-status" name="payment_status" class="w-full rounded-lg border border-sky-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
                            <option value="">All payment statuses</option>
                            @foreach (['Paid', 'Partial'] as $paymentStatusOption)
                            <option value="{{ $paymentStatusOption }}" @selected(($filters['payment_status'] ?? '' )===$paymentStatusOption)>{{ $paymentStatusOption }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="payment-method" class="mb-1 block text-xs font-semibold text-slate-700">Payment method</label>
                        <select id="payment-method" name="payment_method" class="w-full rounded-lg border border-sky-200 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
                            <option value="">All methods</option>
                            @foreach (['Cash', 'GCash'] as $paymentMethodOption)
                            <option value="{{ $paymentMethodOption }}" @selected(($filters['payment_method'] ?? '' )===$paymentMethodOption)>{{ $paymentMethodOption }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="date-from" class="mb-1 block text-xs font-semibold text-slate-700">Order date from</label>
                        <input id="date-from" type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="w-full rounded-lg border border-sky-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
                    </div>

                    <div>
                        <label for="date-to" class="mb-1 block text-xs font-semibold text-slate-700">Order date to</label>
                        <input id="date-to" type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="w-full rounded-lg border border-sky-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
                        @error('date_to')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </details>

            @if ($errors->any())
            <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-sky-100">
                <button type="submit" class="rounded-lg bg-[#168cff] px-4 py-2 text-sm font-bold text-white transition-colors hover:bg-[#0878df]">Apply filters</button>
                <a href="{{ route('records.index') }}" class="rounded-lg border border-sky-200 px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-sky-50">Clear filters</a>
            </div>
        </form>

        {{-- Table Header --}}
        <div data-order-results class="transition-opacity aria-busy:opacity-60" aria-busy="false">
        <p class="mb-2 text-xs font-medium text-slate-700">Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders</p>
        <div class="hidden grid-cols-[1.1fr_1.4fr_1fr_1.2fr_1fr] gap-4 px-5 py-2 text-xs font-semibold uppercase tracking-wide text-slate-500 sm:grid">
            <span>Order Number</span>
            <span>Customer Name</span>
            <span>Laundry Weights/Loads</span>
            <span>Total Amount</span>

            {{-- Center Status Header --}}
            <span class="text-center">
                Status
            </span>
        </div>

        <div class="space-y-2" data-record-card-list>
            @php
                $timelineGroups = $orders->getCollection()->groupBy(function ($order) {
                    $date = \Illuminate\Support\Carbon::parse($order->order_date);

                    return $date->lt(now()->startOfYear()) ? $date->format('Y') : $date->toDateString();
                })->sortKeysDesc();
            @endphp
            @forelse ($timelineGroups as $timelineKey => $timelineOrders)
            <section class="space-y-2" aria-label="Order activity date group">
                <x-timeline-divider :date-key="$timelineKey" />
                @foreach ($timelineOrders as $order)
            <div class="relative">
                <div
                    class="card-entry-animation relative grid min-h-20 grid-cols-2 items-center gap-x-4 gap-y-3 rounded-2xl border border-sky-200 bg-white px-4 py-4 text-sm shadow-sm transition-colors hover:border-sky-300 hover:bg-sky-50/40 sm:grid-cols-[1.1fr_1.4fr_1fr_1.2fr_1fr] sm:gap-4 sm:px-5">
                    {{-- Full row clickable area --}}
                    <button
                        type="button"
                        data-modal-trigger
                        data-modal-target="record-details-{{ $order->order_id }}"
                        aria-label="View details for order {{ $order->order_id }}"
                        class="absolute inset-0 z-10 rounded-2xl focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500">
                    </button>

                    {{-- Order Number --}}
                    <span class="pointer-events-none relative z-0">
                        <span class="inline-flex rounded-md border border-sky-200 bg-sky-50 px-1.5 py-0.5 font-mono text-xs font-bold text-sky-800">
                            Order #{{ $order->order_id }}
                        </span>

                        <span class="mt-1 block font-sans text-xs text-slate-500">
                            {{ \Illuminate\Support\Carbon::parse($order->order_date)->format('M j, Y') }}
                            <span class="block">{{ $order->order_time ? \Illuminate\Support\Carbon::parse($order->order_time)->format('g:i A') : 'Time unavailable' }}</span>
                        </span>
                    </span>

                    {{-- Customer Name --}}
                    <span
                        class="pointer-events-none relative z-0 col-span-2 row-start-2 min-w-0 sm:row-auto sm:col-span-1">
                        <span class="block truncate font-medium text-slate-900">
                            {{ $order->fullname }}
                        </span>
                    </span>

                    {{-- Laundry Weights/Loads --}}
                    <span class="pointer-events-none relative z-0 text-slate-600">
                        @if ($order->order_type === 'Self Service')
                            {{ $order->self_service_loads }} {{ (int) $order->self_service_loads === 1 ? 'load' : 'loads' }}
                        @else
                            {{ $order->weight }} kg
                        @endif
                    </span>

                    {{-- Total Amount --}}
                    <span class="pointer-events-none relative z-0 font-semibold text-slate-900">
                        ₱{{ number_format((float) $order->total_amount, 2) }}
                    </span>

                    {{-- Status --}}
                    <div
                        class="relative z-20 col-start-2 row-start-1 flex justify-start sm:col-start-5 sm:row-auto sm:justify-center">
                        <x-orders.status-control
                            :order="$order"
                            :editable="false" />
                    </div>
                </div>

                <x-orders.record-details-modal :order="$order" />
                @foreach ($order->recordPayments as $payment)
                    <x-orders.payment-receipt-modal
                        :order="$order"
                        :payment="$payment"
                        :id="'payment-receipt-'.$order->order_id.'-'.$payment->payment_id"
                        :label="$loop->first ? 'Partial' : 'Final'" />
                @endforeach
            </div>
                @endforeach
            </section>
            @empty
            <div class="rounded-2xl border border-sky-100 bg-white p-12 text-center text-sm text-slate-400">
                No orders found.
            </div>
            @endforelse
        </div>

        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs font-medium text-slate-700">Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders</p>
            <div class="flex justify-center [&_[aria-current=page]>span]:rounded-lg [&_[aria-current=page]>span]:border-[#168cff] [&_[aria-current=page]>span]:bg-[#168cff] [&_[aria-current=page]>span]:text-white [&_a]:rounded-lg [&_a:hover]:bg-sky-50 [&_a:hover]:text-[#168cff] sm:justify-end">
                {{ $orders->links() }}
            </div>
        </div>
        </div>
    </div>

    @if ($receipt)
        <x-orders.payment-receipt-modal :order="$receipt['order']" :payment="$receipt['payment']" />
    @endif
</x-layout>
