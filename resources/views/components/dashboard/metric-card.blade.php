@props([
    'title',
    'value',
    'icon' => null,
])

@php
    $icon ??= match ($title) {
        'Total Orders' => 'fa-box',
        'Completed', 'Completed Orders' => 'fa-circle-check',
        'Ready for Pickup' => 'fa-clock',
        'Total Revenue', 'Total Collected', 'Outstanding', 'Avg. Spend' => 'fa-money-bill-wave',
        'Total Customers' => 'fa-users',
        'Repeat Customers' => 'fa-user-plus',
        default => 'fa-chart-pie',
    };
@endphp

<div
    @class([
        'flex h-24 min-w-0 w-full flex-col rounded-xl border border-sky-100 bg-white p-2 shadow-sm sm:h-28 sm:p-3',
    ])
>
    <div
        @class([
            'flex w-full items-start justify-between gap-1',
        ])
    >
        <p
            @class([
                'truncate text-[9px] font-medium text-slate-600 sm:text-xs',
            ])
        >
            {{ $title }}
        </p>

        <span class="grid size-7 shrink-0 place-items-center rounded-lg bg-sky-50 text-xs text-[#168cff] sm:size-8">
            <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>
        </span>
    </div>

    <div
        @class([
            'flex flex-1 items-center justify-center',
        ])
    >
        <span
            @class([
                'truncate text-lg font-semibold tracking-wide text-slate-900 sm:text-2xl',
            ])
        >
            {{ $value }}
        </span>
    </div>
</div>
