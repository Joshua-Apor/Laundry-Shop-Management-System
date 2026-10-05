<x-layout>
    <section class="space-y-5">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-sky-700">{{ ucfirst($user->role) }} account</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">My Profile</h1>
            <p class="mt-1 text-sm text-slate-500">Update your account details and password.</p>
        </div>

        <x-profile.form :user="$user" />
    </section>
</x-layout>
