<x-layout>
    <h1 class="mb-6 text-2xl font-bold">Good Morning, User</h1>

    <div class="mb-6 grid max-w-5xl grid-cols-1 gap-6 md:grid-cols-5">
        <x-dashboard.metric-card title="Total Orders" :value="$totalOrders" />
        <x-dashboard.metric-card title="Completed" :value="$completedOrders" />
        <x-dashboard.metric-card title="Ready for Pickup" :value="$readyForPickupOrders" />
        <x-dashboard.metric-card title="Need Notify" :value="$needNotificationOrders" />
        <x-dashboard.metric-card title="Total Revenue" :value="'₱'.number_format($totalRevenue, 2)" />
    </div>

    <div>
        <div class="mb-4 flex flex-row items-center justify-between">
            <span class="text-base font-bold">Recent Orders</span>
            <a href="{{ route('orders.index') }}" class="cursor-pointer text-sm font-medium">View all →</a>
        </div>

        <div class="space-y-2">
            @forelse ($recentOrders as $order)
                <x-dashboard.customer-order
                    :code="'Order #'.$order->id"
                    :name="$order->fullname"
                    :services="$order->service"
                    :weight="$order->weight.'kg'"
                    :phone="$order->phoneNumber" />
            @empty
                <p class="rounded-xl border border-sky-200 bg-white p-4 text-sm text-slate-500">No orders yet.</p>
            @endforelse
        </div>
    </div>
</x-layout>
