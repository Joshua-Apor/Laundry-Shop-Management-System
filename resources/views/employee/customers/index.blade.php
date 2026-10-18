<x-layout>
    <div class="mx-auto max-w-7xl space-y-5 p-5 sm:p-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Customers</h1>
                <p class="mt-1 text-sm text-slate-500">Browse customer details and order history.</p>
            </div>
            <span class="shrink-0 rounded-full bg-white px-3 py-1 text-xs font-semibold text-[#168cff] shadow-sm ring-1 ring-sky-100" data-customer-count>{{ number_format($customers->total()) }} total</span>
        </div>
        <form method="GET" action="{{ route('customers.index') }}" class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_13rem] sm:items-end" data-customer-search-form>
            <div class="relative">
                <label class="mb-1 block text-xs font-semibold text-slate-600" for="customer-search">Search customers</label>
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-[2.55rem] text-sm text-slate-400" aria-hidden="true"></i>
                <input id="customer-search" type="search" name="search" value="{{ $search }}" placeholder="Search by name or phone..." data-customer-search-input class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-10 pr-4 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-[#168cff] focus:outline-none focus:ring-2 focus:ring-sky-100">
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600" for="customer-sort">Sort customers</label>
                <select id="customer-sort" name="sort" data-customer-sort class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-700 shadow-sm focus:border-[#168cff] focus:outline-none focus:ring-2 focus:ring-sky-100">
                    <option value="alphabetical" @selected($sort === 'alphabetical')>Alphabetical</option>
                    <option value="newest" @selected($sort === 'newest')>Newest first order</option>
                    <option value="oldest" @selected($sort === 'oldest')>Oldest first order</option>
                </select>
            </div>
        </form>
        <div data-customer-results>
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3" data-customer-list>
                @forelse ($customers as $customer)
                <x-customers.customer-cont-modal
                    :initial="strtoupper(substr($customer->fullname, 0, 2))"
                    :customer-id="$customer->customer_id"
                    :name="$customer->fullname"
                    :phone="$customer->phoneNumber"
                    :email="$customer->email"
                    :order-total="$customer->orders_count"
                    :price-total="number_format($customer->total_spent, 2)"
                    :first-order-date="$customer->first_order_date"
                    :modal-id="'customer-details-'.$loop->index"
                    :history-url="route('customers.orders', $customer->customer_id)" />
                @empty
                    <p class="rounded-xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-500 sm:col-span-2 xl:col-span-3">No customers with orders yet.</p>
                @endforelse
            </div>
            {{ $customers->links() }}
        </div>
        <p class="sr-only" role="status" aria-live="polite" data-customer-search-status></p>
    </div>
</x-layout>
