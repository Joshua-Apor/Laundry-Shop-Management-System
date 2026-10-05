<x-layout>
    <h1 class="mb-6 text-2xl font-bold">Good Morning, {{ auth()->user()->name }}</h1>

    <div
        data-dashboard-results
        data-dashboard-endpoint="{{ route('dashboard.data') }}"
        aria-busy="true"
        aria-live="polite"
    >
        <div class="space-y-6" role="status" data-dashboard-loading>
            <div class="flex items-center gap-2 text-sm font-medium text-slate-500">
                <span class="size-4 animate-spin rounded-full border-2 border-sky-200 border-t-[#168cff]" aria-hidden="true"></span>
                <span>Loading dashboard data...</span>
            </div>

            <section class="space-y-3">
                <div class="flex items-center justify-between">
                    <div class="h-6 w-40 animate-pulse rounded bg-sky-100"></div>
                    <div class="h-4 w-28 animate-pulse rounded bg-sky-100"></div>
                </div>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                    @foreach (range(1, 4) as $metric)
                        <div class="h-24 animate-pulse rounded-xl border border-sky-100 bg-white"></div>
                    @endforeach
                </div>
            </section>

            <section class="space-y-3">
                <div class="h-10 w-48 animate-pulse rounded bg-sky-100"></div>
                @foreach (range(1, 3) as $order)
                    <div class="h-20 animate-pulse rounded-2xl border border-sky-100 bg-white"></div>
                @endforeach
            </section>
        </div>
    </div>
</x-layout>
