<x-layout>
    <div class="max-w-2xl mx-auto p-6 space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Claim #{{ $order->id }}</p>
                <h1 class="text-2xl font-bold text-slate-900">Order details</h1>
            </div>
            <a href="{{ route('orders.index') }}" class="text-sm font-medium text-[#e92c81] hover:text-[#d01e6f]">Back to orders</a>
        </div>

        <div class="space-y-6 rounded-2xl border border-sky-100 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">{{ $order->fullname }}</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $order->phoneNumber }}</p>
                </div>
                <span class="rounded-full bg-pink-100 px-2.5 py-1 text-xs font-bold uppercase tracking-wider text-[#e92c81]">{{ $order->status }}</span>
            </div>

            <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Services</dt>
                    <dd class="mt-1 text-slate-700">{{ implode(', ', $order->service ?? []) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Weight</dt>
                    <dd class="mt-1 text-slate-700">{{ $order->weight }} kg</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total</dt>
                    <dd class="mt-1 font-bold text-slate-900">₱{{ number_format($order->total_amount, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">Payment</dt>
                    <dd class="mt-1 text-slate-700">{{ $order->paymentMethod }} · Paid: ₱{{ number_format($order->amount_paid, 2) }}</dd>
                </div>
            </dl>

            @if ($order->specialRequest)
                <div class="rounded-lg border border-slate-100 bg-slate-50 p-3 text-sm text-slate-600">
                    <span class="font-semibold text-slate-700">Special request:</span> {{ $order->specialRequest }}
                </div>
            @endif
        </div>
    </div>
</x-layout>
