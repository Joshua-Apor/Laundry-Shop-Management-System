<div class="flex h-24 min-w-0 w-full flex-col rounded-xl border border-sky-200 bg-white p-2 sm:h-28 sm:p-3">
    <div class="flex w-full flex-col items-center">
        <p class="mb-1 truncate text-[9px] font-medium text-slate-600 sm:text-xs">
            {{ $title }}
        </p>
        <div class="h-px w-3/4 bg-[#bcd2f5]"></div>
    </div>

    <div class="flex flex-1 items-center justify-center">
        <span class="text-lg font-semibold tracking-wide text-slate-900 sm:text-2xl">
            {{ $value }}
        </span>
    </div>
</div>
