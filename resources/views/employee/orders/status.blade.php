<x-layout>
    <div class="mx-auto max-w-6xl space-y-6 p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Update Order Status</h1>
                <p class="text-sm text-slate-500">Active orders in Processing or Ready for Pickup.</p>
            </div>
            <p class="text-sm font-medium text-slate-500">{{ $orders->total() }} matching orders</p>
        </div>

        <form method="GET" action="{{ route('employee.orders.status') }}" class="flex flex-col gap-3 rounded-2xl border border-sky-200 bg-white p-4 shadow-sm sm:flex-row sm:items-end">
            <div class="grow">
                <label for="order-status-search" class="mb-1 block text-xs font-semibold text-slate-700">Search active orders</label>
                <input id="order-status-search" type="search" name="search" value="{{ $search }}" placeholder="Order ID, customer name, or phone" class="w-full rounded-lg border border-sky-200 bg-white px-4 py-2 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="rounded-lg bg-[#168cff] px-4 py-2 text-sm font-bold text-white transition-colors hover:bg-[#0878df]">Search</button>
                @if ($search !== '')
                    <a href="{{ route('employee.orders.status') }}" class="rounded-lg border border-sky-200 px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-sky-50">Clear</a>
                @endif
            </div>
        </form>

        <div class="hidden grid-cols-[1.1fr_1.4fr_1fr_1.2fr_1fr] gap-4 px-5 py-2 text-xs font-semibold uppercase tracking-wide text-slate-500 sm:grid">
            <span>Order Number</span>
            <span>Customer Name</span>
            <span>Laundry Weight/Loads</span>
            <span>Total Amount</span>
            <span class="text-center">Status</span>
        </div>

        <div class="space-y-2">
            @forelse ($orders as $order)
                <div class="relative">
                    <div class="card-entry-animation relative grid min-h-20 grid-cols-2 items-center gap-x-4 gap-y-3 rounded-2xl border border-sky-200 bg-white px-4 py-4 text-sm shadow-sm transition-colors hover:border-sky-300 hover:bg-sky-50/40 sm:grid-cols-[1.1fr_1.4fr_1fr_1.2fr_1fr] sm:gap-4 sm:px-5">
                        <button type="button" data-modal-trigger data-modal-target="record-details-{{ $order->order_id }}" aria-label="View details for order {{ $order->order_id }}" class="absolute inset-0 z-10 rounded-2xl focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500"></button>

                        <span class="pointer-events-none relative z-0">
                            <span class="inline-flex rounded-md border border-sky-200 bg-sky-50 px-1.5 py-0.5 font-mono text-xs font-bold text-sky-800">Order #{{ $order->order_id }}</span>
                            <span class="mt-1 block font-sans text-xs text-slate-500">
                                {{ \Illuminate\Support\Carbon::parse($order->order_date)->format('M j, Y') }}
                                <span class="block">{{ $order->order_time ? \Illuminate\Support\Carbon::parse($order->order_time)->format('g:i A') : 'Time unavailable' }}</span>
                            </span>
                        </span>

                        <span class="pointer-events-none relative z-0 col-span-2 row-start-2 min-w-0 sm:row-auto sm:col-span-1">
                            <span class="block truncate font-medium text-slate-900">{{ $order->fullname }}</span>
                            <span class="block truncate text-xs text-slate-500">{{ $order->phoneNumber }}</span>
                        </span>

                        <span class="pointer-events-none relative z-0 text-slate-600">
                            @if ($order->order_type === 'Self Service')
                                {{ $order->self_service_loads }} {{ (int) $order->self_service_loads === 1 ? 'load' : 'loads' }}
                            @else
                                {{ $order->weight }} kg
                            @endif
                        </span>

                        <span class="pointer-events-none relative z-0 font-semibold text-slate-900">&#8369;{{ number_format((float) $order->total_amount, 2) }}</span>

                        <div class="relative z-20 col-start-2 row-start-1 flex justify-start sm:col-start-5 sm:row-auto sm:justify-center">
                            <x-orders.status-control :order="$order" />
                        </div>
                    </div>
                    <x-orders.record-details-modal :order="$order" />
                </div>
            @empty
                <div class="rounded-2xl border border-sky-100 bg-white p-12 text-center text-sm text-slate-400">
                    {{ $search === '' ? 'No active orders need a status update.' : 'No active orders matched your search.' }}
                </div>
            @endforelse
        </div>

        {{ $orders->links() }}
    </div>

    @if ($receipt)
        <x-orders.payment-receipt-modal :order="$receipt['order']" :payment="$receipt['payment']" />
    @endif
</x-layout>

