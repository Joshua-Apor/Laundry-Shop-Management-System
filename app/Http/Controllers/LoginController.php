<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'role' => ['required', 'string', 'in:employee,manager'],
        ]);

        if (! Auth::attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'role' => $credentials['role'],
        ], $request->boolean('remember'))) {
            return back()
                ->withErrors(['username' => 'Those credentials do not match our records.'])
                ->onlyInput('username', 'role');
        }

        $request->session()->regenerate();

        $destination = $request->user()->role === 'employee'
            ? route('employee.dashboard')
            : route('manager.dashboard');

        return redirect($destination);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
