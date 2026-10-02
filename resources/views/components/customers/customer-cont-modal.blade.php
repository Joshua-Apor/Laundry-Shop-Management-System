@props([
'initial' => '',
'customerId',
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
    class="group flex w-full items-center justify-between gap-4 rounded-2xl border border-sky-200 bg-white p-4 text-left shadow-sm transition-colors hover:border-sky-300 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500 disabled:cursor-wait">
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
    <div class="mt-5 border-t border-sky-100 pt-5">
        <div class="flex items-center justify-between gap-2">
            <details class="group relative">
                <summary class="inline-flex cursor-pointer list-none items-center justify-center gap-2 rounded-lg bg-[#168cff] px-4 py-2 text-xs font-bold text-white transition-colors hover:bg-[#0878df] [&::-webkit-details-marker]:hidden">
                    <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                    Edit customer
                </summary>
                <form method="POST" action="{{ route('customers.update', $customerId) }}" class="absolute left-0 bottom-full mb-2 w-72 z-10 space-y-3 rounded-xl border border-slate-200 bg-white p-4 shadow-lg">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label for="customer-name-{{ $customerId }}" class="mb-1 block text-xs font-semibold text-slate-700">Name</label>
                        <input id="customer-name-{{ $customerId }}" name="name" value="{{ $name }}" required maxlength="255" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none">
                    </div>
                    <div>
                        <label for="customer-phone-{{ $customerId }}" class="mb-1 block text-xs font-semibold text-slate-700">Phone number</label>
                        <input id="customer-phone-{{ $customerId }}" name="contact_number" value="{{ $phone }}" required maxlength="255" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none">
                    </div>
                    <div>
                        <label for="customer-address-{{ $customerId }}" class="mb-1 block text-xs font-semibold text-slate-700">Address</label>
                        <textarea id="customer-address-{{ $customerId }}" name="address" required rows="3" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-sky-500 focus:outline-none">{{ $address }}</textarea>
                    </div>
                    <button type="submit" class="w-full rounded-lg bg-[#168cff] px-4 py-2 text-xs font-bold text-white transition-colors hover:bg-[#0878df]">Save changes</button>
                </form>
            </details>
            <button type="button" data-modal-trigger data-modal-target="customer-delete-{{ $customerId }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 px-4 py-2 text-xs font-bold text-red-600 transition-colors hover:bg-red-50">
                <i class="fa-solid fa-trash" aria-hidden="true"></i>
                Delete customer
            </button>
        </div>
    </div>
</x-modal>

<x-modal :id="'customer-delete-'.$customerId" title="Delete customer?">
    <p class="text-sm leading-6 text-slate-600">This permanently deletes {{ $name }} and all of their associated order and payment history.</p>
    <form method="POST" action="{{ route('customers.destroy', $customerId) }}" class="mt-5 flex justify-end gap-2">
        @csrf
        @method('DELETE')
        <button type="button" data-modal-close class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-xs font-bold text-white hover:bg-red-700">Delete customer</button>
    </form>
</x-modal>