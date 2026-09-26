<x-layout>
    <div class="space-y-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Order Records &amp; Payments</h1>
            <p class="text-sm text-slate-500">{{ $orders->total() }} total orders</p>
        </div>

        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
            <x-dashboard.metric-card title="Total Revenue" :value="'₱'.number_format($totalRevenue, 0)" />
            <x-dashboard.metric-card title="Total Collected" :value="'₱'.number_format($totalCollected, 0)" />
            <x-dashboard.metric-card title="Outstanding" :value="'₱'.number_format($outstanding, 0)" />
            <x-dashboard.metric-card title="Completed Orders" :value="$completedOrders" />
        </div>

        <form method="GET" action="{{ route('manager.orders-payments') }}">
            <label class="sr-only" for="order-search">Search orders</label>
            <input id="order-search" name="search" value="{{ $search }}" placeholder="Search by name, claim #, or phone..." class="w-full rounded-lg border border-sky-200 bg-white px-4 py-2 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300">
        </form>

        <div class="overflow-hidden rounded-xl border border-sky-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1100px] text-left text-xs">
                    <thead class="bg-sky-50 uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-3 py-3 font-semibold">Claim #</th><th class="px-3 py-3 font-semibold">Customer</th><th class="px-3 py-3 font-semibold">Date</th><th class="px-3 py-3 font-semibold">Services</th><th class="px-3 py-3 font-semibold">Weight</th><th class="px-3 py-3 font-semibold">Total</th><th class="px-3 py-3 font-semibold">Paid</th><th class="px-3 py-3 font-semibold">Balance</th><th class="px-3 py-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sky-100">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-3 py-3 font-mono text-slate-500">CLM-{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-3 py-3"><p class="font-semibold text-slate-800">{{ $order->fullname }}</p><p class="text-[10px] text-slate-400">{{ $order->phoneNumber }}</p></td>
                                <td class="px-3 py-3 text-slate-500">{{ $order->order_date }}</td>
                                <td class="px-3 py-3 text-slate-600">{{ implode(', ', $order->service ?? []) }}</td>
                                <td class="px-3 py-3 text-slate-600">{{ $order->weight }}kg</td>
                                <td class="px-3 py-3 font-semibold">₱{{ number_format($order->total_amount, 0) }}</td>
                                <td class="px-3 py-3 font-semibold text-cyan-600">₱{{ number_format($order->amount_paid, 0) }}</td>
                                <td class="px-3 py-3 font-semibold text-pink-600">₱{{ number_format($order->balance, 0) }}</td>
                                <td class="px-3 py-3"><span class="rounded-full bg-sky-50 px-2 py-1 text-[10px] font-semibold text-sky-700">{{ $order->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="px-4 py-10 text-center text-slate-400">No orders found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $orders->links() }}
    </div>
</x-layout>
