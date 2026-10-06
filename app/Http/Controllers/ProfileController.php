<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function updateInitialPassword(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user?->role === 'employee' && $user->must_change_password, 403);

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->password = $validated['password'];
        $user->must_change_password = false;
        $user->save();
        $request->session()->forget('password_reset_prompt_dismissed');

        return redirect()->route('employee.dashboard')->with('success', 'Password updated successfully.');
    }

    public function deferInitialPassword(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user?->role === 'employee' && $user->must_change_password, 403);

        $request->session()->put('password_reset_prompt_dismissed', true);

        return redirect()->route('employee.dashboard');
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->getKey(), 'user_id')],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->getKey(), 'user_id')],
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'extensions:jpg,jpeg,png,webp', 'max:2048'],
            'remove_profile_picture' => ['sometimes', 'boolean'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'profile_picture.image' => 'Please choose a valid image file.',
            'profile_picture.mimes' => 'The profile picture must be a JPG, PNG, or WebP image.',
            'profile_picture.extensions' => 'The profile picture file extension must be JPG, PNG, or WebP.',
            'profile_picture.max' => 'The profile picture must not be larger than 2 MB.',
        ]);

        $user->fill([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
        ]);

        if (filled($validated['password'] ?? null)) {
            $user->password = $validated['password'];
        }

        $previousProfilePicturePath = $user->profile_picture_path;

        $removeProfilePicture = $request->boolean('remove_profile_picture');

        if ($removeProfilePicture) {
            $user->profile_picture_path = null;
        } elseif ($request->hasFile('profile_picture')) {
            $user->profile_picture_path = $request->file('profile_picture')->storePublicly('profile-pictures', 'public');
        }

        $user->save();

        if ($previousProfilePicturePath && ($removeProfilePicture || $request->hasFile('profile_picture'))) {
            Storage::disk('public')->delete($previousProfilePicturePath);
        }

        return redirect()->route('profile.edit')->with(
            'success',
            $removeProfilePicture ? 'Profile picture removed.' : 'Profile updated successfully.',
        );
    }
}
