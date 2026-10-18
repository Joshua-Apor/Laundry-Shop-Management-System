@props(['order', 'payment'])

@php
    $receiptNumber = $payment->reference_number ?: 'Not recorded';
    $paymentLabel = (int) $order->balancePaymentId === (int) $payment->payment_id
        ? 'Outstanding balance payment'
        : 'Initial payment';
    $previouslyPaid = max((float) $order->amount_paid - (float) $payment->amount, 0);
@endphp

<x-modal id="payment-receipt-modal" title="Payment receipt" data-modal-open-on-load>
    <div class="space-y-5" data-payment-receipt>
        <header class="border-b border-dashed border-sky-200 pb-4 text-center">
            <img src="{{ asset('images/logo.png') }}" alt="" class="mx-auto mb-2 size-12 object-contain">
            <p class="text-lg font-extrabold text-slate-900">I Laba U</p>
            <p class="mt-1 text-xs text-slate-500">{{ $paymentLabel }}</p>
            <p class="mt-3 font-mono text-sm font-bold text-sky-700">{{ $receiptNumber }}</p>
            <p class="mt-1 text-xs text-slate-500">Order #{{ $order->order_id }}</p>
        </header>

        <section class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
            <div>
                <p class="text-xs font-medium text-slate-500">Customer</p>
                <p class="mt-1 font-semibold text-slate-900">{{ $order->fullname }}</p>
                <p class="text-xs text-slate-600">{{ $order->phoneNumber }}</p>
                @if (filled($order->customerEmail))
                    <p class="mt-1 text-xs text-slate-600">{{ $order->customerEmail }}</p>
                @endif
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Order date</p>
                <p class="mt-1 font-semibold text-slate-900">{{ \Illuminate\Support\Carbon::parse($order->order_date)->format('M j, Y') }}</p>
                <p class="text-xs text-slate-600">{{ $order->order_time ? \Illuminate\Support\Carbon::parse($order->order_time)->format('g:i A') : 'Time unavailable' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Laundry type and quantity</p>
                <p class="mt-1 font-semibold text-slate-900">{{ $order->order_type }}</p>
                <p class="text-xs text-slate-600">{{ $order->order_type === 'Self Service' ? $order->self_service_loads.' loads' : number_format((float) $order->weight, 2).' kg' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">Order status</p>
                <p class="mt-1 font-semibold text-slate-900">{{ $order->status }}</p>
            </div>
            <div class="sm:col-span-2">
                <p class="text-xs font-medium text-slate-500">Services</p>
                <p class="mt-1 font-semibold text-slate-900">{{ $order->itemDetails ?: $order->services ?: 'Not recorded' }}</p>
            </div>
            @if (filled($order->specialRequest))
                <div class="sm:col-span-2">
                    <p class="text-xs font-medium text-slate-500">Special request</p>
                    <p class="mt-1 font-semibold text-slate-900">{{ $order->specialRequest }}</p>
                </div>
            @endif
        </section>

        <section class="rounded-xl border border-sky-100 bg-sky-50/70 p-4">
            <h3 class="text-sm font-bold text-slate-900">Payment details</h3>
            <dl class="mt-3 space-y-2 text-sm">
                <div class="flex items-start justify-between gap-3">
                    <dt class="text-slate-600">Order total</dt>
                    <dd class="text-right font-semibold text-slate-900">&#8369;{{ number_format((float) $order->total_amount, 2) }}</dd>
                </div>
                @if ((float) $order->storage_fee > 0)
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-slate-600">Storage fee included</dt>
                        <dd class="text-right font-semibold text-slate-900">&#8369;{{ number_format((float) $order->storage_fee, 2) }}</dd>
                    </div>
                @endif
                @if ($previouslyPaid > 0)
                    <div class="flex items-start justify-between gap-3">
                    <dt class="text-slate-600">Paid before this transaction &middot; {{ $order->initialPaymentMethod }}</dt>
                        <dd class="text-right font-semibold text-slate-900">&#8369;{{ number_format($previouslyPaid, 2) }}</dd>
                    </div>
                @endif
                <div class="flex items-start justify-between gap-3 border-t border-sky-200 pt-2">
                    <dt class="font-semibold text-slate-700">This payment &middot; {{ $payment->payment_method }}</dt>
                    <dd class="text-right font-bold text-slate-900">&#8369;{{ number_format((float) $payment->amount, 2) }}</dd>
                </div>
                <div class="flex items-start justify-between gap-3">
                    <dt class="text-slate-600">Paid to date</dt>
                    <dd class="text-right font-semibold text-slate-900">&#8369;{{ number_format((float) $order->amount_paid, 2) }}</dd>
                </div>
                <div class="flex items-start justify-between gap-3">
                    <dt class="text-slate-600">Remaining balance</dt>
                    <dd class="text-right font-semibold text-slate-900">&#8369;{{ number_format((float) $order->balance, 2) }}</dd>
                </div>
                <div class="flex items-start justify-between gap-3">
                    <dt class="text-slate-600">Payment reference</dt>
                    <dd class="break-all text-right font-semibold text-slate-900">{{ $payment->reference_number ?: 'Not recorded' }}</dd>
                </div>
                <div class="flex items-start justify-between gap-3">
                    <dt class="text-slate-600">Payment date</dt>
                    <dd class="text-right font-semibold text-slate-900">{{ \Illuminate\Support\Carbon::parse($payment->payment_date)->format('M j, Y') }}</dd>
                </div>
            </dl>
        </section>

        <div class="flex items-center justify-between gap-3 border-t border-dashed border-sky-200 pt-4 text-xs text-slate-500">
            <span>Order recorded by {{ $order->employeeName ?: 'Staff' }}</span>
            <span>{{ $payment->payment_status }}</span>
        </div>
        <p class="text-center text-xs font-medium text-slate-600">Thank you for choosing I Laba U.</p>
    </div>
</x-modal>
