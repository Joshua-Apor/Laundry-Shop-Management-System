<x-layout>
    <div class="space-y-5">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Products and Services</h1>
            <p class="text-sm text-slate-500">
                Manage laundry products and services, their prices, units, and availability.
            </p>
        </div>

    <section class="card-entry-animation rounded-xl border border-sky-200 bg-white p-4 shadow-sm sm:p-5">
        {{-- Add Service Header --}}
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-base font-bold text-slate-800">
                Add a product or service
            </h2>

            {{-- Service Already Taken Error --}}
            @if ($errors->has('service_name'))
                @php
                    $serviceNameError = $errors->first('service_name');
                @endphp

                @if (str_contains(strtolower($serviceNameError), 'taken') ||
                    str_contains(strtolower($serviceNameError), 'already') ||
                    str_contains(strtolower($serviceNameError), 'exists'))
                    <p class="text-right text-xs font-semibold text-red-600">
                        {{ $serviceNameError }}
                    </p>
                @endif
            @endif
        </div>

        <form
            method="POST"
            action="{{ route('manager.services.store') }}"
            class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-[minmax(0,1fr)_9rem_9rem_10rem_auto] xl:items-end"
        >
            @csrf

            {{-- Service Name --}}
            <div>
                <label
                    for="service_name"
                    class="mb-1 block text-xs font-semibold text-slate-700"
                >
                    Product or service name
                </label>

                <input
                    id="service_name"
                    name="service_name"
                    type="text"
                    value="{{ old('service_name') }}"
                    required
                    maxlength="255"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-[#168cff] focus:ring-2 focus:ring-sky-100"
                    placeholder="e.g. Dry Cleaning"
                >

                @error('service_name')
                    @php
                        $message = strtolower($message);
                    @endphp

                    @if (
                        !str_contains($message, 'taken') &&
                        !str_contains($message, 'already') &&
                        !str_contains($message, 'exists')
                    )
                        <p class="mt-1 text-xs text-red-600">
                            {{ $errors->first('service_name') }}
                        </p>
                    @endif
                @enderror
            </div>

            {{-- Price --}}
            <div>
                <label
                    for="base_price"
                    class="mb-1 block text-xs font-semibold text-slate-700"
                >
                    Price (₱)
                </label>

                <input
                    id="base_price"
                    name="base_price"
                    type="number"
                    value="{{ old('base_price') }}"
                    required
                    min="0"
                    max="99999999.99"
                    step="0.01"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-[#168cff] focus:ring-2 focus:ring-sky-100"
                    placeholder="0.00"
                >

                @error('base_price')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Unit --}}
            <div>
                <label
                    for="price_unit"
                    class="mb-1 block text-xs font-semibold text-slate-700"
                >
                    Unit
                </label>

                <input
                    id="price_unit"
                    name="price_unit"
                    type="text"
                    value="{{ old('price_unit') }}"
                    data-price-unit-input
                    required
                    maxlength="30"
                    class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-[#168cff] focus:ring-2 focus:ring-sky-100"
                    placeholder="/kilo"
                >

                @error('price_unit')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Available For --}}
            <div>
                <label for="available_for" class="mb-1 block text-xs font-semibold text-slate-700">Available for</label>
                <select id="available_for" name="available_for" required class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-[#168cff] focus:ring-2 focus:ring-sky-100">
                    <option value="Both" @selected(old('available_for', 'Both') === 'Both')>Both</option>
                    <option value="Drop Off" @selected(old('available_for') === 'Drop Off')>Drop Off</option>
                    <option value="Self Service" @selected(old('available_for') === 'Self Service')>Self Service</option>
                </select>
                @error('available_for')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            {{-- Add Button --}}
            <button
                type="submit"
                data-loading-button
                data-loading-message="Adding..."
                class="inline-flex h-10 w-36 shrink-0 items-center justify-center gap-2 whitespace-nowrap rounded-lg bg-[#168cff] px-4 text-sm font-bold text-white transition-colors hover:bg-[#0878df] disabled:cursor-not-allowed disabled:opacity-70"
            >
                <span data-loading-spinner class="hidden size-4 animate-spin rounded-full border-2 border-white/40 border-t-white" aria-hidden="true"></span>
                <span data-loading-text>Add Item</span>
            </button>
        </form>
    </section>

    {{-- Services List --}}
    <section class="card-entry-animation overflow-hidden rounded-xl border border-sky-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-4 py-3 sm:px-5">
            <h2 class="font-bold text-slate-800">
                Products and services added

                <span class="ml-1 rounded-full bg-sky-50 px-2 py-0.5 text-xs text-sky-700">
                    {{ $services->count() }}
                </span>
            </h2>
        </div>

        <div class="divide-y divide-slate-100" data-manager-service-list>
            @forelse ($services as $service)
                <div class="card-entry-animation grid grid-cols-[minmax(0,1fr)_auto_auto] items-center gap-2 px-4 py-3 sm:grid-cols-[minmax(0,1fr)_minmax(7rem,12rem)_auto] sm:gap-5 sm:px-5">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-slate-800">
                            {{ $service->service_name }}
                        </p>

                        @if ($service->description)
                            <p class="mt-0.5 text-xs text-slate-500">
                                {{ $service->description }}
                            </p>
                        @endif
                    </div>

                    <p class="w-fit whitespace-nowrap text-right text-sm font-bold text-slate-800 sm:w-full">
                        ₱{{ number_format((float) $service->base_price, 2) }}
                        {{ $service->price_unit }}
                        <span class="mt-1 block text-xs font-normal text-slate-500">{{ $service->available_for }}</span>
                    </p>
                    <button type="button" data-modal-trigger data-modal-target="service-delete-{{ $service->service_id }}" class="inline-flex shrink-0 items-center gap-1 rounded-lg border border-red-200 px-2.5 py-1.5 text-xs font-semibold text-red-600 transition-colors hover:bg-red-50">
                        <i class="fa-solid fa-trash" aria-hidden="true"></i>
                        Delete
                    </button>
                </div>
                <x-modal :id="'service-delete-'.$service->service_id" title="Delete service?">
                    <p class="text-sm leading-6 text-slate-600">This action is irreversible. To confirm deletion, please type the full name <strong>{{ $service->service_name }}</strong> below. Existing order records will be preserved.</p>
                    <form method="POST" action="{{ route('manager.services.destroy', $service->service_id) }}" data-name-confirmation-form data-confirmation-name="{{ $service->service_name }}" class="mt-4 space-y-4">
                        @csrf
                        @method('DELETE')
                        <label for="service-delete-name-{{ $service->service_id }}" class="block text-xs font-semibold text-slate-700">Product or service name</label>
                        <input id="service-delete-name-{{ $service->service_id }}" name="confirmation_name" type="text" autocomplete="off" data-name-confirmation-input aria-describedby="service-delete-help-{{ $service->service_id }}" class="-mt-3 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none transition-colors focus:ring-2 focus:ring-sky-200">
                        <p id="service-delete-help-{{ $service->service_id }}" class="-mt-3 text-xs text-slate-500">Enter the name exactly as shown above.</p>
                        <div class="flex justify-end gap-2">
                            <button type="button" data-modal-close class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                            <button type="submit" disabled data-name-confirmation-submit class="cursor-not-allowed rounded-lg bg-red-600 px-4 py-2 text-xs font-bold text-white opacity-50 transition-opacity">Delete Permanently</button>
                        </div>
                    </form>
                </x-modal>
            @empty
                <p class="px-4 py-8 text-center text-sm text-slate-500">
                    No products or services have been added yet.
                </p>
            @endforelse
        </div>
    </section>
</div>
</x-layout>
