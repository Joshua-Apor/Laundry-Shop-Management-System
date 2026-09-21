<x-layout>
    <div class="mx-auto max-w-2xl space-y-6 p-6">
        <h1 class="text-2xl font-bold text-slate-900">New Laundry Order</h1>
        <div class="space-y-6 rounded-2xl border border-sky-100 bg-white p-8 shadow-sm">
            <form action="{{ route('orders.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="space-y-4">
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400">Customer Information</span>
                    <label class="block space-y-1 text-xs font-semibold text-slate-700">Full Name <span class="text-rose-500">*</span>
                        <input type="text" name="full_name" placeholder="e.g. Maria Santos" required class="w-full rounded-lg border border-sky-200 bg-white px-3.5 py-2.5 text-sm font-normal text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300">
                    </label>
                    <label class="block space-y-1 text-xs font-semibold text-slate-700">Phone Number <span class="text-rose-500">*</span>
                        <span class="flex overflow-hidden rounded-lg border border-sky-200 focus-within:ring-2 focus-within:ring-sky-300"><span class="flex items-center justify-center border-r border-sky-200 bg-sky-50 px-3.5 text-[#168cff]"><i class="fa-solid fa-phone" aria-hidden="true"></i></span><input type="text" name="phone_number" placeholder="09XX XXX XXXX" required class="w-full px-3.5 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:outline-none"></span>
                        <span class="block text-[11px] font-normal text-slate-400">Used to send SMS pickup notifications</span>
                    </label>
                </div>

                <div class="space-y-2"><span class="block text-xs font-semibold text-slate-700">SERVICES <span class="text-rose-500">*</span></span><div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @foreach (['Wash & Dry', 'Ironing', 'Folding', 'Self-Service'] as $service)
                        <label class="flex cursor-pointer items-center justify-between rounded-lg border border-sky-200 p-3 text-sm text-slate-700 hover:bg-sky-50"><span class="font-medium">{{ $service }} @if ($service === 'Ironing') <span class="font-normal text-slate-400">(+₱30)</span> @endif</span><input type="checkbox" name="services[]" value="{{ $service }}" class="rounded text-[#168cff] focus:ring-[#168cff]"></label>
                    @endforeach
                </div></div>

                <label class="block space-y-1 text-xs font-semibold text-slate-700">Weight (kg) <span class="text-rose-500">*</span><input type="number" step="0.1" name="weight" placeholder="e.g. 4.5" required class="w-full rounded-lg border border-sky-200 bg-white px-3.5 py-2.5 text-sm font-normal text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300"></label>
                <label class="block space-y-1 text-xs font-semibold text-slate-700">Special Request<input type="text" name="special_request" placeholder="e.g. Separate whites, use specific detergent..." class="w-full rounded-lg border border-sky-200 bg-white px-3.5 py-2.5 text-sm font-normal text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300"></label>

                <div class="space-y-3"><span class="block text-xs font-bold uppercase tracking-wider text-slate-400">Payment</span><div class="grid grid-cols-2 gap-3">
                    @foreach (['Cash', 'GCash'] as $method)
                        <label class="flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-sky-200 bg-white py-2.5 text-sm font-semibold text-slate-700 transition-colors has-[:checked]:bg-[#168cff] has-[:checked]:text-white"><input type="radio" name="payment_method" value="{{ $method }}" class="hidden" @checked($method === 'Cash')><i class="fa-solid {{ $method === 'Cash' ? 'fa-money-bill-wave' : 'fa-mobile-screen-button' }}" aria-hidden="true"></i>{{ $method }}</label>
                    @endforeach
                </div><input type="number" step="0.01" name="amount_paid" placeholder="Amount paid (leave blank if unpaid)" class="w-full rounded-lg border border-sky-200 bg-white px-3.5 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-300"></div>

                <button type="submit" class="w-full rounded-xl bg-[#168cff] py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-[#0878df]">Create Order</button>
            </form>
        </div>
    </div>
</x-layout>
