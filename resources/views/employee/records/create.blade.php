<x-layout>

    <div class="mx-auto max-w-6xl space-y-6 p-6">

        {{-- PAGE HEADER --}}
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                New Laundry Order
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Enter the customer's information, laundry services, and payment details.
            </p>
        </div>


        <form action="{{ route('orders.store') }}" method="POST" data-order-form>
            @csrf
            <input type="hidden" name="customer_id" id="customer-id" value="{{ old('customer_id') }}">

            <section data-order-phase="1" class="mb-5 rounded-2xl border border-sky-100 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900">Choose a laundry service</h2>
                <p class="mt-1 text-sm text-slate-500">Choose Drop Off or Self Service first. Prices and quantities follow each service’s price unit.</p>

                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    @foreach (['Drop Off', 'Self Service'] as $orderType)
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-sky-200 p-4 font-semibold text-slate-800 has-[:checked]:border-sky-500 has-[:checked]:bg-sky-50">
                            <input type="radio" name="order_type" value="{{ $orderType }}" data-order-type-choice @checked(old('order_type') === $orderType) required class="text-sky-600 focus:ring-sky-500">
                            <span>{{ $orderType }}
                                @foreach ($services->where('service_name', $orderType) as $service)
                                    <span class="font-normal text-slate-500">(₱{{ number_format((float) $service->base_price, 2) }} {{ $service->price_unit }})</span>
                                    <input type="hidden" name="services[]" value="{{ $service->service_id }}" data-order-base-service="{{ $orderType }}" disabled>
                                @endforeach
                            </span>
                        </label>
                    @endforeach
                </div>

                <div data-order-type-fields="Drop Off" class="mt-5 hidden">
                    <label class="block space-y-1 text-sm font-semibold text-slate-700">Laundry weight (kg)
                        <input type="number" name="weight" min="0.1" step="0.1" value="{{ old('weight', '0') }}" data-order-weight disabled class="w-full rounded-lg border border-sky-200 px-3.5 py-2.5 text-sm font-normal focus:outline-none focus:ring-2 focus:ring-sky-300">
                    </label>
                </div>

                <div data-order-type-fields="Self Service" class="mt-5 hidden">
                    <label class="block space-y-1 text-sm font-semibold text-slate-700">Number of loads
                        <input type="number" name="self_service_loads" min="1" max="100" step="1" value="{{ old('self_service_loads', '0') }}" data-order-loads disabled class="w-full rounded-lg border border-sky-200 px-3.5 py-2.5 text-sm font-normal focus:outline-none focus:ring-2 focus:ring-sky-300">
                    </label>
                </div>

                <fieldset data-order-addons class="mt-5 hidden space-y-3">
                    <legend class="text-sm font-semibold text-slate-700">Additional services</legend>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach ($services->whereNotIn('service_name', ['Drop Off', 'Self Service']) as $service)
                            <div data-order-addon-option="{{ in_array($service->service_name, ['Dry', 'Sabon'], true) ? 'Self Service' : 'Both' }}" class="hidden rounded-xl border border-sky-200 p-3">
                                <label class="flex cursor-pointer items-center gap-2 text-sm font-medium text-slate-700">
                                    <input type="checkbox" name="services[]" value="{{ $service->service_id }}" @checked(in_array((string) $service->service_id, old('services', []), true)) data-order-addon disabled class="rounded text-sky-600 focus:ring-sky-500">
                                    {{ $service->service_name }} <span class="font-normal text-slate-500">(₱{{ number_format((float) $service->base_price, 2) }} {{ $service->price_unit }})</span>
                                </label>
                                <label class="mt-2 hidden space-y-1 text-xs font-medium text-slate-600" data-order-addon-quantity-label>
                                    Quantity ({{ ltrim($service->price_unit, '/') }})
                                    <input type="number" name="service_quantities[{{ $service->service_id }}]" value="{{ old('service_quantities.'.$service->service_id, 1) }}" min="1" step="1" data-order-addon-quantity disabled class="w-full rounded-lg border border-sky-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-300">
                                </label>
                            </div>
                        @endforeach
                    </div>
                </fieldset>

                @error('weight')<p class="mt-3 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
                @error('self_service_loads')<p class="mt-3 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror

                <div class="mt-6 flex justify-end">
                    <button type="button" data-order-next class="rounded-lg bg-[#168cff] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#0878df]">Continue</button>
                </div>
            </section>

            {{-- MAIN GRID --}}
            <section data-order-phase="2" hidden>
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

                {{-- LEFT: CUSTOMER INFORMATION --}}
                <div class="rounded-2xl border border-sky-100 bg-white p-6 shadow-sm row-start-1 lg:col-start-1 lg:row-start-1">

                    <div class="mb-5 flex items-center gap-3">
                        <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-sky-50 text-[#168cff]">
                            <i class="fa-solid fa-user" aria-hidden="true"></i>
                        </span>

                        <div>
                            <h2 class="text-sm font-bold text-slate-800">
                                Customer Information
                            </h2>

                            <p class="text-xs text-slate-400">
                                Customer contact details
                            </p>
                        </div>
                    </div>

                    <button
                        type="button"
                        data-modal-trigger
                        data-modal-target="customer-picker"
                        class="mb-4 inline-flex w-full items-center justify-center rounded-lg bg-[#168CFF] px-3.5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#0878df] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500"
                    >
                        Choose Existing Customer
                    </button>


                    <div class="space-y-4">

                        {{-- Full Name --}}
                        <label class="block space-y-1 text-xs font-semibold text-slate-700">
                            Full Name
                            <span class="text-rose-500">*</span>

                            <span class="relative block">
                                <input
                                    type="text"
                                    name="full_name"
                                    id="customer-name"
                                    value="{{ old('full_name') }}"
                                    placeholder="e.g. Maria Santos"
                                    autocomplete="off"
                                    aria-autocomplete="list"
                                    aria-controls="customer-suggestions"
                                    aria-expanded="false"
                                    required
                                    class="w-full rounded-lg border border-sky-200 bg-white px-3.5 py-2.5 text-sm font-normal text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300"
                                >
                                <span
                                    id="customer-suggestions"
                                    role="listbox"
                                    class="absolute inset-x-0 top-full z-20 mt-1 hidden max-h-60 overflow-y-auto rounded-lg border border-sky-200 bg-white p-1 shadow-lg"
                                    data-customer-suggestions
                                ></span>
                            </span>
                        </label>


                        {{-- Phone Number --}}
                        <label class="block space-y-1 text-xs font-semibold text-slate-700">
                            Phone Number
                            <span class="text-rose-500">*</span>

                            <span class="flex overflow-hidden rounded-lg border border-sky-200 focus-within:ring-2 focus-within:ring-sky-300">

                                <span class="flex items-center justify-center border-r border-sky-200 bg-sky-50 px-3.5 text-[#168cff]">
                                    <i class="fa-solid fa-phone" aria-hidden="true"></i>
                                </span>

                                <input
                                    type="text"
                                    name="phone_number"
                                    id="customer-phone"
                                    value="{{ old('phone_number') }}"
                                    placeholder="09XX XXX XXXX"
                                    required
                                    class="w-full px-3.5 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:outline-none"
                                >

                            </span>

                            <span class="block text-[11px] font-normal text-slate-400">
                                Used to send SMS pickup notifications
                            </span>
                            @error('phone_number')
                                <span role="alert" class="block text-xs font-medium text-red-600">{{ $message }}</span>
                            @enderror

                        </label>

                        {{-- Address --}}
                        <label class="block space-y-1 text-xs font-semibold text-slate-700">
                            Address

                            <textarea
                                name="address"
                                id="customer-address"
                                rows="2"
                                placeholder="Street, barangay, city"
                                class="w-full resize-y rounded-lg border border-sky-200 bg-white px-3.5 py-2.5 text-sm font-normal text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300"
                            >{{ old('address') }}</textarea>
                        </label>

                    </div>

                </div>


                {{-- MIDDLE: SELECTED SERVICES SUMMARY --}}
                <div class="rounded-2xl border border-sky-100 bg-white p-6 shadow-sm row-start-2 lg:col-start-2 lg:row-start-1">

                    <div class="mb-5 flex items-center gap-3">
                        <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-sky-50 text-[#168cff]">
                            <i class="fa-solid fa-shirt" aria-hidden="true"></i>
                        </span>

                        <div>
                            <h2 class="text-sm font-bold text-slate-800">
                                Selected services
                            </h2>

                            <p class="text-xs text-slate-400">Choose services and quantities</p>
                        </div>
                    </div>


                    <input type="hidden" name="order_type" data-order-selected-type value="{{ old('order_type') }}">
                    <div data-selected-services-list class="space-y-3">
                        <p data-selected-services-empty class="rounded-lg bg-sky-50 p-3 text-sm text-slate-600">No services selected yet.</p>
                    </div>
                    @error('weight')<p class="mt-2 text-xs text-red-600" role="alert">{{ $message }}</p>@enderror
                    @error('self_service_loads')<p class="mt-2 text-xs text-red-600" role="alert">{{ $message }}</p>@enderror
                    <button type="button" data-open-order-service-modal class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#168cff] px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[#0878df]">
                        <i class="fa-solid fa-plus" aria-hidden="true"></i>
                        Select service
                    </button>
                    @error('services')<p class="mt-2 text-xs text-red-600" role="alert">{{ $message }}</p>@enderror

                    <fieldset disabled hidden class="space-y-3">

                        @forelse ($services as $service)

                            <label class="flex cursor-pointer items-center justify-between rounded-lg border border-sky-200 p-3 text-sm text-slate-700 transition-colors hover:bg-sky-50">

                                <span class="font-medium">
                                    {{ $service->service_name }}
                                    <span class="font-normal text-slate-400">(₱{{ number_format((float) $service->base_price, 2) }} {{ $service->price_unit }})</span>
                                </span>

                                <input
                                    type="checkbox"
                                    name="services[]"
                                    value="{{ $service->service_id }}"
                                    class="rounded text-[#168cff] focus:ring-[#168cff]"
                                >

                            </label>

                        @empty
                            <p class="rounded-lg bg-amber-50 p-3 text-xs text-amber-800">No services are available. Ask a manager to add services first.</p>
                        @endforelse

                    </fieldset>

                </div>


                {{-- RIGHT COLUMN --}}
                <div class="contents">

                    {{-- TOP RIGHT: WEIGHT --}}
                    <div class="rounded-2xl border border-sky-100 bg-white p-6 shadow-sm row-start-4 lg:col-start-1 lg:row-start-2">

                        <div class="mb-5 flex items-center gap-3">
                            <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-sky-50 text-[#168cff]">
                                <i class="fa-solid fa-weight-scale" aria-hidden="true"></i>
                            </span>

                            <div>
                                <h2 class="text-sm font-bold text-slate-800">Total Amount</h2>

                                <p class="text-xs text-slate-400">
                                    Based on selected service quantities
                                </p>
                            </div>
                        </div>

                        <p class="text-2xl font-bold text-slate-900" data-order-total>₱0.00</p>

                        <label hidden class="block space-y-1 text-xs font-semibold text-slate-700">
                            Weight (kg)
                            <span class="text-rose-500">*</span>

                            <input
                                type="number"
                                step="0.1"
                                min="0.1"
                                name="legacy_weight"
                                placeholder="e.g. 4.5"
                                disabled
                                class="w-full rounded-lg border border-sky-200 bg-white px-3.5 py-2.5 text-sm font-normal text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300"
                            >
                        </label>

                        <label class="block space-y-1 text-xs font-semibold text-slate-700">
                            Laundry Amount (₱)

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                name="laundry_amount"
                                placeholder="e.g. 150.00"
                                class="w-full rounded-lg border border-sky-200 bg-white px-3.5 py-2.5 text-sm font-normal text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300"
                            >
                        </label>

                    </div>


                    {{-- BOTTOM RIGHT: PAYMENT --}}
                    <div class="rounded-2xl border border-sky-100 bg-white p-6 shadow-sm row-start-5 lg:col-span-2 lg:col-start-2 lg:row-start-2">

                        <div class="mb-5 flex items-center gap-3">
                            <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-sky-50 text-[#168cff]">
                                <i class="fa-solid fa-wallet" aria-hidden="true"></i>
                            </span>

                            <div>
                                <h2 class="text-sm font-bold text-slate-800">
                                    Payment
                                </h2>

                                <p class="text-xs text-slate-400">
                                    Payment details
                                </p>
                            </div>
                        </div>


                        <div class="space-y-4">

                            {{-- Payment Method --}}
                            <div class="space-y-2">

                                <span class="block text-xs font-semibold text-slate-700">
                                    PAYMENT METHOD
                                </span>

                                <div class="grid grid-cols-2 gap-2">

                                    @foreach (['Cash', 'GCash'] as $method)

                                        <label class="flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-sky-200 bg-white py-2.5 text-xs font-semibold text-slate-700 transition-colors has-[:checked]:border-[#168cff] has-[:checked]:bg-[#168cff] has-[:checked]:text-white">

                                            <input
                                                type="radio"
                                                name="payment_method"
                                                value="{{ $method }}"
                                                class="hidden"
                                                @checked($method === 'Cash')
                                            >

                                            <i
                                                class="fa-solid {{ $method === 'Cash' ? 'fa-money-bill-wave' : 'fa-mobile-screen-button' }}"
                                                aria-hidden="true"
                                            ></i>

                                            {{ $method }}

                                        </label>

                                    @endforeach

                                </div>

                            </div>


                            {{-- Amount Paid --}}
                            <label class="block space-y-1 text-xs font-semibold text-slate-700">

                                Amount Paid

                                <input
                                    type="number"
                                    step="0.01"
                                    min="0.01"
                                    name="amount_paid"
                                    value="{{ old('amount_paid') }}"
                                    required
                                    placeholder="Enter payment amount"
                                    class="w-full rounded-lg border border-sky-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300"
                                >

                                @error('amount_paid')
                                    <span role="alert" class="block text-xs font-medium text-red-600">{{ $message }}</span>
                                @enderror

                            </label>

                        </div>

                    </div>

                </div>

            {{-- SPECIAL REQUEST --}}
            <div class="rounded-2xl border border-sky-100 bg-white p-6 shadow-sm row-start-3 lg:col-start-3 lg:row-start-1">

                <div class="mb-4 flex items-center gap-3">
                    <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-sky-50 text-[#168cff]">
                        <i class="fa-solid fa-note-sticky" aria-hidden="true"></i>
                    </span>

                    <div>
                        <h2 class="text-sm font-bold text-slate-800">
                            Special Request
                        </h2>

                        <p class="text-xs text-slate-400">
                            Optional instructions from the customer
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    <input
                        type="text"
                        name="special_request"
                        aria-label="Special request"
                        placeholder="e.g. Separate whites, use specific detergent..."
                        class="w-full rounded-lg border border-sky-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300"
                    >

                    <label class="block space-y-1 text-xs font-semibold text-slate-700">
                        Additional Price (₱)

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="special_request_price"
                            placeholder="e.g. 25.00"
                            class="w-full rounded-lg border border-sky-200 bg-white px-3.5 py-2.5 text-sm font-normal text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300"
                        >
                    </label>
                </div>

            </div>

            </div>


            <button type="button" data-order-back class="mt-5 w-full rounded-xl border border-slate-200 bg-white py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back to services</button>

            {{-- CREATE ORDER --}}
            <button
                type="submit"
                data-loading-button
                data-loading-message="Creating order..."
                class="mt-5 flex w-full items-center justify-center rounded-xl bg-[#168cff] py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-[#0878df] disabled:cursor-not-allowed disabled:opacity-70"
            >
                <span data-loading-text>
                    Create Order
                </span>
            </button>
            </section>

        </form>

        <dialog data-order-service-dialog class="m-auto w-[min(34rem,calc(100vw-2rem))] max-w-none rounded-2xl border border-slate-200 bg-white p-5 text-slate-900 shadow-2xl backdrop:bg-slate-950/60 sm:p-7">
            <div class="space-y-5">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Select services</h2>
                    <p class="mt-1 text-sm text-slate-500">Choose Drop Off or Self Service, then select any additional services.</p>
                </div>

                <fieldset class="space-y-2">
                    <legend class="mb-2 text-sm font-semibold text-slate-700">Laundry service</legend>
                    <div class="grid gap-2 sm:grid-cols-2">
                        @foreach ($services->whereIn('service_name', ['Drop Off', 'Self Service']) as $service)
                            <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-sky-200 p-3 text-sm font-semibold text-slate-700 has-[:checked]:border-sky-500 has-[:checked]:bg-sky-50">
                                <input type="radio" name="dialog_order_type" value="{{ $service->service_name }}" data-dialog-order-type data-service-id="{{ $service->service_id }}" data-service-price="{{ $service->base_price }}" data-service-unit="{{ $service->price_unit }}" @if ($service->service_name === 'Drop Off') data-service-fixed-price="175" data-service-fixed-limit="5" @endif class="text-sky-600 focus:ring-sky-500">
                                <span>{{ $service->service_name }} <span class="font-normal text-slate-500">(@if ($service->service_name === 'Drop Off') ₱175 up to 5 kg, then ₱{{ number_format((float) $service->base_price, 2) }} {{ $service->price_unit }} @else ₱{{ number_format((float) $service->base_price, 2) }} {{ $service->price_unit }} @endif)</span></span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <fieldset data-order-modal-addons class="hidden space-y-2">
                    <legend class="mb-2 text-sm font-semibold text-slate-700">Additional services</legend>
                    <div class="grid gap-2 sm:grid-cols-2">
                        @foreach ($services->whereNotIn('service_name', ['Drop Off', 'Self Service']) as $service)
                            <label data-order-modal-addon="{{ in_array($service->service_name, ['Dry', 'Sabon'], true) ? 'Self Service' : 'Both' }}" class="flex cursor-pointer items-center gap-2 rounded-lg border border-sky-200 p-3 text-sm text-slate-700 has-[:checked]:border-sky-500 has-[:checked]:bg-sky-50">
                                <input type="checkbox" value="{{ $service->service_id }}" data-order-modal-addon-input data-service-name="{{ $service->service_name }}" data-service-price="{{ $service->base_price }}" data-service-unit="{{ $service->price_unit }}" class="rounded text-sky-600 focus:ring-sky-500">
                                <span>{{ $service->service_name }} <span class="text-slate-500">(₱{{ number_format((float) $service->base_price, 2) }} {{ $service->price_unit }})</span></span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <p data-order-modal-error class="hidden text-sm text-red-600" role="alert"></p>
                <div class="flex justify-end gap-2">
                    <button type="button" data-order-modal-cancel class="rounded-lg border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                    <button type="button" data-order-modal-done class="rounded-lg bg-[#168cff] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#0878df]">Done</button>
                </div>
            </div>
        </dialog>

        <x-modal id="customer-picker" title="Choose a customer" class="w-[min(36rem,calc(100vw-2rem))]">
            <div class="space-y-4">
                <label for="customer-search" class="sr-only">Search customers by name or phone number</label>
                <input
                    id="customer-search"
                    type="search"
                    placeholder="Search by customer name or phone number..."
                    autocomplete="off"
                    data-customer-search
                    class="w-full rounded-lg border border-sky-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300"
                >

                <div class="max-h-80 space-y-2 overflow-y-auto" data-customer-list>
                    @forelse ($customers as $customer)
                        <button
                            type="button"
                            data-customer-option
                            data-customer-id="{{ $customer->customer_id }}"
                            data-name="{{ $customer->name }}"
                            data-phone="{{ $customer->contact_number }}"
                            data-address="{{ $customer->address }}"
                            class="flex w-full items-start justify-between gap-3 rounded-lg border border-sky-100 bg-white p-3 text-left transition-colors hover:border-sky-300 hover:bg-sky-50 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500"
                        >
                            <span class="min-w-0">
                                <span class="block truncate text-sm font-semibold text-slate-800">{{ $customer->name }}</span>
                                <span class="mt-1 block text-xs text-slate-500">{{ $customer->contact_number }}</span>
                            </span>
                            <i class="fa-solid fa-chevron-right mt-1 text-xs text-slate-400" aria-hidden="true"></i>
                        </button>
                    @empty
                        <p class="rounded-lg bg-slate-50 p-4 text-center text-sm text-slate-500">No customers to show yet.</p>
                    @endforelse
                    <p class="hidden rounded-lg bg-slate-50 p-4 text-center text-sm text-slate-500" data-customer-empty>No matching customers found.</p>
                </div>
            </div>
        </x-modal>

    </div>

</x-layout>
