@props(['order', 'editable' => true])

@php
    $statusColor = match ($order->status) {
        'Processing' => 'bg-amber-100 text-amber-800',
        'Ready for Pickup' => 'bg-sky-100 text-sky-800',
        'Completed' => 'bg-emerald-100 text-emerald-800',
        default => 'bg-slate-100 text-slate-600',
    };
    $nextStatus = match ($order->status) {
        'Processing' => 'Ready for Pickup',
        'Ready for Pickup' => 'Completed',
        default => null,
    };
    $hasOutstandingBalance = (float) $order->balance > 0;
@endphp

<div @class(['relative z-20 flex min-w-0 flex-col gap-2', 'items-end sm:items-start' => $editable, 'items-center' => ! $editable, 'pointer-events-none' => ! $editable])>
    <span @class(['inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold', $statusColor])>
        {{ $order->status }}
    </span>
    @if ($editable && $nextStatus)
        <button
            type="button"
            data-modal-trigger
            data-modal-target="order-status-confirmation-{{ $order->order_id }}"
            aria-label="Move order {{ $order->order_id }} to {{ $nextStatus }}"
            class="inline-flex max-w-full items-center justify-center gap-1.5 whitespace-nowrap rounded-lg bg-[#168cff] px-2.5 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-[#0878df] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500"
        >
            <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
            {{ $nextStatus }}
        </button>

        @if ($nextStatus === 'Completed' && $hasOutstandingBalance)
            <x-modal id="order-status-confirmation-{{ $order->order_id }}" title="Outstanding balance">
                <div class="space-y-4">
                    <p class="text-sm leading-6 text-slate-600">
                        Order #{{ $order->order_id }} cannot be completed until its balance is fully paid.
                    </p>

                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-800">Outstanding balance</p>
                        <p class="mt-1 text-2xl font-bold text-amber-900">₱{{ number_format((float) $order->balance, 2) }}</p>
                    </div>

                    <form method="POST" action="{{ route('records.payment.record', $order->order_id) }}" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <fieldset class="space-y-2">
                            <legend class="text-xs font-semibold uppercase tracking-wide text-slate-700">Payment method</legend>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach (['Cash', 'GCash'] as $paymentMethod)
                                    <label class="flex cursor-pointer items-center justify-center gap-2 rounded-lg border border-sky-200 bg-white py-2.5 text-xs font-semibold text-slate-700 has-[:checked]:border-[#168cff] has-[:checked]:bg-[#168cff] has-[:checked]:text-white">
                                        <input type="radio" name="payment_method" value="{{ $paymentMethod }}" @checked($paymentMethod === 'Cash') required class="hidden">
                                        <i class="fa-solid {{ $paymentMethod === 'Cash' ? 'fa-money-bill-wave' : 'fa-mobile-screen-button' }}" aria-hidden="true"></i>
                                        {{ $paymentMethod }}
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        <div class="flex justify-end gap-2">
                            <button type="button" data-modal-close class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                            <button type="submit" class="rounded-lg bg-[#168cff] px-4 py-2 text-xs font-bold text-white hover:bg-[#0878df]">Record full payment</button>
                        </div>
                    </form>
                </div>
            </x-modal>
        @else
            <x-modal id="order-status-confirmation-{{ $order->order_id }}" title="Confirm status update">
                <p class="text-sm leading-6 text-slate-600">
                    Are you sure you want to move order #{{ $order->order_id }} from {{ $order->status }} to {{ $nextStatus }}?
                </p>
                <form method="POST" action="{{ route('records.status.update', $order->order_id) }}" class="mt-5 flex justify-end gap-2">
                    @csrf
                    @method('PATCH')
                    <button
                        type="button"
                        data-modal-close
                        class="rounded-lg border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                    >Cancel</button>
                    <button
                        type="submit"
                        data-loading-button
                        data-loading-message="Updating order..."
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#168cff] px-4 py-2 text-xs font-bold text-white hover:bg-[#0878df] disabled:cursor-not-allowed disabled:opacity-70"
                    >
                        <span data-loading-spinner class="hidden size-3 animate-spin rounded-full border-2 border-white/40 border-t-white" aria-hidden="true"></span>
                        <span data-loading-text>Confirm update</span>
                    </button>
                </form>
            </x-modal>
        @endif
    @endif
</div>
