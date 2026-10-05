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
                @foreach (['' => 'All', 'Processing' => 'Processing', 'Ready for Pickup' => 'Ready for Pickup', 'Completed' => 'Completed'] as $filterStatus => $label)
                <button type="submit" name="status" value="{{ $filterStatus }}" @class(['whitespace-nowrap rounded-lg px-3.5 py-2 text-xs font-medium', 'bg-[#168cff] text-white'=> $status === $filterStatus, 'border border-sky-200 bg-white text-slate-600 hover:bg-sky-50' => $status !== $filterStatus])>{{ $label }}</button>
                @endforeach
            </div>
        </form>

        <div class="hidden grid-cols-[1.1fr_1.4fr_1fr_1.2fr_1fr] gap-4 px-5 py-2 text-xs font-semibold uppercase tracking-wide text-slate-500 sm:grid">
            <span>Order Number</span>
            <span>Customer Name</span>
            <span>Laundry Weight</span>
            <span>Total Amount</span>
            <span>Status</span>
        </div>
        <div class="space-y-2">
            @forelse ($orders as $order)
            <div class="relative">
                <div class="relative grid min-h-20 grid-cols-2 items-center gap-x-4 gap-y-3 rounded-2xl border border-sky-200 bg-white px-4 py-4 text-sm shadow-sm transition-colors hover:border-sky-300 hover:bg-sky-50/40 sm:grid-cols-[1.1fr_1.4fr_1fr_1.2fr_1fr] sm:gap-4 sm:px-5">
                    <button type="button" data-modal-trigger data-modal-target="record-details-{{ $order->order_id }}" aria-label="View details for order {{ $order->order_id }}" class="absolute inset-0 z-10 rounded-2xl focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500">
                    </button>
                    <span class="pointer-events-none relative z-0"><span class="inline-flex rounded-md border border-sky-200 bg-sky-50 px-1.5 py-0.5 font-mono text-xs font-bold text-sky-800">Order #{{ $order->order_id }}</span><span class="mt-1 block font-sans text-xs text-slate-500">
                            {{ \Illuminate\Support\Carbon::parse($order->order_date)->format('M j, Y') }}
                            <span class="block">{{ $order->order_time ? \Illuminate\Support\Carbon::parse($order->order_time)->format('g:i A') : 'Time unavailable' }}</span>
                            </span></span>
                    <span class="pointer-events-none relative z-0 col-span-2 row-start-2 min-w-0 sm:row-auto sm:col-span-1">
                        <span class="block truncate font-medium text-slate-900">{{ $order->fullname }}</span>
                    </span>
                    <span class="pointer-events-none relative z-0 text-slate-600">{{ $order->weight }} kg</span>
                    <span class="pointer-events-none relative z-0 font-semibold text-slate-900">₱{{ number_format((float) $order->total_amount, 2) }}</span>
                    <div class="col-start-2 row-start-1 sm:row-auto sm:col-auto">
                        <x-orders.status-control :order="$order" />
                    </div>
                </div>
                <x-orders.record-details-modal :order="$order" />
            </div>
            @empty
            <div class="rounded-2xl border border-sky-100 bg-white p-12 text-center text-sm text-slate-400">No laundry orders found.</div>
            @endforelse
        </div>

        {{ $orders->links() }}
    </div>
</x-layout>
