<x-layout>
    <main class="mx-auto max-w-5xl space-y-6 p-4 sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-sky-700">Order details</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-900">Order #{{ $order->order_id }}</h1>
            </div>
            <a href="{{ route('records.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-sky-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-sky-50">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                Back to orders
            </a>
        </div>

        <section class="grid gap-4 sm:grid-cols-2">
            <div class="rounded-2xl border border-sky-100 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-bold text-slate-900">Customer</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Name</dt>
                        <dd class="mt-1 font-semibold text-slate-800">{{ $order->fullname ?: 'Customer unavailable' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Phone number</dt>
                        <dd class="mt-1 text-slate-800">{{ $order->phoneNumber ?: 'Not available' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Email</dt>
                        <dd class="mt-1 break-words text-slate-800">{{ $order->customerEmail ?: 'Not provided' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-2xl border border-sky-100 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-bold text-slate-900">Order</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Status</dt>
                        <dd class="mt-1 font-semibold text-slate-800">{{ $order->status }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Order date and time</dt>
                        <dd class="mt-1 text-slate-800">{{ $order->order_date }} {{ $order->order_time ?: '' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Laundry type</dt>
                        <dd class="mt-1 text-slate-800">{{ $order->order_type }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500">{{ $order->order_type === 'Self Service' ? 'Loads' : 'Weight' }}</dt>
                        <dd class="mt-1 text-slate-800">{{ $order->order_type === 'Self Service' ? $order->self_service_loads.' loads' : ($order->weight !== null ? number_format((float) $order->weight, 2).' kg' : 'Not recorded') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Services</dt>
                        <dd class="mt-1 text-slate-800">{{ $order->itemDetails ?: $order->services ?: 'Not available' }}</dd>
                    </div>
                    @if (filled($order->specialRequest))
                        <div>
                            <dt class="text-xs font-medium text-slate-500">Special request</dt>
                            <dd class="mt-1 whitespace-pre-wrap text-slate-800">{{ $order->specialRequest }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Recorded by</dt>
                        <dd class="mt-1 text-slate-800">{{ $order->employeeName ?: 'Employee unavailable' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-2xl border border-sky-100 bg-white p-5 shadow-sm sm:col-span-2">
                <h2 class="text-sm font-bold text-slate-900">Payment summary</h2>
                <dl class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Total</dt>
                        <dd class="mt-1 font-semibold text-slate-800">PHP {{ number_format((float) $order->total_amount, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Storage fee</dt>
                        <dd class="mt-1 font-semibold text-slate-800">PHP {{ number_format((float) $order->storage_fee, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Paid</dt>
                        <dd class="mt-1 font-semibold text-slate-800">PHP {{ number_format((float) $order->amount_paid, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500">Balance</dt>
                        <dd class="mt-1 font-semibold text-slate-800">PHP {{ number_format((float) $order->balance, 2) }}</dd>
                    </div>
                </dl>
            </div>
        </section>
    </main>
</x-layout>
