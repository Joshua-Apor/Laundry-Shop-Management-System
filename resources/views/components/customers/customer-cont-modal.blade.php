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
'historyUrl',
])

<button
    type="button"
    data-modal-trigger
    data-modal-target="{{ $modalId }}"
    aria-label="View details for {{ $name }}"
    class="card-entry-animation group relative flex min-h-32 w-full flex-col items-stretch gap-3 rounded-xl border border-slate-200 bg-white p-4 text-left shadow-sm transition-[border-color,box-shadow] hover:border-[#168cff]/40 hover:shadow-md focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#168cff] disabled:cursor-wait">
    <span class="flex min-w-0 items-center gap-3.5">
        <span class="grid size-10 shrink-0 place-items-center rounded-full bg-[#168cff] text-sm font-bold text-white ring-4 ring-sky-50">{{ $initial }}</span>
        <span class="min-w-0">
            <span class="block text-sm font-bold leading-tight text-slate-900">{{ $name }}</span>
            <span class="mt-0.5 flex items-center gap-1 text-xs text-slate-400"><i class="fa-solid fa-phone text-[#168cff]" aria-hidden="true"></i>{{ $phone }}</span>
        </span>
    </span>
    <span class="flex w-full items-center justify-between gap-3 border-t border-slate-100 pt-3">
        <span class="flex flex-1 items-center justify-between gap-3">
            <span class="block text-sm font-bold text-slate-800">{{ $orderTotal }} order{{ $orderTotal == 1 ? '' : 's' }}</span>
            <span class="block text-xs text-slate-400">₱{{ $priceTotal }} total</span>
        </span>
        <span data-loading-spinner class="absolute right-3 top-3 hidden size-4 animate-spin rounded-full border-2 border-sky-200 border-t-[#168cff]" aria-hidden="true"></span>
        <span data-loading-text class="sr-only">View Details</span>
    </span>
</button>

<x-modal :id="$modalId" title="Customer details">
    <div class="mb-5">
        <p class="text-xs font-medium text-slate-500">First order</p>
        <p class="mt-1 text-sm font-semibold text-slate-900">{{ \Illuminate\Support\Carbon::parse($firstOrderDate)->format('F j, Y') }}</p>
    </div>
    <dl class="grid grid-cols-2 gap-3">
        <div class="col-span-2 rounded-lg bg-slate-50 p-3">
            <dt class="text-xs font-medium text-slate-500">Customer name</dt>
            <dd class="mt-1 flex items-center gap-2.5 text-sm font-semibold text-slate-900">
                <span class="grid size-9 shrink-0 place-items-center rounded-full bg-[#168cff] text-xs font-bold text-white">{{ $initial }}</span>
                {{ $name }}
            </dd>
        </div>
        <div class="col-span-2 rounded-lg bg-slate-50 p-3">
            <dt class="text-xs font-medium text-slate-500">Phone number</dt>
            <dd class="mt-1 flex items-center gap-1.5 text-sm font-semibold text-slate-900"><i class="fa-solid fa-phone text-[#168cff]" aria-hidden="true"></i>{{ $phone }}</dd>
        </div>
        <div class="col-span-2 rounded-lg bg-slate-50 p-3">
            <dt class="text-xs font-medium text-slate-500">Address</dt>
            <dd class="mt-1 break-words text-sm font-semibold text-slate-900"><i class="fa-solid fa-map-marker-alt text-[#168cff]" aria-hidden="true"></i> {{ $address ?: 'Not provided' }}</dd>
        </div>
        <div class="rounded-lg bg-slate-50 p-3">
            <dt class="text-xs font-medium text-slate-500">Total orders</dt>
            <dd class="mt-1 text-lg font-bold text-slate-900">{{ $orderTotal }}</dd>
        </div>
        <div class="rounded-lg bg-slate-50 p-3">
            <dt class="text-xs font-medium text-slate-500">Total spent</dt>
            <dd class="mt-1 text-lg font-bold text-slate-900">₱{{ $priceTotal }}</dd>
        </div>
    </dl>
    <div class="mt-5 border-t border-sky-100 pt-5">
        <button type="button" data-modal-trigger data-modal-target="customer-history-{{ $customerId }}" data-customer-history-trigger class="inline-flex items-center gap-2 rounded-lg border border-sky-200 px-4 py-2 text-xs font-bold text-sky-700 transition-colors hover:bg-sky-50">
            <i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i>
            View Order History
        </button>
    </div>
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

<x-modal :id="'customer-history-'.$customerId" :title="$name.' — order history'">
    <div data-customer-order-history data-history-url="{{ $historyUrl }}" data-history-loaded="false" aria-live="polite">
        <p class="text-sm text-slate-500">Order history loads when you open this view.</p>
    </div>
    <div class="mt-5 flex justify-start border-t border-sky-100 pt-4">
        <button type="button" data-modal-back-target="{{ $modalId }}" class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Back</button>
    </div>
</x-modal>

<x-modal :id="'customer-delete-'.$customerId" title="Delete customer?">
    <p class="text-sm leading-6 text-slate-600">This action is irreversible. To confirm deletion, please type the full name <strong>{{ $name }}</strong> below:</p>
    <form method="POST" action="{{ route('customers.destroy', $customerId) }}" data-name-confirmation-form data-confirmation-name="{{ $name }}" class="mt-4 space-y-4">
        @csrf
        @method('DELETE')
        <label for="customer-delete-name-{{ $customerId }}" class="block text-xs font-semibold text-slate-700">Customer name</label>
        <input id="customer-delete-name-{{ $customerId }}" name="confirmation_name" type="text" autocomplete="off" data-name-confirmation-input aria-describedby="customer-delete-help-{{ $customerId }}" class="-mt-3 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none transition-colors focus:ring-2 focus:ring-sky-200">
        <p id="customer-delete-help-{{ $customerId }}" class="-mt-3 text-xs text-slate-500">Enter the name exactly as shown above.</p>
        <div class="flex justify-end gap-2">
            <button type="button" data-modal-close class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
            <button type="submit" disabled data-name-confirmation-submit class="cursor-not-allowed rounded-lg bg-red-600 px-4 py-2 text-xs font-bold text-white opacity-50 transition-opacity">Delete Permanently</button>
        </div>
    </form>
</x-modal>
