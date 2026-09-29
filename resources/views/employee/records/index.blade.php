<x-layout>
    <div class="mx-auto max-w-6xl space-y-6 p-6">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-2xl font-bold text-slate-900">Orders Records</h1>
            <a href="{{ route('orders.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-[#168cff] px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-[#0878df]">
                <i class="fa-solid fa-plus" aria-hidden="true"></i> New Order
            </a>
        </div>

        <form action="{{ route('records.index') }}" method="GET" class="flex flex-col items-center justify-between gap-3 md:flex-row">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, claim #, or phone..." class="w-full rounded-lg border border-sky-200 bg-white px-4 py-2 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300 md:flex-1">
            <div class="flex w-full items-center gap-2 overflow-x-auto pb-1 md:w-auto md:pb-0">
                @foreach (['' => 'All', 'Received' => 'Received', 'Processing' => 'Processing', 'Ready for Pickup' => 'Ready for Pickup', 'Completed' => 'Completed'] as $filterStatus => $label)
                    <button type="submit" name="status" value="{{ $filterStatus }}" @class(['whitespace-nowrap rounded-lg px-3.5 py-2 text-xs font-medium', 'bg-[#168cff] text-white' => $status === $filterStatus, 'border border-sky-200 bg-white text-slate-600 hover:bg-sky-50' => $status !== $filterStatus])>{{ $label }}</button>
                @endforeach
            </div>
        </form>

        @if (session('success'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
        @endif

        <div class="space-y-3">
            @forelse ($orders as $order)
                <article class="flex flex-col justify-between gap-4 rounded-2xl border border-sky-200 bg-white p-5 shadow-sm transition-colors hover:border-sky-300 md:flex-row md:items-center">
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <div class="grid size-10 shrink-0 place-items-center rounded-full bg-[#168cff] text-xs font-bold text-white">{{ strtoupper(substr($order->fullname, 0, 2)) }}</div>
                            <div>
                                <h2 class="text-sm font-bold leading-tight text-slate-900">{{ $order->fullname }}</h2>
                                <p class="mt-0.5 text-xs text-slate-400"><i class="fa-solid fa-phone mr-1" aria-hidden="true"></i>{{ $order->phoneNumber }}</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5">
                            @foreach (($order->service ?? []) as $service)
                                <span class="rounded-full border border-sky-100 bg-sky-50 px-2.5 py-0.5 text-[11px] font-semibold text-sky-700">{{ $service }}</span>
                            @endforeach
                            <span class="ml-1 text-xs font-medium text-slate-500">({{ $order->weight }} kg)</span>
                        </div>
                        @if ($order->specialRequest)
                            <p class="rounded-md border border-slate-100 bg-slate-50 p-2 text-xs italic text-slate-500">"{{ $order->specialRequest }}"</p>
                        @endif
                    </div>
                    <div class="flex items-center justify-between gap-3 border-t border-slate-100 pt-3 md:flex-col md:items-end md:border-t-0 md:pt-0">
                        <div class="text-right">
                            <span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-bold uppercase tracking-wider text-[#168cff]">{{ $order->status }}</span>
                            <p class="mt-2 text-base font-bold text-slate-900">₱{{ number_format($order->total_amount, 2) }}</p>
                            <p class="text-xs text-slate-400">{{ $order->paymentMethod }} · Paid: ₱{{ number_format($order->amount_paid, 2) }}</p>
                        </div>
                        <button
                            type="button"
                            data-record-details-button
                            data-modal-target="record-details-{{ $order->order_id }}"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-[#168CFF] bg-[#168CFF] px-3 py-2 text-xs font-semibold text-white transition-colors hover:bg-[#0878df] disabled:cursor-wait disabled:opacity-80"
                        >
                            <span data-loading-spinner class="hidden h-3.5 w-3.5 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                            <span data-loading-text>View Details</span>
                        </button>
                    </div>
                </article>

                <dialog
                    id="record-details-{{ $order->order_id }}"
                    data-record-details-modal
                    aria-labelledby="record-details-title-{{ $order->order_id }}"
                    class="m-auto max-h-[90vh] w-[min(32rem,calc(100vw-2rem))] overflow-y-auto rounded-2xl border border-sky-100 bg-white p-0 text-slate-900 shadow-2xl backdrop:bg-slate-900/50"
                >
                    <div class="flex items-start justify-between gap-4 border-b border-sky-100 p-5">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-[#168cff]">Order details</p>
                            <h2 id="record-details-title-{{ $order->order_id }}" class="mt-1 text-xl font-bold text-slate-900">
                                Order #{{ $order->order_id }}
                            </h2>
                        </div>
                        <button
                            type="button"
                            data-close-record-modal
                            aria-label="Close order details"
                            class="grid size-9 shrink-0 place-items-center rounded-lg text-slate-500 transition-colors hover:bg-sky-50 hover:text-slate-800"
                        >
                            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                        </button>
                    </div>

                    <dl class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs font-medium text-slate-500">Customer</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $order->fullname }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-slate-500">Phone</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $order->phoneNumber }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-slate-500">Status</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $order->status }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-slate-500">Order date</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $order->order_date }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-xs font-medium text-slate-500">Services</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ implode(', ', $order->service ?? []) ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-slate-500">Laundry weight</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $order->weight }} kg</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-slate-500">Payment method</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">{{ $order->paymentMethod }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-slate-500">Total amount</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">₱{{ number_format($order->total_amount, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-slate-500">Amount paid</dt>
                            <dd class="mt-1 text-sm font-semibold text-slate-900">₱{{ number_format($order->amount_paid, 2) }}</dd>
                        </div>
                        @if ($order->specialRequest)
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-medium text-slate-500">Special request</dt>
                                <dd class="mt-1 whitespace-pre-wrap text-sm text-slate-700">{{ $order->specialRequest }}</dd>
                            </div>
                        @endif
                    </dl>
                </dialog>
            @empty
                <div class="rounded-2xl border border-sky-100 bg-white p-12 text-center text-sm text-slate-400">No laundry orders found.</div>
            @endforelse
        </div>

        {{ $orders->links() }}
    </div>
</x-layout>
