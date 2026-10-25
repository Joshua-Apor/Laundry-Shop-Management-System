@if ($orders->isEmpty())
    <p class="rounded-lg bg-slate-50 p-4 text-sm text-slate-500">No orders found for this customer.</p>
@else
    <p class="mb-2 text-xs font-medium text-slate-700">Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders</p>
    <ol class="max-h-72 space-y-3 overflow-y-auto pr-1">
        @php
            $timelineGroups = $orders->getCollection()->groupBy(function ($order) {
                $date = \Illuminate\Support\Carbon::parse($order->order_date);

                return $date->lt(now()->startOfYear()) ? $date->format('Y') : $date->toDateString();
            })->sortKeysDesc();
        @endphp
        @foreach ($timelineGroups as $timelineKey => $timelineOrders)
            <li class="list-none" aria-label="Order date group"><x-timeline-divider :date-key="$timelineKey" /></li>
            @foreach ($timelineOrders as $order)
            @php
                $statusClasses = match (strtolower($order->status)) {
                    'completed' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                    'cancelled' => 'border-rose-200 bg-rose-50 text-rose-700',
                    'pending', 'processing', 'ready for pickup' => 'border-amber-200 bg-amber-50 text-amber-800',
                    default => 'border-slate-200 bg-slate-100 text-slate-700',
                };
            @endphp
            <li class="rounded-xl border border-slate-200 bg-white p-3">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div>
                        <p class="text-sm font-bold text-slate-900">Order #{{ $order->order_id }}</p>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ \Illuminate\Support\Carbon::parse($order->order_date)->format('M j, Y') }}
                            ·
                            {{ $order->order_time ? \Illuminate\Support\Carbon::parse($order->order_time)->format('g:i A') : 'Time unavailable' }}
                        </p>
                    </div>
                    <span class="rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">{{ $order->status }}</span>
                </div>
                <p class="mt-3 text-xs text-slate-500">Items</p>
                <p class="text-sm text-slate-700">{{ $order->itemDetails ?: 'No items recorded' }}</p>
                <div class="mt-3 flex items-center justify-between border-t border-slate-100 pt-3 text-sm">
                    <span class="text-slate-500">Total</span>
                    <span class="font-bold text-slate-900">₱{{ number_format((float) $order->total_amount, 2) }}</span>
                </div>
                <div class="mt-3 flex flex-wrap justify-end gap-2">
                    <button type="button" data-modal-trigger data-modal-target="record-details-{{ $order->order_id }}" class="rounded-lg bg-[#168cff] px-3 py-2 text-xs font-bold text-white transition-colors hover:bg-[#0878df]">View Details</button>
                    <button type="button" data-modal-trigger data-modal-target="order-receipt-{{ $order->order_id }}" class="rounded-lg border border-sky-200 bg-white px-3 py-2 text-xs font-bold text-sky-700 transition-colors hover:bg-sky-50">View Receipt</button>
                </div>
                <x-orders.record-details-modal :order="$order" :back-modal-id="$backModalId" />
            </li>
            @endforeach
        @endforeach
    </ol>
    <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-xs font-medium text-slate-700">Showing {{ $orders->firstItem() ?? 0 }} to {{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders</p>
        <div class="flex justify-center [&_[aria-current=page]>span]:rounded-lg [&_[aria-current=page]>span]:border-[#168cff] [&_[aria-current=page]>span]:bg-[#168cff] [&_[aria-current=page]>span]:text-white [&_a]:rounded-lg [&_a:hover]:bg-sky-50 [&_a:hover]:text-[#168cff] sm:justify-end">
            {{ $orders->links() }}
        </div>
    </div>
@endif
