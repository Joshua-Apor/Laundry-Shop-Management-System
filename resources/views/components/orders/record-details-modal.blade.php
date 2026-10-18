@props(['order', 'backModalId' => null])

<x-modal :id="'record-details-'.$order->order_id" :title="'Order #'.$order->order_id" :data-history-detail-modal="$backModalId">
    <div class="space-y-5">
        <section>
            <h3 class="mb-3 flex items-center gap-2 text-sm font-bold text-slate-800">
                <i class="fa-solid fa-user text-[#168cff]" aria-hidden="true"></i>
                Customer information
            </h3>
            <div class="grid grid-cols-2 gap-2 sm:gap-3">
                <x-orders.info-item label="Customer name" icon="fa-user-tag">{{ $order->fullname }}</x-orders.info-item>
                <x-orders.info-item label="Phone number" icon="fa-phone">{{ $order->phoneNumber }}</x-orders.info-item>
                <div class="col-span-2">
                    <x-orders.info-item label="Email" icon="fa-envelope">{{ $order->customerEmail }}</x-orders.info-item>
                </div>
            </div>
        </section>

        <details class="group rounded-xl border border-sky-100 bg-white">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 p-4 text-sm font-bold text-slate-800 marker:content-none">
                <span class="flex items-center gap-2"><i class="fa-solid fa-shirt text-[#168cff]" aria-hidden="true"></i>Order information</span>
                <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform group-open:rotate-180" aria-hidden="true"></i>
            </summary>
            <div class="grid grid-cols-2 gap-2 border-t border-sky-100 p-3 sm:gap-3 sm:p-4">
                <x-orders.info-item label="Status" icon="fa-circle-check">{{ $order->status }}</x-orders.info-item>
                <x-orders.info-item :label="$order->order_type === 'Self Service' ? 'Self service loads' : 'Drop off weight'" icon="fa-weight-scale">{{ $order->order_type === 'Self Service' ? $order->self_service_loads.' loads' : number_format((float) $order->weight, 2).' kg' }}</x-orders.info-item>
                <x-orders.info-item label="Order date" icon="fa-calendar-days">{{ $order->order_date }}</x-orders.info-item>
                <x-orders.info-item label="Order time" icon="fa-clock">{{ $order->order_time ? \Illuminate\Support\Carbon::parse($order->order_time)->format('g:i A') : 'Time unavailable' }}</x-orders.info-item>
                <x-orders.info-item label="Pickup date" icon="fa-calendar-check">{{ $order->pickup_date }}</x-orders.info-item>
                <div class="sm:col-span-2">
                    <x-orders.info-item label="Services" icon="fa-soap">{{ $order->services }}</x-orders.info-item>
                </div>
                @if (filled($order->specialRequest))
                    <div class="col-span-2">
                        <x-orders.info-item label="Special request" icon="fa-note-sticky">{{ $order->specialRequest }}</x-orders.info-item>
                    </div>
                @endif
                <div class="col-span-2">
                    <x-orders.info-item label="Recorded by" icon="fa-user-gear">{{ $order->employeeName }}</x-orders.info-item>
                </div>
            </div>
        </details>

        <details class="group rounded-xl border border-sky-100 bg-white">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 p-4 text-sm font-bold text-slate-800 marker:content-none">
                <span class="flex items-center gap-2"><i class="fa-solid fa-credit-card text-[#168cff]" aria-hidden="true"></i>Payment information</span>
                <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform group-open:rotate-180" aria-hidden="true"></i>
            </summary>
            <div class="grid grid-cols-2 gap-2 border-t border-sky-100 p-3 sm:gap-3 sm:p-4">
                <x-orders.info-item label="Total amount" icon="fa-receipt">₱{{ number_format((float) $order->total_amount, 2) }}</x-orders.info-item>
                @if ((float) $order->storage_fee > 0)
                    <x-orders.info-item label="Storage fee" icon="fa-box">PHP {{ number_format((float) $order->storage_fee, 2) }}</x-orders.info-item>
                @endif
                <x-orders.info-item label="Amount paid" icon="fa-money-bill-wave">₱{{ number_format((float) $order->amount_paid, 2) }}</x-orders.info-item>
                <x-orders.info-item label="Balance" icon="fa-wallet">₱{{ number_format((float) $order->balance, 2) }}@if ((float) $order->balance <= 0 && filled($order->initialPaymentMethod) && $order->initialPaymentMethod !== $order->paymentMethod) - {{ $order->paymentMethod }}@endif</x-orders.info-item>
                <x-orders.info-item label="Payment method" icon="fa-credit-card">{{ $order->initialPaymentMethod ?: $order->paymentMethod }}</x-orders.info-item>
                <x-orders.info-item label="Payment status" icon="fa-circle-info">{{ $order->paymentStatus }}</x-orders.info-item>
                <x-orders.info-item label="Payment date" icon="fa-calendar-day">{{ $order->paymentDate }}</x-orders.info-item>
                @if ($order->balancePaymentId !== null)
                    <div class="col-span-2 grid grid-cols-2 gap-2 sm:gap-3">
                        <x-orders.info-item label="Initial payment reference" icon="fa-hashtag">{{ $order->initialReferenceNumber }}</x-orders.info-item>
                        <x-orders.info-item label="Balance payment reference" icon="fa-hashtag">{{ $order->balancePaymentReferenceNumber }}</x-orders.info-item>
                    </div>
                @else
                    <div class="col-span-2">
                        <x-orders.info-item label="Initial payment reference" icon="fa-hashtag">{{ $order->initialReferenceNumber }}</x-orders.info-item>
                    </div>
                @endif
            </div>
        </details>

        @if ($backModalId)
            <div class="flex justify-start border-t border-slate-100 pt-4">
                <button type="button" data-modal-back-target="{{ $backModalId }}" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Back</button>
            </div>
        @endif

    </div>
</x-modal>
