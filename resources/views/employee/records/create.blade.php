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


        <form action="{{ route('orders.store') }}" method="POST">
            @csrf

            {{-- MAIN GRID --}}
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

                {{-- LEFT: CUSTOMER INFORMATION --}}
                <div class="rounded-2xl border border-sky-100 bg-white p-6 shadow-sm">

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


                    <div class="space-y-4">

                        {{-- Full Name --}}
                        <label class="block space-y-1 text-xs font-semibold text-slate-700">
                            Full Name
                            <span class="text-rose-500">*</span>

                            <input
                                type="text"
                                name="full_name"
                                placeholder="e.g. Maria Santos"
                                required
                                class="w-full rounded-lg border border-sky-200 bg-white px-3.5 py-2.5 text-sm font-normal text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300"
                            >
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
                                    placeholder="09XX XXX XXXX"
                                    required
                                    class="w-full px-3.5 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:outline-none"
                                >

                            </span>

                            <span class="block text-[11px] font-normal text-slate-400">
                                Used to send SMS pickup notifications
                            </span>

                        </label>

                        {{-- Address --}}
                        <label class="block space-y-1 text-xs font-semibold text-slate-700">
                            Address

                            <textarea
                                name="address"
                                rows="2"
                                placeholder="Street, barangay, city"
                                class="w-full resize-y rounded-lg border border-sky-200 bg-white px-3.5 py-2.5 text-sm font-normal text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300"
                            ></textarea>
                        </label>

                    </div>

                </div>


                {{-- MIDDLE: SERVICES --}}
                <div class="rounded-2xl border border-sky-100 bg-white p-6 shadow-sm">

                    <div class="mb-5 flex items-center gap-3">
                        <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-sky-50 text-[#168cff]">
                            <i class="fa-solid fa-shirt" aria-hidden="true"></i>
                        </span>

                        <div>
                            <h2 class="text-sm font-bold text-slate-800">
                                Services
                            </h2>

                            <p class="text-xs text-slate-400">
                                Select laundry services
                            </p>
                        </div>
                    </div>


                    <div class="space-y-3">

                        @foreach (['Wash & Dry', 'Ironing', 'Folding', 'Self-Service'] as $service)

                            <label class="flex cursor-pointer items-center justify-between rounded-lg border border-sky-200 p-3 text-sm text-slate-700 transition-colors hover:bg-sky-50">

                                <span class="font-medium">
                                    {{ $service }}

                                    @if ($service === 'Ironing')
                                        <span class="font-normal text-slate-400">
                                            (+₱30)
                                        </span>
                                    @endif
                                </span>

                                <input
                                    type="checkbox"
                                    name="services[]"
                                    value="{{ $service }}"
                                    class="rounded text-[#168cff] focus:ring-[#168cff]"
                                >

                            </label>

                        @endforeach

                    </div>

                </div>


                {{-- RIGHT COLUMN --}}
                <div class="space-y-5">

                    {{-- TOP RIGHT: WEIGHT --}}
                    <div class="rounded-2xl border border-sky-100 bg-white p-6 shadow-sm">

                        <div class="mb-5 flex items-center gap-3">
                            <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-sky-50 text-[#168cff]">
                                <i class="fa-solid fa-weight-scale" aria-hidden="true"></i>
                            </span>

                            <div>
                                <h2 class="text-sm font-bold text-slate-800">
                                    Laundry Weight
                                </h2>

                                <p class="text-xs text-slate-400">
                                    Enter laundry weight
                                </p>
                            </div>
                        </div>


                        <label class="block space-y-1 text-xs font-semibold text-slate-700">
                            Weight (kg)
                            <span class="text-rose-500">*</span>

                            <input
                                type="number"
                                step="0.1"
                                min="0.1"
                                name="weight"
                                placeholder="e.g. 4.5"
                                required
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
                    <div class="rounded-2xl border border-sky-100 bg-white p-6 shadow-sm">

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
                                    name="amount_paid"
                                    placeholder="Leave blank if unpaid"
                                    class="w-full rounded-lg border border-sky-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300"
                                >

                            </label>

                        </div>

                    </div>

                </div>

            </div>


            {{-- SPECIAL REQUEST --}}
            <div class="mt-5 rounded-2xl border border-sky-100 bg-white p-6 shadow-sm">

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

                <div class="grid grid-cols-1 gap-4 md:grid-cols-[minmax(0,1fr)_14rem]">
                    <input
                        type="text"
                        name="special_request"
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

        </form>

    </div>

</x-layout>
