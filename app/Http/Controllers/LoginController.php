<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
            'role' => ['required', 'string', 'in:employee,manager'],
        ]);

        $loginCredentials = [
            'password' => $credentials['password'],
            'role' => $credentials['role'],
        ];
        $identifier = $credentials['identifier'];

        if (filter_var($identifier, FILTER_VALIDATE_EMAIL) !== false) {
            $authenticated = Auth::attempt([
                ...$loginCredentials,
                'email' => $identifier,
            ], $request->boolean('remember'));
        } else {
            $authenticated = Auth::attempt([
                ...$loginCredentials,
                'username' => $identifier,
            ], $request->boolean('remember')) || Auth::attempt([
                ...$loginCredentials,
                'name' => $identifier,
            ], $request->boolean('remember'));
        }

        if (! $authenticated) {
            return back()
                ->withErrors(['identifier' => 'Those credentials do not match our records.'])
                ->onlyInput('identifier', 'role');
        }

        $request->session()->regenerate();
        $request->session()->forget('password_reset_prompt_dismissed');

        $destination = $request->user()->role === 'employee'
            ? route('employee.dashboard')
            : route('manager.dashboard');

        return redirect($destination)->with('success', 'Signed in successfully.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        DB::table(config('session.table', 'sessions'))
            ->where('id', $request->session()->getId())
            ->delete();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Signed out successfully.');
    }
}
