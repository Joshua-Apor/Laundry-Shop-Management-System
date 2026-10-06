<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetCode as PasswordResetCodeMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

class ProfilePasswordResetController extends Controller
{
    public function sendCode(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (blank($user->email)) {
            $request->session()->flash('profile_password_reset_stage', 'email');

            return back()->withErrors([
                'profile_reset' => 'Add an email address to your profile before requesting a reset code.',
            ]);
        }

        $limiterKey = 'profile-password-reset:'.$user->getKey().'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($limiterKey, 3)) {
            $request->session()->flash('profile_password_reset_stage', 'email');

            return back()->withErrors([
                'profile_reset' => 'Please wait a minute before requesting another code.',
            ]);
        }

        RateLimiter::hit($limiterKey, 60);
        DB::table('password_reset_codes')->where('expires_at', '<=', now())->delete();

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $now = now();

        DB::table('password_reset_codes')->updateOrInsert(
            ['email' => mb_strtolower(trim($user->email))],
            [
                'token_hash' => Hash::make($code),
                'expires_at' => $now->copy()->addMinutes(10),
                'attempts' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        );

        $resetCode = DB::table('password_reset_codes')
            ->where('email', mb_strtolower(trim($user->email)))
            ->first(['id']);

        try {
            Mail::to($user->email)->send(new PasswordResetCodeMail($code));
        } catch (Throwable $exception) {
            report($exception);
            DB::table('password_reset_codes')->where('id', $resetCode->id)->delete();
            $request->session()->flash('profile_password_reset_stage', 'email');

            return back()->withErrors([
                'profile_reset' => 'The reset email could not be sent. Please try again later.',
            ]);
        }

        $request->session()->put('profile_password_reset', [
            'stage' => 'code',
            'user_id' => $user->getKey(),
            'email' => mb_strtolower(trim($user->email)),
            'record_id' => $resetCode->id,
        ]);

        return back()->with('profile_password_reset_notice', 'A six-digit verification code was sent to your email.');
    }

    public function verifyCode(Request $request): RedirectResponse
    {
        $reset = $request->session()->get('profile_password_reset');

        if (! is_array($reset)
            || ($reset['stage'] ?? null) !== 'code'
            || ($reset['user_id'] ?? null) !== $request->user()->getKey()) {
            $request->session()->flash('profile_password_reset_stage', 'email');

            return back()->withErrors(['profile_reset' => 'Request a new verification code to continue.']);
        }

        $request->session()->flash('profile_password_reset_stage', 'code');

        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $resetCode = DB::table('password_reset_codes')
            ->where('id', $reset['record_id'])
            ->where('email', $reset['email'])
            ->first();

        if ($resetCode === null || Carbon::parse($resetCode->expires_at)->isPast()) {
            if ($resetCode !== null) {
                DB::table('password_reset_codes')->where('id', $resetCode->id)->delete();
            }

            $request->session()->forget('profile_password_reset');
            $request->session()->flash('profile_password_reset_stage', 'email');

            return back()->withErrors(['profile_reset' => 'That code is invalid or expired. Request a new code.']);
        }

        if (! Hash::check($validated['code'], $resetCode->token_hash)) {
            $attempts = $resetCode->attempts + 1;

            if ($attempts >= 5) {
                DB::table('password_reset_codes')->where('id', $resetCode->id)->delete();
                $request->session()->forget('profile_password_reset');
                $request->session()->flash('profile_password_reset_stage', 'email');

                return back()->withErrors(['profile_reset' => 'Too many incorrect attempts. Request a new code.']);
            }

            DB::table('password_reset_codes')->where('id', $resetCode->id)->update(['attempts' => $attempts]);

            return back()->withErrors(['code' => 'That code is incorrect. Check the email and try again.']);
        }

        DB::table('password_reset_codes')->where('id', $resetCode->id)->delete();
        $request->session()->put('profile_password_reset', [
            'stage' => 'password',
            'user_id' => $request->user()->getKey(),
            'verified_until' => now()->addMinutes(10)->timestamp,
        ]);

        return back()->with('profile_password_reset_notice', 'Code verified. Choose your new password.');
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $reset = $request->session()->get('profile_password_reset');

        if (! is_array($reset)
            || ($reset['stage'] ?? null) !== 'password'
            || ($reset['user_id'] ?? null) !== $request->user()->getKey()
            || now()->timestamp > ($reset['verified_until'] ?? 0)) {
            $request->session()->forget('profile_password_reset');
            $request->session()->flash('profile_password_reset_stage', 'email');

            return back()->withErrors(['profile_reset' => 'Your verification expired. Request a new code.']);
        }

        $request->session()->flash('profile_password_reset_stage', 'password');

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();
        $user->password = $validated['password'];
        $user->must_change_password = false;
        $user->save();
        $request->session()->forget('profile_password_reset');

        return redirect()->route('profile.edit')->with('success', 'Password reset successfully.');
    }

    public function cancel(Request $request): RedirectResponse
    {
        $request->session()->forget('profile_password_reset');

        return redirect()->route('profile.edit');
    }
}
