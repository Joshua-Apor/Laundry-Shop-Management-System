<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
        ]);

        if (filled($validated['password'] ?? null)) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }
}
