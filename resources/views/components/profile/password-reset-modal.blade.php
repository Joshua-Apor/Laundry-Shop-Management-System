@props(['user'])

@php
    $resetState = session('profile_password_reset', []);
    $stage = $resetState['stage'] ?? session('profile_password_reset_stage', 'email');
    $autoOpen = session()->has('profile_password_reset')
        || session()->has('profile_password_reset_stage')
        || $errors->hasAny(['profile_reset', 'code', 'password']);
    $title = match ($stage) {
        'code' => 'Enter verification code',
        'password' => 'Reset password',
        default => 'Reset password with email',
    };
@endphp

<x-modal id="profile-password-reset-modal" :title="$title" :data-modal-open-on-load="$autoOpen">
    @if (session('profile_password_reset_notice'))
        <p class="mb-4 rounded-lg bg-sky-50 p-3 text-sm leading-5 text-sky-800" role="status">
            {{ session('profile_password_reset_notice') }}
        </p>
    @endif

    @error('profile_reset')
        <p class="mb-4 text-sm text-red-600" role="alert">{{ $message }}</p>
    @enderror

    @if ($stage === 'code')
        <p class="text-sm leading-6 text-slate-600">Enter the six-digit code sent to <strong>{{ $user->email }}</strong>. The code expires in 10 minutes.</p>

        @error('code')
            <p class="mt-3 text-sm text-red-600" role="alert">{{ $message }}</p>
        @enderror

        <form method="POST" action="{{ route('profile.password.code.verify') }}" class="mt-5 space-y-4">
            @csrf
            <p class="text-xs font-semibold text-slate-700">Six-digit code</p>
            <x-auth.pin-code-input id="profile-password-reset-code" name="code" :length="6" />
            <button type="submit" data-loading-button data-loading-message="Verifying code..." class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-lg bg-[#168cff] px-4 text-sm font-bold text-white transition-colors hover:bg-[#0878df] disabled:cursor-not-allowed disabled:opacity-70">
                <span data-loading-spinner class="hidden size-4 animate-spin rounded-full border-2 border-white/40 border-t-white" aria-hidden="true"></span>
                <span data-loading-text>Verify code</span>
            </button>
        </form>

        <div class="mt-3 flex justify-between gap-3">
            <form method="POST" action="{{ route('profile.password.code.send') }}">
                @csrf
                <button type="submit" data-loading-button data-loading-message="Sending code..." class="text-xs font-semibold text-[#168cff] hover:text-[#0878df]">
                    <span data-loading-spinner class="mr-1 hidden size-3 animate-spin rounded-full border-2 border-sky-200 border-t-[#168cff]" aria-hidden="true"></span>
                    <span data-loading-text>Resend code</span>
                </button>
            </form>
            <form method="POST" action="{{ route('profile.password.cancel') }}">
                @csrf
                <button type="submit" data-loading-button data-loading-message="Returning..." class="text-xs font-semibold text-slate-500 hover:text-slate-700">
                    <span data-loading-text>Cancel</span>
                </button>
            </form>
        </div>
    @elseif ($stage === 'password')
        <p class="text-sm leading-6 text-slate-600">Your code is verified. Enter and confirm a new password with at least 8 characters.</p>

        <form method="POST" action="{{ route('profile.password.reset') }}" class="mt-5 space-y-4">
            @csrf
            <div>
                <label for="profile-reset-new-password" class="mb-1 block text-xs font-semibold text-slate-700">New password</label>
                <input id="profile-reset-new-password" name="password" type="password" required minlength="8" autocomplete="new-password" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-[#168cff] focus:ring-2 focus:ring-sky-100">
                @error('password')
                    <p class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="profile-reset-new-password-confirmation" class="mb-1 block text-xs font-semibold text-slate-700">Confirm new password</label>
                <input id="profile-reset-new-password-confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-[#168cff] focus:ring-2 focus:ring-sky-100">
            </div>
            <button type="submit" data-loading-button data-loading-message="Saving password..." class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-lg bg-[#168cff] px-4 text-sm font-bold text-white transition-colors hover:bg-[#0878df] disabled:cursor-not-allowed disabled:opacity-70">
                <span data-loading-spinner class="hidden size-4 animate-spin rounded-full border-2 border-white/40 border-t-white" aria-hidden="true"></span>
                <span data-loading-text>Reset password</span>
            </button>
        </form>
    @else
        <p class="text-sm leading-6 text-slate-600">We will send a six-digit code to your account email address: <strong>{{ $user->email ?: 'No email address is set' }}</strong>.</p>

        <form method="POST" action="{{ route('profile.password.code.send') }}" class="mt-5">
            @csrf
            <button type="submit" @disabled(blank($user->email)) data-loading-button data-loading-message="Sending code..." class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-lg bg-[#168cff] px-4 text-sm font-bold text-white transition-colors hover:bg-[#0878df] disabled:cursor-not-allowed disabled:opacity-70">
                <span data-loading-spinner class="hidden size-4 animate-spin rounded-full border-2 border-white/40 border-t-white" aria-hidden="true"></span>
                <span data-loading-text>Send verification code</span>
            </button>
        </form>
    @endif
</x-modal>
