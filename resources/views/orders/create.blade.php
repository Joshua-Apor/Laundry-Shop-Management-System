<x-layout>

    <div class="max-w-2xl mx-auto p-6 space-y-6">
        <h1 class="text-2xl font-bold text-slate-900">New Laundry Order</h1>

        <!-- Cont -->
        <div class="bg-white rounded-2xl border border-sky-100 p-8 shadow-sm space-y-6">
            <form action="{{ route('orders.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Customer Info -->
                <div class="space-y-4">
                    <span class="text-xs font-bold text-slate-400 tracking-wider uppercase block">
                        Customer Information
                    </span>

                    <!-- Full Name -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 block">
                            Full Name <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="full_name"
                            placeholder="e.g. Maria Santos"
                            required
                            class="w-full bg-white border border-sky-200 rounded-lg px-3.5 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300" />
                    </div>

                    <!-- Phone Number -->
                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-slate-700 block">
                            Phone Number <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex rounded-lg border border-sky-200 overflow-hidden focus-within:ring-2 focus-within:ring-sky-300">
                            <span class="bg-pink-50 px-3.5 flex items-center justify-center text-rose-500 border-r border-sky-200">
                                📞
                            </span>
                            <input
                                type="text"
                                name="phone_number"
                                placeholder="09XX XXX XXXX"
                                required
                                class="w-full px-3.5 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:outline-none" />
                        </div>
                        <p class="text-[11px] text-slate-400">Used to send SMS pickup notifications</p>
                    </div>
                </div>

                <!-- Services (Checkboxes) -->
                <div class="space-y-2">
                    <label class="text-xs font-semibold text-slate-700 block">
                        SERVICES <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-center justify-between border border-sky-200 rounded-lg p-3 text-sm text-slate-700 cursor-pointer hover:bg-slate-50">
                            <span class="font-medium">Wash & Dry</span>
                            <input type="checkbox" name="services[]" value="Wash & Dry" class="rounded text-[#e92c81] focus:ring-[#e92c81]" />
                        </label>

                        <label class="flex items-center justify-between border border-sky-200 rounded-lg p-3 text-sm text-slate-700 cursor-pointer hover:bg-slate-50">
                            <span class="font-medium">Ironing <span class="text-slate-400 font-normal">(+₱30)</span></span>
                            <input type="checkbox" name="services[]" value="Ironing" class="rounded text-[#e92c81] focus:ring-[#e92c81]" />
                        </label>

                        <label class="flex items-center justify-between border border-sky-200 rounded-lg p-3 text-sm text-slate-700 cursor-pointer hover:bg-slate-50">
                            <span class="font-medium">Folding</span>
                            <input type="checkbox" name="services[]" value="Folding" class="rounded text-[#e92c81] focus:ring-[#e92c81]" />
                        </label>

                        <label class="flex items-center justify-between border border-sky-200 rounded-lg p-3 text-sm text-slate-700 cursor-pointer hover:bg-slate-50">
                            <span class="font-medium">Self-Service</span>
                            <input type="checkbox" name="services[]" value="Self-Service" class="rounded text-[#e92c81] focus:ring-[#e92c81]" />
                        </label>
                    </div>
                </div>

                <!-- Weight -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-700 block">
                        Weight (kg) <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="number"
                        step="0.1"
                        name="weight"
                        placeholder="e.g. 4.5"
                        required
                        class="w-full bg-white border border-sky-200 rounded-lg px-3.5 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300" />
                </div>

                <!-- Special Request -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-700 block">
                        Special Request
                    </label>
                    <input
                        type="text"
                        name="special_request"
                        placeholder="e.g. Separate whites, use specific detergent..."
                        class="w-full bg-white border border-sky-200 rounded-lg px-3.5 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300" />
                </div>

                <!-- Payment Method (Radio Choice) -->
                <div class="space-y-3">
                    <span class="text-xs font-bold text-slate-400 tracking-wider uppercase block">
                        Payment
                    </span>

                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center justify-center gap-2 bg-white border border-sky-200 text-slate-700 py-2.5 rounded-lg text-sm font-semibold cursor-pointer has-[:checked]:bg-[#e92c81] has-[:checked]:text-white transition-colors">
                            <input type="radio" name="payment_method" value="Cash" class="hidden" checked />
                            Cash
                        </label>
                        <label class="flex items-center justify-center gap-2 bg-white border border-sky-200 text-slate-700 py-2.5 rounded-lg text-sm font-semibold cursor-pointer has-[:checked]:bg-[#e92c81] has-[:checked]:text-white transition-colors">
                            <input type="radio" name="payment_method" value="GCash" class="hidden" />
                            GCash
                        </label>
                    </div>

                    <!-- Amount Paid -->
                    <input
                        type="number"
                        step="0.01"
                        name="amount_paid"
                        placeholder="Amount paid (leave 0 if unpaid)"
                        class="w-full bg-white border border-sky-200 rounded-lg px-3.5 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300" />
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full bg-[#f6a0c0] hover:bg-[#e92c81] text-white font-semibold py-3 rounded-xl transition-colors shadow-sm text-sm">
                    Create Order
                </button>
            </form>
        </div>
    </div>

</x-layout>