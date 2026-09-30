<x-layout>
    <div class="mx-auto max-w-6xl space-y-6 p-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Order Records &amp; Payments
                </h1>
                <p class="text-sm text-slate-500">
                    {{ $orders->total() }} total orders
                </p>
            </div>
        </div>

        <form method="GET" action="{{ route('manager.orders-payments') }}">
            <label class="sr-only" for="order-search">
                Search orders
            </label>

            <input
                id="order-search"
                type="search"
                name="search"
                value="{{ $search }}"
                placeholder="Search by name, claim #, or phone..."
                class="w-full rounded-lg border border-sky-200 bg-white px-4 py-2 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300"
            >
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
                        class="relative grid min-h-20 grid-cols-2 items-center gap-x-4 gap-y-3 rounded-2xl border border-sky-200 bg-white px-4 py-4 text-sm shadow-sm transition-colors hover:border-sky-300 hover:bg-sky-50/40 sm:grid-cols-[1.1fr_1.4fr_1fr_1.2fr_1fr] sm:gap-4 sm:px-5"
                    >
                        {{-- Full row clickable area --}}
                        <button
                            type="button"
                            data-modal-trigger
                            data-modal-target="record-details-{{ $order->order_id }}"
                            aria-label="View details for order {{ $order->order_id }}"
                            class="absolute inset-0 z-10 rounded-2xl focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500"
                        >
                            <span
                                data-loading-spinner
                                class="absolute right-3 top-3 hidden size-3.5 animate-spin rounded-full border-2 border-sky-200 border-t-sky-600 sm:right-5 sm:top-1/2 sm:-translate-y-1/2"
                                aria-hidden="true"
                            ></span>

                            <span data-loading-text class="sr-only">
                                Open order details
                            </span>
                        </button>

                        {{-- Order Number --}}
                        <span class="pointer-events-none relative z-0 font-mono font-medium text-sky-700">
                            Order #{{ $order->order_id }}

                            <span class="mt-1 block font-sans text-xs text-slate-500">
                                {{ $order->order_date }}
                            </span>
                        </span>

                        {{-- Customer Name --}}
                        <span
                            class="pointer-events-none relative z-0 col-span-2 row-start-2 min-w-0 sm:row-auto sm:col-span-1"
                        >
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
                            class="relative z-20 col-start-2 row-start-1 flex justify-start sm:col-start-5 sm:row-auto sm:justify-center"
                        >
                            <x-orders.status-control
                                :order="$order"
                                :editable="false"
                            />
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