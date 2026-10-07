@props(['order'])

<x-modal :id="'record-details-'.$order->order_id" :title="'Order #'.$order->order_id">
    <div class="space-y-5">
        <section>
            <h3 class="mb-3 flex items-center gap-2 text-sm font-bold text-slate-800">
                <i class="fa-solid fa-user text-[#168cff]" aria-hidden="true"></i>
                Customer information
            </h3>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <x-orders.info-item label="Customer name" icon="fa-user-tag">{{ $order->fullname }}</x-orders.info-item>
                <x-orders.info-item label="Phone number" icon="fa-phone">{{ $order->phoneNumber }}</x-orders.info-item>
                <div class="sm:col-span-2">
                    <x-orders.info-item label="Address" icon="fa-location-dot">{{ $order->customerAddress }}</x-orders.info-item>
                </div>
            </div>
        </section>

        <details class="group rounded-xl border border-sky-100 bg-white">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 p-4 text-sm font-bold text-slate-800 marker:content-none">
                <span class="flex items-center gap-2"><i class="fa-solid fa-shirt text-[#168cff]" aria-hidden="true"></i>Order information</span>
                <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform group-open:rotate-180" aria-hidden="true"></i>
            </summary>
            <div class="grid grid-cols-1 gap-3 border-t border-sky-100 p-4 sm:grid-cols-2">
                <x-orders.info-item label="Status" icon="fa-circle-check">{{ $order->status }}</x-orders.info-item>
                <x-orders.info-item :label="$order->order_type === 'Self Service' ? 'Self service loads' : 'Drop off weight'" icon="fa-weight-scale">{{ $order->order_type === 'Self Service' ? $order->self_service_loads.' loads' : number_format((float) $order->weight, 2).' kg' }}</x-orders.info-item>
                <x-orders.info-item label="Order date" icon="fa-calendar-days">{{ $order->order_date }}</x-orders.info-item>
                <x-orders.info-item label="Order time" icon="fa-clock">{{ $order->order_time ? \Illuminate\Support\Carbon::parse($order->order_time)->format('g:i A') : 'Time unavailable' }}</x-orders.info-item>
                <x-orders.info-item label="Pickup date" icon="fa-calendar-check">{{ $order->pickup_date }}</x-orders.info-item>
                <div class="sm:col-span-2">
                    <x-orders.info-item label="Services" icon="fa-soap">{{ $order->services }}</x-orders.info-item>
                </div>
                <div class="sm:col-span-2">
                    <x-orders.info-item label="Recorded by" icon="fa-user-gear">{{ $order->employeeName }}</x-orders.info-item>
                </div>
            </div>
        </details>

        <details class="group rounded-xl border border-sky-100 bg-white">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 p-4 text-sm font-bold text-slate-800 marker:content-none">
                <span class="flex items-center gap-2"><i class="fa-solid fa-credit-card text-[#168cff]" aria-hidden="true"></i>Payment information</span>
                <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform group-open:rotate-180" aria-hidden="true"></i>
            </summary>
            <div class="grid grid-cols-1 gap-3 border-t border-sky-100 p-4 sm:grid-cols-2">
                <x-orders.info-item label="Total amount" icon="fa-receipt">₱{{ number_format((float) $order->total_amount, 2) }}</x-orders.info-item>
                <x-orders.info-item label="Amount paid" icon="fa-money-bill-wave">₱{{ number_format((float) $order->amount_paid, 2) }}</x-orders.info-item>
                <x-orders.info-item label="Balance" icon="fa-wallet">₱{{ number_format((float) $order->balance, 2) }}</x-orders.info-item>
                <x-orders.info-item label="Payment method" icon="fa-credit-card">{{ $order->paymentMethod }}</x-orders.info-item>
                <x-orders.info-item label="Payment status" icon="fa-circle-info">{{ $order->paymentStatus }}</x-orders.info-item>
                <x-orders.info-item label="Payment date" icon="fa-calendar-day">{{ $order->paymentDate }}</x-orders.info-item>
                <div class="sm:col-span-2">
                    <x-orders.info-item label="Payment reference" icon="fa-hashtag">{{ $order->referenceNumber }}</x-orders.info-item>
                </div>
            </div>
        </details>
    </div>
</x-modal>
