@props([
    'initial' => '',
    'name' => '',
    'phone' => '',
    'address' => '',
    'orderTotal' => 0,
    'priceTotal' => '0.00',
    'firstOrderDate',
    'modalId',
])

<button
    type="button"
    data-modal-trigger
    data-modal-target="{{ $modalId }}"
    aria-label="View details for {{ $name }}"
    class="group flex w-full items-center justify-between gap-4 rounded-2xl border border-sky-200 bg-white p-4 text-left shadow-sm transition-colors hover:border-sky-300 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500 disabled:cursor-wait"
>
    <span class="flex min-w-0 items-center gap-3.5">
        <span class="grid size-10 shrink-0 place-items-center rounded-full bg-[#168cff] text-sm font-bold text-white">{{ $initial }}</span>
        <span class="min-w-0">
            <span class="block text-sm font-bold leading-tight text-slate-900">{{ $name }}</span>
            <span class="mt-0.5 flex items-center gap-1 text-xs text-slate-400"><i class="fa-solid fa-phone text-[#168cff]" aria-hidden="true"></i>{{ $phone }}</span>
        </span>
    </span>
    <span class="flex shrink-0 items-center gap-3">
        <span class="text-right">
            <span class="block text-sm font-bold text-slate-800">{{ $orderTotal }} order{{ $orderTotal == 1 ? '' : 's' }}</span>
            <span class="block text-xs text-slate-400">₱{{ $priceTotal }} total</span>
        </span>
        <span data-loading-spinner class="hidden size-4 animate-spin rounded-full border-2 border-sky-200 border-t-sky-600" aria-hidden="true"></span>
        <span data-loading-text class="sr-only">View Details</span>
    </span>
</button>

<x-modal :id="$modalId" title="Customer details">
    <div class="mb-5">
        <p class="text-xs font-medium text-slate-500">First order</p>
        <p class="mt-1 text-sm font-semibold text-slate-900">{{ \Illuminate\Support\Carbon::parse($firstOrderDate)->format('F j, Y') }}</p>
    </div>
    <dl class="grid grid-cols-2 gap-3">
        <div class="col-span-2 rounded-xl bg-sky-50 p-4">
            <dt class="text-xs font-medium text-slate-500">Customer name</dt>
            <dd class="mt-1 flex items-center gap-2.5 text-sm font-semibold text-slate-900">
                <span class="grid size-9 shrink-0 place-items-center rounded-full bg-[#168cff] text-xs font-bold text-white">{{ $initial }}</span>
                {{ $name }}
            </dd>
        </div>
        <div class="col-span-2 rounded-xl bg-sky-50 p-4">
            <dt class="text-xs font-medium text-slate-500">Phone number</dt>
            <dd class="mt-1 flex items-center gap-1.5 text-sm font-semibold text-slate-900"><i class="fa-solid fa-phone text-[#168cff]" aria-hidden="true"></i>{{ $phone }}</dd>
        </div>
        <div class="col-span-2 rounded-xl bg-sky-50 p-4">
            <dt class="text-xs font-medium text-slate-500">Address</dt>
            <dd class="mt-1 break-words text-sm font-semibold text-slate-900"><i class="fa-solid fa-map-marker-alt text-[#168cff]" aria-hidden="true"></i> {{ $address ?: 'Not provided' }}</dd>
        </div>
        <div class="rounded-xl bg-sky-50 p-4">
            <dt class="text-xs font-medium text-slate-500">Total orders</dt>
            <dd class="mt-1 text-lg font-bold text-slate-900">{{ $orderTotal }}</dd>
        </div>
        <div class="rounded-xl bg-sky-50 p-4">
            <dt class="text-xs font-medium text-slate-500">Total spent</dt>
            <dd class="mt-1 text-lg font-bold text-slate-900">₱{{ $priceTotal }}</dd>
        </div>
    </dl>
</x-modal>
