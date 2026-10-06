<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetCode as PasswordResetCodeMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

class PasswordResetController extends Controller
{
    public function sendCode(Request $request): RedirectResponse
    {
        $request->session()->flash('password_reset_stage', 'email');

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = mb_strtolower(trim($validated['email']));
        $limiterKey = 'password-reset-email:'.hash('sha256', $email.'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($limiterKey, 3)) {
            return back()->withErrors([
                'email' => 'Please wait a minute before requesting another code.',
            ]);
        }

        RateLimiter::hit($limiterKey, 60);
        DB::table('password_reset_codes')->where('expires_at', '<=', now())->delete();

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if ($user === null) {
            $request->session()->put('password_reset', [
                'stage' => 'code',
                'email' => $email,
                'record_id' => null,
            ]);

            return redirect()->route('login')->with(
                'password_reset_notice',
                'If an account exists for that email, a six-digit code has been sent.',
            );
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $now = now();

        DB::table('password_reset_codes')->updateOrInsert(
            ['email' => $email],
            [
                'token_hash' => Hash::make($code),
                'expires_at' => $now->copy()->addMinutes(10),
                'attempts' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );

        $resetCode = DB::table('password_reset_codes')->where('email', $email)->first(['id']);

        $request->session()->put('password_reset', [
            'stage' => 'code',
            'email' => $email,
            'record_id' => $resetCode->id,
        ]);

        try {
            Mail::to($user->email)->send(new PasswordResetCodeMail($code));
        } catch (Throwable $exception) {
            report($exception);
            DB::table('password_reset_codes')->where('id', $resetCode->id)->delete();
        }

        return redirect()->route('login')->with(
            'password_reset_notice',
            'If an account exists for that email, a six-digit code has been sent.',
        );
    }

    public function verifyCode(Request $request): RedirectResponse
    {
        $reset = $request->session()->get('password_reset');

        if (! is_array($reset) || ($reset['stage'] ?? null) !== 'code') {
            $request->session()->flash('password_reset_stage', 'email');

            return redirect()->route('login')->withErrors([
                'email' => 'Start a password reset request first.',
            ]);
        }

        $request->session()->flash('password_reset_stage', 'code');

        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $resetCode = isset($reset['record_id'])
            ? DB::table('password_reset_codes')
                ->where('id', $reset['record_id'])
                ->where('email', $reset['email'])
                ->first()
            : null;

        if ($resetCode === null || Carbon::parse($resetCode->expires_at)->isPast()) {
            if ($resetCode !== null) {
                DB::table('password_reset_codes')->where('id', $resetCode->id)->delete();
            }

            return back()->withErrors(['code' => 'That code is invalid or expired. Request a new code and try again.']);
        }

        if (! Hash::check($validated['code'], $resetCode->token_hash)) {
            $attempts = $resetCode->attempts + 1;

            if ($attempts >= 5) {
                DB::table('password_reset_codes')->where('id', $resetCode->id)->delete();
            } else {
                DB::table('password_reset_codes')->where('id', $resetCode->id)->update(['attempts' => $attempts]);
            }

            return back()->withErrors([
                'code' => $attempts >= 5
                    ? 'Too many incorrect attempts. Request a new code.'
                    : 'That code is incorrect. Check the email and try again.',
            ]);
        }

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$reset['email']])
            ->first();

        if ($user === null) {
            DB::table('password_reset_codes')->where('id', $resetCode->id)->delete();

            return back()->withErrors(['code' => 'That code is invalid or expired. Request a new code and try again.']);
        }

        DB::table('password_reset_codes')->where('id', $resetCode->id)->delete();
        $request->session()->put('password_reset', [
            'stage' => 'password',
            'user_id' => $user->getKey(),
            'verified_until' => now()->addMinutes(10)->timestamp,
        ]);

        return redirect()->route('login')->with('password_reset_notice', 'Code verified. Choose a new password.');
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $reset = $request->session()->get('password_reset');

        if (! is_array($reset)
            || ($reset['stage'] ?? null) !== 'password'
            || now()->timestamp > ($reset['verified_until'] ?? 0)) {
            $request->session()->forget('password_reset');
            $request->session()->flash('password_reset_stage', 'email');

            return redirect()->route('login')->withErrors([
                'email' => 'Your verification expired. Request a new code to continue.',
            ]);
        }

        $request->session()->flash('password_reset_stage', 'password');

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::query()->find($reset['user_id']);

        if ($user === null) {
            $request->session()->forget('password_reset');
            $request->session()->flash('password_reset_stage', 'email');

            return redirect()->route('login')->withErrors([
                'email' => 'That account is no longer available. Request a new code.',
            ]);
        }

        $user->password = $validated['password'];
        $user->must_change_password = false;
        $user->save();
        $request->session()->forget('password_reset');
        $request->session()->forget('password_reset_stage');

        return redirect()->route('login')->with('success', 'Your password was reset. You can now sign in.');
    }

    public function cancel(Request $request): RedirectResponse
    {
        $request->session()->forget('password_reset');
        $request->session()->forget('password_reset_stage');

        return redirect()->route('login');
    }
}
