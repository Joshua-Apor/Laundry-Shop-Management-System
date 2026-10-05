<x-layout>
    <div class="space-y-5">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Services</h1>
            <p class="text-sm text-slate-500">
                Add laundry services with a price and its unit, such as per kilo or per piece.
            </p>
        </div>

    <section class="rounded-xl border border-sky-200 bg-white p-4 shadow-sm sm:p-5">
        {{-- Add Service Header --}}
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-base font-bold text-slate-800">
                Add a service
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
            class="mt-4 grid gap-3 sm:grid-cols-[minmax(0,1fr)_10rem_10rem_auto] sm:items-end"
        >
            @csrf

            {{-- Service Name --}}
            <div>
                <label
                    for="service_name"
                    class="mb-1 block text-xs font-semibold text-slate-700"
                >
                    Service name
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

            {{-- Add Button --}}
            <button
                type="submit"
                data-loading-button
                data-loading-message="Adding..."
                class="inline-flex h-10 w-36 shrink-0 items-center justify-center gap-2 whitespace-nowrap rounded-lg bg-[#168cff] px-4 text-sm font-bold text-white transition-colors hover:bg-[#0878df] disabled:cursor-not-allowed disabled:opacity-70"
            >
                <span data-loading-spinner class="hidden size-4 animate-spin rounded-full border-2 border-white/40 border-t-white" aria-hidden="true"></span>
                <span data-loading-text>Add Service</span>
            </button>
        </form>
    </section>

    {{-- Services List --}}
    <section class="overflow-hidden rounded-xl border border-sky-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-4 py-3 sm:px-5">
            <h2 class="font-bold text-slate-800">
                Services added

                <span class="ml-1 rounded-full bg-sky-50 px-2 py-0.5 text-xs text-sky-700">
                    {{ $services->count() }}
                </span>
            </h2>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($services as $service)
                <div class="grid grid-cols-[minmax(0,1fr)_minmax(7rem,12rem)_auto] items-center gap-3 px-4 py-3 sm:gap-5 sm:px-5">
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

                    <p class="w-full whitespace-nowrap text-right text-sm font-bold text-slate-800">
                        ₱{{ number_format((float) $service->base_price, 2) }}
                        {{ $service->price_unit }}
                    </p>
                    <button type="button" data-modal-trigger data-modal-target="service-delete-{{ $service->service_id }}" class="inline-flex shrink-0 items-center gap-1 rounded-lg border border-red-200 px-2.5 py-1.5 text-xs font-semibold text-red-600 transition-colors hover:bg-red-50">
                        <i class="fa-solid fa-trash" aria-hidden="true"></i>
                        Delete
                    </button>
                </div>
                <x-modal :id="'service-delete-'.$service->service_id" title="Delete service?">
                    <p class="text-sm leading-6 text-slate-600">Remove {{ $service->service_name }} from available services? Existing order records will be preserved.</p>
                    <form method="POST" action="{{ route('manager.services.destroy', $service->service_id) }}" class="mt-5 flex justify-end gap-2">
                        @csrf
                        @method('DELETE')
                        <button type="button" data-modal-close class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                        <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-xs font-bold text-white hover:bg-red-700">Delete service</button>
                    </form>
                </x-modal>
            @empty
                <p class="px-4 py-8 text-center text-sm text-slate-500">
                    No services have been added yet.
                </p>
            @endforelse
        </div>
    </section>
</div>
</x-layout>
