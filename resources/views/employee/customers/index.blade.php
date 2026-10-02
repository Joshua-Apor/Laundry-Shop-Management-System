<x-layout>
    <div class="mx-auto max-w-6xl space-y-6 p-6">
        <h1 class="text-2xl font-bold text-slate-900">Customers</h1>
        <form method="GET" action="{{ route('customers.index') }}">
            <label class="sr-only" for="customer-search">Search customers</label>
            <input
                id="customer-search"
                type="search"
                name="search"
                value="{{ $search }}"
                placeholder="Search by name or phone..."
                class="w-full rounded-lg border border-sky-200 bg-white px-4 py-2 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300"
            >
        </form>
        <div class="space-y-3">
            @forelse ($customers as $customer)
                <x-customers.customer-cont-modal
                    :initial="strtoupper(substr($customer->fullname, 0, 2))"
                    :customer-id="$customer->customer_id"
                    :name="$customer->fullname"
                    :phone="$customer->phoneNumber"
                    :address="$customer->address"
                    :order-total="$customer->orders_count"
                    :price-total="number_format($customer->total_spent, 2)"
                    :first-order-date="$customer->first_order_date"
                    :modal-id="'customer-details-'.$loop->index" />
            @empty
                <p class="rounded-2xl border border-sky-100 bg-white p-6 text-center text-sm text-slate-400">No customers with orders yet.</p>
            @endforelse
        </div>
        {{ $customers->links() }}
    </div>
</x-layout>
