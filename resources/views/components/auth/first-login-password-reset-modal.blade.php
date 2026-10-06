<x-modal id="employee-password-reset" title="Reset Password" data-modal-open-on-load>
    <p class="text-sm leading-6 text-slate-600">You are using a temporary password. Create a new password now, or choose Later to do this another time.</p>

    <form method="POST" action="{{ route('employee.password.initial.update') }}" class="mt-5 space-y-4">
        @csrf
        <div>
            <label for="initial-password" class="mb-1 block text-xs font-semibold text-slate-700">Password</label>
            <input id="initial-password" name="password" type="password" required minlength="8" autocomplete="new-password" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-[#168cff] focus:ring-2 focus:ring-sky-100">
            @error('password')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="initial-password-confirmation" class="mb-1 block text-xs font-semibold text-slate-700">Confirm password</label>
            <input id="initial-password-confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-[#168cff] focus:ring-2 focus:ring-sky-100">
        </div>

        <button type="submit" data-loading-button data-loading-message="Updating password..." class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-lg bg-[#168cff] px-4 text-sm font-bold text-white transition-colors hover:bg-[#0878df] disabled:cursor-not-allowed disabled:opacity-70">
            <span data-loading-spinner class="hidden size-4 animate-spin rounded-full border-2 border-white/40 border-t-white" aria-hidden="true"></span>
            <span data-loading-text>Confirm</span>
        </button>
    </form>

    <form method="POST" action="{{ route('employee.password.initial.defer') }}" class="mt-2">
        @csrf
        <button type="submit" data-loading-button data-loading-message="Opening dashboard..." class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-lg border border-slate-200 px-4 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-70">
            <span data-loading-spinner class="hidden size-4 animate-spin rounded-full border-2 border-slate-400/40 border-t-slate-600" aria-hidden="true"></span>
            <span data-loading-text>Later</span>
        </button>
    </form>
</x-modal>