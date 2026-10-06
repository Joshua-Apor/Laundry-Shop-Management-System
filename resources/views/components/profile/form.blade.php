@props(['user'])

<form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6 rounded-2xl border border-sky-100 bg-white p-5 shadow-sm sm:p-7">
    @csrf
    @method('PATCH')

    <section class="space-y-4">
        <div>
            <h2 class="font-bold text-slate-900">Profile picture</h2>
            <p class="mt-1 text-xs text-slate-500">Upload a JPG, PNG, or WebP image up to 2 MB, then crop it to fit.</p>
        </div>

        <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
            <div class="grid size-36 shrink-0 place-items-center overflow-hidden rounded-full bg-sky-100 text-3xl font-bold text-sky-700 ring-4 ring-sky-50 sm:size-44">
                @if ($user->profile_picture_path)
                    <img data-profile-picture-preview src="{{ asset('storage/'.$user->profile_picture_path) }}" alt="Your profile picture" class="size-full object-cover">
                @else
                    <img data-profile-picture-preview alt="Your selected profile picture" class="hidden size-full object-cover">
                    <span data-profile-picture-initials>{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                @endif
            </div>

            <div class="min-w-0 space-y-3">
                <div>
                    <label for="profile-picture" class="mb-1 block text-xs font-semibold text-slate-700">Choose a picture</label>
                    <input id="profile-picture" name="profile_picture" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-profile-picture-input aria-describedby="profile-picture-help" class="block w-full max-w-sm text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-sky-50 file:px-3 file:py-2 file:text-xs file:font-bold file:text-sky-700 hover:file:bg-sky-100">
                    <p id="profile-picture-help" class="mt-1 text-xs text-slate-500">JPG, PNG, or WebP only. Maximum file size: 2 MB.</p>
                    @error('profile_picture')<p class="mt-1 text-xs text-red-600" role="alert">{{ $message }}</p>@enderror
                    <p data-profile-picture-error class="mt-1 hidden text-xs text-red-600" role="alert"></p>
                </div>

                @if ($user->profile_picture_path)
                    <button type="button" data-modal-trigger data-modal-target="remove-profile-picture-confirmation" class="inline-flex items-center gap-2 rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 transition-colors hover:bg-red-50">
                        <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                        Remove profile picture
                    </button>
                @endif
            </div>
        </div>
    </section>

    <dialog data-profile-picture-cropper class="m-auto w-[min(32rem,calc(100vw-2rem))] max-w-none rounded-2xl border border-slate-200 bg-white p-5 text-slate-900 shadow-2xl backdrop:bg-slate-950/60 sm:p-7">
        <div class="space-y-5">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Crop profile picture</h2>
                <p class="mt-1 text-xs text-slate-500">Drag the circle to move the crop area. Drag any corner handle to resize it.</p>
            </div>

            <div data-profile-picture-crop-frame tabindex="0" role="group" class="relative mx-auto aspect-square w-full max-w-md touch-none cursor-move overflow-hidden rounded-xl bg-slate-900 ring-4 ring-sky-100 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-sky-600" aria-label="Square image preview with a movable and resizable circular crop area. Drag outside the circle to reposition the image, or use arrow keys.">
                <img data-profile-picture-crop-image alt="Crop your profile picture" draggable="false" class="pointer-events-none absolute max-w-none select-none">
                <span data-profile-picture-crop-circle tabindex="0" role="group" aria-label="Crop circle. Arrow keys move it; hold Shift and use arrow keys to resize." class="pointer-events-auto absolute left-0 top-0 cursor-move rounded-full border-2 border-dashed border-white shadow-[0_0_0_9999px_rgb(15_23_42_/_45%)] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                    <span data-profile-picture-crop-resize="top-left" class="absolute -left-1.5 -top-1.5 size-3 cursor-nwse-resize rounded-full border border-slate-700 bg-white shadow" aria-hidden="true"></span>
                    <span data-profile-picture-crop-resize="top-right" class="absolute -right-1.5 -top-1.5 size-3 cursor-nesw-resize rounded-full border border-slate-700 bg-white shadow" aria-hidden="true"></span>
                    <span data-profile-picture-crop-resize="bottom-left" class="absolute -bottom-1.5 -left-1.5 size-3 cursor-nesw-resize rounded-full border border-slate-700 bg-white shadow" aria-hidden="true"></span>
                    <span data-profile-picture-crop-resize="bottom-right" class="absolute -bottom-1.5 -right-1.5 size-3 cursor-nwse-resize rounded-full border border-slate-700 bg-white shadow" aria-hidden="true"></span>
                </span>
            </div>

            <div class="flex flex-wrap justify-end gap-2">
                <button type="button" data-profile-picture-cancel class="rounded-lg border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-50">Cancel</button>
                <button type="button" data-profile-picture-crop class="rounded-lg bg-[#168cff] px-4 py-2.5 text-xs font-bold text-white transition-colors hover:bg-[#0878df]">Use this crop</button>
            </div>
        </div>
    </dialog>

    @if ($user->profile_picture_path)
        <dialog id="remove-profile-picture-confirmation" data-modal-dialog class="m-auto w-[min(26rem,calc(100vw-2rem))] max-w-none rounded-2xl border border-slate-200 bg-white p-6 text-slate-900 shadow-2xl backdrop:bg-slate-950/50">
            <div class="space-y-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Remove profile picture?</h2>
                    <p class="mt-1 text-sm leading-6 text-slate-600">Your profile will show your initials until you add another picture.</p>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" data-modal-close class="rounded-lg border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-700 transition-colors hover:bg-slate-50">Cancel</button>
                    <button type="submit" name="remove_profile_picture" value="1" class="rounded-lg bg-red-600 px-4 py-2.5 text-xs font-bold text-white transition-colors hover:bg-red-700">Remove picture</button>
                </div>
            </div>
        </dialog>
    @endif

    <section class="space-y-4 border-t border-slate-100 pt-5">
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
            <p class="mt-1 text-xs text-slate-500">Leave these fields blank to keep your current password. New passwords need at least 8 characters.</p>
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

    <section class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-5">
        <div>
            <h2 class="font-bold text-slate-900">Reset password with email</h2>
            <p class="mt-1 text-xs text-slate-500">Verify a code sent to your account email to choose a new password.</p>
        </div>
        <button type="button" data-modal-trigger data-modal-target="profile-password-reset-modal" data-modal-loading-trigger class="relative inline-flex h-10 items-center justify-center rounded-lg border border-sky-200 pl-7 pr-4 text-sm font-bold text-sky-700 transition-colors hover:bg-sky-50">
            <span data-loading-spinner class="absolute left-2 hidden size-3 animate-spin rounded-full border-2 border-sky-200 border-t-[#168cff]" aria-hidden="true"></span>
            <span>Reset using email code</span>
        </button>
    </section>

    <div class="flex flex-wrap items-center justify-end gap-3 border-t border-slate-100 pt-5">
        <button type="submit" data-loading-button data-loading-message="Saving profile..." class="inline-flex h-10 items-center justify-center gap-2 rounded-lg bg-[#168cff] px-5 text-sm font-bold text-white transition-colors hover:bg-[#0878df] disabled:cursor-not-allowed disabled:opacity-70">
            <span data-loading-spinner class="hidden size-4 animate-spin rounded-full border-2 border-white/40 border-t-white" aria-hidden="true"></span>
            <span data-loading-text><i class="fa-solid fa-floppy-disk mr-1" aria-hidden="true"></i>Save profile</span>
        </button>
    </div>
</form>
