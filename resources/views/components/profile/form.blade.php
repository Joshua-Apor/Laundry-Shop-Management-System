@props(['user'])

<form method="POST" action="{{ route('profile.update') }}" class="space-y-6 rounded-2xl border border-sky-100 bg-white p-5 shadow-sm sm:p-7">
    @csrf
    @method('PATCH')

    <section class="space-y-4">
        <div>
            <h2 class="font-bold text-slate-900">Account details</h2>
            <p class="mt-1 text-xs text-slate-500">Your role is managed by the system.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="profile-name" class="mb-1 block text-xs font-semibold text-slate-700">Full name</label>
                <input id="profile-name" name="name" type="text" value="{{ old('name', $user->name) }}" required maxlength="255" autocomplete="name" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="profile-username" class="mb-1 block text-xs font-semibold text-slate-700">Username</label>
                <input id="profile-username" name="username" type="text" value="{{ old('username', $user->username) }}" required maxlength="255" autocomplete="username" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                @error('username')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-2">
                <label for="profile-email" class="mb-1 block text-xs font-semibold text-slate-700">Email</label>
                <input id="profile-email" name="email" type="email" value="{{ old('email', $user->email) }}" maxlength="255" autocomplete="email" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>
    </section>

    <section class="space-y-4 border-t border-slate-100 pt-5">
        <div>
            <h2 class="font-bold text-slate-900">Change password</h2>
            <p class="mt-1 text-xs text-slate-500">Leave these fields blank to keep your current password.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="profile-current-password" class="mb-1 block text-xs font-semibold text-slate-700">Current password</label>
                <input id="profile-current-password" name="current_password" type="password" autocomplete="current-password" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                @error('current_password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="profile-password" class="mb-1 block text-xs font-semibold text-slate-700">New password</label>
                <input id="profile-password" name="password" type="password" minlength="8" autocomplete="new-password" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="profile-password-confirmation" class="mb-1 block text-xs font-semibold text-slate-700">Confirm new password</label>
                <input id="profile-password-confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
            </div>
        </div>
    </section>

    <div class="flex flex-wrap items-center justify-end gap-3 border-t border-slate-100 pt-5">
        <button type="submit" data-loading-button data-loading-message="Saving profile..." class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-[#168cff] px-5 text-sm font-bold text-white transition-colors hover:bg-[#0878df] disabled:cursor-not-allowed disabled:opacity-70">
            <span data-loading-spinner class="hidden size-4 animate-spin rounded-full border-2 border-white/40 border-t-white" aria-hidden="true"></span>
            <span data-loading-text><i class="fa-solid fa-floppy-disk mr-1" aria-hidden="true"></i>Save profile</span>
        </button>
    </div>
</form>
