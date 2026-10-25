<x-layout>
    <div class="mx-auto max-w-7xl space-y-5 p-5 sm:p-6">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Customers</h1>
                <p class="mt-1 text-sm text-slate-500">Browse customer details and order history.</p>
            </div>
            <span class="shrink-0 rounded-full bg-white px-3 py-1 text-xs font-semibold text-[#168cff] shadow-sm ring-1 ring-sky-100" data-customer-count>{{ number_format($customers->total()) }} total</span>
        </div>
        <form method="GET" action="{{ route('manager.customer-records') }}" class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_13rem] sm:items-end" data-customer-search-form>
            @if ($activity !== 'custom')
            <input type="hidden" name="date_from" value="{{ $dateFrom ?? '' }}">
            <input type="hidden" name="date_to" value="{{ $dateTo ?? '' }}">
            @endif
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600" for="customer-search">Search customers</label>
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400" aria-hidden="true"></i>
                    <input id="customer-search" type="search" name="search" value="{{ $search }}" placeholder="Search by name or phone..." data-customer-search-input class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-10 pr-4 text-sm text-slate-700 shadow-sm placeholder:text-slate-400 focus:border-[#168cff] focus:outline-none focus:ring-2 focus:ring-sky-100">
                </div>
            </div>
            <div>
                <label class="mb-1 block text-xs font-semibold text-slate-600" for="customer-sort">Sort customers</label>
                <select id="customer-sort" name="sort" data-customer-sort class="w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm text-slate-700 shadow-sm focus:border-[#168cff] focus:outline-none focus:ring-2 focus:ring-sky-100">
                    <option value="alphabetical" @selected($sort==='alphabetical' )>Alphabetical</option>
                    <option value="newest" @selected($sort==='newest' )>Newest activity</option>
                    <option value="oldest" @selected($sort==='oldest' )>Oldest first order</option>
                </select>
            </div>
        </form>
        <div data-customer-results class="transition-opacity" aria-busy="false">
            <p class="mb-2 text-xs font-medium text-slate-700">Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} customers</p>
            @php
            $timelineGroups = $customers->getCollection()->groupBy(function ($customer) {
            $date = \Illuminate\Support\Carbon::parse($customer->latest_order_date);

            return $date->lt(now()->startOfYear()) ? $date->format('Y') : $date->toDateString();
            })->sortKeysDesc();
            @endphp
            <div class="space-y-4" data-customer-list>
                @forelse ($timelineGroups as $timelineKey => $customerGroup)
                <section aria-label="Customer activity date group">
                    <x-timeline-divider :date-key="$timelineKey" />
                    <div class="grid gap-3">
                        @foreach ($customerGroup as $customer)
                        <x-customers.customer-cont-modal
                            :customer-id="$customer->customer_id"
                            :name="$customer->fullname"
                            :phone="$customer->phoneNumber"
                            :email="$customer->email"
                            :order-total="$customer->orders_count"
                            :price-total="number_format($customer->total_spent, 2)"
                            :first-order-date="$customer->first_order_date"
                            :modal-id="'manager-customer-details-'.$customer->customer_id"
                            :history-url="route('customers.orders', $customer->customer_id)" />
                        @endforeach
                    </div>
                </section>
                @empty
                <p class="col-span-full rounded-xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-500">No customers with orders yet.</p>
                @endforelse
            </div>
            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs font-medium text-slate-700">Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} customers</p>
                <div class="flex justify-center [&_[aria-current=page]>span]:rounded-lg [&_[aria-current=page]>span]:border-[#168cff] [&_[aria-current=page]>span]:bg-[#168cff] [&_[aria-current=page]>span]:text-white [&_a]:rounded-lg [&_a:hover]:bg-sky-50 [&_a:hover]:text-[#168cff] sm:justify-end">
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
        <p class="sr-only" role="status" aria-live="polite" data-customer-search-status></p>
    </div>
</x-layout>
