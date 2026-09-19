<x-layout>

    <div class="max-w-6xl mx-auto p-6 space-y-6">
        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-slate-900">Orders</h1>
            <a href="{{ route('orders.create') }}" class="bg-[#e92c81] hover:bg-[#d01e6f] text-white text-sm font-medium px-4 py-2 rounded-lg flex items-center gap-1.5 transition-colors shadow-sm">
                <span>+</span> New Order
            </a>
        </div>

        <!-- Filter & Search Bar -->
        <form action="{{ route('orders.index') }}" method="GET" class="flex flex-col md:flex-row gap-3 items-center justify-between">
            <!-- Search Input -->
            <div class="relative w-full md:flex-1">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search by name, claim #, or phone..."
                    class="w-full bg-white border border-sky-200 rounded-lg px-4 py-2 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300" />
            </div>

            <!-- Filter  -->
            <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-1 md:pb-0">
                @foreach (['' => 'All', 'Received' => 'Received', 'Processing' => 'Processing', 'Ready for Pickup' => 'Ready for Pickup', 'Completed' => 'Completed'] as $filterStatus => $label)
                    <button
                        type="submit"
                        name="status"
                        value="{{ $filterStatus }}"
                        @class([
                            'text-xs font-medium px-3.5 py-2 rounded-lg whitespace-nowrap',
                            'bg-[#e92c81] text-white' => $status === $filterStatus,
                            'bg-white border border-sky-200 text-slate-600 hover:bg-slate-50' => $status !== $filterStatus,
                        ])>
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </form>

        <!-- Orders List -->
        <div class="space-y-4">
            @if (session('success'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Customer Area -->
            <div class="space-y-3">
                @forelse ($orders as $order)
                <div class="bg-white rounded-2xl border border-sky-100 p-5 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 hover:border-sky-200 transition-colors">

                    <!-- Left Column: Customer & Service Details -->
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <!-- Initials Avatar -->
                            <div class="w-10 h-10 rounded-full bg-[#e92c81] text-white flex items-center justify-center font-bold text-xs shrink-0">
                                {{ strtoupper(substr($order->fullname, 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm leading-tight">{{ $order->fullname }}</h3>
                                <p class="text-xs text-slate-400 mt-0.5">📞 {{ $order->phoneNumber }}</p>
                            </div>
                        </div>

                        <!-- Services Badges -->
                        <div class="flex flex-wrap items-center gap-1.5 pt-1">
                            @if(is_array($order->service))
                            @foreach($order->service as $service)
                            <span class="bg-sky-50 text-sky-700 text-[11px] font-semibold px-2.5 py-0.5 rounded-full border border-sky-100">
                                {{ $service }}
                            </span>
                            @endforeach
                            @endif
                            <span class="text-xs text-slate-500 font-medium ml-1">({{ $order->weight }} kg)</span>
                        </div>

                        @if($order->specialRequest)
                        <p class="text-xs text-slate-500 italic bg-slate-50 p-2 rounded-md border border-slate-100">
                            "{{ $order->specialRequest }}"
                        </p>
                        @endif
                    </div>

                    <!-- Right Column: Status, Payment Totals & Actions -->
                    <div class="flex md:flex-col justify-between md:items-end items-center border-t md:border-t-0 pt-3 md:pt-0 border-slate-100 gap-3">
                        <span class="bg-pink-100 text-[#e92c81] text-xs font-bold px-2.5 py-1 rounded-full uppercase tracking-wider mb-1">
                            {{ $order->status }}
                        </span>

                        <div class="text-right">
                            <p class="text-base font-bold text-slate-900">₱{{ number_format($order->total_amount, 2) }}</p>
                            <p class="text-xs text-slate-400">
                                {{ $order->paymentMethod }} • Paid: ₱{{ number_format($order->amount_paid, 2) }}
                            </p>
                        </div>

                        <details class="relative md:self-end">
                            <summary class="cursor-pointer list-none rounded-lg border border-sky-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                Actions
                            </summary>
                            <div class="absolute right-0 z-10 mt-2 w-52 rounded-xl border border-sky-100 bg-white p-2 shadow-lg space-y-1">
                                <a href="{{ route('orders.show', $order) }}" class="block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-sky-50">View details</a>

                                @foreach (['Processing' => 'Move to processing', 'Ready for Pickup' => 'Move to ready for pickup', 'Completed' => 'Move to completed'] as $newStatus => $label)
                                    @if ($order->status !== $newStatus)
                                        <form action="{{ route('orders.status.update', $order) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $newStatus }}">
                                            <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm text-slate-700 hover:bg-sky-50">{{ $label }}</button>
                                        </form>
                                    @endif
                                @endforeach

                                @if ($order->amount_paid < $order->total_amount)
                                    <form action="{{ route('orders.payment.record', $order) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full rounded-lg px-3 py-2 text-left text-sm text-slate-700 hover:bg-sky-50">Record payment</button>
                                    </form>
                                @endif

                                <a href="sms:{{ $order->phoneNumber }}?body={{ rawurlencode("Hello {$order->fullname}, your laundry order #{$order->id} is {$order->status}.") }}" class="block rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-sky-50">Notify customer</a>
                            </div>
                        </details>
                    </div>

                </div>
                @empty
                <div class="text-center py-12 bg-white rounded-2xl border border-sky-100 p-6">
                    <p class="text-slate-400 text-sm">No laundry orders found.</p>
                </div>
                @endforelse
            </div>
            
            {{ $orders->links() }}
        </div>

</x-layout>
