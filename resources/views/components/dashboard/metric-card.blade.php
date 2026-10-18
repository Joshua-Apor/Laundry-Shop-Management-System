@props([
    'title',
    'value',
    'icon' => null,
    'featured' => false,
    'compact' => false,
    'stretch' => false,
    'comparison' => null,
    'trendPoints' => null,
    'entryAnimation' => false,
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
        'flex min-w-0 w-full flex-col rounded-xl border border-sky-100 bg-white shadow-sm',
        'card-entry-animation' => $entryAnimation,
        'h-full p-1.5 sm:p-3' => $compact && $stretch,
        'h-16 p-1.5 sm:h-20 sm:p-3' => $compact && ! $stretch,
        'h-24 p-2 sm:h-28 sm:p-3' => ! $featured && ! $compact,
        'row-span-2 min-h-52 p-3 sm:min-h-64 sm:p-5' => $featured,
    ])
>
    @if ($featured)
        <div class="flex flex-1 flex-col">
            <div class="flex items-start justify-between gap-1.5 sm:gap-2">
                <p class="min-w-0 truncate text-xs font-medium text-slate-600 sm:text-base">{{ $title }}</p>

                @if ($comparison)
                    <div class="shrink-0 text-right">
                        <span @class([
                            'inline-flex items-center gap-1 rounded-full px-1.5 py-0.5 text-[9px] font-semibold sm:px-2 sm:py-1 sm:text-xs',
                            'bg-emerald-50 text-emerald-700' => $comparison['direction'] === 'up',
                            'bg-red-50 text-red-700' => $comparison['direction'] === 'down',
                            'bg-slate-100 text-slate-600' => $comparison['direction'] === 'flat',
                        ])>
                            @if ($comparison['direction'] === 'up')
                                <i class="fa-solid fa-arrow-trend-up" aria-hidden="true"></i>
                            @elseif ($comparison['direction'] === 'down')
                                <i class="fa-solid fa-arrow-trend-down" aria-hidden="true"></i>
                            @else
                                <i class="fa-solid fa-minus" aria-hidden="true"></i>
                            @endif
                            {{ $comparison['value'] }}
                        </span>
                        <span class="mt-0.5 block text-[8px] text-slate-500 sm:mt-1 sm:text-[10px]">vs yesterday</span>
                    </div>
                @endif
            </div>

            <span class="mt-1 truncate text-xl font-semibold tracking-wide text-slate-900 sm:text-3xl">
                {{ $value }}
            </span>

            @if ($trendPoints)
                <div class="mt-auto border-t border-slate-100 pt-1.5 sm:pt-2">
                    <p class="mb-1 text-[9px] font-medium text-slate-500 sm:text-xs">Revenue - last 7 days</p>
                    <svg class="h-8 w-full overflow-visible text-[#168cff] sm:h-14" viewBox="0 0 100 40" preserveAspectRatio="none" role="img" aria-label="Revenue trend over the last seven days">
                        <polygon points="0,40 {{ $trendPoints }} 98,40" class="fill-sky-100" />
                        <polyline points="{{ $trendPoints }}" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                    </svg>
                </div>
            @endif
        </div>
    @elseif ($compact)
        <div class="flex flex-1 items-center justify-between gap-1.5 sm:gap-2">
            <div class="min-w-0">
                <p class="truncate text-[8px] font-medium leading-tight text-slate-600 sm:text-xs">{{ $title }}</p>
                <span class="mt-0.5 block truncate text-sm font-semibold tracking-wide text-slate-900 sm:mt-1 sm:text-xl">
                    {{ $value }}
                </span>
            </div>

            @if ($comparison)
                <div class="shrink-0 text-right">
                    <span @class([
                        'inline-flex items-center gap-1 rounded-full px-1.5 py-0.5 text-[8px] font-semibold sm:px-2 sm:py-1 sm:text-[10px]',
                        'bg-emerald-50 text-emerald-700' => $comparison['direction'] === 'up',
                        'bg-red-50 text-red-700' => $comparison['direction'] === 'down',
                        'bg-slate-100 text-slate-600' => $comparison['direction'] === 'flat',
                    ])>
                        @if ($comparison['direction'] === 'up')
                            <i class="fa-solid fa-arrow-trend-up" aria-hidden="true"></i>
                        @elseif ($comparison['direction'] === 'down')
                            <i class="fa-solid fa-arrow-trend-down" aria-hidden="true"></i>
                        @else
                            <i class="fa-solid fa-minus" aria-hidden="true"></i>
                        @endif
                        {{ $comparison['value'] }}
                    </span>
                    <span class="mt-0.5 block text-[8px] text-slate-500 sm:mt-1 sm:text-[9px]">vs yesterday</span>
                </div>
            @endif
        </div>
    @else
        <div class="flex w-full items-start justify-between gap-2">
            <p @class([
                'truncate font-medium text-slate-600',
                'text-[9px] sm:text-xs' => ! $featured,
                'text-sm sm:text-base' => $featured,
            ])>
                {{ $title }}
            </p>

            <span @class([
                'grid shrink-0 place-items-center rounded-lg bg-sky-50 text-[#168cff]',
                'size-7 text-xs sm:size-8' => ! $featured,
                'size-9 text-sm sm:size-10' => $featured,
            ])>
                <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>
            </span>
        </div>

        <div @class([
            'flex flex-1 items-center',
            'justify-center' => ! $featured,
            'justify-start' => $featured,
        ])>
            <span @class([
                'truncate font-semibold tracking-wide text-slate-900',
                'text-lg sm:text-2xl' => ! $featured,
                'text-3xl sm:text-4xl' => $featured,
            ])>
                {{ $value }}
            </span>
        </div>
    @endif
</div>
