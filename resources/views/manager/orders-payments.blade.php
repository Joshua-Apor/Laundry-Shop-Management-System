<x-layout>
    <div class="mx-auto max-w-6xl space-y-6 p-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Order Records &amp; Payments
                </h1>
                <p class="text-sm text-slate-500">
                    {{ $orders->total() }} matching orders
                </p>
            </div>
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

        <form method="GET" action="{{ route('manager.orders-payments') }}" class="space-y-4 rounded-2xl border border-sky-200 bg-white p-4 shadow-sm">
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
            <details class="group" @if($hasActiveFilters) open @endif>
                <summary class="inline-flex cursor-pointer list-none items-center gap-2 rounded-lg border border-sky-200 bg-slate-50 px-3.5 py-1.5 text-xs font-semibold text-slate-700 transition-colors hover:bg-sky-50 [&::-webkit-details-marker]:hidden">
                    <i class="fa-solid fa-filter text-sky-600" aria-hidden="true"></i>
                    <span>Advanced Filters</span>
                    @if($hasActiveFilters)
                    <span class="flex size-2 rounded-full bg-sky-500"></span>
                    @endif
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform group-open:rotate-180" aria-hidden="true"></i>
                </summary>

                <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4 pt-2 border-t border-sky-100">
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
                            @foreach (\App\Models\Order::STATUSES as $statusOption)
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
                <a href="{{ route('manager.orders-payments') }}" class="rounded-lg border border-sky-200 px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-sky-50">Clear filters</a>
            </div>
        </form>

        {{-- Table Header --}}
        <div class="hidden grid-cols-[1.1fr_1.4fr_1fr_1.2fr_1fr] gap-4 px-5 py-2 text-xs font-semibold uppercase tracking-wide text-slate-500 sm:grid">
            <span>Order Number</span>
            <span>Customer Name</span>
            <span>Laundry Weight</span>
            <span>Total Amount</span>

            {{-- Center Status Header --}}
            <span class="text-center">
                Status
            </span>
        </div>

        <div class="space-y-2">
            @forelse ($orders as $order)
            <div class="relative">
                <div
                    class="relative grid min-h-20 grid-cols-2 items-center gap-x-4 gap-y-3 rounded-2xl border border-sky-200 bg-white px-4 py-4 text-sm shadow-sm transition-colors hover:border-sky-300 hover:bg-sky-50/40 sm:grid-cols-[1.1fr_1.4fr_1fr_1.2fr_1fr] sm:gap-4 sm:px-5">
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

                    {{-- Laundry Weight --}}
                    <span class="pointer-events-none relative z-0 text-slate-600">
                        {{ $order->weight }} kg
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
            </div>
            @empty
            <div class="rounded-2xl border border-sky-100 bg-white p-12 text-center text-sm text-slate-400">
                No orders found.
            </div>
            @endforelse
        </div>

        {{ $orders->links() }}
    </div>
</x-layout>
