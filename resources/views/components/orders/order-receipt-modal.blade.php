@props(['order', 'backModalId' => null])

<x-modal :id="'order-receipt-'.$order->order_id" title="Order receipt" :data-history-detail-modal="$backModalId">
    <article class="space-y-5" data-print-receipt>
        <header class="border-b border-dashed border-sky-200 pb-4 text-center">
            <img src="{{ asset('images/logo.png') }}" alt="" class="mx-auto mb-2 size-12 object-contain">
            <p class="text-lg font-extrabold text-slate-900">I Laba U</p>
            <p class="mt-1 text-xs text-slate-500">Order receipt</p>
            <p class="mt-3 font-mono text-sm font-bold text-sky-700">Order #{{ $order->order_id }}</p>
            <p class="mt-1 text-xs text-slate-500">Issued {{ \Illuminate\Support\Carbon::parse($order->order_date)->format('M j, Y') }} &middot; {{ $order->order_time ? \Illuminate\Support\Carbon::parse($order->order_time)->format('g:i A') : 'Time unavailable' }}</p>
        </header>
        <section class="grid grid-cols-2 gap-3 text-sm">
            <div><p class="text-xs text-slate-500">Customer</p><p class="font-semibold text-slate-900">{{ $order->fullname }}</p><p class="text-xs text-slate-600">{{ $order->phoneNumber }}</p></div>
            <div><p class="text-xs text-slate-500">Payment method</p><p class="font-semibold text-slate-900">{{ $order->paymentMethod ?: $order->initialPaymentMethod ?: 'Not recorded' }}</p></div>
        </section>
        <section class="rounded-xl border border-sky-100 bg-sky-50/70 p-4">
            <h3 class="text-sm font-bold text-slate-900">Items</h3>
            <div class="mt-3 space-y-2 text-sm">
                <div class="flex justify-between gap-3"><span class="text-slate-700">{{ $order->itemDetails ?: $order->services ?: $order->order_type }}</span><span class="shrink-0 font-semibold text-slate-900">&#8369;{{ number_format(max((float) $order->total_amount - (float) $order->storage_fee, 0), 2) }}</span></div>
                @if ((float) $order->storage_fee > 0)
                    <div class="flex justify-between gap-3"><span class="text-slate-600">Storage fee</span><span class="font-semibold text-slate-900">&#8369;{{ number_format((float) $order->storage_fee, 2) }}</span></div>
                @endif
                <div class="flex justify-between gap-3"><span class="text-slate-600">Discounts / taxes</span><span class="font-semibold text-slate-900">Not recorded</span></div>
                <div class="flex justify-between gap-3 border-t border-sky-200 pt-2"><span class="font-semibold text-slate-700">Subtotal</span><span class="font-semibold text-slate-900">&#8369;{{ number_format(max((float) $order->total_amount - (float) $order->storage_fee, 0), 2) }}</span></div>
                <div class="flex justify-between gap-3"><span class="font-semibold text-slate-700">Total</span><span class="font-bold text-slate-900">&#8369;{{ number_format((float) $order->total_amount, 2) }}</span></div>
                <div class="flex justify-between gap-3"><span class="text-slate-600">Total paid</span><span class="font-semibold text-slate-900">&#8369;{{ number_format((float) $order->amount_paid, 2) }}</span></div>
                @if ((float) $order->balance > 0)
                    <div class="flex justify-between gap-3"><span class="text-slate-600">Balance due</span><span class="font-semibold text-slate-900">&#8369;{{ number_format((float) $order->balance, 2) }}</span></div>
                @endif
            </div>
        </section>
        <section class="grid grid-cols-2 gap-3 text-xs text-slate-600">
            <p>Payment reference: <span class="font-semibold text-slate-900">{{ $order->balancePaymentReferenceNumber ?: $order->initialReferenceNumber ?: 'Not recorded' }}</span></p>
            <p>Payment date: <span class="font-semibold text-slate-900">{{ $order->paymentDate ? \Illuminate\Support\Carbon::parse($order->paymentDate)->format('M j, Y') : 'Not recorded' }}</span></p>
        </section>
        <div class="flex items-center justify-between gap-3 border-t border-slate-100 pt-4">
            @if ($backModalId)
                <button type="button" data-modal-back-target="{{ $backModalId }}" class="rounded-lg bg-[#F3F4F6] px-4 py-2 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-200">Back to order history</button>
            @else
                <span></span>
            @endif
            <button type="button" data-print-receipt-button class="inline-flex items-center gap-2 rounded-lg bg-[#168cff] px-4 py-2 text-xs font-bold text-white transition-colors hover:bg-[#0878df]"><i class="fa-solid fa-print" aria-hidden="true"></i>Print / Download PDF</button>
        </div>
    </article>
</x-modal>
