<x-layout>
    <div class="mx-auto max-w-6xl space-y-6 p-6">
        <h1 class="text-2xl font-bold text-slate-900">Customers</h1>
        <div class="space-y-3">
            @forelse ($customers as $customer)
                <x-customers.customer-cont
                    :initial="strtoupper(substr($customer->fullname, 0, 2))"
                    :name="$customer->fullname"
                    :phone="$customer->phoneNumber"
                    :order-total="$customer->orders_count"
                    :price-total="number_format($customer->total_spent, 2)" />
            @empty
                <p class="rounded-2xl border border-sky-100 bg-white p-6 text-center text-sm text-slate-400">No customers with orders yet.</p>
            @endforelse
        </div>
        {{ $customers->links() }}
    </div>
</x-layout>
