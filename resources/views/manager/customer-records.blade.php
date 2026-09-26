<x-layout>
    <div class="space-y-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Customer Records</h1>
            <p class="text-sm text-slate-500">{{ $customerCount }} registered customers</p>
        </div>

        <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
            <x-dashboard.metric-card title="Total Customers" :value="$customerCount" />
            <x-dashboard.metric-card title="Total Orders" :value="$orderCount" />
            <x-dashboard.metric-card title="Repeat Customers" :value="$repeatCustomerCount" />
            <x-dashboard.metric-card title="Avg. Spend" :value="'₱'.number_format($averageSpend, 0)" />
        </div>

        <form method="GET" action="{{ route('manager.customer-records') }}">
            <label class="sr-only" for="customer-search">Search customers</label>
            <input id="customer-search" name="search" value="{{ $search }}" placeholder="Search by name or phone..." class="w-full rounded-lg border border-sky-200 bg-white px-4 py-2 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300">
        </form>

        <div class="overflow-hidden rounded-xl border border-sky-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left text-xs">
                    <thead class="bg-sky-50 uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-3 py-3 font-semibold">Customer</th>
                            <th class="px-3 py-3 font-semibold">Phone Number</th>
                            <th class="px-3 py-3 text-center font-semibold">Total Orders</th>
                            <th class="px-3 py-3 font-semibold">Total Spent</th>
                            <th class="px-3 py-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sky-100">
                        @forelse ($customers as $customer)
                            <tr>
                                <td class="px-3 py-3 font-semibold text-slate-800"><span class="mr-2 inline-grid size-7 place-items-center rounded-full bg-[#e91e8c] text-[10px] text-white">{{ strtoupper(substr($customer->fullname, 0, 2)) }}</span>{{ $customer->fullname }}</td>
                                <td class="px-3 py-3 text-slate-500">{{ $customer->phoneNumber }}</td>
                                <td class="px-3 py-3 text-center font-semibold text-slate-800">{{ $customer->orders_count }}</td>
                                <td class="px-3 py-3 font-semibold text-slate-800">₱{{ number_format($customer->total_spent, 2) }}</td>
                                <td class="px-3 py-3"><span class="rounded-full border border-pink-200 bg-pink-50 px-2 py-1 text-[10px] font-semibold text-pink-600">Active</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-10 text-center text-slate-400">No customers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{ $customers->links() }}
    </div>
</x-layout>
