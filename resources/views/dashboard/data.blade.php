<div data-dashboard-fragment>
    <div class="mb-2 flex items-center justify-between">
        <h2 class="text-lg font-semibold">Today's Summary</h2>
        <span class="text-sm font-medium">{{ now()->format('F d, Y') }}</span>
    </div>

    <div class="mx-auto mb-6 grid w-full max-w-5xl grid-cols-2 gap-2 sm:grid-cols-4">
        <x-dashboard.metric-card title="Total Orders" :value="$totalOrders" />
        <x-dashboard.metric-card title="Completed" :value="$completedOrders" />
        <x-dashboard.metric-card title="Ready for Pickup" :value="$readyForPickupOrders" />
        <x-dashboard.metric-card title="Total Revenue" :value="'₱'.number_format($totalRevenue, 2)" />
    </div>

    <div>
        <div class="mb-4 flex flex-row items-center justify-between">
            <span>
                <span class="block text-base font-bold">Today's Orders</span>
                <span class="block text-xs font-medium text-slate-500">Orders placed today</span>
            </span>
            <a href="{{ auth()->user()->role === 'manager' ? route('manager.orders-payments') : route('records.index') }}" class="cursor-pointer text-sm font-medium">
                View all →
            </a>
        </div>

        {{-- Table Header --}}
        <div class="hidden grid-cols-[1.1fr_1.4fr_1fr_1.2fr_1fr] gap-4 px-5 py-2 text-xs font-semibold uppercase tracking-wide text-slate-500 sm:grid">
            <span>Order Number</span>
            <span>Customer Name</span>
            <span>Laundry Weight</span>
            <span>Total Amount</span>
            <span>Status</span>
        </div>

        <ul class="space-y-2">
            @forelse ($recentOrders as $order)
                <li class="relative">
                    <div
                        class="relative grid min-h-20 grid-cols-2 items-center gap-x-4 gap-y-3 rounded-2xl border border-sky-200 bg-white px-4 py-4 text-sm transition-colors hover:bg-sky-50/40 sm:grid-cols-[1.1fr_1.4fr_1fr_1.2fr_1fr] sm:gap-4 sm:px-5"
                    >
                        <button
                            type="button"
                            data-modal-trigger
                            data-modal-target="record-details-{{ $order->order_id }}"
                            aria-label="View details for order {{ $order->order_id }} for {{ $order->fullname }}"
                            class="absolute inset-0 z-10 rounded-2xl focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500"
                        >
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
                            class="pointer-events-none relative z-0 col-span-2 row-start-2 truncate font-medium text-slate-900 sm:row-auto sm:col-span-1"
                        >
                            {{ $order->fullname }}
                        </span>

                        {{-- Laundry Weight --}}
                        <span class="pointer-events-none relative z-0 text-slate-600">
                            {{ $order->order_type === 'Self Service' ? $order->self_service_loads.' loads' : $order->weight.' kg' }}
                        </span>

                        {{-- Total Amount --}}
                        <span class="pointer-events-none relative z-0 font-semibold text-slate-900">
                            ₱{{ number_format((float) $order->total_amount, 2) }}
                        </span>

                        {{-- Status --}}
                        <div
                            class="relative z-20 col-start-2 row-start-1 flex justify-start sm:col-start-5 sm:row-start-1 sm:justify-start"
                        >
                            <x-orders.status-control
                                :order="$order"
                                :editable="auth()->user()->role !== 'manager'"
                            />
                        </div>
                    </div>

                    <x-orders.record-details-modal :order="$order" />
                </li>
            @empty
                <li class="rounded-2xl border border-sky-200 bg-white px-5 py-8 text-center text-sm text-slate-500">
                No orders today.
                </li>
            @endforelse
        </ul>
    </div>

    <section class="mt-8 border-t border-slate-200 pt-6">
        <div class="mb-4">
            <h2 class="text-base font-bold">Unclaimed Orders</h2>
            <p class="text-xs font-medium text-slate-500">Older orders still awaiting completion or pickup</p>
        </div>

        <div class="hidden grid-cols-[1.1fr_1.4fr_1fr_1.2fr_1fr] gap-4 px-5 py-2 text-xs font-semibold uppercase tracking-wide text-slate-500 sm:grid">
            <span>Order Number</span>
            <span>Customer Name</span>
            <span>Laundry Weight</span>
            <span>Total Amount</span>
            <span>Status</span>
        </div>

        <ul class="space-y-2">
            @forelse ($unclaimedOrders as $order)
                <li class="relative">
                    <div class="relative grid min-h-20 grid-cols-2 items-center gap-x-4 gap-y-3 rounded-2xl border border-sky-200 bg-white px-4 py-4 text-sm transition-colors hover:bg-sky-50/40 sm:grid-cols-[1.1fr_1.4fr_1fr_1.2fr_1fr] sm:gap-4 sm:px-5">
                        <button
                            type="button"
                            data-modal-trigger
                            data-modal-target="record-details-{{ $order->order_id }}"
                            aria-label="View details for order {{ $order->order_id }} for {{ $order->fullname }}"
                            class="absolute inset-0 z-10 rounded-2xl focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500"
                        ></button>

                        <span class="pointer-events-none relative z-0">
                            <span class="inline-flex rounded-md border border-sky-200 bg-sky-50 px-1.5 py-0.5 font-mono text-xs font-bold text-sky-800">
                                Order #{{ $order->order_id }}
                            </span>
                            <span class="mt-1 block font-sans text-xs text-slate-500">
                                {{ \Illuminate\Support\Carbon::parse($order->order_date)->format('M j, Y') }}
                                <span class="block">{{ $order->order_time ? \Illuminate\Support\Carbon::parse($order->order_time)->format('g:i A') : 'Time unavailable' }}</span>
                            </span>
                        </span>

                        <span class="pointer-events-none relative z-0 col-span-2 row-start-2 truncate font-medium text-slate-900 sm:row-auto sm:col-span-1">
                            {{ $order->fullname }}
                        </span>

                        <span class="pointer-events-none relative z-0 text-slate-600">
                            {{ $order->order_type === 'Self Service' ? $order->self_service_loads.' loads' : $order->weight.' kg' }}
                        </span>

                        <span class="pointer-events-none relative z-0 font-semibold text-slate-900">
                            ₱{{ number_format((float) $order->total_amount, 2) }}
                        </span>

                        <div class="relative z-20 col-start-2 row-start-1 flex justify-start sm:col-start-5 sm:row-start-1 sm:justify-start">
                            <x-orders.status-control
                                :order="$order"
                                :editable="auth()->user()->role !== 'manager'"
                            />
                        </div>
                    </div>

                    <x-orders.record-details-modal :order="$order" />
                </li>
            @empty
                <li class="rounded-2xl border border-sky-200 bg-white px-5 py-8 text-center text-sm text-slate-500">
                    No unclaimed orders.
                </li>
            @endforelse
        </ul>
    </section>
</div>
