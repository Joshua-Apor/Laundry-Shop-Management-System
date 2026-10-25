@props(['dateKey'])

@php
    $dateKey = (string) $dateKey;
    $date = \Illuminate\Support\Carbon::parse($dateKey);
    $label = match (true) {
        $dateKey === now()->toDateString() => 'Today · '.now()->format('F j, Y'),
        $dateKey === now()->subDay()->toDateString() => 'Yesterday · '.now()->subDay()->format('F j, Y'),
        strlen($dateKey) === 4 => $dateKey,
        default => $date->format('F j, Y'),
    };
@endphp

<div class="my-4 flex items-center gap-3" {{ $attributes }}>
    <span class="h-px grow bg-sky-200" aria-hidden="true"></span>
    <h2 class="shrink-0 text-center text-xs font-bold uppercase tracking-wider text-sky-700">
        {{ $label }}
    </h2>
    <span class="h-px grow bg-sky-200" aria-hidden="true"></span>
</div>
