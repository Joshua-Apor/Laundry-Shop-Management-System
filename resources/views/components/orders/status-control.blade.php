@props(['order', 'editable' => true])

@php
    $statusColor = match ($order->status) {
        'Processing' => 'bg-amber-100 text-amber-800',
        'Ready for Pickup' => 'bg-sky-100 text-sky-800',
        'Completed' => 'bg-emerald-100 text-emerald-800',
        default => 'bg-slate-100 text-slate-600',
    };
@endphp

<div @class(['relative z-20 flex min-w-0 flex-col gap-2', 'items-end sm:items-start' => $editable, 'items-center' => ! $editable, 'pointer-events-none' => ! $editable])>
    <span @class(['inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold', $statusColor])>
        {{ $order->status }}
    </span>
    @if ($editable)
        <form method="POST" action="{{ route('records.status.update', $order->order_id) }}" class="flex w-full min-w-0 items-center gap-1.5 sm:max-w-44">
            @csrf
            @method('PATCH')
            <label class="sr-only" for="order-status-{{ $order->order_id }}">Change status for order {{ $order->order_id }}</label>
            <select
                id="order-status-{{ $order->order_id }}"
                name="status"
                required
                class="min-w-0 flex-1 rounded-lg border border-sky-200 bg-white px-2 py-1.5 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-300"
            >
                @if (! in_array($order->status, \App\Models\Order::STATUSES, true))
                    <option value="" selected disabled>Choose status</option>
                @endif
                @foreach (\App\Models\Order::STATUSES as $statusOption)
                    <option value="{{ $statusOption }}" @selected($order->status === $statusOption)>{{ $statusOption }}</option>
                @endforeach
            </select>
            <button
                type="submit"
                aria-label="Save status for order {{ $order->order_id }}"
                class="grid size-8 shrink-0 place-items-center rounded-lg bg-[#168cff] text-white transition-colors hover:bg-[#0878df] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-500"
            >
                <i class="fa-solid fa-check" aria-hidden="true"></i>
            </button>
        </form>
    @endif
</div>