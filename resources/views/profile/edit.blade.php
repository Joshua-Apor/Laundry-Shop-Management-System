<x-layout>
    <section class="mx-auto max-w-5xl space-y-5">
        <div class="flex items-center gap-4 rounded-2xl border border-sky-100 bg-white p-5 shadow-sm sm:p-6">
            <div class="grid size-16 shrink-0 place-items-center overflow-hidden rounded-full bg-sky-100 text-xl font-bold text-sky-700 ring-4 ring-sky-50">
                @if ($user->profile_picture_path)
                    <img src="{{ asset('storage/'.$user->profile_picture_path) }}" alt="" class="size-full object-cover">
                @else
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                @endif
            </div>
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="truncate text-xl font-bold text-slate-900 sm:text-2xl">{{ $user->name }}</h1>
                    <span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-bold capitalize text-sky-700 ring-1 ring-inset ring-sky-200">{{ $user->role }}</span>
                </div>
                <p class="mt-1 text-sm text-slate-500">Manage your account details and password.</p>
            </div>
        </div>

        <x-profile.form :user="$user" />
        <x-profile.password-reset-modal :user="$user" />
    </section>
</x-layout>
